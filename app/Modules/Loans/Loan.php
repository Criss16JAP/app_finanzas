<?php

namespace App\Modules\Loans;

use App\Modules\Core\Transaction;
use App\Modules\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'borrower_name',
        'initial_amount',
        'outstanding_balance',
        'interest_rate',
        'interest_type',
        'disbursement_transaction_id',
    ];

    protected $casts = [
        'initial_amount' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
        'interest_rate' => 'float',
    ];

    /**
     * El préstamo pertenece a un Usuario (el prestamista).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * El préstamo tiene un Egreso de desembolso asociado.
     */
    public function disbursementTransaction()
    {
        return $this->belongsTo(Transaction::class, 'disbursement_transaction_id');
    }

    /**
     * Un préstamo tiene muchos pagos recibidos.
     */
    public function payments()
    {
        return $this->hasMany(LoanPayment::class);
    }
}
