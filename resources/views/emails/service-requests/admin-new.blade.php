<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Нова заявка за оферта във FixNow.bg</title>
</head>
<body style="margin:0;background:#f6f8fb;color:#0f172a;font-family:Arial,sans-serif;">
    <div style="max-width:680px;margin:0 auto;padding:28px 18px;">
        <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:18px;padding:26px;">
            <p style="margin:0 0 8px;color:#2563eb;font-size:13px;font-weight:700;text-transform:uppercase;">FixNow.bg</p>
            <h1 style="margin:0 0 16px;font-size:26px;line-height:1.2;">Нова заявка за оферта</h1>

            <p style="margin:0 0 18px;color:#475569;line-height:1.6;">Клиент изпрати заявка през публичната форма. Данните са по-долу.</p>

            <table style="width:100%;border-collapse:collapse;margin:0 0 20px;">
                <tr><td style="padding:8px 0;color:#64748b;">Име</td><td style="padding:8px 0;font-weight:700;">{{ $serviceRequest->name }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Телефон</td><td style="padding:8px 0;font-weight:700;">{{ $serviceRequest->phone }}</td></tr>
                @if($serviceRequest->email)
                    <tr><td style="padding:8px 0;color:#64748b;">Имейл</td><td style="padding:8px 0;font-weight:700;">{{ $serviceRequest->email }}</td></tr>
                @endif
                <tr><td style="padding:8px 0;color:#64748b;">Град</td><td style="padding:8px 0;font-weight:700;">{{ $serviceRequest->city }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Категория/услуга</td><td style="padding:8px 0;font-weight:700;">{{ $serviceRequest->category }}{{ $serviceRequest->service ? ' · ' . $serviceRequest->service : '' }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Спешност</td><td style="padding:8px 0;font-weight:700;">{{ $serviceRequest->urgency === 'urgent' ? 'Спешна' : 'Нормална' }}</td></tr>
                @if($serviceRequest->budget)
                    <tr><td style="padding:8px 0;color:#64748b;">Бюджет</td><td style="padding:8px 0;font-weight:700;">{{ $serviceRequest->budget }}</td></tr>
                @endif
            </table>

            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:16px;margin-bottom:20px;">
                <p style="margin:0 0 8px;font-weight:700;">Описание</p>
                <p style="margin:0;color:#475569;line-height:1.6;">{{ $serviceRequest->description }}</p>
            </div>

            <h2 style="margin:0 0 10px;font-size:18px;">Автоматично назначени изпълнители</h2>
            @forelse($assignments as $assignment)
                <p style="margin:0 0 8px;color:#334155;">
                    <strong>{{ $assignment->business?->business_name ?: $assignment->business?->name ?: 'Изтрит изпълнител' }}</strong>
                    <span style="color:#64748b;">· {{ $assignment->status }}</span>
                </p>
            @empty
                <p style="margin:0 0 18px;color:#b45309;">Няма автоматично назначени изпълнители.</p>
            @endforelse

            <a href="{{ $dashboardUrl }}" style="display:inline-block;margin-top:18px;background:#2563eb;color:#ffffff;text-decoration:none;font-weight:700;border-radius:12px;padding:13px 18px;">Отвори admin dashboard</a>
        </div>
    </div>
</body>
</html>
