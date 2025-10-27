<?php

namespace App\Modules\SmartCards;

use App\Modules\Core\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'smart_credit_card_id',
        'original_transaction_id',
        'total_amount',
        'installments_number',
        'installments_paid',
        'monthly_payment',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
    ];

    /**
     * La cuota pertenece a una Tarjeta Inteligente.
     */
    public function smartCreditCard()
    {
        return $this->belongsTo(SmartCreditCard::class);
    }

    /**
     * La cuota se origina de una Transacción (Egreso) específica.
     */
    public function originalTransaction()
    {
        return $this->belongsTo(Transaction::class, 'original_transaction_id');
    }
}
