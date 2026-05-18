<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Клиент прие вашата оферта</title>
</head>
<body style="margin:0;background:#f6f8fb;font-family:Arial,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f8fb;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border-radius:20px;overflow:hidden;border:1px solid #e2e8f0;">
                    <tr>
                        <td style="padding:28px 28px 12px;">
                            <p style="margin:0 0 8px;color:#2563eb;font-weight:700;text-transform:uppercase;letter-spacing:.08em;font-size:12px;">FixNow.bg</p>
                            <h1 style="margin:0;font-size:26px;line-height:1.25;color:#0f172a;">Клиент прие вашата оферта</h1>
                            <p style="margin:14px 0 0;color:#475569;line-height:1.6;">
                                Клиент прие вашата оферта за заявка: <strong>{{ $serviceRequest->service ?: $serviceRequest->category ?: 'Заявка във FixNow' }}</strong>.
                                Свържете се с него, за да уточните детайлите.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:12px 28px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;background:#f8fafc;border-radius:16px;">
                                <tr>
                                    <td style="padding:16px;border-bottom:1px solid #e2e8f0;color:#475569;">Клиент</td>
                                    <td style="padding:16px;border-bottom:1px solid #e2e8f0;font-weight:700;">{{ $serviceRequest->name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:16px;border-bottom:1px solid #e2e8f0;color:#475569;">Телефон</td>
                                    <td style="padding:16px;border-bottom:1px solid #e2e8f0;font-weight:700;">{{ $serviceRequest->phone }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:16px;border-bottom:1px solid #e2e8f0;color:#475569;">Град</td>
                                    <td style="padding:16px;border-bottom:1px solid #e2e8f0;font-weight:700;">{{ $serviceRequest->city }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:16px;color:#475569;">Оферта</td>
                                    <td style="padding:16px;font-weight:700;">{{ $offer->price_estimate }} · {{ $offer->timeframe }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 28px 30px;">
                            <a href="{{ $dashboardUrl }}" style="display:inline-block;background:#2563eb;color:#fff;text-decoration:none;border-radius:14px;padding:14px 18px;font-weight:700;">Отвори панела на изпълнител</a>
                            <p style="margin:18px 0 0;color:#64748b;font-size:13px;line-height:1.6;">Ако бутонът не работи, отворете: {{ $dashboardUrl }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
