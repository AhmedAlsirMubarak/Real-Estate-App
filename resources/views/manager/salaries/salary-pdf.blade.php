<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8">
<title>تقرير الرواتب</title>
<style>
    @page { margin: 14mm 12mm 14mm 12mm; }
    body  { font-family: dejavusans, sans-serif; font-size: 8.5pt; color: #1e293b; direction: rtl; margin:0; padding:0; }

    /* ── Header ── */
    .hdr      { background:#1e3a8a; color:#fff; padding:14px 18px 12px; margin-bottom:12px; }
    .hdr-tbl  { width:100%; border-collapse:collapse; }
    .hdr-title{ font-size:15pt; font-weight:bold; margin:0 0 3px; }
    .hdr-sub  { font-size:8.5pt; color:#bfdbfe; margin:0; }
    .hdr-right{ font-size:8.5pt; color:#bfdbfe; text-align:left; vertical-align:top; white-space:nowrap; }

    /* ── Section title ── */
    .sec { font-size:11pt; font-weight:bold; color:#1e3a8a; border-bottom:2px solid #1e3a8a; padding-bottom:3px; margin:13px 0 8px; }

    /* ── Stat cards ── */
    .cards   { width:100%; border-collapse:collapse; margin-bottom:12px; }
    .card    { border:1px solid #e2e8f0; padding:9px 6px; text-align:center; vertical-align:top; }
    .cblue   { background:#eff6ff; border-color:#bfdbfe; }
    .cgreen  { background:#f0fdf4; border-color:#bbf7d0; }
    .cred    { background:#fff1f2; border-color:#fecdd3; }
    .camber  { background:#fffbeb; border-color:#fde68a; }
    .cgray   { background:#f9fafb; border-color:#e5e7eb; }
    .clbl    { font-size:7pt; color:#64748b; margin-bottom:4px; }
    .cval    { font-size:12pt; font-weight:bold; }
    .cunit   { font-size:7pt; color:#94a3b8; margin-top:2px; }
    .vblue   { color:#1d4ed8; }
    .vgreen  { color:#15803d; }
    .vred    { color:#b91c1c; }
    .vamber  { color:#b45309; }
    .vgray   { color:#374151; }

    /* ── Data table ── */
    .tbl          { width:100%; border-collapse:collapse; margin-bottom:13px; font-size:8pt; }
    .tbl thead th { background:#1e3a8a; color:#fff; padding:5px 5px; text-align:right; font-weight:bold; }
    .tbl tbody td { padding:4px 5px; border-bottom:1px solid #f1f5f9; }
    .tbl tbody tr:nth-child(even) td { background:#f8fafc; }
    .tbl .tr-tot td { background:#eff6ff; font-weight:bold; border-top:2px solid #bfdbfe; }

    /* ── Badges ── */
    .bg  { padding:1px 5px; border-radius:5px; font-size:7pt; white-space:nowrap; }
    .bg-green { background:#dcfce7; color:#166534; }
    .bg-amber { background:#fef9c3; color:#92400e; }
    .bg-gray  { background:#f3f4f6; color:#374151; }

    /* ── Utilities ── */
    .pos  { color:#15803d; font-weight:bold; }
    .neg  { color:#b91c1c; font-weight:bold; }
    .muted{ color:#9ca3af; }
    .bold { font-weight:bold; }
    .sm   { font-size:7.5pt; }
    .num  { text-align:left; }

    /* ── Footer ── */
    .footer { margin-top:18px; text-align:center; font-size:7.5pt; color:#9ca3af; border-top:1px solid #e2e8f0; padding-top:8px; }
</style>
</head>
<body>
@php
    $monthNames = ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
    $periodLabel = $month ? (($monthNames[$month] ?? $month) . ' ' . ($year ?? '')) : ($year ?? '');
    $fmt = fn($v) => number_format((float) $v, 2);
    $countPaid    = $salaries->where('status', 'paid')->count();
    $countPending = $salaries->where('status', 'pending')->count();
    $countDraft   = $salaries->where('status', 'draft')->count();
@endphp

{{-- ── Header ── --}}
<div class="hdr">
    <table class="hdr-tbl">
        <tr>
            <td>
                <p class="hdr-title">تقرير الرواتب التفصيلي</p>
                <p class="hdr-sub">
                    @if($periodLabel) الفترة: {{ $periodLabel }} &bull; @endif
                    عدد السجلات: {{ $salaries->count() }} موظف
                    @if($status) &bull; الحالة: {{ match($status){ 'paid'=>'مدفوعة','pending'=>'معلقة','draft'=>'مسودة',default=>$status } }} @endif
                </p>
            </td>
            <td class="hdr-right">
                <div>شركة ثروة للعقارات</div>
                <div style="margin-top:3px;">{{ now()->format('Y/m/d H:i') }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ── Summary Cards ── --}}
<div class="sec">ملخص كشف الرواتب</div>
<table class="cards">
    <tr>
        <td class="card cgray" style="width:14%;">
            <div class="clbl">إجمالي الموظفين</div>
            <div class="cval vgray">{{ $salaries->count() }}</div>
            <div class="cunit">سجل راتب</div>
        </td>
        <td width="4"></td>
        <td class="card cgreen" style="width:14%;">
            <div class="clbl">مدفوعة</div>
            <div class="cval vgreen">{{ $countPaid }}</div>
            <div class="cunit">{{ $fmt($totals['paid']) }} ر.ع</div>
        </td>
        <td width="4"></td>
        <td class="card camber" style="width:14%;">
            <div class="clbl">معلقة</div>
            <div class="cval vamber">{{ $countPending }}</div>
            <div class="cunit">موظف</div>
        </td>
        <td width="4"></td>
        <td class="card cblue" style="width:14%;">
            <div class="clbl">إجمالي الرواتب الأساسية</div>
            <div class="cval vblue" style="font-size:10pt;">{{ $fmt($totals['base']) }}</div>
            <div class="cunit">ر.ع</div>
        </td>
        <td width="4"></td>
        <td class="card cblue" style="width:14%;">
            <div class="clbl">إجمالي البدلات</div>
            <div class="cval vblue" style="font-size:10pt;">{{ $fmt($totals['housing'] + $totals['transport'] + $totals['food'] + $totals['other']) }}</div>
            <div class="cunit">ر.ع</div>
        </td>
        <td width="4"></td>
        <td class="card cgreen" style="width:14%;">
            <div class="clbl">إجمالي الصافي المدفوع</div>
            <div class="cval vgreen" style="font-size:10pt;">{{ $fmt($totals['net']) }}</div>
            <div class="cunit">ر.ع</div>
        </td>
    </tr>
</table>

{{-- ── Detailed Table ── --}}
<div class="sec">تفاصيل رواتب الموظفين ({{ $salaries->count() }} سجل)</div>
<table class="tbl">
    <thead>
        <tr>
            <th style="width:14%;">الموظف</th>
            <th style="width:8%;">الفترة</th>
            <th style="width:9%;">الراتب الأساسي</th>
            <th style="width:9%;">بدل السكن</th>
            <th style="width:9%;">بدل المواصلات</th>
            <th style="width:9%;">بدل الطعام</th>
            <th style="width:9%;">بدلات أخرى</th>
            <th style="width:7%;">المكافآت</th>
            <th style="width:7%;">الاستقطاعات</th>
            <th style="width:9%;">الصافي</th>
            <th style="width:7%;">الحالة</th>
            <th style="width:9%;">تاريخ الدفع</th>
        </tr>
    </thead>
    <tbody>
        @forelse($salaries as $s)
        @php
            $sbg = match($s->status){ 'paid'=>'bg-green','pending'=>'bg-amber',default=>'bg-gray' };
        @endphp
        <tr>
            <td class="bold">{{ $s->employee?->name ?? '—' }}</td>
            <td class="sm">{{ $s->periodLabel() }}</td>
            <td>{{ $fmt($s->base_salary) }}</td>
            <td>@if((float)$s->housing_allowance > 0)<span class="vblue">{{ $fmt($s->housing_allowance) }}</span>@else<span class="muted">—</span>@endif</td>
            <td>@if((float)$s->transport_allowance > 0)<span class="vblue">{{ $fmt($s->transport_allowance) }}</span>@else<span class="muted">—</span>@endif</td>
            <td>@if((float)$s->food_allowance > 0)<span class="vblue">{{ $fmt($s->food_allowance) }}</span>@else<span class="muted">—</span>@endif</td>
            <td>@if((float)$s->other_allowances > 0)<span class="vblue">{{ $fmt($s->other_allowances) }}</span>@else<span class="muted">—</span>@endif</td>
            <td>@if((float)$s->bonuses > 0)<span class="vgreen">{{ $fmt($s->bonuses) }}</span>@else<span class="muted">—</span>@endif</td>
            <td>@if((float)$s->deductions > 0)<span class="vred">{{ $fmt($s->deductions) }}</span>@else<span class="muted">—</span>@endif</td>
            <td class="bold vgreen">{{ $fmt($s->net_paid) }}</td>
            <td><span class="bg {{ $sbg }}">{{ $s->statusLabel() }}</span></td>
            <td class="sm muted">{{ $s->paid_at?->format('Y/m/d') ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="12" style="text-align:center;" class="muted">لا توجد بيانات</td></tr>
        @endforelse

        {{-- Totals row --}}
        @if($salaries->count())
        <tr class="tr-tot">
            <td colspan="2">الإجمالي ({{ $salaries->count() }} موظف)</td>
            <td>{{ $fmt($totals['base']) }}</td>
            <td>{{ $totals['housing'] > 0 ? $fmt($totals['housing']) : '—' }}</td>
            <td>{{ $totals['transport'] > 0 ? $fmt($totals['transport']) : '—' }}</td>
            <td>{{ $totals['food'] > 0 ? $fmt($totals['food']) : '—' }}</td>
            <td>{{ $totals['other'] > 0 ? $fmt($totals['other']) : '—' }}</td>
            <td class="vgreen">{{ $totals['bonuses'] > 0 ? $fmt($totals['bonuses']) : '—' }}</td>
            <td class="vred">{{ $totals['deductions'] > 0 ? $fmt($totals['deductions']) : '—' }}</td>
            <td class="vgreen">{{ $fmt($totals['net']) }}</td>
            <td colspan="2"></td>
        </tr>
        @endif
    </tbody>
</table>

{{-- ── Allowances Breakdown ── --}}
@if(($totals['housing'] + $totals['transport'] + $totals['food'] + $totals['other']) > 0)
<div class="sec">ملخص البدلات</div>
<table class="tbl" style="width:55%;">
    <thead>
        <tr>
            <th>نوع البدل</th>
            <th>الإجمالي (ر.ع)</th>
        </tr>
    </thead>
    <tbody>
        @if($totals['housing'] > 0)
        <tr><td>بدل السكن</td><td class="vblue bold">{{ $fmt($totals['housing']) }}</td></tr>
        @endif
        @if($totals['transport'] > 0)
        <tr><td>بدل المواصلات</td><td class="vblue bold">{{ $fmt($totals['transport']) }}</td></tr>
        @endif
        @if($totals['food'] > 0)
        <tr><td>بدل الطعام</td><td class="vblue bold">{{ $fmt($totals['food']) }}</td></tr>
        @endif
        @if($totals['other'] > 0)
        <tr><td>بدلات أخرى</td><td class="vblue bold">{{ $fmt($totals['other']) }}</td></tr>
        @endif
        <tr class="tr-tot">
            <td>إجمالي البدلات</td>
            <td class="vblue">{{ $fmt($totals['housing'] + $totals['transport'] + $totals['food'] + $totals['other']) }}</td>
        </tr>
    </tbody>
</table>
@endif

<div class="footer">
    شركة ثروة للعقارات &mdash; تقرير الرواتب التفصيلي
    @if($periodLabel) &mdash; الفترة: {{ $periodLabel }} @endif
    &mdash; تم التوليد في {{ now()->format('Y/m/d H:i') }}
</div>
</body>
</html>
