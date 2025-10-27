<?php

namespace App\Modules\SmartCards;

use App\Modules\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartCreditCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'credit_limit',
        'current_balance',
        'interest_rate',
        'statement_day',
        'payment_day',
        'monthly_fee',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'monthly_fee' => 'decimal:2',
        'interest_rate' => 'float',
    ];

    /**
     * La tarjeta pertenece a un Usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Una tarjeta tiene muchas compras a cuotas.
     */
    public function installments()
    {
        return $this->hasMany(CardInstallment::class);
    }
}
