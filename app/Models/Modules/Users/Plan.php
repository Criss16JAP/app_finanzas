<?php

namespace App\Modules\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price_monthly',
        'price_semester',
        'price_yearly',
    ];

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_semester' => 'decimal:2',
        'price_yearly' => 'decimal:2',
    ];

    /**
     * Un plan puede tener muchos Usuarios.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'current_plan_id');
    }
}
