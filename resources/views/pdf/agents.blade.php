<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Agentes - VisioHome</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #6b0000; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 28px; font-weight: bold; color: #6b0000; letter-spacing: 2px; }
        .meta { font-size: 12px; color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 12px; text-align: left; font-size: 11px; color: #6b0000; }
        td { border: 1px solid #dee2e6; padding: 10px; font-size: 10px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; padding: 10px 0; }
        .cv-yes { color: #28a745; font-weight: bold; }
        .cv-no { color: #dc3545; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">VISIOHOME</div>
        <div style="font-size: 18px; margin-top: 5px;">Reporte Profesional de Agentes Inmobiliarios</div>
        <div class="meta">Generado el: {{ $date }}</div>
    </div>

    <div class="content">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Especialidad</th>
                    <th>Exp. (Años)</th>
                    <th>NIT Inmobiliaria</th>
                    <th>Estado</th>
                    <th>CV</th>
                </tr>
            </thead>
            <tbody>
                @foreach($agents as $agent)
                <tr>
                    <td>{{ $agent->nombre }}</td>
                    <td>{{ $agent->correo }}</td>
                    <td>{{ $agent->agenteProfile->especialidad ?? 'Asesor' }}</td>
                    <td>{{ $agent->agenteProfile->experiencia_anos ?? 0 }}</td>
                    <td>{{ $agent->agenteProfile->nitInmobiliaria ?? 'N/A' }}</td>
                    <td>{{ $agent->activo ? 'Activo' : 'Inactivo' }}</td>
                    <td>
                        <span class="{{ $agent->cv_path ? 'cv-yes' : 'cv-no' }}">
                            {{ $agent->cv_path ? 'Cargado' : 'Pendiente' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        © {{ date('Y') }} VisioHome - Reporte Interno. Página 1
    </div>
</body>
</html>
