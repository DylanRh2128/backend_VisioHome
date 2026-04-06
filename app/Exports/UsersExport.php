<?php

namespace App\Exports;

use App\Models\Usuario;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Usuario::query()->where('idRol', 2); // Solo clientes

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
            'Fecha Registro',
            'Estado',
            'Ciudad'
        ];
    }

    public function map($user): array
    {
        return [
            $user->docUsuario,
            $user->nombre,
            $user->correo,
            $user->creado_en ? $user->creado_en->format('d/m/Y H:i') : 'N/A',
            $user->activo ? 'Activo' : 'Inactivo',
            $user->ciudad ?: 'N/A'
        ];
    }
}
