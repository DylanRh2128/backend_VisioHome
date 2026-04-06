<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Propiedades - VisioHome</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 2px solid #6b0000; padding: 20px 0; margin-bottom: 30px; }
        .logo { font-size: 28px; font-weight: bold; color: #6b0000; letter-spacing: 2px; }
        .meta { font-size: 12px; color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; table-layout: fixed; }
        th { background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 12px; text-align: left; font-size: 11px; color: #6b0000; text-transform: uppercase; }
        td { border: 1px solid #dee2e6; padding: 10px; font-size: 10px; word-wrap: break-word; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; padding: 15px 0; border-top: 1px solid #eee; }
        .price { font-weight: bold; color: #000; }
        .status-badge { font-weight: bold; text-transform: uppercase; font-size: 9px; padding: 2px 5px; border-radius: 3px; }
        .status-disponible { background-color: #e6f4ea; color: #1e8e3e; }
        .status-vendido { background-color: #fce8e6; color: #d93025; }
        .status-rentado { background-color: #e8f0fe; color: #1967d2; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">VISIOHOME</div>
        <div style="font-size: 18px; margin-top: 5px; font-weight: bold;">Inventario General de Propiedades</div>
        <div class="meta">Generado el: {{ $date }}</div>
    </div>

    <div class="content">
        @if(!empty($filters['status']) || !empty($filters['type']) || !empty($filters['search']))
        <div style="margin-bottom: 15px; font-size: 10px; font-style: italic; color: #555; background: #f9f9f9; padding: 8px; border-radius: 4px;">
            <strong>Filtros aplicados:</strong> 
            @if(!empty($filters['search'])) Búsqueda: "{{ $filters['search'] }}" | @endif
            @if(!empty($filters['status'])) Estado: {{ strtoupper($filters['status']) }} | @endif
            @if(!empty($filters['type'])) Tipo: {{ strtoupper($filters['type']) }} @endif
        </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">ID</th>
                    <th style="width: 30%;">Propiedad</th>
                    <th style="width: 20%;">Ubicación</th>
                    <th style="width: 10%;">Tipo</th>
                    <th style="width: 15%;">Precio</th>
                    <th style="width: 15%;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($properties as $prop)
                <tr>
                    <td>#{{ $prop->idPropiedad }}</td>
                    <td>
                        <div style="font-weight: bold;">{{ $prop->titulo }}</div>
                        <div style="font-size: 8px; color: #777;">Área: {{ $prop->area }}m² | Hab: {{ $prop->habitaciones }}</div>
                    </td>
                    <td>{{ $prop->ubicacion }}</td>
                    <td>{{ $prop->tipo }}</td>
                    <td class="price">${{ number_format($prop->precio, 0, ',', '.') }}</td>
                    <td>
                        <span class="status-badge status-{{ strtolower($prop->estado ?: 'disponible') }}">
                            {{ strtoupper($prop->estado ?: 'disponible') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        © {{ date('Y') }} VisioHome - Catálogo de Inmuebles Digitalizado
    </div>
</body>
</html>
