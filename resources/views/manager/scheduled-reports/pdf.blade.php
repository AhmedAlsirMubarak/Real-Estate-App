<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<title>{{ $report->name }}</title>
<style>
    body {
        font-family: dejavusans, sans-serif;
        font-size: 11pt;
        color: #1e293b;
        direction: rtl;
        margin: 0;
        padding: 0;
    }

    /* ── Header ── */
    .hdr {
        background: #1e3a8a;
        color: #ffffff;
        padding: 20px 24px 16px;
        margin-bottom: 18px;
    }
    .hdr-inner { width: 100%; border-collapse: collapse; }
    .hdr-title  { font-size: 17pt; font-weight: bold; margin: 0 0 3px; }
    .hdr-sub    { font-size: 9pt; color: #bfdbfe; margin: 0; }
    .hdr-right  { font-size: 9pt; color: #bfdbfe; text-align: left; vertical-align: top; white-space: nowrap; }

    /* ── Meta row ── */
    .meta { width: 100%; border-collapse: collapse; margin-bottom: 18px; font-size: 10pt; }
    .meta td { padding: 3px 6px; }
    .meta .lbl { font-weight: bold; color: #1e293b; width: 110px; }
    .meta .val { color: #475569; }
    .badge-green { background: #dcfce7; color: #166534; padding: 1px 10px; border-radius: 10px; font-size: 9pt; }
    .badge-amber { background: #fef9c3; color: #854d0e; padding: 1px 10px; border-radius: 10px; font-size: 9pt; }
    .divider { border: none; border-top: 1px solid #e2e8f0; margin: 0 0 16px; }

    /* ── Section title ── */
    .sec-title {
        font-size: 12pt;
        font-weight: bold;
        color: #1e3a8a;
        border-bottom: 2px solid #1e3a8a;
        padding-bottom: 3px;
        margin: 16px 0 12px;
    }

    /* ── Summary cards ── */
    .cards { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    .card {
        width: 25%;
        border: 1px solid #e2e8f0;
        padding: 12px 8px;
        text-align: center;
        vertical-align: top;
    }
    .card-blue  { background: #eff6ff; border-color: #bfdbfe; }
    .card-green { background: #f0fdf4; border-color: #bbf7d0; }
    .card-red   { background: #fff1f2; border-color: #fecdd3; }
    .card-amber { background: #fffbeb; border-color: #fde68a; }
    .card-lbl  { font-size: 8.5pt; color: #64748b; margin-bottom: 6px; }
    .card-val  { font-size: 15pt; font-weight: bold; }
    .card-unit { font-size: 8pt; color: #94a3b8; margin-top: 2px; }
    .c-blue  { color: #1d4ed8; }
    .c-green { color: #15803d; }
    .c-red   { color: #b91c1c; }
    .c-amber { color: #b45309; }

    /* ── Metrics table ── */
    .tbl { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .tbl thead th {
        background: #1e3a8a;
        color: #ffffff;
        padding: 9px 14px;
        text-align: right;
        font-size: 10pt;
    }
    .tbl tbody td { padding: 9px 14px; border-bottom: 1px solid #f1f5f9; font-size: 10pt; }
    .tbl tbody tr:nth-child(even) td { background: #f8fafc; }
    .tbl .row-total td { background: #eff6ff; font-weight: bold; border-top: 2px solid #bfdbfe; }
    .positive { color: #15803d; font-weight: bold; }
    .negative { color: #b91c1c; font-weight: bold; }
    .muted    { color: #94a3b8; }
    .bold     { font-weight: bold; }
    .pct      { font-size: 8.5pt; color: #64748b; }

    /* ── Footer ── */
    .footer {
        margin-top: 22px;
        text-align: center;
        font-size: 8pt;
        color: #94a3b8;
        border-top: 1px solid #e2e8f0;
        padding-top: 10px;
    }
</style>
</head>
<body>

{{-- ── Header ── --}}
<div class="hdr">
    <table class="hdr-inner">
        <tr>
            <td>
                <p class="hdr-title">{{ $report->name }}</p>
                <p class="hdr-sub">{{ $report->section === 'hoa' ? 'جمعية الملاك' : 'إدارة المباني' }} — تقرير دوري</p>
            </td>
            <td class="hdr-right">
                <div>شركة ثروة للعقارات</div>
                <div style="margin-top:3px;">{{ now()->format('Y/m/d H:i') }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ── Report meta ── --}}
@php
    $duesTotal  = (float) ($data['dues_total']        ?? 0);
    $duesPaid   = (float) ($data['dues_paid']          ?? 0);
    $duesUnpaid = (float) ($data['dues_unpaid']        ?? 0);
    $duesWaived = (float) ($data['dues_waived']        ?? 0);
    $expenses   = (float) ($data['total_expenses']     ?? 0);
    $balance    = (float) ($data['balance']            ?? 0);
    $assocCount = (int)   ($data['associations_count'] ?? 0);
    $meetings   = (int)   ($data['meetings_count']     ?? 0);

    $paidPct    = $duesTotal > 0 ? round($duesPaid   / $duesTotal * 100, 1) : 0;
    $unpaidPct  = $duesTotal > 0 ? round($duesUnpaid / $duesTotal * 100, 1) : 0;

    $scopeLabel = $report->association
        ? ('جمعية: ' . ($report->association->name_ar ?? $report->association->name_en ?? $report->association->name ?? '—'))
        : ($report->property
            ? ('عقار: ' . $report->property->name)
            : 'جميع الجمعيات');
@endphp

<table class="meta">
    <tr>
        <td class="lbl">الفترة:</td>
        <td class="val">{{ $start->format('Y/m/d') }} — {{ $end->format('Y/m/d') }}
            <span class="muted">({{ $report->period_months }} {{ $report->period_months === 1 ? 'شهر' : 'أشهر' }})</span>
        </td>
        <td class="lbl" style="padding-right:24px;">النطاق:</td>
        <td class="val">{{ $scopeLabel }}</td>
    </tr>
    <tr>
        <td class="lbl">الحالة:</td>
        <td colspan="3">
            <span class="{{ $report->status === 'active' ? 'badge-green' : 'badge-amber' }}">
                {{ $report->statusLabel() }}
            </span>
        </td>
    </tr>
</table>
<hr class="divider">

{{-- ── Summary Cards ── --}}
<div class="sec-title">الملخص المالي للفترة</div>
<table class="cards">
    <tr>
        <td class="card card-blue">
            <div class="card-lbl">إجمالي الاشتراكات</div>
            <div class="card-val c-blue">{{ number_format($duesTotal) }}</div>
            <div class="card-unit">ر.ع</div>
        </td>
        <td width="8"></td>
        <td class="card card-green">
            <div class="card-lbl">المحصّل</div>
            <div class="card-val c-green">{{ number_format($duesPaid) }}</div>
            <div class="card-unit">ر.ع — {{ $paidPct }}%</div>
        </td>
        <td width="8"></td>
        <td class="card card-red">
            <div class="card-lbl">غير محصّل</div>
            <div class="card-val c-red">{{ number_format($duesUnpaid) }}</div>
            <div class="card-unit">ر.ع — {{ $unpaidPct }}%</div>
        </td>
        <td width="8"></td>
        <td class="card {{ $balance >= 0 ? 'card-green' : 'card-red' }}">
            <div class="card-lbl">صافي الرصيد</div>
            <div class="card-val {{ $balance >= 0 ? 'c-green' : 'c-red' }}">{{ number_format($balance) }}</div>
            <div class="card-unit">ر.ع (محصّل – مصروفات)</div>
        </td>
    </tr>
</table>

{{-- ── Detailed Metrics ── --}}
<div class="sec-title">تفاصيل المؤشرات</div>
<table class="tbl">
    <thead>
        <tr>
            <th style="width:55%;">المؤشر</th>
            <th style="width:25%;">القيمة</th>
            <th style="width:20%;">النسبة / ملاحظة</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>عدد الجمعيات المشمولة</td>
            <td class="bold">{{ $assocCount }} جمعية</td>
            <td class="muted">—</td>
        </tr>
        <tr>
            <td>إجمالي الاشتراكات المُصدَرة</td>
            <td class="positive">{{ number_format($duesTotal) }} ر.ع</td>
            <td class="muted">الفترة بأكملها</td>
        </tr>
        <tr>
            <td>المبالغ المحصّلة (مدفوع)</td>
            <td class="positive">{{ number_format($duesPaid) }} ر.ع</td>
            <td class="pct">{{ $paidPct }}% من الإجمالي</td>
        </tr>
        <tr>
            <td>المبالغ غير المحصّلة (معلق + متأخر)</td>
            <td class="negative">{{ number_format($duesUnpaid) }} ر.ع</td>
            <td class="pct">{{ $unpaidPct }}% من الإجمالي</td>
        </tr>
        <tr>
            <td>المبالغ المُعفاة</td>
            <td>{{ number_format($duesWaived) }} ر.ع</td>
            <td class="muted">—</td>
        </tr>
        <tr>
            <td>إجمالي المصروفات المرتبطة</td>
            <td class="negative">{{ number_format($expenses) }} ر.ع</td>
            <td class="muted">مصروفات العقارات</td>
        </tr>
        <tr>
            <td>عدد الاجتماعات المنعقدة</td>
            <td class="bold">{{ $meetings }} اجتماع</td>
            <td class="muted">خلال الفترة</td>
        </tr>
        <tr class="row-total">
            <td>صافي الرصيد (محصّل – مصروفات)</td>
            <td class="{{ $balance >= 0 ? 'positive' : 'negative' }}">{{ number_format($balance) }} ر.ع</td>
            <td class="muted">{{ $balance >= 0 ? 'فائض' : 'عجز' }}</td>
        </tr>
    </tbody>
</table>

{{-- ── Footer ── --}}
<div class="footer">
    شركة ثروة للعقارات &mdash;
    تم توليد هذا التقرير تلقائياً بواسطة نظام ثروة
    في {{ now()->format('Y/m/d H:i') }}
</div>

</body>
</html>
