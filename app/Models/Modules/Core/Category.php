<?php

namespace App\Modules\Core;

use App\Modules\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
    ];

    /**
     * Una categoría personalizada pertenece a un Usuario.
     * (Las categorías por defecto tendrán user_id nulo).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Una categoría tiene muchas Transacciones.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
