<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Клиент избра вашата оферта във FixNow.bg</title>
</head>
<body style="margin:0;background:#f6f8fb;color:#0f172a;font-family:Arial,sans-serif;">
    <div style="max-width:680px;margin:0 auto;padding:28px 18px;">
        <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:18px;padding:26px;">
            <p style="margin:0 0 8px;color:#16a34a;font-size:13px;font-weight:700;text-transform:uppercase;">FixNow.bg</p>
            <h1 style="margin:0 0 16px;font-size:26px;line-height:1.2;">Клиент избра вашата оферта</h1>

            <p style="margin:0 0 18px;color:#475569;line-height:1.6;">
                Вашата оферта беше избрана. Свържете се с клиента възможно най-скоро, за да уточните детайлите.
            </p>

            <table style="width:100%;border-collapse:collapse;margin:0 0 20px;">
                <tr><td style="padding:8px 0;color:#64748b;">Заявка</td><td style="padding:8px 0;font-weight:700;">{{ $serviceRequest->service ?: $serviceRequest->category ?: 'Заявка за услуга' }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Град</td><td style="padding:8px 0;font-weight:700;">{{ $serviceRequest->city }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Категория</td><td style="padding:8px 0;font-weight:700;">{{ $serviceRequest->category ?: 'Не е посочена' }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Клиент</td><td style="padding:8px 0;font-weight:700;">{{ $serviceRequest->name }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Телефон</td><td style="padding:8px 0;font-weight:700;">{{ $serviceRequest->phone }}</td></tr>
                @if($serviceRequest->email)
                    <tr><td style="padding:8px 0;color:#64748b;">Имейл</td><td style="padding:8px 0;font-weight:700;">{{ $serviceRequest->email }}</td></tr>
                @endif
            </table>

            <a href="{{ $dashboardUrl }}" style="display:inline-block;background:#16a34a;color:#ffffff;text-decoration:none;font-weight:700;border-radius:12px;padding:13px 18px;">Отвори заявките</a>
        </div>
    </div>
</body>
</html>
