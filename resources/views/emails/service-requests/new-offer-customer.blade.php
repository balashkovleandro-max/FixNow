<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Получихте нова оферта във FixNow.bg</title>
</head>
<body style="margin:0;background:#f6f8fb;color:#0f172a;font-family:Arial,sans-serif;">
    <div style="max-width:680px;margin:0 auto;padding:28px 18px;">
        <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:18px;padding:26px;">
            <p style="margin:0 0 8px;color:#2563eb;font-size:13px;font-weight:700;text-transform:uppercase;">FixNow.bg</p>
            <h1 style="margin:0 0 16px;font-size:26px;line-height:1.2;">Получихте нова оферта</h1>

            <p style="margin:0 0 18px;color:#475569;line-height:1.6;">
                Изпълнител изпрати предложение по вашата заявка:
                <strong>{{ $serviceRequest->service ?: $serviceRequest->category ?: 'Заявка за услуга' }}</strong>.
            </p>

            <table style="width:100%;border-collapse:collapse;margin:0 0 20px;">
                <tr><td style="padding:8px 0;color:#64748b;">Изпълнител</td><td style="padding:8px 0;font-weight:700;">{{ $executor->business_name ?: $executor->name }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Ориентировъчна цена</td><td style="padding:8px 0;font-weight:700;">{{ $offer->price_estimate }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Срок</td><td style="padding:8px 0;font-weight:700;">{{ $offer->timeframe }}</td></tr>
            </table>

            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:16px;margin-bottom:20px;">
                <p style="margin:0 0 8px;font-weight:700;">Съобщение от офертата</p>
                <p style="margin:0;color:#475569;line-height:1.6;">{{ $offer->message }}</p>
            </div>

            <a href="{{ $offersUrl }}" style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;font-weight:700;border-radius:12px;padding:13px 18px;">Виж офертите</a>
        </div>
    </div>
</body>
</html>
