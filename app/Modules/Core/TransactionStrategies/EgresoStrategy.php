<?php

namespace App\Modules\Core\TransactionStrategies;

use App\Modules\Core\Account;
use App\Modules\Core\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EgresoStrategy implements TransactionStrategyInterface
{
    public function store(array $data): Transaction
    {
        $account = Account::find($data['account_id']);
        $amount = $data['amount'];

        // REGLAS 2 y 3: Validar Saldo
        $this->validateBalance($account, $amount);

        return DB::transaction(function () use ($data, $account, $amount) {
            // 1. Aplicar el egreso
            $account->balance -= $amount;
            $account->save();

            // 2. Crear la transacción
            return Transaction::create($data);
        });
    }

    public function update(Transaction $transaction, array $data): Transaction
    {
        $newAccount = Account::find($data['account_id']);
        $newAmount = $data['amount'];

        // REGLAS 2 y 3: Validar Saldo para la nueva transacción
        $this->validateBalance($newAccount, $newAmount, $transaction);

        return DB::transaction(function () use ($transaction, $data, $newAccount, $newAmount) {
            // REGLA 1: BUG DE ACTUALIZACIÓN CORREGIDO

            // 1. Revertir la transacción original (sea Ingreso o Egreso)
            $oldAccount = Account::find($transaction->account_id);
            if ($transaction->type == 'Ingreso') {
                $oldAccount->balance -= $transaction->amount;
            } else {
                $oldAccount->balance += $transaction->amount;
            }

            // 2. Aplicar la nueva transacción (Egreso)
            $newAccount->balance -= $newAmount;

            // 3. Guardar
            $oldAccount->save();
            if ($oldAccount->id != $newAccount->id) {
                $newAccount->save();
            }

            // 4. Actualizar el registro
            $transaction->update($data);
            return $transaction;
        });
    }

    public function delete(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            // 1. Revertir (sumar) el egreso
            $account = $transaction->account;
            $account->balance += $transaction->amount;
            $account->save();

            // 2. Eliminar la transacción
            $transaction->delete();
        });
    }

    /**
     * REGLAS 2 y 3: Lógica de validación de saldo.
     * No permite saldos negativos en Bank/Cash.
     */
    private function validateBalance(Account $account, float $amount, Transaction $originalTransaction = null): void
    {
        // Esta validación no aplica para Tarjetas de Crédito
        if ($account->type == 'ManualCreditCard') {
            return;
        }

        $availableBalance = $account->balance;

        // Si estamos actualizando, el saldo disponible "real"
        // incluye el monto que estamos a punto de revertir
        if ($originalTransaction && $originalTransaction->account_id == $account->id && $originalTransaction->type == 'Egreso') {
            $availableBalance += $originalTransaction->amount;
        }

        if ($availableBalance < $amount) {
            throw ValidationException::withMessages([
                'amount' => "Saldo insuficiente en la cuenta '{$account->name}'. Disponible: $" . number_format($availableBalance, 2)
            ]);
        }
    }
}
