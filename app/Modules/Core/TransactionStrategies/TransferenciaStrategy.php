<?php

namespace App\Modules\Core\TransactionStrategies;

use App\Modules\Core\Account;
use App\Modules\Core\Category;
use App\Modules\Core\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransferenciaStrategy implements TransactionStrategyInterface
{
    private ?Category $taxCategory = null;

    public function store(array $data): Transaction
    {
        $accountFrom = Account::find($data['account_from_id']);
        $accountTo = Account::find($data['account_to_id']);
        $amount = $data['amount'];

        // 1. Validar Saldo Origen
        $this->validateBalance($accountFrom, $amount);

        // --- NUEVA REGLA: Validar Saldo Destino (si es tarjeta) ---
        $this->validateManualCardPayment($accountTo, $amount);
        // --- FIN NUEVA REGLA ---

        return DB::transaction(function () use ($data, $accountFrom, $accountTo, $amount) {

            $accountFrom->balance -= $amount;
            $accountTo->balance += $amount;

            $taxAmount = $this->calculateTax($accountFrom, $amount);
            if ($taxAmount > 0) {
                $accountFrom->balance -= $taxAmount;
            }

            $accountFrom->save();
            $accountTo->save();

            // 1. Crear Egreso
            $egresoTx = Transaction::create([
                'user_id' => $data['user_id'],
                'account_id' => $accountFrom->id,
                'category_id' => $this->getCategory('Egreso', $data['user_id'])->id,
                'type' => 'Egreso', // Sigue siendo tipo Egreso
                'amount' => $amount,
                'description' => $data['description'] ?: "Transferencia a {$accountTo->name}",
                'transaction_date' => $data['transaction_date'],
                'related_account_id' => $accountTo->id, // Enlace
            ]);

            // 2. Crear Ingreso
            Transaction::create([
                'user_id' => $data['user_id'],
                'account_id' => $accountTo->id,
                'category_id' => $this->getCategory('Ingreso', $data['user_id'])->id,
                'type' => 'Ingreso',
                'amount' => $amount,
                'description' => $data['description'] ?: "Transferencia desde {$accountFrom->name}",
                'transaction_date' => $data['transaction_date'],
                'related_account_id' => $accountFrom->id, // Enlace
                'parent_transaction_id' => $egresoTx->id, // Enlace
            ]);

            // 3. Crear Impuesto (si aplica)
            if ($taxAmount > 0) {
                $this->createTaxTransaction($data, $accountFrom, $taxAmount, $egresoTx);
            }

            return $egresoTx;
        });
    }

    public function update(Transaction $transaction, array $data): Transaction
    {
        $transactions = $this->findLinkedTransactions($transaction);

        $newAmount = $data['amount'];
        $newAccountFrom = Account::find($data['account_from_id']);
        $newAccountTo = Account::find($data['account_to_id']);

        // Las validaciones ahora van DENTRO de la transacción DB

        return DB::transaction(function () use ($data, $transactions, $newAmount, $newAccountFrom, $newAccountTo) {

            // 1. REVERTIR TRANSACCIONES ANTIGUAS
            $this->revertTransaction($transactions['egreso']);
            if ($transactions['ingreso']) {
                $this->revertTransaction($transactions['ingreso']);
            }
            if ($transactions['tax']) {
                $this->revertTransaction($transactions['tax']);
            }

            // 2. REFRESCAR LOS MODELOS
            $newAccountFrom->refresh();
            $newAccountTo->refresh();

            // 3. VALIDAR AHORA (con saldos actualizados post-reversión)
            $this->validateBalance($newAccountFrom, $newAmount);
            $this->validateManualCardPayment($newAccountTo, $newAmount); // Usa el saldo refrescado

            // 4. APLICAR NUEVOS SALDOS
            $newAccountFrom->balance -= $newAmount;
            $newAccountTo->balance += $newAmount;

            $newTaxAmount = $this->calculateTax($newAccountFrom, $newAmount);
            if ($newTaxAmount > 0) {
                $newAccountFrom->balance -= $newTaxAmount;
            }

            $newAccountFrom->save();
            $newAccountTo->save();

            // --- INICIO CÓDIGO FALTANTE ---
            // 5. ACTUALIZAR/CREAR TRANSACCIONES

            // Actualizar Egreso
            $transactions['egreso']->update([
                'account_id' => $newAccountFrom->id,
                'amount' => $newAmount,
                'description' => $data['description'] ?: "Transferencia a {$newAccountTo->name}",
                'transaction_date' => $data['transaction_date'],
                'related_account_id' => $newAccountTo->id,
            ]);

            // Actualizar Ingreso (si existe)
            if ($transactions['ingreso']) {
                $transactions['ingreso']->update([
                    'account_id' => $newAccountTo->id,
                    'amount' => $newAmount,
                    'description' => $data['description'] ?: "Transferencia desde {$newAccountFrom->name}",
                    'transaction_date' => $data['transaction_date'],
                    'related_account_id' => $newAccountFrom->id,
                ]);
            }

            // MANEJAR IMPUESTO
            // Eliminar impuesto viejo (si existía)
            if ($transactions['tax']) {
                $transactions['tax']->delete();
            }
            // Crear impuesto nuevo (si aplica)
            if ($newTaxAmount > 0) {
                $this->createTaxTransaction($data, $newAccountFrom, $newTaxAmount, $transactions['egreso']);
            }
            // --- FIN CÓDIGO FALTANTE ---

            return $transactions['egreso'];
        });
    }

    public function delete(Transaction $transaction): void
    {
        $transactions = $this->findLinkedTransactions($transaction);

        DB::transaction(function () use ($transactions) {
            $this->revertTransaction($transactions['egreso']);
            $transactions['egreso']->delete();

            if ($transactions['ingreso']) {
                $this->revertTransaction($transactions['ingreso']);
                $transactions['ingreso']->delete();
            }
            if ($transactions['tax']) {
                $this->revertTransaction($transactions['tax']);
                $transactions['tax']->delete();
            }
        });
    }

    // --- MÉTODOS DE AYUDA ---

    private function findLinkedTransactions(Transaction $transaction): array
    {
        $egreso = null;
        $ingreso = null;
        $tax = null;

        if ($transaction->type == 'Ingreso' && $transaction->parent_transaction_id) {
            $egreso = Transaction::find($transaction->parent_transaction_id);
            $ingreso = $transaction;
        } else {
            $egreso = (optional($transaction->category)->name == 'Impuestos' && $transaction->parent_transaction_id)
                ? Transaction::find($transaction->parent_transaction_id)
                : $transaction;

            $ingreso = Transaction::where('parent_transaction_id', $egreso->id)
                ->where('type', 'Ingreso')
                ->first();
        }

        $tax = Transaction::where('parent_transaction_id', $egreso->id)
            ->where('category_id', $this->getTaxCategory($egreso->user_id)->id)
            ->first();

        return compact('egreso', 'ingreso', 'tax');
    }

    private function revertTransaction(Transaction $transaction): void
    {
        $account = $transaction->account;
        if ($transaction->type == 'Ingreso') {
            $account->balance -= $transaction->amount;
        } else {
            $account->balance += $transaction->amount;
        }
        $account->save();
    }

    private function calculateTax(Account $account, float $amount): float
    {
        if ($account->type == 'Bank' && !$account->is_exempt_4x1000) {
            return $amount * 0.004;
        }
        return 0;
    }

    private function createTaxTransaction(array $data, Account $account, float $taxAmount, Transaction $parent): void
    {
        Transaction::create([
            'user_id' => $data['user_id'],
            'account_id' => $account->id,
            'category_id' => $this->getTaxCategory($data['user_id'])->id,
            'type' => 'Egreso',
            'amount' => $taxAmount,
            'description' => "Impuesto 4x1000 por transferencia",
            'transaction_date' => $data['transaction_date'],
            'parent_transaction_id' => $parent->id,
        ]);
    }

    private function validateBalance(Account $account, float $amount): void
    {
        if ($account->type == 'ManualCreditCard')
            return;
        if ($account->balance < $amount) {
            throw ValidationException::withMessages([
                'amount' => "Saldo insuficiente en la cuenta '{$account->name}'. Disponible: $" . number_format($account->balance, 2)
            ]);
        }
    }

    private function validateManualCardPayment(Account $accountTo, float $amount, float $effectiveBalance = null): void
    {

        $balance = $effectiveBalance ?? $accountTo->balance;

        // El resto de la lógica es igual, pero ahora usa $balance
        if ($accountTo->type == 'ManualCreditCard' && ($balance + $amount) > 0.001) { // Usamos un margen
            $debt = abs($balance);
            throw ValidationException::withMessages([
                'amount' => "El pago excede la deuda de la tarjeta '{$accountTo->name}'. Saldo: $" . number_format($balance, 2) . ". Monto máximo a pagar: $" . number_format($debt, 2)
            ]);
        }
    }

    private function getCategory(string $type, int $userId): Category
    {
        return Category::where('name', 'Transferencia')->where('type', $type)
            ->where(fn($q) => $q->where('user_id', $userId)->orWhereNull('user_id'))
            ->firstOrFail();
    }

    private function getTaxCategory(int $userId): Category
    {
        if ($this->taxCategory)
            return $this->taxCategory;
        $this->taxCategory = Category::where('name', 'Impuestos')->where('type', 'Egreso')
            ->where(fn($q) => $q->where('user_id', $userId)->orWhereNull('user_id'))
            ->firstOrFail();
        return $this->taxCategory;
    }
}
