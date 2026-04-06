<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Facturación - VisioHome</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 2px solid #6b0000; padding: 20px 0; margin-bottom: 30px; }
        .logo { font-size: 28px; font-weight: bold; color: #6b0000; letter-spacing: 2px; }
        .meta { font-size: 12px; color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; table-layout: fixed; }
        th { background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 12px; text-align: left; font-size: 11px; color: #6b0000; text-transform: uppercase; }
        td { border: 1px solid #dee2e6; padding: 10px; font-size: 10px; word-wrap: break-word; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; padding: 15px 0; border-top: 1px solid #eee; }
        .amount { font-weight: bold; text-align: right; }
        .status { font-weight: bold; text-transform: uppercase; font-size: 9px; }
        .status-approved { color: #1a73e8; }
        .status-pending { color: #f29900; }
        .status-rejected { color: #d93025; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">VISIOHOME</div>
        <div style="font-size: 18px; margin-top: 5px; font-weight: bold;">Reporte de Facturación y Pagos</div>
        <div class="meta">Generado el: {{ $date }}</div>
    </div>

    <div class="content">
        @if(!empty($filters['status']) || !empty($filters['date_from']) || !empty($filters['search']))
        <div style="margin-bottom: 15px; font-size: 10px; font-style: italic; color: #555; background: #f9f9f9; padding: 8px; border-radius: 4px;">
            <strong>Filtros aplicados:</strong> 
            @if(!empty($filters['search'])) Búsqueda: "{{ $filters['search'] }}" | @endif
            @if(!empty($filters['status'])) Estado: {{ strtoupper($filters['status']) }} | @endif
            @if(!empty($filters['date_from'])) Desde: {{ $filters['date_from'] }} | @endif
            @if(!empty($filters['date_to'])) Hasta: {{ $filters['date_to'] }} @endif
        </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">ID</th>
                    <th style="width: 25%;">Cliente</th>
                    <th style="width: 20%;">Referencia / MP</th>
                    <th style="width: 15%;">Monto</th>
                    <th style="width: 15%;">Fecha</th>
                    <th style="width: 15%;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $pago)
                <tr>
                    <td>#{{ $pago->idPago }}</td>
                    <td>
                        <div style="font-weight: bold;">{{ $pago->cliente ?: 'N/A' }}</div>
                        <div style="font-size: 8px; color: #777;">CC: {{ $pago->docUsuario }}</div>
                    </td>
                    <td>{{ $pago->referencia ?: ($pago->numero ?: 'N/A') }}</td>
                    <td class="amount">${{ number_format($pago->monto, 0, ',', '.') }}</td>
                    <td>{{ $pago->fecha ? \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y') : 'N/A' }}</td>
                    <td>
                        <span class="status status-{{ $pago->estado }}">
                            {{ strtoupper($pago->estado ?: 'pendiente') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        © {{ date('Y') }} VisioHome - Reporte Administrativo Generado Automáticamente
    </div>
</body>
</html>
