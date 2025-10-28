<?php

namespace App\Modules\Core\TransactionStrategies;

use App\Modules\Core\Transaction;
use Illuminate\Validation\ValidationException;

/**
 * Define el contrato para todas las estrategias de manejo de transacciones.
 */
interface TransactionStrategyInterface
{
    /**
     * Valida y aplica una nueva transacción.
     * @param array $data Datos validados.
     * @return Transaction
     * @throws ValidationException
     */
    public function store(array $data): Transaction;

    /**
     * Valida, revierte la transacción antigua y aplica la nueva.
     * @param Transaction $transaction La transacción original.
     * @param array $data Los nuevos datos validados.
     * @return Transaction
     * @throws ValidationException
     */
    public function update(Transaction $transaction, array $data): Transaction;

    /**
     * Revierte y elimina una transacción.
     * @param Transaction $transaction La transacción a eliminar.
     * @return void
     * @throws ValidationException
     */
    public function delete(Transaction $transaction): void;
}
