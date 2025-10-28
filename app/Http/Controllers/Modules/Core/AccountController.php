<?php

namespace App\Http\Controllers\Modules\Core;

use App\Http\Controllers\Controller;
use App\Modules\Core\Account;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    /**
     * Muestra una lista de las cuentas del usuario.
     */
    public function index(Request $request)
    {
        $accounts = $request->user()->accounts()->get();

        return view('modules.core.accounts.index', ['accounts' => $accounts]);
    }

    /**
     * Muestra el formulario para crear una nueva cuenta.
     */
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

        // Asignamos el ID del usuario logueado
        $validatedData['user_id'] = $request->user()->id;

        Account::create($validatedData);

        return redirect()->route('cuentas.index')
            ->with('success', '¡Cuenta creada exitosamente!');
    }

    /**
     * Muestra una cuenta específica (opcional).
     */
    public function show(Request $request, Account $account)
    {
        // Verificación de autorización
        if ($account->user_id != $request->user()->id) {
            abort(403);
        }

        return view('modules.core.accounts.show', ['account' => $account]);
    }

    /**
     * Muestra el formulario para editar una cuenta.
     */
    public function edit(Request $request, Account $account)
    {


        // Verificación de autorización
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
        // Verificación de autorización
        if ($account->user_id != $request->user()->id) {
            abort(403);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['Bank', 'Cash', 'ManualCreditCard'])],
            'balance' => 'required|numeric', // En la edición, el saldo es requerido
        ]);

        $account->update($validatedData);

        return redirect()->route('cuentas.index')
            ->with('success', '¡Cuenta actualizada exitosamente!');
    }

    /**
     * Elimina una cuenta.
     */
    public function destroy(Request $request, Account $account)
    {
        // Verificación de autorización
        if ($account->user_id != $request->user()->id) {
            abort(403);
        }

        // (En el futuro, podríamos añadir una verificación
        // para no borrar cuentas con transacciones)

        $account->delete();

        return redirect()->route('cuentas.index')
            ->with('success', '¡Cuenta eliminada exitosamente!');
    }
}
