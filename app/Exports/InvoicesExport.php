<?php

namespace App\Exports;

use App\Models\Pago;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoicesExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Pago::query()
            ->with(['propiedad', 'cita.agente', 'cita.usuario']);

        if (!empty($this->filters['status'])) {
            $query->where('estado', $this->filters['status']);
        }

        if (!empty($this->filters['amount_min'])) {
            $query->where('monto', '>=', $this->filters['amount_min']);
        }

        if (!empty($this->filters['amount_max'])) {
            $query->where('monto', '<=', $this->filters['amount_max']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->where('fecha', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->where('fecha', '<=', $this->filters['date_to'] . ' 23:59:59');
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID Pago',
            'Referencia MP',
            'Cliente',
            'Agente',
            'Propiedad',
            'Monto',
            'Método',
            'Estado',
            'Fecha'
        ];
    }

    public function map($pago): array
    {
        return [
            $pago->idPago,
            $pago->mp_preference_id ?? 'N/A',
            $pago->cita->usuario->nombre ?? 'N/A',
            $pago->cita->agente->nombre ?? 'N/A',
            $pago->propiedad->titulo ?? 'N/A',
            $pago->monto,
            $pago->metodoPago,
            $pago->estado,
            $pago->fecha
        ];
    }
}
