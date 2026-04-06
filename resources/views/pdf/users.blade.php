<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Usuarios - VisioHome</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #6b0000; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 28px; font-weight: bold; color: #6b0000; letter-spacing: 2px; }
        .meta { font-size: 12px; color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 12px; text-align: left; font-size: 13px; color: #6b0000; }
        td { border: 1px solid #dee2e6; padding: 10px; font-size: 12px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; padding: 10px 0; }
        .status-active { color: green; font-weight: bold; }
        .status-inactive { color: red; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">VISIOHOME</div>
        <div style="font-size: 18px; margin-top: 5px;">Reporte Administrativo de Usuarios</div>
        <div class="meta">Generado el: {{ $date }}</div>
    </div>

    <div class="content">
        @if(!empty($filters['status']) || !empty($filters['date_from']))
        <div style="margin-bottom: 15px; font-size: 11px; font-style: italic;">
            Filtros aplicados: 
            @if(!empty($filters['status'])) Estado: {{ ucfirst($filters['status']) }} | @endif
            @if(!empty($filters['date_from'])) Desde: {{ $filters['date_from'] }} | @endif
            @if(!empty($filters['date_to'])) Hasta: {{ $filters['date_to'] }} @endif
        </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Registro</th>
                    <th>Estado</th>
                    <th>Ciudad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->nombre }}</td>
                    <td>{{ $user->correo }}</td>
                    <td>{{ $user->creado_en ? $user->creado_en->format('d/m/Y') : 'N/A' }}</td>
                    <td>
                        <span class="{{ $user->activo ? 'status-active' : 'status-inactive' }}">
                            {{ $user->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>{{ $user->ciudad ?: 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        © {{ date('Y') }} VisioHome - Confidencial. Página 1
    </div>
</body>
</html>
