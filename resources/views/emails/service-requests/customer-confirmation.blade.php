<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Получихме заявката ви във BON</title>
</head>
<body style="margin:0;background:#f6f8fb;color:#0f172a;font-family:Arial,sans-serif;">
    <div style="max-width:680px;margin:0 auto;padding:28px 18px;">
        <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:18px;padding:26px;">
            <p style="margin:0 0 8px;color:#2563eb;font-size:13px;font-weight:700;text-transform:uppercase;">BON</p>
            <h1 style="margin:0 0 16px;font-size:26px;line-height:1.2;">Заявката е изпратена успешно</h1>

            <p style="margin:0 0 18px;color:#475569;line-height:1.6;">
                Благодарим ви. Изпратихме заявката към {{ $serviceRequest->assignedBusiness?->business_name ?: $serviceRequest->assignedBusiness?->name ?: 'избрания бизнес' }}. Бизнесят ще се свърже с вас възможно най-скоро.
            </p>

            <table style="width:100%;border-collapse:collapse;margin:0 0 20px;">
                <tr><td style="padding:8px 0;color:#64748b;">Име</td><td style="padding:8px 0;font-weight:700;">{{ $serviceRequest->name }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Телефон</td><td style="padding:8px 0;font-weight:700;">{{ $serviceRequest->phone }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Град</td><td style="padding:8px 0;font-weight:700;">{{ $serviceRequest->city }}</td></tr>
            </table>

            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:16px;margin-bottom:20px;">
                <p style="margin:0 0 8px;font-weight:700;">Описание</p>
                <p style="margin:0;color:#475569;line-height:1.6;">{{ $serviceRequest->description }}</p>
            </div>

            <p style="margin:0 0 14px;color:#475569;line-height:1.6;">
                Можете да следите получените оферти тук:
            </p>

            <a href="{{ $offersUrl }}" style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;font-weight:700;border-radius:12px;padding:13px 18px;margin-right:8px;">Виж офертите</a>
            <a href="{{ $homeUrl }}" style="display:inline-block;background:#0f172a;color:#ffffff;text-decoration:none;font-weight:700;border-radius:12px;padding:13px 18px;">Отвори BON</a>
        </div>
    </div>
</body>
</html>
