<?php

namespace App\Http\Controllers\Modules\Core;

use App\Http\Controllers\Controller;
use App\Modules\Core\Account;
use App\Modules\Core\Category;
use App\Modules\Core\Transaction;
use App\Modules\Core\TransactionStrategies\TransactionStrategyFactory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    /**
     * Inyectamos la fábrica de estrategias.
     */
    public function __construct(private TransactionStrategyFactory $strategyFactory)
    {
    }

    /**
     * Muestra el historial de transacciones.
     */
    public function index(Request $request)
    {
        $transactions = $request->user()
            ->transactions()
            // Cargamos las relaciones para ver el nombre de la cuenta, categoría y cuenta relacionada
            ->with(['account', 'category', 'relatedAccount'])
            ->latest('transaction_date') // Ordena por fecha
            ->paginate(20); // Pagina

        return view('modules.core.transactions.index', [
            'transactions' => $transactions
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva transacción.
     */
    public function create(Request $request)
    {
        $accounts = $request->user()->accounts()->get();

        // Excluimos las Tarjetas Manuales de las "Cuentas Origen" de transferencias
        $transferFromAccounts = $accounts->where('type', '!=', 'ManualCreditCard');

        // Obtenemos categorías (las del usuario Y las por defecto)
        $categories = Category::where('user_id', $request->user()->id)
            ->orWhereNull('user_id')
            ->orderBy('type')
            ->get();

        $egresoCategories = $categories->where('type', 'Egreso');
        $ingresoCategories = $categories->where('type', 'Ingreso');

        return view('modules.core.transactions.create', [
            'accounts' => $accounts, // Todas las cuentas (para 'destino')
            'transferFromAccounts' => $transferFromAccounts, // Cuentas (para 'origen')
            'egresoCategories' => $egresoCategories,
            'ingresoCategories' => $ingresoCategories
        ]);
    }

    /**
     * Valida y guarda la nueva transacción usando la estrategia.
     */
    public function store(Request $request)
    {
        $userId = $request->user()->id;
        $type = $request->input('type');

        // 1. Validaciones comunes
        $validatedData = $request->validate([
            'type' => ['required', Rule::in(['Ingreso', 'Egreso', 'Transferencia'])],
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);

        // 2. Validaciones específicas del tipo
        if ($type == 'Transferencia') {
            $transferData = $request->validate([
                'account_from_id' => [
                    'required',
                    'different:account_to_id', // No se puede transferir a la misma cuenta
                    Rule::exists('accounts', 'id')->where('user_id', $userId)
                ],
                'account_to_id' => [
                    'required',
                    Rule::exists('accounts', 'id')->where('user_id', $userId)
                ],
            ]);
            $validatedData = array_merge($validatedData, $transferData);

        } else { // Ingreso o Egreso
            $txData = $request->validate([
                'account_id' => [
                    'required',
                    Rule::exists('accounts', 'id')->where('user_id', $userId)
                ],
                'category_id' => [
                    'required',
                    Rule::exists('categories', 'id')->where(function ($query) use ($userId) {
                        $query->where('user_id', $userId)->orWhereNull('user_id');
                    })
                ],
            ]);
            $validatedData = array_merge($validatedData, $txData);
        }

        $validatedData['user_id'] = $userId;

        try {
            // 3. Obtener y ejecutar la estrategia
            $strategy = $this->strategyFactory->getStrategy($validatedData['type']);
            $strategy->store($validatedData);

        } catch (ValidationException $e) {
            // Regresar con el error de validación (ej. Saldo Insuficiente)
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()]);
        }

        return redirect()->route('transacciones.index')
            ->with('success', '¡Movimiento registrado exitosamente!');
    }

    /**
     * Muestra el formulario de edición (bloqueando transferencias).
     */
    public function edit(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id != $request->user()->id)
            abort(403);

        // --- LÓGICA DE DETECCIÓN MEJORADA ---
        $isTransfer = $transaction->related_account_id != null;
        $isTax = optional($transaction->category)->name == 'Impuestos';

        if ($isTax || ($isTransfer && $transaction->type == 'Ingreso')) {
            return redirect()->route('transacciones.index')
                ->withErrors(['error' => 'Esta transacción no se puede editar. Edite la transacción de egreso principal.']);
        }
        // --- FIN LÓGICA ---

        $accounts = $request->user()->accounts()->get();
        $transferFromAccounts = $accounts->where('type', '!=', 'ManualCreditCard');
        $categories = Category::where('user_id', $request->user()->id)->orWhereNull('user_id')->orderBy('type')->get();
        $egresoCategories = $categories->where('type', 'Egreso');
        $ingresoCategories = $categories->where('type', 'Ingreso');

        // Buscamos la transacción de ingreso si esta es una transferencia
        $transferInfo = null;
        if ($isTransfer) {
            $transferInfo = Transaction::where('parent_transaction_id', $transaction->id)->first();
        }

        return view('modules.core.transactions.edit', [
            'transaction' => $transaction,
            'transferInfo' => $transferInfo, // Enviamos la info de la transferencia
            'isTransfer' => $isTransfer,     // Bandera para la vista
            'accounts' => $accounts,
            'transferFromAccounts' => $transferFromAccounts,
            'egresoCategories' => $egresoCategories,
            'ingresoCategories' => $ingresoCategories
        ]);
    }

    /**
     * Actualiza una transacción.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $userId = $request->user()->id;
        if ($transaction->user_id != $userId)
            abort(403);

        $type = $request->input('type');

        // 1. Validaciones comunes
        $validatedData = $request->validate([
            'type' => ['required', Rule::in(['Ingreso', 'Egreso', 'Transferencia'])],
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);

        // 2. Validaciones específicas
        if ($type == 'Transferencia') {
            $transferData = $request->validate([
                'account_from_id' => ['required', 'different:account_to_id', Rule::exists('accounts', 'id')->where('user_id', $userId)],
                'account_to_id' => ['required', Rule::exists('accounts', 'id')->where('user_id', $userId)],
            ]);
            $validatedData = array_merge($validatedData, $transferData);
        } else {
            $txData = $request->validate([
                'account_id' => ['required', Rule::exists('accounts', 'id')->where('user_id', $userId)],
                'category_id' => ['required', Rule::exists('categories', 'id')->where(fn($q) => $q->where('user_id', $userId)->orWhereNull('user_id'))],
            ]);
            $validatedData = array_merge($validatedData, $txData);
        }

        $validatedData['user_id'] = $userId;

        try {
            // 3. DECIDIR LA ESTRATEGIA
            // Si el *tipo original* era una transferencia (tenía related_account),
            // o si el *nuevo tipo* es una transferencia, usamos la TransferenciaStrategy.
            $strategyType = ($transaction->related_account_id || $type == 'Transferencia') ? 'Transferencia' : $type;

            // PERO, si el tipo original es 'Egreso' y el nuevo es 'Transferencia',
            // no podemos "actualizar" de un tipo a otro.
            if ($transaction->related_account_id == null && $type == 'Transferencia') {
                throw ValidationException::withMessages(['type' => 'No se puede convertir un Egreso simple en una Transferencia.']);
            }
            if ($transaction->related_account_id && $type != 'Transferencia') {
                throw ValidationException::withMessages(['type' => 'No se puede convertir una Transferencia en un Egreso simple.']);
            }

            $strategy = $this->strategyFactory->getStrategy($strategyType);
            $strategy->update($transaction, $validatedData);

        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()]);
        }

        return redirect()->route('transacciones.index')
            ->with('success', '¡Transacción actualizada exitosamente!');
    }

    /**
     * Elimina una transacción.
     */
    public function destroy(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id != $request->user()->id)
            abort(403);

        $isTax = optional($transaction->category)->name == 'Impuestos';

        // Si es un impuesto o el ingreso de una transferencia, no se puede borrar
        if ($isTax || ($transaction->related_account_id && $transaction->type == 'Ingreso')) {
            return redirect()->route('transacciones.index')
                ->withErrors(['error' => 'Esta transacción no se puede eliminar. Elimine la transacción de egreso principal.']);
        }

        try {
            // Decidir la estrategia de borrado
            $strategyType = $transaction->related_account_id ? 'Transferencia' : $transaction->type;
            $strategy = $this->strategyFactory->getStrategy($strategyType);
            $strategy->delete($transaction);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar: ' . $e->getMessage()]);
        }

        return redirect()->route('transacciones.index')
            ->with('success', '¡Transacción eliminada exitosamente!');
    }
}
