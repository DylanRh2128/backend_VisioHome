<?php

namespace App\Exports;

use App\Models\Propiedad;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PropertiesExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Propiedad::query();

        if (!empty($this->filters['status'])) {
            $query->where('estado', $this->filters['status']);
        }

        if (!empty($this->filters['tipo'])) {
            $query->where('tipo', $this->filters['tipo']);
        }

        if (!empty($this->filters['ubicacion'])) {
            $query->where('ubicacion', 'LIKE', '%' . $this->filters['ubicacion'] . '%');
        }

        if (!empty($this->filters['price_min'])) {
            $query->where('precio', '>=', $this->filters['price_min']);
        }

        if (!empty($this->filters['price_max'])) {
            $query->where('precio', '<=', $this->filters['price_max']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->where('creado_en', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->where('creado_en', '<=', $this->filters['date_to'] . ' 23:59:59');
        }

        if (!empty($this->filters['nitInmobiliaria'])) {
            $query->where('nitInmobiliaria', $this->filters['nitInmobiliaria']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID Propiedad',
            'Título',
            'Precio',
            'Estado',
            'Tipo',
            'Ubicación',
            'NIT Inmobiliaria',
            'Fecha Publicación'
        ];
    }

    public function map($prop): array
    {
        return [
            $prop->idPropiedad,
            $prop->titulo,
            $prop->precio,
            $prop->estado,
            $prop->tipo,
            $prop->ubicacion,
            $prop->nitInmobiliaria,
            $prop->creado_en
        ];
    }
}
