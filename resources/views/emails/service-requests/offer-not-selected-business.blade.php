<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Заявката беше затворена във BON</title>
</head>
<body style="margin:0;background:#f6f8fb;color:#0f172a;font-family:Arial,sans-serif;">
    <div style="max-width:680px;margin:0 auto;padding:28px 18px;">
        <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:18px;padding:26px;">
            <p style="margin:0 0 8px;color:#7c3aed;font-size:13px;font-weight:700;text-transform:uppercase;">BON</p>
            <h1 style="margin:0 0 16px;font-size:26px;line-height:1.2;">Клиентът избра друг бизнес</h1>

            <p style="margin:0 0 18px;color:#475569;line-height:1.6;">
                Заявката <strong>{{ $serviceRequest->service ?: $serviceRequest->category ?: 'Заявка за услуга' }}</strong>
                беше затворена, защото клиентът вече избра друг бизнес.
            </p>

            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:16px;margin-bottom:20px;">
                <p style="margin:0;color:#475569;line-height:1.6;">
                    Ще продължим да ви показваме подходящи заявки във вашите категории и градове.
                </p>
            </div>

            <a href="{{ $dashboardUrl }}" style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;font-weight:700;border-radius:12px;padding:13px 18px;">Виж нови заявки</a>
        </div>
    </div>
</body>
</html>
