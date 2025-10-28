<?php

namespace App\Modules\Core\TransactionStrategies;

use InvalidArgumentException;

class TransactionStrategyFactory
{
    /**
     * Devuelve la instancia de la estrategia correcta basada en el tipo.
     *
     * @param string $type ('Ingreso', 'Egreso', etc.)
     * @return TransactionStrategyInterface
     * @throws InvalidArgumentException
     */
    public function getStrategy(string $type): TransactionStrategyInterface
    {
        return match ($type) {
            'Ingreso' => new IngresoStrategy(),
            'Egreso' => new EgresoStrategy(),
            'Transferencia' => new TransferenciaStrategy(), // <-- AÑADIDO
            default => throw new InvalidArgumentException("Tipo de transacción no válido: {$type}"),
        };
    }
}
