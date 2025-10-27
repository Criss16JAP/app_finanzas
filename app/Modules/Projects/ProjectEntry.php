<?php

namespace App\Modules\Projects;

use App\Modules\Core\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'type',
        'amount',
        'description',
        'linked_transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * La entrada pertenece a un Proyecto.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * La entrada (costo o recibido) tiene una Transacción (egreso o ingreso) asociada.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'linked_transaction_id');
    }
}
