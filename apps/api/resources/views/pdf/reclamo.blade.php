<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Hoja de Reclamación {{ $complaint->correlative_code }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #0F172A; line-height: 1.45; }
        .header { border-bottom: 2px solid #0A2540; padding-bottom: 10px; margin-bottom: 14px; }
        .title { font-size: 16px; font-weight: bold; color: #0A2540; margin: 0; }
        .subtitle { font-size: 11px; color: #475569; margin: 2px 0 0; }
        .badge { display: inline-block; background: #0A2540; color: #C9A961; padding: 4px 10px; font-weight: bold; font-size: 11px; margin-top: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #CBD5E1; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #F1F5F9; width: 28%; font-weight: bold; color: #0A2540; }
        .section { font-size: 12px; font-weight: bold; color: #0A2540; margin: 12px 0 6px; text-transform: uppercase; }
        .footer { margin-top: 16px; font-size: 9.5px; color: #475569; border-top: 1px solid #CBD5E1; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">LIBRO DE RECLAMACIONES VIRTUAL — HOJA DE RECLAMACIÓN</p>
        <p class="subtitle">{{ $company['name'] }} · RUC {{ $company['ruc'] }} · {{ $company['address'] }}</p>
        <span class="badge">Código: {{ $complaint->correlative_code }}</span>
    </div>

    <table>
        <tr>
            <th>Fecha de Registro</th>
            <td>{{ $complaint->created_at?->timezone('America/Lima')->format('d/m/Y H:i') }}</td>
            <th>Plazo Legal de Respuesta</th>
            <td>{{ $complaint->legal_deadline->format('d/m/Y') }} (15 días hábiles)</td>
        </tr>
        <tr>
            <th>Tipo de Registro</th>
            <td>{{ $complaint->complaint_type === 'claim' ? 'RECLAMO (Disconformidad con producto/servicio)' : 'QUEJA (Malestar o descontento en la atención)' }}</td>
            <th>Estado</th>
            <td>{{ strtoupper($complaint->status) }}</td>
        </tr>
    </table>

    <p class="section">1. Identificación del Consumidor</p>
    <table>
        <tr>
            <th>Nombre / Razón Social</th>
            <td colspan="3">{{ $complaint->consumer_name }}</td>
        </tr>
        <tr>
            <th>Documento</th>
            <td>{{ strtoupper($complaint->consumer_document_type) }} {{ $complaint->consumer_document_number }}</td>
            <th>Teléfono</th>
            <td>{{ $complaint->consumer_phone }}</td>
        </tr>
        <tr>
            <th>Correo Electrónico</th>
            <td>{{ $complaint->consumer_email }}</td>
            <th>Domicilio</th>
            <td>{{ $complaint->consumer_address }}</td>
        </tr>
    </table>

    <p class="section">2. Bien Contratado y Detalle</p>
    <table>
        <tr>
            <th>Tipo de Bien</th>
            <td>{{ $complaint->item_type === 'product' ? 'Producto' : 'Servicio' }}</td>
            <th>Monto Reclamado</th>
            <td>{{ $complaint->claimed_amount !== null ? 'S/ ' . number_format((float) $complaint->claimed_amount, 2) : 'No aplica' }}</td>
        </tr>
        <tr>
            <th>Descripción del Bien</th>
            <td colspan="3">{{ $complaint->item_description ?? '—' }}</td>
        </tr>
        <tr>
            <th>Detalle del Reclamo / Queja</th>
            <td colspan="3">{{ $complaint->detail }}</td>
        </tr>
        <tr>
            <th>Pedido del Consumidor</th>
            <td colspan="3">{{ $complaint->consumer_request }}</td>
        </tr>
    </table>

    @if ($complaint->company_response)
        <p class="section">3. Respuesta del Proveedor</p>
        <table>
            <tr>
                <th>Fecha de Respuesta</th>
                <td>{{ $complaint->responded_at?->timezone('America/Lima')->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <th>Respuesta Oficial</th>
                <td>{{ $complaint->company_response }}</td>
            </tr>
        </table>
    @endif

    <div class="footer">
        De acuerdo al D.S. N.° 011-2011-PCM y Ley N.° 29571 (Código de Protección y Defensa del Consumidor), el proveedor deberá dar respuesta al reclamo en un plazo no mayor de quince (15) días hábiles improrrogables.
    </div>
</body>
</html>
