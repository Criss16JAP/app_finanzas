<?php

namespace App\Modules\Admin;

use App\Modules\Users\PaymentReport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Importante: usa Authenticatable
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Staff extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Especificamos la tabla porque el nombre del modelo no es plural de 'staff'
     */
    protected $table = 'staff';

    /**
     * Especificamos el 'guard' de autenticación que usará.
     */
    protected $guard = 'staff';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Un miembro del staff ha procesado muchos reportes de pago.
     */
    public function paymentReportsProcessed()
    {
        return $this->hasMany(PaymentReport::class, 'processed_by_staff_id');
    }
}
