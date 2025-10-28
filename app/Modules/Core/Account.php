<?php

namespace App\Modules\Core;

use App\Modules\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    protected $table = 'accounts';


    protected $fillable = [
        'user_id',
        'name',
        'type',
        'balance',
        'is_exempt_4x1000',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_exempt_4x1000' => 'boolean',
    ];

    /**
     * Una cuenta pertenece a un Usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Una cuenta tiene muchas Transacciones.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
