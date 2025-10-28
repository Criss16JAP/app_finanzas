<?php

namespace App\Http\Controllers\Modules\Core;

use App\Http\Controllers\Controller;
use App\Modules\Core\Account;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    // ... index() y create() se quedan igual ...
    public function index(Request $request)
    {
        $accounts = $request->user()->accounts()->get();
        return view('modules.core.accounts.index', ['accounts' => $accounts]);
    }
    public function create()
    {
        return view('modules.core.accounts.create');
    }

    /**
     * Guarda la nueva cuenta en la base de datos.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['Bank', 'Cash', 'ManualCreditCard'])],
            'balance' => 'nullable|numeric',
        ]);

        $validatedData['user_id'] = $request->user()->id;

        // --- REGLA 4: Forzar saldo 0 si es Tarjeta Manual ---
        if ($validatedData['type'] == 'ManualCreditCard') {
            $validatedData['balance'] = 0;
        }
        // --- FIN REGLA 4 ---

        Account::create($validatedData);

        return redirect()->route('cuentas.index')
            ->with('success', '¡Cuenta creada exitosamente!');
    }

    // ... show() y edit() se quedan igual ...
    public function show(Request $request, Account $account)
    {
        if ($account->user_id != $request->user()->id) {
            abort(403);
        }
        return view('modules.core.accounts.show', ['account' => $account]);
    }
    public function edit(Request $request, Account $account)
    {
        if ($account->user_id != $request->user()->id) {
            abort(403);
        }
        return view('modules.core.accounts.edit', ['account' => $account]);
    }


    /**
     * Actualiza una cuenta existente.
     */
    public function update(Request $request, Account $account)
    {
        if ($account->user_id != $request->user()->id) {
            abort(403);
        }

        // Validación base
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'is_exempt_4x1000' => 'sometimes|boolean', // Añadimos la exención
        ]);

        DB::transaction(function () use ($request, $account, &$validatedData) {

            // --- REGLA 4: LÓGICA DE EXENCIÓN ÚNICA ---
            if ($request->input('is_exempt_4x1000') == true) {
                // Si el usuario marcó esta cuenta como exenta,
                // quitamos la marca de todas las demás.
                $request->user()->accounts()
                    ->where('id', '!=', $account->id)
                    ->update(['is_exempt_4x1000' => false]);

                $validatedData['is_exempt_4x1000'] = true;
            } else {
                $validatedData['is_exempt_4x1000'] = false;
            }
            // --- FIN REGLA 4 ---

            // --- Lógica de Saldo (Regla de Tarjetas Manuales) ---
            if ($account->type == 'ManualCreditCard') {
                // A una tarjeta manual solo se le puede cambiar el nombre y exención
                $account->update($validatedData);
            } else {
                // A las otras cuentas se les puede cambiar nombre, saldo y exención
                $validatedData = array_merge($validatedData, $request->validate([
                    'balance' => 'required|numeric|min:0',
                    'type' => ['required', Rule::in(['Bank', 'Cash'])],
                ]));
                $account->update($validatedData);
            }
        });

        return redirect()->route('cuentas.index')
            ->with('success', '¡Cuenta actualizada exitosamente!');
    }

    // ... destroy() se queda igual ...
    public function destroy(Request $request, Account $account)
    {
        if ($account->user_id != $request->user()->id) {
            abort(403);
        }
        $account->delete();
        return redirect()->route('cuentas.index')
            ->with('success', '¡Cuenta eliminada exitosamente!');
    }
}
