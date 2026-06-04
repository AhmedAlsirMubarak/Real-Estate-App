<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8">
<title>تقرير المصروفات</title>
<style>
    @page { margin: 14mm 12mm 14mm 12mm; }
    body  { font-family: dejavusans, sans-serif; font-size: 8.5pt; color: #1e293b; direction: rtl; margin:0; padding:0; }

    .hdr       { background:#fff; border-bottom:3px solid #7f1d1d; padding:14px 18px 12px; margin-bottom:14px; }
    .hdr-tbl   { width:100%; border-collapse:collapse; }
    .hdr-logo  { max-height:50px; max-width:70px; }
    .hdr-title { font-size:15pt; font-weight:bold; color:#1e293b; margin:0 0 3px; }
    .hdr-sub   { font-size:8.5pt; color:#64748b; margin:0; }
    .hdr-right { font-size:8.5pt; color:#475569; text-align:left; vertical-align:middle; white-space:nowrap; }
    .hdr-co    { font-size:9.5pt; font-weight:bold; color:#1e293b; margin-bottom:3px; }
    .hdr-date  { font-size:7.5pt; color:#94a3b8; }

    .sec { font-size:11pt; font-weight:bold; color:#7f1d1d; border-bottom:2px solid #7f1d1d; padding-bottom:3px; margin:13px 0 8px; }

    .cards   { width:100%; border-collapse:collapse; margin-bottom:12px; }
    .card    { border:1px solid #e2e8f0; padding:9px 6px; text-align:center; vertical-align:top; }
    .cred    { background:#fff1f2; border-color:#fecdd3; }
    .cblue   { background:#eff6ff; border-color:#bfdbfe; }
    .corange { background:#fff7ed; border-color:#fed7aa; }
    .cgray   { background:#f9fafb; border-color:#e5e7eb; }
    .clbl    { font-size:7pt; color:#64748b; margin-bottom:4px; }
    .cval    { font-size:12pt; font-weight:bold; }
    .cunit   { font-size:7pt; color:#94a3b8; margin-top:2px; }
    .vred    { color:#b91c1c; }
    .vblue   { color:#1d4ed8; }
    .vorange { color:#c2410c; }
    .vgray   { color:#374151; }

    .tbl          { width:100%; border-collapse:collapse; margin-bottom:13px; font-size:8pt; }
    .tbl thead th { background:#7f1d1d; color:#fff; padding:5px 5px; text-align:right; font-weight:bold; }
    .tbl tbody td { padding:4px 5px; border-bottom:1px solid #f1f5f9; }
    .tbl tbody tr:nth-child(even) td { background:#fafafa; }
    .tbl .tr-tot td { background:#fff1f2; font-weight:bold; border-top:2px solid #fecdd3; }

    .bg  { padding:1px 5px; border-radius:5px; font-size:7pt; white-space:nowrap; }
    .bg-blue   { background:#dbeafe; color:#1d4ed8; }
    .bg-orange { background:#ffedd5; color:#c2410c; }
    .bg-gray   { background:#f3f4f6; color:#374151; }

    .neg  { color:#b91c1c; font-weight:bold; }
    .muted{ color:#9ca3af; }
    .bold { font-weight:bold; }
    .sm   { font-size:7.5pt; }

    .footer     { margin-top:18px; border-top:1px solid #e2e8f0; padding-top:8px; }
    .footer-tbl { width:100%; border-collapse:collapse; }
    .footer-co  { font-size:8pt; font-weight:bold; color:#7f1d1d; }
    .footer-txt { font-size:7pt; color:#9ca3af; margin-top:2px; }
    .footer-logo{ max-height:26px; max-width:46px; opacity:.55; }
</style>
</head>
<body>
@php
    $monthNames = ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
    $periodLabel = $month ? (($monthNames[$month] ?? $month) . ' ' . $year) : $year;
    $fmt = fn($v) => number_format((float)$v, 2);

    $expenses = $query; // passed as $query from controller

    $byCategory = $expenses->groupBy('category');
    $scopeFilter = $scope;
@endphp

{{-- Header --}}
<div class="hdr">
    <table class="hdr-tbl">
        <tr>
            <td style="vertical-align:middle; width:62%;">
                <p class="hdr-title">تقرير المصروفات التفصيلي</p>
                <p class="hdr-sub">
                    الفترة: {{ $periodLabel }}
                    @if($scopeFilter) &bull; النطاق: {{ $scopeFilter === 'company' ? 'مصروفات الشركة' : 'مصروفات العقارات' }} @endif
                    @if($category) &bull; الفئة: {{ $expenses->first()?->categoryLabel() ?? $category }} @endif
                    &bull; {{ $expenses->count() }} بند
                </p>
            </td>
            <td class="hdr-right">
                @if(file_exists(public_path('img/logo.png')))
                <img src="{{ public_path('img/logo.png') }}" class="hdr-logo"><br>
                @endif
                <div class="hdr-co" style="margin-top:4px;">شركة ثروة للعقارات</div>
                <div class="hdr-date">{{ now()->format('Y/m/d H:i') }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- Summary cards --}}
<div class="sec">الملخص المالي</div>
<table class="cards">
    <tr>
        <td class="card cgray" style="width:18%;">
            <div class="clbl">عدد البنود</div>
            <div class="cval vgray">{{ $expenses->count() }}</div>
            <div class="cunit">مصروف مسجّل</div>
        </td>
        <td width="5"></td>
        <td class="card cblue" style="width:18%;">
            <div class="clbl">مصروفات الشركة</div>
            <div class="cval vblue" style="font-size:10pt;">{{ $fmt($totals['company']) }}</div>
            <div class="cunit">ر.ع</div>
        </td>
        <td width="5"></td>
        <td class="card corange" style="width:18%;">
            <div class="clbl">مصروفات العقارات</div>
            <div class="cval vorange" style="font-size:10pt;">{{ $fmt($totals['property']) }}</div>
            <div class="cunit">ر.ع</div>
        </td>
        <td width="5"></td>
        <td class="card cred" style="width:18%;">
            <div class="clbl">الإجمالي</div>
            <div class="cval vred" style="font-size:10pt;">{{ $fmt($totals['total']) }}</div>
            <div class="cunit">ر.ع</div>
        </td>
        <td width="5"></td>
        <td class="card cgray" style="width:18%;">
            <div class="clbl">متوسط المصروف</div>
            <div class="cval vgray" style="font-size:10pt;">{{ $expenses->count() > 0 ? $fmt($totals['total'] / $expenses->count()) : '0.00' }}</div>
            <div class="cunit">ر.ع</div>
        </td>
    </tr>
</table>

{{-- Detailed expenses table --}}
<div class="sec">تفاصيل المصروفات ({{ $expenses->count() }} بند)</div>
<table class="tbl">
    <thead>
        <tr>
            <th style="width:22%;">البيان</th>
            <th style="width:11%;">الفئة</th>
            <th style="width:9%;">النطاق</th>
            <th style="width:16%;">العقار</th>
            <th style="width:10%;">التاريخ</th>
            <th style="width:11%;">المبلغ (ر.ع)</th>
            <th style="width:13%;">دُفع بواسطة</th>
            <th style="width:8%;">فاتورة</th>
        </tr>
    </thead>
    <tbody>
        @forelse($expenses as $e)
        <tr>
            <td>
                <div class="bold">{{ $e->title }}</div>
                @if($e->description)<div class="sm muted" style="margin-top:1px;">{{ mb_substr($e->description, 0, 55) }}</div>@endif
            </td>
            <td><span class="bg bg-gray">{{ $e->categoryLabel() }}</span></td>
            <td>
                <span class="bg {{ $e->scope === 'company' ? 'bg-blue' : 'bg-orange' }}">
                    {{ $e->scope === 'company' ? 'شركة' : 'عقار' }}
                </span>
            </td>
            <td class="sm muted">{{ $e->scope === 'property' && $e->expensable ? $e->expensable->name : '—' }}</td>
            <td class="sm">{{ $e->expense_date->format('Y/m/d') }}</td>
            <td class="neg bold">{{ $fmt($e->amount) }}</td>
            <td class="sm muted">{{ $e->paidByUser?->name ?? '—' }}</td>
            <td class="sm">
                @php
                    $invCount = ($e->relationLoaded('invoices') ? $e->invoices->count() : 0)
                              + ($e->receipt_path ? 1 : 0);
                @endphp
                @if($invCount > 0)
                <span style="color:#1d4ed8;">✓ {{ $invCount }}</span>
                @else
                <span class="muted">—</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;" class="muted">لا توجد بيانات</td></tr>
        @endforelse

        @if($expenses->count())
        <tr class="tr-tot">
            <td colspan="5">الإجمالي ({{ $expenses->count() }} بند)</td>
            <td class="neg">{{ $fmt($totals['total']) }}</td>
            <td colspan="2"></td>
        </tr>
        @endif
    </tbody>
</table>

{{-- Category breakdown --}}
@if($byCategory->count() > 1)
<div class="sec">توزيع المصروفات حسب الفئة</div>
<table class="tbl" style="width:55%;">
    <thead>
        <tr>
            <th>الفئة</th>
            <th>عدد البنود</th>
            <th>الإجمالي (ر.ع)</th>
            <th>النسبة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($byCategory->sortByDesc(fn($g) => $g->sum('amount')) as $cat => $group)
        @php $catTotal = $group->sum('amount'); $pct = $totals['total'] > 0 ? round($catTotal / $totals['total'] * 100, 1) : 0; @endphp
        <tr>
            <td class="bold">{{ $group->first()->categoryLabel() }}</td>
            <td style="text-align:center;">{{ $group->count() }}</td>
            <td class="neg">{{ $fmt($catTotal) }}</td>
            <td style="color:#6b7280;">{{ $pct }}%</td>
        </tr>
        @endforeach
        <tr class="tr-tot">
            <td>الإجمالي</td>
            <td style="text-align:center;">{{ $expenses->count() }}</td>
            <td class="neg">{{ $fmt($totals['total']) }}</td>
            <td>100%</td>
        </tr>
    </tbody>
</table>
@endif

<div class="footer">
    <table class="footer-tbl">
        <tr>
            <td>
                <div class="footer-co">شركة ثروة للعقارات</div>
                <div class="footer-txt">تقرير المصروفات &mdash; الفترة: {{ $periodLabel }} &mdash; تم التوليد: {{ now()->format('Y/m/d H:i') }}</div>
            </td>
            @if(file_exists(public_path('img/logo.png')))
            <td style="text-align:left; vertical-align:middle; width:56px;">
                <img src="{{ public_path('img/logo.png') }}" class="footer-logo">
            </td>
            @endif
        </tr>
    </table>
</div>
</body>
</html>
