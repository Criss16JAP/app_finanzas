<?php

namespace App\Modules\Projects;

use App\Modules\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'estimated_time',
        'subtotal_amount',
        'status',
    ];

    protected $casts = [
        'subtotal_amount' => 'decimal:2',
    ];

    /**
     * El proyecto pertenece a un Usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Un proyecto tiene muchas entradas (costos y recibidos).
     */
    public function entries()
    {
        return $this->hasMany(ProjectEntry::class);
    }

    /**
     * Relación helper para obtener solo los costos.
     */
    public function costs()
    {
        return $this->hasMany(ProjectEntry::class)->where('type', 'Cost');
    }

    /**
     * Relación helper para obtener solo los pagos recibidos.
     */
    public function received()
    {
        return $this->hasMany(ProjectEntry::class)->where('type', 'Received');
    }
}
