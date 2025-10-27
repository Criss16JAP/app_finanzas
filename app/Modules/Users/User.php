<?php

namespace App\Modules\Users;


// Asegúrate de importar todas estas clases
use App\Modules\Core\Account;
use App\Modules\Core\Category;
use App\Modules\Core\Transaction;
use App\Modules\Credits\Credit;
use App\Modules\Loans\Loan;
use App\Modules\Projects\Project;
use App\Modules\SmartCards\SmartCreditCard;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * El nombre de la tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */


    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_type',
        'current_plan_id',
        'subscription_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'password' => 'hashed',
    ];

    // --- RELACIONES ---

    /**
     * Un usuario pertenece a un Plan.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'current_plan_id');
    }

    /**
     * Un usuario tiene muchas Cuentas (Módulo 1).
     */
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    /**
     * Un usuario tiene muchas Transacciones (Módulo 1).
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Un usuario tiene muchas Categorías personalizadas (Módulo 2).
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Un usuario tiene muchos Créditos (Módulo 3).
     */
    public function credits()
    {
        return $this->hasMany(Credit::class);
    }

    /**
     * Un usuario tiene muchas Tarjetas Inteligentes (Módulo 4).
     */
    public function smartCreditCards()
    {
        return $this->hasMany(SmartCreditCard::class);
    }

    /**
     * Un usuario tiene muchos Préstamos a terceros (Módulo 5).
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Un usuario tiene muchos Proyectos (Módulo 6).
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
