<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';
    protected $primaryKey = 'idPago';
    public $timestamps = false;

    // Estados permitidos por CHECK constraint (pagos_chk_2)
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_APROBADO  = 'aprobado';
    const ESTADO_RECHAZADO = 'rechazado';

    protected $casts = [
        'monto' => 'float',
    ];

    protected $fillable = [
        'docUsuario',
        'idPropiedad',
        'idCita',
        'monto',
        'metodoPago',
        'estado',
        'referencia',
        'external_reference',
        'mp_preference_id',
        'fecha',
    ];

    // Relación: un pago pertenece a una propiedad
    public function propiedad()
    {
        return $this->belongsTo(Propiedad::class, 'idPropiedad', 'idPropiedad');
    }

    // Relación: un pago pertenece a una cita
    public function cita()
    {
        return $this->belongsTo(Cita::class, 'idCita', 'idCita');
    }
}
