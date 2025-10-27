<?php

namespace App\Modules\Core;

use App\Modules\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'type',
        'amount',
        'description',
        'transaction_date',
        'parent_transaction_id',
        'related_account_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    /**
     * Una transacción pertenece a un Usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Una transacción pertenece a una Cuenta.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Una transacción pertenece a una Categoría.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Para el 4x1000: Una transacción (impuesto) pertenece a una transacción padre (egreso).
     */
    public function parentTransaction()
    {
        return $this->belongsTo(Transaction::class, 'parent_transaction_id');
    }

    /**
     * Para transferencias: La cuenta relacionada (origen o destino).
     */
    public function relatedAccount()
    {
        return $this->belongsTo(Account::class, 'related_account_id');
    }
}
