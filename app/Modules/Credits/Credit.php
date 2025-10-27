<?php

namespace App\Modules\Credits;

use App\Modules\Core\Transaction;
use App\Modules\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'initial_amount',
        'outstanding_balance',
        'interest_rate',
        'fixed_fee_amount',
        'payment_day',
        'total_installments',
        'disbursement_transaction_id',
    ];

    protected $casts = [
        'initial_amount' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
        'fixed_fee_amount' => 'decimal:2',
        'interest_rate' => 'float',
    ];

    /**
     * El crédito pertenece a un Usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * El crédito tiene un Ingreso de desembolso asociado.
     */
    public function disbursementTransaction()
    {
        return $this->belongsTo(Transaction::class, 'disbursement_transaction_id');
    }

    /**
     * Un crédito tiene muchos pagos.
     */
    public function payments()
    {
        return $this->hasMany(CreditPayment::class);
    }
}
