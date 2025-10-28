<?php

namespace App\Modules\Core\TransactionStrategies;

use App\Modules\Core\Account;
use App\Modules\Core\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IngresoStrategy implements TransactionStrategyInterface
{
    public function store(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $account = Account::find($data['account_id']);

            // 1. Aplicar el ingreso
            $account->balance += $data['amount'];
            $account->save();

            // 2. Crear la transacción
            return Transaction::create($data);
        });
    }

    public function update(Transaction $transaction, array $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            // REGLA 1: BUG DE ACTUALIZACIÓN CORREGIDO

            // 1. Revertir la transacción original (sea Ingreso o Egreso)
            $oldAccount = Account::find($transaction->account_id);
            if ($transaction->type == 'Ingreso') {
                $oldAccount->balance -= $transaction->amount;
            } else {
                $oldAccount->balance += $transaction->amount;
            }

            // 2. Aplicar la nueva transacción (Ingreso)
            $newAccount = Account::find($data['account_id']);
            $newAccount->balance += $data['amount'];

            // 3. Guardar (manejando si es la misma cuenta)
            $oldAccount->save();
            if ($oldAccount->id != $newAccount->id) {
                $newAccount->save();
            }

            // 4. Actualizar el registro de la transacción
            $transaction->update($data);
            return $transaction;
        });
    }

    public function delete(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            // 1. Revertir (restar) el ingreso
            $account = $transaction->account;
            $account->balance -= $transaction->amount;
            $account->save();

            // 2. Eliminar la transacción
            $transaction->delete();
        });
    }
}
