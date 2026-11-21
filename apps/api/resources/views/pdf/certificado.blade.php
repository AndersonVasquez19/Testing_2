<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado {{ $certificate->certificate_code }}</title>
    <style>
        @page { margin: 20px; }
        body { font-family: DejaVu Sans, sans-serif; color: #0A2540; margin: 0; padding: 0; }
        .frame { border: 6px solid #0A2540; padding: 24px 36px; position: relative; min-height: 495px; }
        .inner-frame { border: 2px solid #C9A961; padding: 28px 36px; min-height: 455px; }
        .brand { text-align: center; font-size: 13px; letter-spacing: 3px; text-transform: uppercase; color: #856424; font-weight: bold; }
        .heading { text-align: center; font-size: 30px; font-weight: bold; color: #0A2540; margin: 12px 0 6px; letter-spacing: 2px; }
        .sub { text-align: center; font-size: 13px; color: #475569; margin-bottom: 18px; }
        .student { text-align: center; font-size: 26px; font-weight: bold; color: #0A2540; border-bottom: 2px solid #C9A961; display: block; width: 78%; margin: 0 auto 14px; padding-bottom: 6px; }
        .body-text { text-align: center; font-size: 13.5px; line-height: 1.6; max-width: 720px; margin: 0 auto 24px; color: #1E293B; }
        .meta-table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .meta-table td { vertical-align: bottom; padding: 6px; }
        .qr-box { text-align: right; }
        .qr-box img { width: 105px; height: 105px; }
        .qr-code { font-size: 9px; color: #475569; margin-top: 4px; }
        .sign-line { border-top: 1px solid #0A2540; width: 220px; margin: 36px auto 4px; text-align: center; padding-top: 4px; font-size: 11px; font-weight: bold; }
        .sign-role { text-align: center; font-size: 10px; color: #475569; }
        .code-pill { font-size: 10px; color: #0A2540; font-weight: bold; }
    </style>
</head>
<body>
    <div class="frame">
        <div class="inner-frame">
            <div class="brand">{{ $company['name'] }} · RUC {{ $company['ruc'] }}</div>
            <div class="heading">CERTIFICADO</div>
            <div class="sub">Otorgado a:</div>

            <div class="student">{{ $snapshot['student_name'] ?? '' }}</div>

            <div class="body-text">
                Por haber aprobado satisfactoriamente el programa académico
                <strong>«{{ $snapshot['program_name'] ?? '' }}»</strong>
                (Edición {{ $snapshot['cohort_code'] ?? '' }}),
                desarrollado del {{ $snapshot['start_date'] ?? '' }} al {{ $snapshot['end_date'] ?? '' }},
                con una duración de <strong>{{ $snapshot['duration_hours'] ?? 0 }} horas académicas</strong>
                y una calificación final de <strong>{{ $snapshot['final_grade'] ?? '—' }}</strong>.
            </div>

            <table class="meta-table">
                <tr>
                    <td style="width: 34%;">
                        <div class="code-pill">Código: {{ $certificate->certificate_code }}</div>
                        <div class="qr-code">Fecha de emisión: {{ $certificate->issued_at->timezone('America/Lima')->format('d/m/Y') }}</div>
                        <div class="qr-code">DNI: {{ $snapshot['student_dni'] ?? '—' }}</div>
                    </td>
                    <td style="width: 38%;">
                        <div class="sign-line">Dirección Académica</div>
                        <div class="sign-role">{{ $company['name'] }}</div>
                    </td>
                    <td class="qr-box" style="width: 28%;">
                        <img src="{{ $qrDataUri }}" alt="QR de verificación">
                        <div class="qr-code">Verificar autenticidad:<br>{{ $certificate->verification_uuid }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
