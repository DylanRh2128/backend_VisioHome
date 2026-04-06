<?php

namespace App\Exports;

use App\Models\Usuario;
use App\Models\Propiedad;
use App\Models\Pago;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

class GeneralSummaryExport implements FromCollection, WithHeadings, WithTitle
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $stats = [
            [
                'Categoría' => 'Usuarios',
                'Total' => Usuario::clientes()->count(),
                'Activos' => Usuario::clientes()->where('activo', 1)->count(),
                'Detalle' => 'Clientes registrados'
            ],
            [
                'Categoría' => 'Agentes',
                'Total' => Usuario::agentes()->count(),
                'Activos' => Usuario::agentes()->where('activo', 1)->count(),
                'Detalle' => 'Agentes activos'
            ],
            [
                'Categoría' => 'Propiedades',
                'Total' => Propiedad::count(),
                'Activos' => Propiedad::where('estado', 'disponible')->count(),
                'Detalle' => 'Propiedades disponibles'
            ],
            [
                'Categoría' => 'Pagos/Ventas',
                'Total' => Pago::where('estado', 'aprobado')->count(),
                'Activos' => Pago::where('estado', 'aprobado')->sum('monto'),
                'Detalle' => 'Monto total aprobado'
            ]
        ];

        return new Collection($stats);
    }

    public function headings(): array
    {
        return [
            'Categoría',
            'Total / Cantidad',
            'Activos / Monto',
            'Detalle'
        ];
    }

    public function title(): string
    {
        return 'Resumen General VisioHome';
    }
}
