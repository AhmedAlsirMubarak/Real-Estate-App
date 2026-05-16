<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8">
<title>تقرير {{ $property->name }}</title>
<style>
    @page { margin: 20mm 15mm; }
    body { font-family: dejavusans, sans-serif; font-size: 11pt; color: #222; direction: rtl; }
    h1 { color: #1e3a8a; font-size: 18pt; margin: 0 0 8px; }
    h2 { color: #1e3a8a; font-size: 13pt; margin: 18px 0 6px; border-bottom: 2px solid #1e3a8a; padding-bottom: 3px; }
    .meta { color: #666; font-size: 9pt; margin-bottom: 15px; }
    .stats { display: table; width: 100%; margin-bottom: 15px; }
    .stat-box { display: table-cell; border: 1px solid #ddd; padding: 8px; text-align: center; width: 25%; }
    .stat-label { font-size: 8pt; color: #666; }
    .stat-value { font-size: 12pt; font-weight: bold; margin-top: 3px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th, td { border: 1px solid #ddd; padding: 5px 8px; text-align: right; font-size: 9pt; }
    th { background: #f3f4f6; color: #555; font-weight: bold; }
    .positive { color: #047857; font-weight: bold; }
    .negative { color: #b91c1c; font-weight: bold; }
    .empty { text-align: center; color: #999; padding: 10px; }
</style>
</head>
<body>
    <h1>تقرير العقار: {{ $property->name }}</h1>
    <div class="meta">
        {{ $property->code }} · {{ $property->typeLabel() }} · {{ $property->purposeLabel() }}<br>
        {{ $property->address }}@if($property->city) — {{ $property->city }}@endif<br>
        المالك: @if($property->owner) {{ $property->owner->user?->name ?? 'مالك' }} (عمولة {{ $property->owner->commission_rate }}%) @else الشركة @endif<br>
        الفترة: سنة {{ $year }}@if($month) — شهر {{ $month }}@endif
    </div>

    <div class="stats">
        <div class="stat-box"><div class="stat-label">الوحدات</div><div class="stat-value">{{ $stats['total_units'] }}</div></div>
        <div class="stat-box"><div class="stat-label">الإيرادات</div><div class="stat-value positive">{{ number_format($stats['total_revenue']) }}</div></div>
        <div class="stat-box"><div class="stat-label">المصروفات</div><div class="stat-value negative">{{ number_format($stats['total_expenses']) }}</div></div>
        <div class="stat-box"><div class="stat-label">صافي الربح</div><div class="stat-value {{ $stats['net_income']>=0?'positive':'negative' }}">{{ number_format($stats['net_income']) }}</div></div>
    </div>

    <h2>المدفوعات ({{ $payments->count() }})</h2>
    <table>
        <thead>
            <tr><th>المستأجر</th><th>الوحدة</th><th>الشهر</th><th>المبلغ</th><th>الحالة</th></tr>
        </thead>
        <tbody>
            @forelse($payments as $p)
            <tr>
                <td>{{ $p->tenant?->user?->name ?? '—' }}</td>
                <td>{{ $p->rentalContract->unit->unit_number ?? '—' }}</td>
                <td>{{ $p->month }}/{{ $p->year }}</td>
                <td>{{ number_format($p->amount) }}</td>
                <td>{{ $p->statusLabel() }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="empty">لا توجد مدفوعات</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>المصروفات ({{ $expenses->count() }})</h2>
    <table>
        <thead><tr><th>البيان</th><th>الفئة</th><th>التاريخ</th><th>المبلغ</th></tr></thead>
        <tbody>
            @forelse($expenses as $e)
            <tr>
                <td>{{ $e->title }}</td>
                <td>{{ $e->categoryLabel() }}</td>
                <td>{{ $e->expense_date->format('Y-m-d') }}</td>
                <td class="negative">-{{ number_format($e->amount) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="empty">لا توجد مصروفات</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>طلبات الصيانة ({{ $maintenanceRequests->count() }})</h2>
    <table>
        <thead><tr><th>العنوان</th><th>المستأجر</th><th>الوحدة</th><th>الأولوية</th><th>الحالة</th></tr></thead>
        <tbody>
            @forelse($maintenanceRequests as $mr)
            <tr>
                <td>{{ $mr->title }}</td>
                <td>{{ $mr->tenant?->user?->name ?? '—' }}</td>
                <td>{{ $mr->unit->unit_number ?? '—' }}</td>
                <td>{{ $mr->priorityLabel() }}</td>
                <td>{{ $mr->statusLabel() }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="empty">لا توجد طلبات</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
