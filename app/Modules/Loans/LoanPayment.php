<?php

namespace App\Modules\Loans;

use App\Modules\Core\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'amount_received',
        'received_transaction_id',
    ];

    protected $casts = [
        'amount_received' => 'decimal:2',
    ];

    /**
     * El pago pertenece a un Préstamo.
     */
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * El pago tiene un Ingreso asociado.
     */
    public function receivedTransaction()
    {
        return $this->belongsTo(Transaction::class, 'received_transaction_id');
    }
}
