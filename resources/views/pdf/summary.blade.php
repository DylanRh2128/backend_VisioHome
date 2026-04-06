<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen General - VisioHome</title>
    <style>
        body { font-family: 'Arial', sans-serif; color: #333; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #d40000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #d40000; margin: 0; }
        .header p { margin: 5px 0 0; color: #666; }
        .section { margin-bottom: 30px; }
        .section h2 { background-color: #f8f8f8; padding: 8px; border-left: 4px solid #d40000; font-size: 18px; }
        .stats-grid { display: block; margin-top: 10px; }
        .stat-card { border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; border-radius: 5px; }
        .stat-card strong { color: #d40000; font-size: 20px; }
        .stat-card span { display: block; color: #888; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; color: #333; }
        .footer { text-align: center; font-size: 12px; color: #999; position: fixed; bottom: 0; width: 100%; padding: 10px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>VisioHome</h1>
        <p>Reporte de Resumen General</p>
        <p>Fecha de generación: {{ $date }}</p>
    </div>

    <div class="section">
        <h2>Indicadores Generales</h2>
        <table>
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Total Registrado</th>
                    <th>Estado / Monto</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Usuarios (Clientes)</strong></td>
                    <td>{{ $stats['usuarios']['total'] ?? 0 }}</td>
                    <td>{{ $stats['usuarios']['activos'] ?? 0 }} Activos</td>
                </tr>
                <tr>
                    <td><strong>Agentes de Ventas</strong></td>
                    <td>{{ $stats['agentes']['total'] ?? 0 }}</td>
                    <td>{{ $stats['agentes']['activos'] ?? 0 }} Activos</td>
                </tr>
                <tr>
                    <td><strong>Propiedades</strong></td>
                    <td>{{ $stats['propiedades']['total'] ?? 0 }}</td>
                    <td>{{ $stats['propiedades']['disponibles'] ?? 0 }} Disponibles</td>
                </tr>
                <tr>
                    <td><strong>Ventas / Pagos</strong></td>
                    <td>{{ $stats['pagos']['total'] ?? 0 }}</td>
                    <td>${{ number_format($stats['pagos']['montoAprobado'] ?? 0, 2) }} Aprobado</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>KPIs de Operación (Periodo Actual)</h2>
        <!-- Usamos una tabla para el grid ya que DomPDF maneja mejor tablas que divs flotantes -->
        <table style="border: none;">
            <tr>
                <td style="border: 1px solid #ddd; width: 50%;">
                    <div class="stat-card" style="border: none; margin-bottom: 0;">
                        <span>Ingresos Totales (Periodo)</span>
                        <strong>${{ number_format($stats['kpis']['income']['current'] ?? 0, 2) }}</strong>
                    </div>
                </td>
                <td style="border: 1px solid #ddd; width: 50%;">
                    <div class="stat-card" style="border: none; margin-bottom: 0;">
                        <span>Ventas Realizadas</span>
                        <strong>{{ $stats['kpis']['sales']['current'] ?? 0 }}</strong>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; width: 50%;">
                    <div class="stat-card" style="border: none; margin-bottom: 0;">
                        <span>Nuevos Usuarios</span>
                        <strong>{{ $stats['kpis']['users']['current'] ?? 0 }}</strong>
                    </div>
                </td>
                <td style="border: 1px solid #ddd; width: 50%;">
                    <div class="stat-card" style="border: none; margin-bottom: 0;">
                        <span>Nuevas Propiedades</span>
                        <strong>{{ $stats['kpis']['properties']['current'] ?? 0 }}</strong>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        VisioHome &copy; {{ date('Y') }} - Sistema de Gestión Inmobiliaria
    </div>
</body>
</html>
