<?php

namespace App\Exports;

use App\Models\Usuario;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AgentsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Usuario::query()->where('idRol', 3); // Solo agentes

        if (!empty($this->filters['status'])) {
            $query->where('activo', $this->filters['status'] === 'activo' ? 1 : 0);
        }

        if (!empty($this->filters['date_from'])) {
            $query->where('creado_en', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->where('creado_en', '<=', $this->filters['date_to'] . ' 23:59:59');
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%$search%")
                  ->orWhere('correo', 'LIKE', "%$search%");
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID Documento',
            'Nombre',
            'Email',
            'Estado',
            'CV Cargado'
        ];
    }

    public function map($agent): array
    {
        return [
            $agent->docUsuario,
            $agent->nombre,
            $agent->correo,
            $agent->activo ? 'Activo' : 'Inactivo',
            $agent->cv_path ? 'Sí' : 'No'
        ];
    }
}
