<?php

namespace App\Modules\Users;

use App\Modules\Admin\Staff;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'receipt_image_path',
        'rejection_reason',
        'processed_by_staff_id',
    ];

    /**
     * El reporte de pago pertenece a un Usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * El reporte de pago es para un Plan.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * El reporte fue procesado por un miembro del Staff.
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'processed_by_staff_id');
    }
}
