@php
$isAr = $locale === 'ar';
$tr   = fn($ar,$en) => $isAr ? $ar : $en;
$cur  = 'OMR';
$fmt  = fn($n) => number_format((float)$n, 3);
$fmtN = fn($n) => number_format((float)$n);
$has  = fn(string $s) => in_array($s, $sections);

// Unpack data
$properties         = $data['properties'];
$allUnits           = $data['allUnits'];
$allContracts       = $data['allContracts'];
$activeContracts    = $data['activeContracts'];
$allPayments        = $data['allPayments'];
$expenses           = $data['expenses'];
$maintenance        = $data['maintenance'];
$expectedRevenue    = $data['expectedRevenue'];
$collectedRevenue   = $data['collectedRevenue'];
$outstandingRev     = $data['outstandingRev'];
$totalExpenses      = $data['totalExpenses'];
$netProfit          = $data['netProfit'];
$collectionRate     = $data['collectionRate'];
$profitMargin       = $data['profitMargin'];
$totalUnits         = $data['totalUnits'];
$occupiedUnits      = $data['occupiedUnits'];
$vacantUnits        = $data['vacantUnits'];
$reservedUnits      = $data['reservedUnits'];
$maintenanceUnits   = $data['maintenanceUnits'];
$occupancyRate      = $data['occupancyRate'];
$expiringIn30       = $data['expiringIn30'];
$expiringIn60       = $data['expiringIn60'];
$expiringIn90       = $data['expiringIn90'];
$expiredContracts   = $data['expiredContracts'];
$aging              = $data['aging'];
$monthlyTrends      = $data['monthlyTrends'];
$propProfit         = $data['propertyProfitability'];
$vacantList         = $data['vacantUnitsList'];
$alerts             = $data['alerts'];
$commissionInvoices = $data['commissionInvoices'];
$totalCommissions   = $data['totalCommissions'];
$today              = now()->startOfDay();
@endphp
<!DOCTYPE html>
<html lang="{{ $isAr?'ar':'en' }}" dir="{{ $isAr?'rtl':'ltr' }}">
<head>
<meta charset="utf-8">
<title>{{ $reportTitle }}</title>
<style>
@page { margin: 18mm 10mm 18mm; }
body  { font-family:dejavusans,sans-serif; font-size:9pt; color:#1e293b; direction:{{ $isAr?'rtl':'ltr' }}; margin:0; padding:0; }

.cover         { background:linear-gradient(135deg,#312e81 0%,#4f46e5 60%,#7c3aed 100%); color:#fff; padding:32px 24px 24px; margin:-18mm -10mm 0; text-align:center; }
.cover-logo    { max-height:60px; margin-bottom:12px; }
.cover-title   { font-size:17pt; font-weight:bold; margin:0 0 6px; }
.cover-sub     { font-size:10pt; opacity:.85; margin:0 0 18px; }
.cover-meta    { display:inline-block; background:rgba(255,255,255,.15); border-radius:10px; padding:10px 22px; font-size:8.5pt; }
.cover-meta td { padding:3px 12px; text-align:{{ $isAr?'right':'left' }}; }
.cover-lbl     { opacity:.75; }
.cover-val     { font-weight:bold; }
.cover-divider { height:4px; background:linear-gradient(90deg,#7c3aed,#4f46e5,#06b6d4); margin:0 -10mm; }

.sec-title   { font-size:11.5pt; font-weight:bold; color:#fff; background:#312e81; padding:7px 12px; margin:18px -10mm 10px; }
.sec-title span { font-size:8.5pt; opacity:.75; font-weight:normal; }
.sub-title   { font-size:9.5pt; font-weight:bold; color:#312e81; border-{{ $isAr?'right':'left' }}:4px solid #4f46e5; padding:5px 9px; background:#eef2ff; margin:12px 0 7px; }

.kpi-row  { width:100%; border-collapse:collapse; margin:10px 0; }
.kpi      { padding:10px 7px; text-align:center; border:1px solid #e2e8f0; vertical-align:top; }
.kpi-lbl  { font-size:7pt; color:#64748b; margin-bottom:4px; line-height:1.3; }
.kpi-val  { font-size:13pt; font-weight:bold; }
.kpi-unit { font-size:6.5pt; color:#94a3b8; margin-top:2px; }
.kpi-blue   { background:#eff6ff; border-color:#bfdbfe; }
.kpi-green  { background:#f0fdf4; border-color:#bbf7d0; }
.kpi-red    { background:#fff1f2; border-color:#fecdd3; }
.kpi-amber  { background:#fffbeb; border-color:#fde68a; }
.kpi-gray   { background:#f9fafb; border-color:#e5e7eb; }
.kpi-indigo { background:#eef2ff; border-color:#c7d2fe; }
.kpi-violet { background:#f5f3ff; border-color:#ddd6fe; }
.kpi-teal   { background:#f0fdfa; border-color:#99f6e4; }
.v-blue{color:#1d4ed8;} .v-green{color:#15803d;} .v-red{color:#b91c1c;}
.v-amber{color:#b45309;} .v-gray{color:#374151;} .v-indigo{color:#4338ca;}
.v-violet{color:#7c3aed;} .v-teal{color:#0f766e;}

.pbar-wrap { background:#e2e8f0; border-radius:99px; height:8px; overflow:hidden; }
.pbar-fill { height:8px; border-radius:99px; }
.pbar-green { background:#22c55e; }
.pbar-amber { background:#f59e0b; }
.pbar-red   { background:#ef4444; }
.pbar-indigo{ background:#6366f1; }

.tbl             { width:100%; border-collapse:collapse; margin:8px 0 12px; font-size:8pt; }
.tbl thead th    { background:#312e81; color:#fff; padding:6px 7px; text-align:{{ $isAr?'right':'left' }}; font-weight:bold; font-size:7.5pt; }
.tbl tbody td    { padding:5px 7px; border-bottom:1px solid #f1f5f9; vertical-align:top; }
.tbl tbody tr:nth-child(even) td { background:#f8fafc; }
.tbl .tr-sub     { background:#eef2ff !important; font-weight:bold; }
.tbl .tr-total   { background:#312e81 !important; color:#fff !important; font-weight:bold; }
.tbl .tr-total td{ color:#fff !important; border-bottom:none; }
.tbl-indigo thead th { background:#4338ca; }
.tbl-green  thead th { background:#15803d; }
.tbl-amber  thead th { background:#92400e; }
.tbl-red    thead th { background:#9f1239; }
.tbl-gray   thead th { background:#374151; }
.tbl-teal   thead th { background:#0f766e; }

.bg       { display:inline-block; padding:1px 7px; border-radius:6px; font-size:7pt; white-space:nowrap; }
.bg-green  { background:#dcfce7; color:#166534; }
.bg-blue   { background:#dbeafe; color:#1d4ed8; }
.bg-amber  { background:#fef9c3; color:#92400e; }
.bg-red    { background:#fee2e2; color:#b91c1c; }
.bg-gray   { background:#f3f4f6; color:#374151; }
.bg-indigo { background:#e0e7ff; color:#4338ca; }
.bg-violet { background:#ede9fe; color:#6d28d9; }

.info-grid        { width:100%; border-collapse:collapse; margin:8px 0; }
.info-grid td     { padding:5px 8px; border:1px solid #e2e8f0; font-size:8.5pt; }
.info-grid .lbl   { background:#f8fafc; font-weight:bold; color:#475569; width:35%; }

.alert-high   { background:#fff1f2; border-{{ $isAr?'right':'left' }}:3px solid #ef4444; padding:4px 8px; margin-bottom:4px; }
.alert-medium { background:#fffbeb; border-{{ $isAr?'right':'left' }}:3px solid #f59e0b; padding:4px 8px; margin-bottom:4px; }
.alert-low    { background:#f0fdf4; border-{{ $isAr?'right':'left' }}:3px solid #22c55e; padding:4px 8px; margin-bottom:4px; }

.pos   { color:#15803d; font-weight:bold; }
.neg   { color:#b91c1c; font-weight:bold; }
.muted { color:#9ca3af; }
.bold  { font-weight:bold; }
.sm    { font-size:7.5pt; }
.xs    { font-size:7pt; }
.center { text-align:center; }
.nowrap { white-space:nowrap; }
.overdue-row td { background:#fff1f2 !important; }
</style>
</head>
<body>

{{-- COVER --}}
<div class="cover">
    @if(file_exists(public_path('img/logo.png')))
    <img src="{{ public_path('img/logo.png') }}" class="cover-logo" alt="logo">
    @endif
    <div class="cover-title">{{ $tr('التقرير الشامل لإدارة المباني','Comprehensive Building Management Report') }}</div>
    <div class="cover-sub">{{ $reportTitle }}</div>
    <table class="cover-meta" cellspacing="0">
        <tr>
            <td class="cover-lbl">{{ $tr('عدد العقارات','Properties') }}</td>
            <td class="cover-val">{{ $properties->count() }}</td>
        </tr>
        <tr>
            <td class="cover-lbl">{{ $tr('فترة التقرير','Report Period') }}</td>
            <td class="cover-val">{{ $from->format('Y/m/d') }} — {{ $to->format('Y/m/d') }}</td>
        </tr>
        <tr>
            <td class="cover-lbl">{{ $tr('إجمالي الوحدات','Total Units') }}</td>
            <td class="cover-val">{{ $totalUnits }}</td>
        </tr>
        <tr>
            <td class="cover-lbl">{{ $tr('تاريخ الإصدار','Issued On') }}</td>
            <td class="cover-val">{{ now()->format('Y/m/d H:i') }}</td>
        </tr>
    </table>
</div>
<div class="cover-divider"></div>

{{-- ══════════════════════════════════════════════════════════════════════════
     1. EXECUTIVE SUMMARY
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('executive_summary'))
<div class="sec-title">1. {{ $tr('الملخص التنفيذي','Executive Summary') }}</div>

@php
$collColor  = $collectionRate >= 90 ? 'kpi-green' : ($collectionRate >= 70 ? 'kpi-amber' : 'kpi-red');
$collVColor = $collectionRate >= 90 ? 'v-green'   : ($collectionRate >= 70 ? 'v-amber'   : 'v-red');
@endphp

<table class="kpi-row">
<tr>
    <td class="kpi kpi-indigo" style="width:14%;"><div class="kpi-lbl">{{ $tr('إجمالي العقارات','Total Properties') }}</div><div class="kpi-val v-indigo">{{ $properties->count() }}</div></td>
    <td width="4"></td>
    <td class="kpi kpi-gray"   style="width:14%;"><div class="kpi-lbl">{{ $tr('إجمالي الوحدات','Total Units') }}</div><div class="kpi-val v-gray">{{ $totalUnits }}</div></td>
    <td width="4"></td>
    <td class="kpi kpi-teal"   style="width:14%;"><div class="kpi-lbl">{{ $tr('الوحدات المشغولة','Occupied') }}</div><div class="kpi-val v-teal">{{ $occupiedUnits }}</div><div class="kpi-unit">{{ $occupancyRate }}%</div></td>
    <td width="4"></td>
    <td class="kpi kpi-amber"  style="width:14%;"><div class="kpi-lbl">{{ $tr('الوحدات الشاغرة','Vacant') }}</div><div class="kpi-val v-amber">{{ $vacantUnits }}</div></td>
    <td width="4"></td>
    <td class="kpi kpi-violet" style="width:14%;"><div class="kpi-lbl">{{ $tr('محجوز / صيانة','Reserved/Maint.') }}</div><div class="kpi-val v-violet">{{ $reservedUnits + $maintenanceUnits }}</div></td>
    <td width="4"></td>
    <td class="kpi {{ $collColor }}" style="width:14%;"><div class="kpi-lbl">{{ $tr('نسبة الإشغال','Occupancy Rate') }}</div><div class="kpi-val {{ $collVColor }}">{{ $occupancyRate }}%</div><div class="kpi-unit"><div class="pbar-wrap" style="margin-top:3px;"><div class="pbar-fill pbar-indigo" style="width:{{ min(100,$occupancyRate) }}%;"></div></div></div></td>
</tr>
</table>

<table class="kpi-row" style="margin-top:6px;">
<tr>
    <td class="kpi kpi-blue"   style="width:18%;"><div class="kpi-lbl">{{ $tr('الإيرادات المتوقعة','Expected Revenue') }}</div><div class="kpi-val v-blue">{{ $fmtN($expectedRevenue) }}</div><div class="kpi-unit">{{ $cur }}</div></td>
    <td width="4"></td>
    <td class="kpi kpi-green"  style="width:18%;"><div class="kpi-lbl">{{ $tr('الإيرادات المحصّلة','Collected Revenue') }}</div><div class="kpi-val v-green">{{ $fmtN($collectedRevenue) }}</div><div class="kpi-unit">{{ $cur }}</div></td>
    <td width="4"></td>
    <td class="kpi kpi-red"    style="width:18%;"><div class="kpi-lbl">{{ $tr('الإيرادات غير المحصّلة','Outstanding Revenue') }}</div><div class="kpi-val v-red">{{ $fmtN($outstandingRev) }}</div><div class="kpi-unit">{{ $cur }}</div></td>
    <td width="4"></td>
    <td class="kpi kpi-amber"  style="width:18%;"><div class="kpi-lbl">{{ $tr('إجمالي المصروفات','Total Expenses') }}</div><div class="kpi-val v-amber">{{ $fmtN($totalExpenses) }}</div><div class="kpi-unit">{{ $cur }}</div></td>
    <td width="4"></td>
    <td class="kpi {{ $netProfit>=0?'kpi-green':'kpi-red' }}" style="width:18%;"><div class="kpi-lbl">{{ $tr('صافي الربح','Net Profit') }}</div><div class="kpi-val {{ $netProfit>=0?'v-green':'v-red' }}">{{ $fmtN($netProfit) }}</div><div class="kpi-unit">{{ $cur }} — {{ $netProfit>=0?$tr('ربح','Profit'):$tr('خسارة','Loss') }}</div></td>
</tr>
</table>

<table class="kpi-row" style="margin-top:6px;">
<tr>
    <td class="kpi {{ $collColor }}" style="width:22%;"><div class="kpi-lbl">{{ $tr('نسبة التحصيل','Collection Rate') }}</div><div class="kpi-val {{ $collVColor }}">{{ $collectionRate }}%</div><div class="kpi-unit"><div class="pbar-wrap" style="margin-top:3px;"><div class="pbar-fill {{ $collectionRate>=90?'pbar-green':($collectionRate>=70?'pbar-amber':'pbar-red') }}" style="width:{{ min(100,$collectionRate) }}%;"></div></div></div></td>
    <td width="4"></td>
    <td class="kpi kpi-indigo" style="width:22%;"><div class="kpi-lbl">{{ $tr('العقود النشطة','Active Contracts') }}</div><div class="kpi-val v-indigo">{{ $activeContracts->count() }}</div></td>
    <td width="4"></td>
    <td class="kpi kpi-amber"  style="width:22%;"><div class="kpi-lbl">{{ $tr('عقود تنتهي قريبًا','Expiring ≤30 Days') }}</div><div class="kpi-val v-amber">{{ $expiringIn30->count() }}</div></td>
    <td width="4"></td>
    <td class="kpi kpi-red"    style="width:22%;"><div class="kpi-lbl">{{ $tr('طلبات صيانة مفتوحة','Open Maintenance') }}</div><div class="kpi-val v-red">{{ $maintenance->whereIn('status',['pending','in_progress'])->count() }}</div></td>
</tr>
</table>

{{-- Monthly trends --}}
@if(count($monthlyTrends) > 1)
<div class="sub-title">{{ $tr('الاتجاه الشهري','Monthly Trend') }}</div>
<table class="tbl tbl-indigo">
    <thead>
        <tr>
            <th>{{ $tr('الشهر','Month') }}</th>
            <th>{{ $tr('المتوقع','Expected') }}</th>
            <th>{{ $tr('المحصّل','Collected') }}</th>
            <th>{{ $tr('غير محصّل','Outstanding') }}</th>
            <th>{{ $tr('المصروفات','Expenses') }}</th>
            <th>{{ $tr('الصافي','Net') }}</th>
            <th style="width:18%;">{{ $tr('نسبة التحصيل','Coll. %') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($monthlyTrends as $t)
        @php $mTotal = $t['expected']; $mRate = $mTotal>0 ? round($t['collected']/$mTotal*100) : 0; @endphp
        <tr>
            <td class="bold">{{ $isAr ? $t['label'] : $t['label_en'] }}</td>
            <td>{{ $fmtN($t['expected']) }}</td>
            <td class="pos">{{ $fmtN($t['collected']) }}</td>
            <td class="{{ $t['outstanding']>0?'neg':'muted' }}">{{ $fmtN($t['outstanding']) }}</td>
            <td class="neg">{{ $fmtN($t['expenses']) }}</td>
            <td class="{{ $t['net']>=0?'pos':'neg' }}">{{ $fmtN($t['net']) }}</td>
            <td><div class="pbar-wrap" style="width:60px;display:inline-block;vertical-align:middle;"><div class="pbar-fill {{ $mRate>=90?'pbar-green':($mRate>=70?'pbar-amber':'pbar-red') }}" style="width:{{ min(100,$mRate) }}%;"></div></div> <span class="xs">{{ $mRate }}%</span></td>
        </tr>
        @endforeach
        <tr class="tr-total">
            <td>{{ $tr('الإجمالي','Total') }}</td>
            <td>{{ $fmtN(collect($monthlyTrends)->sum('expected')) }}</td>
            <td>{{ $fmtN(collect($monthlyTrends)->sum('collected')) }}</td>
            <td>{{ $fmtN(collect($monthlyTrends)->sum('outstanding')) }}</td>
            <td>{{ $fmtN(collect($monthlyTrends)->sum('expenses')) }}</td>
            <td>{{ $fmtN(collect($monthlyTrends)->sum('net')) }}</td>
            <td>{{ $collectionRate }}%</td>
        </tr>
    </tbody>
</table>
@endif
@endif {{-- executive_summary --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     2. PROPERTY INFORMATION
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('property_info'))
<div class="sec-title">2. {{ $tr('معلومات العقارات','Property Information') }}</div>
@foreach($properties as $prop)
<div class="sub-title">
    {{ $isAr ? ($prop->name_ar ?? $prop->name_en) : ($prop->name_en ?? $prop->name_ar) }}
    @if($prop->code) <span class="xs muted"> — {{ $prop->code }}</span> @endif
</div>
<table class="info-grid">
    <tr>
        <td class="lbl">{{ $tr('اسم العقار','Property Name') }}</td>
        <td>{{ $prop->name_ar }} @if($prop->name_en) / {{ $prop->name_en }} @endif</td>
        <td class="lbl">{{ $tr('الكود','Code') }}</td>
        <td>{{ $prop->code ?? '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('النوع','Type') }}</td>
        <td>{{ $prop->typeLabel() }}</td>
        <td class="lbl">{{ $tr('الغرض','Purpose') }}</td>
        <td>{{ $prop->purposeLabel() }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('العنوان','Address') }}</td>
        <td colspan="3">{{ $prop->address }}@if($prop->city), {{ $prop->city }}@endif</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('المالك','Owner') }}</td>
        <td>{{ $prop->owner?->user?->name ?? $tr('الشركة','Company-owned') }}</td>
        <td class="lbl">{{ $tr('الموظف المسؤول','Assigned Employee') }}</td>
        <td>{{ $prop->employee?->name ?? '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('الحالة','Status') }}</td>
        <td><span class="bg {{ $prop->status==='active'?'bg-green':'bg-gray' }}">{{ $prop->statusLabel() }}</span></td>
        <td class="lbl">{{ $tr('عدد الطوابق','Floors') }}</td>
        <td>{{ $prop->floors ?? '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('المساحة الإجمالية','Total Area') }}</td>
        <td>{{ $prop->total_area ? number_format($prop->total_area).' م²' : '—' }}</td>
        <td class="lbl">{{ $tr('عدد الوحدات','Units') }}</td>
        <td class="bold">{{ $prop->units->count() }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('ح. الكهرباء','Electricity Acct.') }}</td>
        <td>{{ $prop->electricity_account_number ?? '—' }}</td>
        <td class="lbl">{{ $tr('ح. الماء','Water Acct.') }}</td>
        <td>{{ $prop->water_account_number ?? '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('تاريخ الإضافة','Date Added') }}</td>
        <td>{{ $prop->created_at->format('Y/m/d') }}</td>
        <td class="lbl">{{ $tr('القسم','Section') }}</td>
        <td>{{ $prop->sectionLabel() }}</td>
    </tr>
</table>
@endforeach
@endif {{-- property_info --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     3. FINANCIAL PERFORMANCE
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('financial_performance'))
<div class="sec-title">3. {{ $tr('الأداء المالي','Financial Performance Summary') }}</div>
<table class="info-grid">
    <tr>
        <td class="lbl">{{ $tr('إجمالي الإيرادات المتوقعة','Total Expected Revenue') }}</td>
        <td class="bold v-blue">{{ $fmt($expectedRevenue) }} {{ $cur }}</td>
        <td class="lbl">{{ $tr('الإيرادات المحصّلة','Total Collected Revenue') }}</td>
        <td class="pos">{{ $fmt($collectedRevenue) }} {{ $cur }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('الإيرادات غير المحصّلة','Outstanding Revenue') }}</td>
        <td class="neg">{{ $fmt($outstandingRev) }} {{ $cur }}</td>
        <td class="lbl">{{ $tr('نسبة التحصيل','Collection Rate') }}</td>
        <td class="bold {{ $collectionRate>=90?'v-green':($collectionRate>=70?'v-amber':'v-red') }}">{{ $collectionRate }}%</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('إجمالي المصروفات','Total Expenses') }}</td>
        <td class="neg">{{ $fmt($totalExpenses) }} {{ $cur }}</td>
        <td class="lbl">{{ $tr('صافي الربح / الخسارة','Net Profit / Loss') }}</td>
        <td class="{{ $netProfit>=0?'pos':'neg' }}">{{ $fmt($netProfit) }} {{ $cur }} — {{ $netProfit>=0?$tr('ربح','Profit'):$tr('خسارة','Loss') }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('هامش الربح %','Profit Margin %') }}</td>
        <td class="bold {{ $profitMargin>=0?'v-green':'v-red' }}">{{ $profitMargin }}%</td>
        <td class="lbl">{{ $tr('متوسط الإيجار الشهري','Avg Monthly Rent') }}</td>
        <td class="bold">{{ $activeContracts->count()>0 ? $fmtN($activeContracts->sum('monthly_rent')/$activeContracts->count()) : '—' }} {{ $cur }}</td>
    </tr>
</table>

@if($properties->count() > 1)
<div class="sub-title">{{ $tr('ربحية كل عقار','Per-Property Profitability') }}</div>
<table class="tbl tbl-indigo">
    <thead>
        <tr>
            <th>{{ $tr('العقار','Property') }}</th>
            <th>{{ $tr('المحصّل','Collected') }}</th>
            <th>{{ $tr('غير محصّل','Outstanding') }}</th>
            <th>{{ $tr('المصروفات','Expenses') }}</th>
            <th>{{ $tr('الصافي','Net') }}</th>
            <th>{{ $tr('الهامش %','Margin %') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($propProfit as $pp)
        <tr>
            <td class="bold">{{ $isAr?($pp['property']->name_ar??$pp['property']->name_en):($pp['property']->name_en??$pp['property']->name_ar) }}</td>
            <td class="pos">{{ $fmtN($pp['collected']) }}</td>
            <td class="{{ $pp['outstanding']>0?'neg':'muted' }}">{{ $fmtN($pp['outstanding']) }}</td>
            <td class="neg">{{ $fmtN($pp['expenses']) }}</td>
            <td class="{{ $pp['net']>=0?'pos':'neg' }}">{{ $fmtN($pp['net']) }}</td>
            <td class="{{ $pp['margin']>=0?'pos':'neg' }}">{{ $pp['margin'] }}%</td>
        </tr>
        @endforeach
        <tr class="tr-total">
            <td>{{ $tr('الإجمالي','Total') }}</td>
            <td>{{ $fmtN($collectedRevenue) }}</td>
            <td>{{ $fmtN($outstandingRev) }}</td>
            <td>{{ $fmtN($totalExpenses) }}</td>
            <td>{{ $fmtN($netProfit) }}</td>
            <td>{{ $profitMargin }}%</td>
        </tr>
    </tbody>
</table>
@endif
@endif {{-- financial_performance --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     4. OCCUPANCY ANALYSIS
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('occupancy'))
<div class="sec-title">4. {{ $tr('تحليل الإشغال','Occupancy Analysis') }}</div>
<table class="kpi-row">
    <tr>
        <td class="kpi kpi-gray"   style="width:18%;"><div class="kpi-lbl">{{ $tr('إجمالي الوحدات','Total Units') }}</div><div class="kpi-val v-gray">{{ $totalUnits }}</div></td>
        <td width="4"></td>
        <td class="kpi kpi-teal"   style="width:18%;"><div class="kpi-lbl">{{ $tr('مشغولة','Occupied') }}</div><div class="kpi-val v-teal">{{ $occupiedUnits }}</div><div class="kpi-unit">{{ $occupancyRate }}%</div></td>
        <td width="4"></td>
        <td class="kpi kpi-amber"  style="width:18%;"><div class="kpi-lbl">{{ $tr('شاغرة','Vacant') }}</div><div class="kpi-val v-amber">{{ $vacantUnits }}</div><div class="kpi-unit">{{ $totalUnits>0?round($vacantUnits/$totalUnits*100).'%':'—' }}</div></td>
        <td width="4"></td>
        <td class="kpi kpi-indigo" style="width:18%;"><div class="kpi-lbl">{{ $tr('محجوزة','Reserved') }}</div><div class="kpi-val v-indigo">{{ $reservedUnits }}</div></td>
        <td width="4"></td>
        <td class="kpi kpi-red"    style="width:18%;"><div class="kpi-lbl">{{ $tr('قيد الصيانة','Maintenance') }}</div><div class="kpi-val v-red">{{ $maintenanceUnits }}</div></td>
    </tr>
</table>

@if($properties->count() > 1)
<div class="sub-title">{{ $tr('الإشغال لكل عقار','Occupancy per Property') }}</div>
<table class="tbl tbl-teal">
    <thead>
        <tr>
            <th>{{ $tr('العقار','Property') }}</th>
            <th>{{ $tr('إجمالي','Total') }}</th>
            <th>{{ $tr('مشغولة','Occupied') }}</th>
            <th>{{ $tr('شاغرة','Vacant') }}</th>
            <th>{{ $tr('محجوزة','Reserved') }}</th>
            <th>{{ $tr('صيانة','Maint.') }}</th>
            <th>{{ $tr('نسبة الإشغال','Occupancy %') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($properties as $prop)
        @php
            $pu = $prop->units;
            $po = $pu->whereIn('status',['rented','sold'])->count();
            $pv = $pu->where('status','available')->count();
            $pr = $pu->where('status','reserved')->count();
            $pm = $pu->where('status','maintenance')->count();
            $pt = $pu->count();
            $pRate = $pt>0?round($po/$pt*100):0;
        @endphp
        <tr>
            <td class="bold">{{ $isAr?($prop->name_ar??$prop->name_en):($prop->name_en??$prop->name_ar) }}</td>
            <td class="center">{{ $pt }}</td>
            <td class="center pos">{{ $po }}</td>
            <td class="center {{ $pv>0?'neg':'' }}">{{ $pv }}</td>
            <td class="center">{{ $pr }}</td>
            <td class="center">{{ $pm }}</td>
            <td>
                <div class="pbar-wrap" style="width:70px;display:inline-block;vertical-align:middle;">
                    <div class="pbar-fill pbar-indigo" style="width:{{ min(100,$pRate) }}%;"></div>
                </div>
                <span class="sm" style="margin-{{ $isAr?'right':'left' }}:4px;">{{ $pRate }}%</span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endif {{-- occupancy --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     5. UNIT DETAILS
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('unit_details'))
<div class="sec-title">5. {{ $tr('تفاصيل الوحدات','Unit Details Report') }}</div>
@foreach($properties as $prop)
@if($prop->units->count())
<div class="sub-title">{{ $isAr?($prop->name_ar??$prop->name_en):($prop->name_en??$prop->name_ar) }} ({{ $prop->units->count() }} {{ $tr('وحدة','units') }})</div>
<table class="tbl tbl-indigo" style="font-size:7.5pt;">
    <thead>
        <tr>
            <th style="width:8%;">{{ $tr('رقم الوحدة','Unit No.') }}</th>
            <th style="width:7%;">{{ $tr('طابق','Floor') }}</th>
            <th style="width:10%;">{{ $tr('النوع','Type') }}</th>
            <th style="width:8%;">{{ $tr('م²','Area') }}</th>
            <th style="width:5%;">{{ $tr('غرف','BR') }}</th>
            <th style="width:5%;">{{ $tr('حمام','BA') }}</th>
            <th style="width:10%;">{{ $tr('الإيجار الشهري','Monthly Rent') }}</th>
            <th style="width:10%;">{{ $tr('الحالة','Status') }}</th>
            <th style="width:15%;">{{ $tr('المستأجر','Tenant') }}</th>
            <th style="width:10%;">{{ $tr('انتهاء العقد','Lease End') }}</th>
            <th>{{ $tr('أيام الشغور','Days Vacant') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($prop->units->sortBy('unit_number') as $unit)
        @php
            $ac = $unit->activeRentalContract;
            $dLeft = $ac?->end_date ? (int) $ac->end_date->diffInDays($today, false) : null;
            $lastC = $unit->rentalContracts->sortByDesc('end_date')->first();
            $vacantDays = ($unit->status === 'available' && $lastC?->end_date) ? (int)$lastC->end_date->diffInDays(now()) : null;
            $sbg = match($unit->status){'rented'=>'bg-teal','sold'=>'bg-green','available'=>'bg-amber','reserved'=>'bg-blue','maintenance'=>'bg-red',default=>'bg-gray'};
        @endphp
        <tr>
            <td class="bold">{{ $unit->unit_number ?? '—' }}</td>
            <td class="center">{{ $unit->floor ?? '—' }}</td>
            <td>{{ $unit->typeLabel() }}</td>
            <td class="center">{{ $unit->area ? number_format($unit->area) : '—' }}</td>
            <td class="center">{{ $unit->bedrooms ?? '—' }}</td>
            <td class="center">{{ $unit->bathrooms ?? '—' }}</td>
            <td>{{ $ac?->monthly_rent ? $fmtN($ac->monthly_rent) : ($unit->rent_price ? $fmtN($unit->rent_price) : '—') }}</td>
            <td><span class="bg {{ $sbg }}">{{ $unit->statusLabel() }}</span></td>
            <td>{{ $ac?->tenant?->user?->name ?? '—' }}</td>
            <td class="sm">
                @if($ac?->end_date)
                    {{ $ac->end_date->format('Y/m/d') }}
                    @if($dLeft !== null && $dLeft >= 0 && $dLeft <= 30) <span class="bg bg-amber xs">{{ $dLeft }}د</span>
                    @elseif($dLeft !== null && $dLeft < 0) <span class="bg bg-red xs">{{ $tr('منتهي','Expired') }}</span>
                    @endif
                @else <span class="muted">—</span>
                @endif
            </td>
            <td class="center {{ $vacantDays && $vacantDays > 30 ? 'neg' : '' }}">{{ $vacantDays ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endforeach
@endif {{-- unit_details --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     6. LEASE CONTRACTS
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('contracts'))
<div class="sec-title">6. {{ $tr('عقود الإيجار','Lease Contract Report') }}</div>

@php
$contractsByProp = $allContracts->groupBy(fn($c) => $c->unit?->property_id);
@endphp

{{-- Expiry Alerts --}}
@if($expiringIn30->count() || $expiringIn60->count() || $expiringIn90->count() || $expiredContracts->count())
<div class="sub-title" style="background:#fff1f2; border-color:#ef4444; color:#9f1239;">{{ $tr('تنبيهات انتهاء العقود','Contract Expiry Alerts') }}</div>
<table class="kpi-row">
    <tr>
        <td class="kpi kpi-red"   style="width:22%;"><div class="kpi-lbl">{{ $tr('منتهية','Expired') }}</div><div class="kpi-val v-red">{{ $expiredContracts->count() }}</div></td>
        <td width="4"></td>
        <td class="kpi kpi-red"   style="width:22%;"><div class="kpi-lbl">{{ $tr('تنتهي خلال 30 يومًا','Expiring ≤30 Days') }}</div><div class="kpi-val v-red">{{ $expiringIn30->count() }}</div></td>
        <td width="4"></td>
        <td class="kpi kpi-amber" style="width:22%;"><div class="kpi-lbl">{{ $tr('تنتهي خلال 60 يومًا','Expiring ≤60 Days') }}</div><div class="kpi-val v-amber">{{ $expiringIn60->count() }}</div></td>
        <td width="4"></td>
        <td class="kpi kpi-indigo"style="width:22%;"><div class="kpi-lbl">{{ $tr('تنتهي خلال 90 يومًا','Expiring ≤90 Days') }}</div><div class="kpi-val v-indigo">{{ $expiringIn90->count() }}</div></td>
    </tr>
</table>
@endif

{{-- Contract table per property --}}
@foreach($properties as $prop)
@php $pContracts = $allContracts->filter(fn($c) => $c->unit?->property_id === $prop->id); @endphp
@if($pContracts->count())
<div class="sub-title">{{ $isAr?($prop->name_ar??$prop->name_en):($prop->name_en??$prop->name_ar) }}</div>
<table class="tbl tbl-indigo" style="font-size:7.5pt;">
    <thead>
        <tr>
            <th>{{ $tr('الوحدة','Unit') }}</th>
            <th>{{ $tr('المستأجر','Tenant') }}</th>
            <th>{{ $tr('بداية العقد','Start') }}</th>
            <th>{{ $tr('نهاية العقد','End') }}</th>
            <th>{{ $tr('الإيجار الشهري','Monthly Rent') }}</th>
            <th>{{ $tr('الإيجار السنوي','Annual') }}</th>
            <th>{{ $tr('التأمين','Deposit') }}</th>
            <th>{{ $tr('الحالة','Status') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pContracts->sortByDesc('start_date') as $c)
        @php
            $cDaysLeft = $c->end_date ? (int)$c->end_date->diffInDays($today, false) : null;
            $cbg = match($c->status){'active'=>'bg-green','expired'=>'bg-red','terminated'=>'bg-red',default=>'bg-gray'};
            $rowClass = ($c->end_date && $cDaysLeft !== null && $cDaysLeft >= -30 && $cDaysLeft < 0) ? 'overdue-row' : '';
        @endphp
        <tr class="{{ $rowClass }}">
            <td class="bold">{{ $c->unit?->unit_number ?? '—' }}</td>
            <td>{{ $c->tenant?->user?->name ?? '—' }}</td>
            <td class="sm nowrap">{{ $c->start_date?->format('Y/m/d') ?? '—' }}</td>
            <td class="sm nowrap">
                {{ $c->end_date?->format('Y/m/d') ?? '—' }}
                @if($cDaysLeft !== null && $cDaysLeft >= 0 && $cDaysLeft <= 30) <span class="bg bg-amber xs">{{ $cDaysLeft }}{{ $tr('ي','d') }}</span>
                @elseif($cDaysLeft !== null && $cDaysLeft < 0) <span class="bg bg-red xs">{{ $tr('منتهي','Exp.') }}</span>
                @endif
            </td>
            <td class="bold">{{ $fmtN($c->monthly_rent) }}</td>
            <td>{{ $fmtN($c->monthly_rent * 12) }}</td>
            <td>{{ $fmtN($c->deposit ?? 0) }}</td>
            <td><span class="bg {{ $cbg }}">{{ $c->status }}</span></td>
        </tr>
        @endforeach
        <tr class="tr-total">
            <td colspan="4">{{ $tr('إجمالي','Total') }} ({{ $pContracts->count() }} {{ $tr('عقد','contracts') }})</td>
            <td>{{ $fmtN($pContracts->sum('monthly_rent')) }}</td>
            <td>{{ $fmtN($pContracts->sum('monthly_rent') * 12) }}</td>
            <td>{{ $fmtN($pContracts->sum('deposit')) }}</td>
            <td></td>
        </tr>
    </tbody>
</table>
@endif
@endforeach
@endif {{-- contracts --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     7. TENANT REPORT
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('tenants'))
<div class="sec-title">7. {{ $tr('تقرير المستأجرين','Tenant Report') }}</div>
@php $tenantMap = []; foreach($activeContracts as $c){ if(!$c->tenant) continue; $tid=$c->tenant_id; if(!isset($tenantMap[$tid])){ $tenantMap[$tid]=['contract'=>$c,'paid'=>0,'outstanding'=>0,'total'=>0,'payments'=>collect()]; } $cp=$c->payments; $tenantMap[$tid]['paid']+=$cp->where('status','paid')->sum('amount'); $tenantMap[$tid]['outstanding']+=$cp->whereIn('status',['pending','overdue'])->sum('amount'); $tenantMap[$tid]['total']+=$cp->sum('amount'); $tenantMap[$tid]['payments']=$tenantMap[$tid]['payments']->concat($cp); } @endphp
@if(count($tenantMap))
<table class="tbl tbl-teal" style="font-size:7.5pt;">
    <thead>
        <tr>
            <th>{{ $tr('المستأجر','Tenant') }}</th>
            <th>{{ $tr('الهاتف','Phone') }}</th>
            <th>{{ $tr('الوحدة','Unit') }}</th>
            <th>{{ $tr('العقار','Property') }}</th>
            <th>{{ $tr('بداية العقد','Start') }}</th>
            <th>{{ $tr('نهاية العقد','End') }}</th>
            <th>{{ $tr('الإيجار الشهري','Monthly Rent') }}</th>
            <th>{{ $tr('المدفوع','Paid') }}</th>
            <th>{{ $tr('المتبقي','Outstanding') }}</th>
            <th>{{ $tr('نسبة الدفع','Pay %') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tenantMap as $tm)
        @php
            $c = $tm['contract'];
            $tPct = $tm['total']>0?round($tm['paid']/$tm['total']*100):0;
        @endphp
        <tr>
            <td class="bold">{{ $c->tenant?->user?->name ?? '—' }}</td>
            <td class="sm">{{ $c->tenant?->user?->phone ?? $c->tenant?->phone ?? '—' }}</td>
            <td class="bold">{{ $c->unit?->unit_number ?? '—' }}</td>
            <td class="sm">{{ $isAr?($c->unit?->property?->name_ar??$c->unit?->property?->name_en):($c->unit?->property?->name_en??$c->unit?->property?->name_ar) }}</td>
            <td class="sm">{{ $c->start_date?->format('Y/m/d') ?? '—' }}</td>
            <td class="sm">{{ $c->end_date?->format('Y/m/d') ?? '—' }}</td>
            <td>{{ $fmtN($c->monthly_rent) }}</td>
            <td class="pos">{{ $fmtN($tm['paid']) }}</td>
            <td class="{{ $tm['outstanding']>0?'neg':'muted' }}">{{ $fmtN($tm['outstanding']) }}</td>
            <td class="{{ $tPct>=90?'pos':($tPct>=70?'v-amber':'neg') }}">{{ $tPct }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p class="muted sm">{{ $tr('لا يوجد مستأجرون نشطون.','No active tenants.') }}</p>
@endif
@endif {{-- tenants --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     8. RENTAL INCOME
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('rental_income'))
<div class="sec-title">8. {{ $tr('الإيرادات الإيجارية','Rental Income Report') }}</div>
<table class="info-grid">
    <tr>
        <td class="lbl">{{ $tr('الإيرادات المتوقعة','Expected Revenue') }}</td><td class="bold v-blue">{{ $fmt($expectedRevenue) }} {{ $cur }}</td>
        <td class="lbl">{{ $tr('الإيرادات المحصّلة','Collected Revenue') }}</td><td class="pos">{{ $fmt($collectedRevenue) }} {{ $cur }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('الإيرادات غير المحصّلة','Outstanding Revenue') }}</td><td class="neg">{{ $fmt($outstandingRev) }} {{ $cur }}</td>
        <td class="lbl">{{ $tr('نسبة التحصيل','Collection Rate') }}</td><td class="bold {{ $collectionRate>=90?'v-green':($collectionRate>=70?'v-amber':'v-red') }}">{{ $collectionRate }}%</td>
    </tr>
</table>
@if($allPayments->count())
<div class="sub-title">{{ $tr('تفاصيل المدفوعات','Payment Details') }}</div>
<table class="tbl tbl-green" style="font-size:7.5pt;">
    <thead>
        <tr>
            <th>{{ $tr('المستأجر','Tenant') }}</th>
            <th>{{ $tr('الوحدة','Unit') }}</th>
            <th>{{ $tr('العقار','Property') }}</th>
            <th>{{ $tr('الشهر','Month') }}</th>
            <th>{{ $tr('المبلغ','Amount') }}</th>
            <th>{{ $tr('الحالة','Status') }}</th>
            <th>{{ $tr('تاريخ الدفع','Pay Date') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($allPayments->sortByDesc('year')->sortByDesc('month')->take(100) as $p)
        @php $pbg = match($p->status){'paid'=>'bg-green','overdue'=>'bg-red',default=>'bg-amber'}; @endphp
        <tr class="{{ $p->status==='overdue'?'overdue-row':'' }}">
            <td>{{ $p->tenant?->user?->name ?? '—' }}</td>
            <td class="bold">{{ $p->rentalContract?->unit?->unit_number ?? '—' }}</td>
            <td class="sm">{{ $isAr?($p->rentalContract?->unit?->property?->name_ar??'—'):($p->rentalContract?->unit?->property?->name_en??'—') }}</td>
            <td class="sm nowrap">{{ $p->monthName() }}/{{ $p->year }}</td>
            <td>{{ $fmtN($p->amount) }}</td>
            <td><span class="bg {{ $pbg }}">{{ $p->statusLabel() }}</span></td>
            <td class="sm">{{ $p->paid_at?->format('Y/m/d') ?? '—' }}</td>
        </tr>
        @endforeach
        <tr class="tr-total">
            <td colspan="4">{{ $tr('الإجمالي','Total') }} ({{ $allPayments->count() }} {{ $tr('دفعة','payments') }})</td>
            <td>{{ $fmtN($expectedRevenue) }}</td>
            <td colspan="2">{{ $tr('محصّل:','Paid:') }} {{ $fmtN($collectedRevenue) }}</td>
        </tr>
    </tbody>
</table>
@if($allPayments->count() > 100) <p class="xs muted">{{ $tr('* يعرض أحدث 100 دفعة.','* Showing latest 100 payments.') }}</p> @endif
@endif
@endif {{-- rental_income --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     9. OUTSTANDING BALANCES & AGING
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('outstanding'))
<div class="sec-title">9. {{ $tr('التحصيل والأرصدة المتأخرة','Rent Collection & Outstanding Balances') }}</div>
@php
$agTotals = [
    'current' => ['label_ar'=>'لم تستحق بعد','label_en'=>'Not Yet Due','dues'=>$aging['current'],'color'=>'kpi-blue','vc'=>'v-blue'],
    '0_30'    => ['label_ar'=>'0–30 يوم',    'label_en'=>'0–30 Days',    'dues'=>$aging['0_30'],  'color'=>'kpi-amber','vc'=>'v-amber'],
    '31_60'   => ['label_ar'=>'31–60 يوم',   'label_en'=>'31–60 Days',   'dues'=>$aging['31_60'],'color'=>'kpi-amber','vc'=>'v-amber'],
    '61_90'   => ['label_ar'=>'61–90 يوم',   'label_en'=>'61–90 Days',   'dues'=>$aging['61_90'],'color'=>'kpi-red',  'vc'=>'v-red'],
    'over_90' => ['label_ar'=>'+90 يوم',     'label_en'=>'Over 90 Days', 'dues'=>$aging['over_90'],'color'=>'kpi-red','vc'=>'v-red'],
];
$outstandingAll = $allPayments->whereIn('status',['pending','overdue']);
$agingTotal = (float)$outstandingAll->sum('amount');
@endphp
<table class="kpi-row">
    @foreach($agTotals as $ag)
    @php $agAmt=(float)$ag['dues']->sum('amount'); $agPct=$agingTotal>0?round($agAmt/$agingTotal*100):0; @endphp
    <tr><td class="kpi {{ $ag['color'] }}" style="padding:8px;">
        <div class="kpi-lbl">{{ $tr($ag['label_ar'],$ag['label_en']) }}</div>
        <div class="kpi-val {{ $ag['vc'] }}" style="font-size:11pt;">{{ $fmtN($agAmt) }} {{ $cur }}</div>
        <div class="kpi-unit">{{ $ag['dues']->count() }} {{ $tr('دفعة','payments') }} — {{ $agPct }}%</div>
    </td></tr>
    @endforeach
</table>

@if($outstandingAll->count())
<table class="tbl tbl-red" style="margin-top:8px;font-size:7.5pt;">
    <thead>
        <tr>
            <th>{{ $tr('المستأجر','Tenant') }}</th>
            <th>{{ $tr('الوحدة','Unit') }}</th>
            <th>{{ $tr('العقار','Property') }}</th>
            <th>{{ $tr('الشهر','Month') }}</th>
            <th>{{ $tr('المبلغ','Amount') }}</th>
            <th>{{ $tr('أيام التأخر','Days Late') }}</th>
            <th>{{ $tr('الحالة','Status') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($outstandingAll->sortByDesc('year')->sortByDesc('month') as $op)
        @php $due = \Carbon\Carbon::create($op->year, $op->month)->endOfMonth(); $daysLate = max(0,(int)$due->diffInDays(now(),false)); @endphp
        <tr class="{{ $daysLate>30?'overdue-row':'' }}">
            <td>{{ $op->tenant?->user?->name ?? '—' }}</td>
            <td class="bold">{{ $op->rentalContract?->unit?->unit_number ?? '—' }}</td>
            <td class="sm">{{ $isAr?($op->rentalContract?->unit?->property?->name_ar??'—'):($op->rentalContract?->unit?->property?->name_en??'—') }}</td>
            <td class="sm nowrap">{{ $op->monthName() }}/{{ $op->year }}</td>
            <td class="neg bold">{{ $fmtN($op->amount) }} {{ $cur }}</td>
            <td class="center {{ $daysLate>0?'neg':'' }}">{{ $daysLate > 0 ? $daysLate : '—' }}</td>
            <td><span class="bg {{ $op->status==='overdue'?'bg-red':'bg-amber' }}">{{ $op->statusLabel() }}</span></td>
        </tr>
        @endforeach
        <tr class="tr-total">
            <td colspan="4">{{ $tr('إجمالي المتأخرات','Total Outstanding') }}</td>
            <td>{{ $fmtN($agingTotal) }} {{ $cur }}</td>
            <td colspan="2">{{ $outstandingAll->count() }} {{ $tr('دفعة','payments') }}</td>
        </tr>
    </tbody>
</table>
@else
<p class="muted sm">{{ $tr('لا توجد أرصدة متأخرة — جيد!','No outstanding balances — great!') }}</p>
@endif
@endif {{-- outstanding --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     10. EXPENSE MANAGEMENT
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('expenses'))
<div class="sec-title">10. {{ $tr('تقرير المصروفات','Expense Management Report') }}</div>
@if($expenses->count())
@php $expByCat = $expenses->groupBy('category'); @endphp
<div class="sub-title">{{ $tr('توزيع المصروفات حسب الفئة','Expense Breakdown by Category') }}</div>
<table class="tbl tbl-amber">
    <thead>
        <tr>
            <th style="width:35%;">{{ $tr('الفئة','Category') }}</th>
            <th>{{ $tr('عدد البنود','Items') }}</th>
            <th>{{ $tr('الإجمالي','Total') }}</th>
            <th>{{ $tr('النسبة %','% of Total') }}</th>
            <th style="width:25%;">{{ $tr('التوزيع','Distribution') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expByCat->sortByDesc(fn($g)=>$g->sum('amount')) as $cat => $catExps)
        @php
            $catTotal=(float)$catExps->sum('amount');
            $catPct=$totalExpenses>0?round($catTotal/$totalExpenses*100,1):0;
            $catLabels=['utilities'=>$tr('مرافق','Utilities'),'maintenance'=>$tr('صيانة','Maintenance'),'salaries'=>$tr('رواتب','Salaries'),'marketing'=>$tr('تسويق','Marketing'),'taxes'=>$tr('ضرائب','Taxes'),'supplies'=>$tr('مستلزمات','Supplies'),'insurance'=>$tr('تأمين','Insurance'),'legal'=>$tr('قانوني','Legal'),'other'=>$tr('أخرى','Other')];
        @endphp
        <tr>
            <td class="bold">{{ $catLabels[$cat] ?? $cat }}</td>
            <td class="center">{{ $catExps->count() }}</td>
            <td class="neg bold">{{ $fmt($catTotal) }} {{ $cur }}</td>
            <td class="center">{{ $catPct }}%</td>
            <td><div class="pbar-wrap"><div class="pbar-fill pbar-amber" style="width:{{ min(100,$catPct) }}%;"></div></div></td>
        </tr>
        @endforeach
        <tr class="tr-total">
            <td>{{ $tr('الإجمالي','Total') }}</td>
            <td class="center">{{ $expenses->count() }}</td>
            <td>{{ $fmt($totalExpenses) }} {{ $cur }}</td>
            <td colspan="2">100%</td>
        </tr>
    </tbody>
</table>

<div class="sub-title">{{ $tr('تفاصيل المصروفات','Expense Details') }}</div>
<table class="tbl tbl-amber" style="font-size:7.5pt;">
    <thead>
        <tr>
            <th style="width:10%;">{{ $tr('التاريخ','Date') }}</th>
            <th style="width:25%;">{{ $tr('البيان','Title') }}</th>
            <th style="width:12%;">{{ $tr('الفئة','Category') }}</th>
            <th style="width:15%;">{{ $tr('العقار','Property') }}</th>
            <th>{{ $tr('المبلغ','Amount') }}</th>
            <th>{{ $tr('مدفوع بواسطة','Paid By') }}</th>
            <th>{{ $tr('ملاحظات','Notes') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenses->sortBy('expense_date') as $exp)
        @php $catLabels2=['utilities'=>$tr('مرافق','Utilities'),'maintenance'=>$tr('صيانة','Maintenance'),'salaries'=>$tr('رواتب','Salaries'),'marketing'=>$tr('تسويق','Marketing'),'taxes'=>$tr('ضرائب','Taxes'),'supplies'=>$tr('مستلزمات','Supplies'),'insurance'=>$tr('تأمين','Insurance'),'legal'=>$tr('قانوني','Legal'),'other'=>$tr('أخرى','Other')]; @endphp
        <tr>
            <td class="sm nowrap">{{ $exp->expense_date->format('Y/m/d') }}</td>
            <td class="bold">{{ $exp->title }}</td>
            <td>{{ $catLabels2[$exp->category] ?? $exp->category }}</td>
            <td class="sm">{{ $isAr?($data['properties']->firstWhere('id',$exp->expensable_id)?->name_ar??'—'):($data['properties']->firstWhere('id',$exp->expensable_id)?->name_en??'—') }}</td>
            <td class="neg bold nowrap">{{ $fmt($exp->amount) }} {{ $cur }}</td>
            <td class="sm">{{ $exp->paidByUser?->name ?? '—' }}</td>
            <td class="sm muted">{{ mb_substr($exp->description ?? '', 0, 45) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p class="muted sm">{{ $tr('لا توجد مصروفات في الفترة المحددة.','No expenses recorded in this period.') }}</p>
@endif
@endif {{-- expenses --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     11. PROFITABILITY ANALYSIS
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('profitability') && $properties->count() > 1)
<div class="sec-title">11. {{ $tr('تحليل الربحية','Profitability Analysis') }}</div>
<table class="tbl tbl-indigo">
    <thead>
        <tr>
            <th>{{ $tr('العقار','Property') }}</th>
            <th>{{ $tr('النوع','Type') }}</th>
            <th>{{ $tr('الإيرادات المحصّلة','Revenue') }}</th>
            <th>{{ $tr('المصروفات','Expenses') }}</th>
            <th>{{ $tr('صافي الربح','Net Profit') }}</th>
            <th>{{ $tr('هامش الربح %','Margin %') }}</th>
            <th style="width:18%;">{{ $tr('الأداء','Performance') }}</th>
        </tr>
    </thead>
    <tbody>
        @php $sortedProfit = collect($propProfit)->sortByDesc('net'); @endphp
        @foreach($sortedProfit as $pp)
        <tr>
            <td class="bold">{{ $isAr?($pp['property']->name_ar??$pp['property']->name_en):($pp['property']->name_en??$pp['property']->name_ar) }}</td>
            <td class="sm">{{ $pp['property']->typeLabel() }}</td>
            <td class="pos">{{ $fmtN($pp['collected']) }}</td>
            <td class="neg">{{ $fmtN($pp['expenses']) }}</td>
            <td class="{{ $pp['net']>=0?'pos':'neg' }}">{{ $fmtN($pp['net']) }}</td>
            <td class="{{ $pp['margin']>=0?'pos':'neg' }}">{{ $pp['margin'] }}%</td>
            <td><div class="pbar-wrap"><div class="pbar-fill {{ $pp['margin']>=20?'pbar-green':($pp['margin']>=0?'pbar-amber':'pbar-red') }}" style="width:{{ min(100,max(0,$pp['margin'])) }}%;"></div></div></td>
        </tr>
        @endforeach
        <tr class="tr-total">
            <td colspan="2">{{ $tr('الإجمالي','Total') }}</td>
            <td>{{ $fmtN($collectedRevenue) }}</td>
            <td>{{ $fmtN($totalExpenses) }}</td>
            <td>{{ $fmtN($netProfit) }}</td>
            <td>{{ $profitMargin }}%</td>
            <td></td>
        </tr>
    </tbody>
</table>
@endif {{-- profitability --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     12. MAINTENANCE
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('maintenance'))
<div class="sec-title">12. {{ $tr('تقرير الصيانة','Maintenance Report') }}</div>
@php
$mPending    = $maintenance->where('status','pending')->count();
$mInProgress = $maintenance->where('status','in_progress')->count();
$mCompleted  = $maintenance->where('status','completed')->count();
$mTotal      = $maintenance->count();
$mTotalCost  = (float)$maintenance->sum('external_worker_cost');
$mWithCost   = $maintenance->where('external_worker_cost', '>', 0);
$mAvgResol   = $maintenance->where('status','completed')->filter(fn($r)=>$r->resolved_at && $r->created_at)->average(fn($r)=>(int)$r->created_at->diffInDays($r->resolved_at));
@endphp
<table class="kpi-row">
    <tr>
        <td class="kpi kpi-gray"   style="width:18%;"><div class="kpi-lbl">{{ $tr('إجمالي الطلبات','Total') }}</div><div class="kpi-val v-gray">{{ $mTotal }}</div></td>
        <td width="4"></td>
        <td class="kpi kpi-amber"  style="width:18%;"><div class="kpi-lbl">{{ $tr('معلقة','Pending') }}</div><div class="kpi-val v-amber">{{ $mPending }}</div></td>
        <td width="4"></td>
        <td class="kpi kpi-blue"   style="width:18%;"><div class="kpi-lbl">{{ $tr('قيد التنفيذ','In Progress') }}</div><div class="kpi-val v-blue">{{ $mInProgress }}</div></td>
        <td width="4"></td>
        <td class="kpi kpi-green"  style="width:18%;"><div class="kpi-lbl">{{ $tr('مكتملة','Completed') }}</div><div class="kpi-val v-green">{{ $mCompleted }}</div></td>
        <td width="4"></td>
        <td class="kpi kpi-red"    style="width:18%;"><div class="kpi-lbl">{{ $tr('تكلفة الصيانة','Maint. Cost') }}</div><div class="kpi-val v-red">{{ $fmtN($mTotalCost) }}</div><div class="kpi-unit">{{ $cur }}</div></td>
    </tr>
</table>

@if($maintenance->count())
<table class="tbl tbl-gray" style="font-size:7.5pt;">
    <thead>
        <tr>
            <th style="width:6%;">#</th>
            <th style="width:14%;">{{ $tr('العقار','Property') }}</th>
            <th style="width:8%;">{{ $tr('الوحدة','Unit') }}</th>
            <th style="width:18%;">{{ $tr('العنوان','Title') }}</th>
            <th style="width:12%;">{{ $tr('المبلّغ','Reported By') }}</th>
            <th style="width:8%;">{{ $tr('الأولوية','Priority') }}</th>
            <th style="width:9%;">{{ $tr('الحالة','Status') }}</th>
            <th style="width:9%;">{{ $tr('التاريخ','Date') }}</th>
            <th style="width:9%;">{{ $tr('التكلفة','Cost') }}</th>
            <th>{{ $tr('الفني','Tech.') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($maintenance as $mr)
        @php
            $priBg = match($mr->priority??'medium'){'urgent'=>'bg-red','high'=>'bg-amber','medium'=>'bg-blue',default=>'bg-gray'};
            $stsBg = match($mr->status){'completed'=>'bg-green','in_progress'=>'bg-blue','rejected'=>'bg-red',default=>'bg-amber'};
        @endphp
        <tr>
            <td class="muted xs">{{ $mr->id }}</td>
            <td class="sm">{{ $isAr?($mr->unit?->property?->name_ar??'—'):($mr->unit?->property?->name_en??'—') }}</td>
            <td class="bold">{{ $mr->unit?->unit_number ?? '—' }}</td>
            <td>{{ $mr->title }}</td>
            <td class="sm">{{ $mr->tenant?->user?->name ?? '—' }}</td>
            <td><span class="bg {{ $priBg }} xs">{{ $mr->priorityLabel() }}</span></td>
            <td><span class="bg {{ $stsBg }} xs">{{ $mr->statusLabel() }}</span></td>
            <td class="sm">{{ $mr->created_at->format('Y/m/d') }}</td>
            <td class="{{ $mr->external_worker_cost?'neg':'' }}">{{ $mr->external_worker_cost?$fmtN($mr->external_worker_cost):'—' }}</td>
            <td class="sm">{{ $mr->assignedEmployee?->name ?? ($mr->external_worker_name ?? '—') }}</td>
        </tr>
        @endforeach
        @if($mTotalCost > 0)
        <tr class="tr-total">
            <td colspan="8">{{ $tr('إجمالي تكلفة الصيانة','Total Maintenance Cost') }}</td>
            <td>{{ $fmtN($mTotalCost) }} {{ $cur }}</td>
            <td></td>
        </tr>
        @endif
    </tbody>
</table>
@endif
@endif {{-- maintenance --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     13. VACANCY REPORT
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('vacancy'))
<div class="sec-title">13. {{ $tr('تقرير الوحدات الشاغرة','Vacancy Report') }}</div>
@if($vacantList->count())
<table class="tbl tbl-amber">
    <thead>
        <tr>
            <th>{{ $tr('الوحدة','Unit') }}</th>
            <th>{{ $tr('العقار','Property') }}</th>
            <th>{{ $tr('النوع','Type') }}</th>
            <th>{{ $tr('المساحة','Area') }}</th>
            <th>{{ $tr('الإيجار الشهري','Listed Rent') }}</th>
            <th>{{ $tr('شاغر منذ','Vacant Since') }}</th>
            <th>{{ $tr('أيام الشغور','Days Vacant') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vacantList as $vr)
        @php $u=$vr['unit']; $dv=$vr['days_vacant']; @endphp
        <tr class="{{ $dv && $dv > 60 ? 'overdue-row' : '' }}">
            <td class="bold">{{ $u->unit_number ?? '—' }}</td>
            <td class="sm">{{ $isAr?($u->property?->name_ar??'—'):($u->property?->name_en??'—') }}</td>
            <td>{{ $u->typeLabel() }}</td>
            <td>{{ $u->area ? number_format($u->area).' م²' : '—' }}</td>
            <td>{{ $u->rent_price ? $fmtN($u->rent_price) : '—' }}</td>
            <td class="sm">{{ $vr['vacant_since'] ? (is_string($vr['vacant_since']) ? $vr['vacant_since'] : $vr['vacant_since']->format('Y/m/d')) : '—' }}</td>
            <td class="center bold {{ $dv && $dv > 60 ? 'neg' : ($dv && $dv > 30 ? 'v-amber' : '') }}">{{ $dv ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p class="muted sm">{{ $tr('لا توجد وحدات شاغرة — جميع الوحدات مشغولة!','No vacant units — all units are occupied!') }}</p>
@endif
@endif {{-- vacancy --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     14. UPCOMING ALERTS
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('alerts') && count($alerts))
<div class="sec-title">14. {{ $tr('التنبيهات والأنشطة القادمة','Upcoming Activities & Alerts') }}</div>
@foreach($alerts as $alert)
<div class="alert-{{ $alert['priority'] }}">
    <span class="bold sm">{{ $isAr ? $alert['label_ar'] : $alert['label_en'] }}:</span>
    <span class="sm">{{ $alert['detail'] }}</span>
    @if($alert['date']) <span class="xs muted">— {{ is_string($alert['date']) ? $alert['date'] : $alert['date']->format('Y/m/d') }}</span> @endif
</div>
@endforeach
@elseif($has('alerts'))
<div class="sec-title">14. {{ $tr('التنبيهات والأنشطة القادمة','Upcoming Activities & Alerts') }}</div>
<p class="muted sm">{{ $tr('لا توجد تنبيهات نشطة.','No active alerts.') }}</p>
@endif {{-- alerts --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     15. COMMISSION INVOICES
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('commission_invoices'))
<div class="sec-title">15. {{ $tr('فواتير العمولة', 'Commission Invoices') }}</div>
@if($commissionInvoices->count())
<table class="kpi-row" style="margin-bottom:8px;">
    <tr>
        <td class="kpi kpi-teal" style="width:30%;"><div class="kpi-lbl">{{ $tr('إجمالي الفواتير', 'Total Invoices') }}</div><div class="kpi-val v-teal">{{ $commissionInvoices->count() }}</div></td>
        <td width="4"></td>
        <td class="kpi kpi-green" style="width:30%;"><div class="kpi-lbl">{{ $tr('إجمالي العمولات', 'Total Commission') }}</div><div class="kpi-val v-green">{{ number_format($totalCommissions, 3) }}</div><div class="kpi-unit">OMR</div></td>
        <td width="4"></td>
        <td class="kpi kpi-blue" style="width:30%;"><div class="kpi-lbl">{{ $tr('فواتير المالك', 'Owner Invoices') }}</div><div class="kpi-val v-blue">{{ $commissionInvoices->where('invoice_for','owner')->count() }}</div></td>
        <td width="4"></td>
        <td class="kpi kpi-indigo" style="width:30%;"><div class="kpi-lbl">{{ $tr('فواتير العميل', 'Client Invoices') }}</div><div class="kpi-val v-indigo">{{ $commissionInvoices->where('invoice_for','client')->count() }}</div></td>
    </tr>
</table>
<table class="tbl tbl-teal" style="font-size:7.5pt;">
    <thead>
        <tr>
            <th style="width:18%;">{{ $tr('رقم الفاتورة', 'Invoice No.') }}</th>
            <th style="width:10%;">{{ $tr('التاريخ', 'Date') }}</th>
            <th style="width:14%;">{{ $tr('العقار', 'Property') }}</th>
            <th style="width:8%;">{{ $tr('موجهة إلى', 'For') }}</th>
            <th style="width:16%;">{{ $tr('المستلم', 'Recipient') }}</th>
            <th style="width:8%;">{{ $tr('المدة (شهر)', 'Months') }}</th>
            <th style="width:10%;">{{ $tr('الإيجار الشهري', 'Monthly') }}</th>
            <th style="width:8%;">{{ $tr('النسبة %', 'Rate %') }}</th>
            <th style="width:12%;">{{ $tr('مبلغ العمولة', 'Commission') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($commissionInvoices as $cinv)
        <tr>
            <td class="bold xs">{{ $cinv->invoice_number }}</td>
            <td class="sm nowrap">{{ $cinv->invoice_date->format('Y/m/d') }}</td>
            <td class="sm">{{ $isAr ? ($cinv->property->name_ar ?? $cinv->property->name_en) : ($cinv->property->name_en ?? $cinv->property->name_ar) }}</td>
            <td>
                <span class="bg {{ $cinv->invoice_for === 'owner' ? 'bg-blue' : 'bg-green' }}">
                    {{ $cinv->invoice_for === 'owner' ? $tr('المالك','Owner') : $tr('العميل','Client') }}
                </span>
            </td>
            <td>{{ $cinv->recipient_name }}</td>
            <td class="center">{{ $cinv->duration_months }}</td>
            <td>{{ number_format($cinv->monthly_rent, 3) }}</td>
            <td class="center">{{ $cinv->commission_rate }}%</td>
            <td class="bold pos">{{ number_format($cinv->commission_amount, 3) }}</td>
        </tr>
        @endforeach
        <tr class="tr-total">
            <td colspan="8">{{ $tr('إجمالي العمولات', 'Total Commissions') }} ({{ $commissionInvoices->count() }} {{ $tr('فاتورة','invoices') }})</td>
            <td>{{ number_format($totalCommissions, 3) }}</td>
        </tr>
    </tbody>
</table>
@else
<p class="muted sm">{{ $tr('لا توجد فواتير عمولة في الفترة المحددة.', 'No commission invoices in this period.') }}</p>
@endif
@endif {{-- commission_invoices --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     16. ATTACHMENTS
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('attachments'))
<div class="sec-title">16. {{ $tr('المرفقات والوثائق','Attachments & Supporting Documents') }}</div>
@php
$contractsWithFiles   = $allContracts->filter(fn($c) => $c->contract_file);
$expensesWithInvoices = $expenses->filter(fn($e) => $e->invoices->count() || $e->receipt_path);
$commInvWithFiles     = $commissionInvoices->filter(fn($i) => $i->file_path);
@endphp
<table class="info-grid">
    <tr>
        <td class="lbl">{{ $tr('عقود الإيجار مع ملفات','Lease Contracts with Files') }}</td>
        <td class="bold">{{ $contractsWithFiles->count() }}</td>
        <td class="lbl">{{ $tr('فواتير المصروفات','Expense Invoices') }}</td>
        <td class="bold">{{ $expensesWithInvoices->count() }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('فواتير العمولة المرفقة','Commission Invoice PDFs') }}</td>
        <td class="bold pos">{{ $commInvWithFiles->count() }}</td>
        <td class="lbl"></td>
        <td></td>
    </tr>
</table>

@if($contractsWithFiles->count())
<div class="sub-title">{{ $tr('عقود الإيجار المرفقة','Attached Lease Contracts') }}</div>
<table class="tbl">
    <thead><tr>
        <th>{{ $tr('المستأجر','Tenant') }}</th>
        <th>{{ $tr('الوحدة','Unit') }}</th>
        <th>{{ $tr('العقار','Property') }}</th>
        <th>{{ $tr('الملف','File') }}</th>
    </tr></thead>
    <tbody>
        @foreach($contractsWithFiles as $c)
        <tr>
            <td>{{ $c->tenant?->user?->name ?? '—' }}</td>
            <td class="bold">{{ $c->unit?->unit_number ?? '—' }}</td>
            <td class="sm">{{ $isAr?($c->unit?->property?->name_ar??'—'):($c->unit?->property?->name_en??'—') }}</td>
            <td class="sm muted">{{ basename($c->contract_file) }} <span class="bg bg-green xs">{{ $tr('مرفق','Attached') }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@if($commInvWithFiles->count())
<div class="sub-title">{{ $tr('فواتير العمولة المرفقة','Attached Commission Invoices') }}</div>
<table class="tbl">
    <thead><tr>
        <th>{{ $tr('رقم الفاتورة','Invoice No.') }}</th>
        <th>{{ $tr('التاريخ','Date') }}</th>
        <th>{{ $tr('العقار','Property') }}</th>
        <th>{{ $tr('المستلم','Recipient') }}</th>
        <th>{{ $tr('مبلغ العمولة','Commission') }}</th>
        <th>{{ $tr('الملف','File') }}</th>
    </tr></thead>
    <tbody>
        @foreach($commInvWithFiles as $ci)
        <tr>
            <td class="bold xs">{{ $ci->invoice_number }}</td>
            <td class="sm">{{ $ci->invoice_date->format('Y/m/d') }}</td>
            <td class="sm">{{ $isAr?($ci->property->name_ar??$ci->property->name_en):($ci->property->name_en??$ci->property->name_ar) }}</td>
            <td>{{ $ci->recipient_name }}</td>
            <td class="pos bold">{{ number_format($ci->commission_amount, 3) }} OMR</td>
            <td class="sm muted">{{ basename($ci->file_path) }} <span class="bg bg-green xs">{{ $tr('مرفق','Attached') }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endif {{-- attachments --}}


{{-- FOOTER --}}
<div style="margin-top:24px; border-top:2px solid #312e81; padding-top:10px;">
    <table width="100%" border="0" cellspacing="0">
        <tr>
            <td style="font-size:7.5pt; color:#312e81; font-weight:bold;">{{ $tr('شركة ثروة للتطوير العقاري','Tharwa Real Estate') }}</td>
            <td style="text-align:center; font-size:7pt; color:#9ca3af;">{{ $reportTitle }}</td>
            <td style="text-align:{{ $isAr?'left':'right' }}; font-size:7pt; color:#9ca3af;">{{ now()->format('Y/m/d H:i') }}</td>
        </tr>
    </table>
</div>

</body>
</html>
