<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Constancia de Reclamación {{ $complaint->correlative_code }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #0F172A; line-height: 1.5;">
    <h2 style="color: #0A2540;">Horus Group SRL · Libro de Reclamaciones Virtual</h2>
    <p>Estimado(a) <strong>{{ $complaint->consumer_name }}</strong>,</p>
    <p>
        Confirmamos el registro de su {{ $complaint->complaint_type === 'claim' ? 'reclamo' : 'queja' }} con el código correlativo
        <strong>{{ $complaint->correlative_code }}</strong>.
    </p>
    <p>
        <strong>Plazo legal de respuesta (15 días hábiles):</strong> {{ $complaint->legal_deadline->format('d/m/Y') }}.
    </p>
    <p>Adjuntamos su constancia en formato PDF.</p>
    <hr style="border: 0; border-top: 1px solid #E2E8F0;">
    <p style="font-size: 12px; color: #475569;">{{ $company['name'] }} · RUC {{ $company['ruc'] }} · {{ $company['address'] }}</p>
</body>
</html>
