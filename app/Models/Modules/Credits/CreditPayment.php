<?php

namespace App\Modules\Credits;

use App\Modules\Core\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_id',
        'amount_paid',
        'principal_amount',
        'interest_amount',
        'fee_amount',
        'payment_transaction_id',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'principal_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
    ];

    /**
     * El pago pertenece a un Crédito.
     */
    public function credit()
    {
        return $this->belongsTo(Credit::class);
    }

    /**
     * El pago tiene un Egreso asociado.
     */
    public function paymentTransaction()
    {
        return $this->belongsTo(Transaction::class, 'payment_transaction_id');
    }
}
