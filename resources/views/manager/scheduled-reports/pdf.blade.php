<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ $report->name }}</title>
    <style>
        body { font-family: dejavusans, sans-serif; font-size: 12px; padding: 20px; }
        h1 { font-size: 18px; color: #1e3a8a; margin-bottom: 4px; }
        .meta { color: #64748b; font-size: 11px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px; text-align: right; }
        th { background: #f1f5f9; }
        .summary { background: #f8fafc; padding: 12px; border-radius: 6px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <h1>{{ $report->name }}</h1>
    <div class="meta">
        {{ $report->sectionLabel() }}
        — الفترة: {{ $start->format('Y-m-d') }} → {{ $end->format('Y-m-d') }}
        ({{ $report->period_months }} شهر)
    </div>

    <div class="summary">
        <strong>ملخص التقرير</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th>المؤشر</th>
                <th>القيمة</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $value)
                <tr>
                    <td>{{ str_replace('_', ' ', $key) }}</td>
                    <td>{{ is_numeric($value) ? number_format((float)$value, 2) : $value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top:20px; color:#94a3b8; font-size:10px;">
        تم توليد هذا التقرير تلقائياً بواسطة نظام ثروة في {{ now()->format('Y-m-d H:i') }}
    </p>
</body>
</html>
