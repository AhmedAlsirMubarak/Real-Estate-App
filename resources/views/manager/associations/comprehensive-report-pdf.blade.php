@php
$isAr    = $locale === 'ar';
$tr      = fn($ar,$en) => $isAr ? $ar : $en;
$cur     = 'OMR';
$fmt     = fn($n) => number_format((float)$n, 3);
$fmtN    = fn($n) => number_format((float)$n);
$has     = fn(string $s) => in_array($s, $sections);
$isMulti = $data['isMulti'] ?? false;
$allData = $data['associations_data'];
$agg     = $data['aggregate'];
@endphp
<!DOCTYPE html>
<html lang="{{ $isAr ? 'ar' : 'en' }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
<head>
<meta charset="utf-8">
<title>{{ $tr('تقرير جمعية الملاك','HOA Comprehensive Report') }} — {{ $reportTitle }}</title>
<style>
@page { margin: 18mm 10mm 18mm; }
body  { font-family: dejavusans, sans-serif; font-size: 9pt; color: #1e293b; direction: {{ $isAr ? 'rtl' : 'ltr' }}; margin:0; padding:0; }

/* Cover / Header */
.cover         { background: linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 60%,#0ea5e9 100%); color:#fff; padding:32px 24px 24px; margin:-18mm -10mm 0; text-align:center; }
.cover-logo    { max-height:60px; margin-bottom:12px; }
.cover-title   { font-size:18pt; font-weight:bold; margin:0 0 6px; }
.cover-sub     { font-size:10pt; opacity:.85; margin:0 0 18px; }
.cover-meta    { display:inline-block; background:rgba(255,255,255,.15); border-radius:10px; padding:10px 22px; font-size:8.5pt; }
.cover-meta td { padding:3px 12px; text-align:{{ $isAr ? 'right' : 'left' }}; }
.cover-lbl     { opacity:.75; width:120px; }
.cover-val     { font-weight:bold; }
.cover-divider { height:4px; background: linear-gradient(90deg,#0ea5e9,#6366f1,#10b981); margin:0 -10mm; }

/* Section headers */
.sec-title   { font-size:12pt; font-weight:bold; color:#fff; background:#1e3a8a; padding:7px 12px; margin:18px -10mm 10px; }
.sec-title span { font-size:9pt; opacity:.75; font-weight:normal; margin-{{ $isAr?'right':'left' }}:8px; }
.sub-title   { font-size:9.5pt; font-weight:bold; color:#1e40af; border-{{ $isAr?'right':'left' }}:4px solid #1e3a8a; padding:5px 9px; background:#eff6ff; margin:12px 0 7px; }

/* KPI cards */
.kpi-row  { width:100%; border-collapse:collapse; margin:10px 0; }
.kpi      { padding:10px 7px; text-align:center; border:1px solid #e2e8f0; border-radius:6px; vertical-align:top; }
.kpi-lbl  { font-size:7pt; color:#64748b; margin-bottom:5px; line-height:1.3; }
.kpi-val  { font-size:14pt; font-weight:bold; margin:2px 0; }
.kpi-unit { font-size:7pt; color:#94a3b8; margin-top:2px; }
.kpi-blue  { background:#eff6ff; border-color:#bfdbfe; }
.kpi-green { background:#f0fdf4; border-color:#bbf7d0; }
.kpi-red   { background:#fff1f2; border-color:#fecdd3; }
.kpi-amber { background:#fffbeb; border-color:#fde68a; }
.kpi-gray  { background:#f9fafb; border-color:#e5e7eb; }
.kpi-teal  { background:#f0fdfa; border-color:#99f6e4; }
.kpi-violet{ background:#f5f3ff; border-color:#ddd6fe; }
.v-blue    { color:#1d4ed8; } .v-green { color:#15803d; } .v-red { color:#b91c1c; }
.v-amber   { color:#b45309; } .v-gray  { color:#374151; } .v-teal { color:#0f766e; }
.v-violet  { color:#7c3aed; }

/* Progress bar */
.pbar-wrap { background:#e2e8f0; border-radius:99px; height:8px; width:100%; overflow:hidden; }
.pbar-fill { height:8px; border-radius:99px; }
.pbar-green { background:#22c55e; }
.pbar-amber { background:#f59e0b; }
.pbar-red   { background:#ef4444; }

/* Tables */
.tbl             { width:100%; border-collapse:collapse; margin:8px 0 12px; font-size:8pt; }
.tbl thead th    { background:#1e3a8a; color:#fff; padding:6px 7px; text-align:{{ $isAr?'right':'left' }}; font-weight:bold; font-size:7.5pt; }
.tbl tbody td    { padding:5px 7px; border-bottom:1px solid #f1f5f9; vertical-align:top; }
.tbl tbody tr:nth-child(even) td { background:#f8fafc; }
.tbl .tr-sub     { background:#eff6ff !important; font-weight:bold; }
.tbl .tr-total   { background:#1e3a8a !important; color:#fff !important; font-weight:bold; }
.tbl .tr-total td{ color:#fff !important; border-bottom:none; }
.tbl-teal thead th  { background:#0f766e; }
.tbl-green thead th { background:#15803d; }
.tbl-amber thead th { background:#92400e; }
.tbl-red   thead th { background:#9f1239; }
.tbl-gray  thead th { background:#374151; }
.tbl-violet thead th{ background:#6d28d9; }

/* Badges */
.bg       { display:inline-block; padding:1px 7px; border-radius:6px; font-size:7pt; white-space:nowrap; }
.bg-green  { background:#dcfce7; color:#166534; }
.bg-blue   { background:#dbeafe; color:#1d4ed8; }
.bg-amber  { background:#fef9c3; color:#92400e; }
.bg-red    { background:#fee2e2; color:#b91c1c; }
.bg-gray   { background:#f3f4f6; color:#374151; }
.bg-teal   { background:#ccfbf1; color:#0f766e; }
.bg-violet { background:#ede9fe; color:#6d28d9; }

/* Info grid */
.info-grid        { width:100%; border-collapse:collapse; margin:8px 0; }
.info-grid td     { padding:5px 8px; border:1px solid #e2e8f0; font-size:8.5pt; }
.info-grid .lbl   { background:#f8fafc; font-weight:bold; color:#475569; width:38%; }
.info-grid .val   { color:#1e293b; }

/* Utilities */
.pos  { color:#15803d; font-weight:bold; }
.neg  { color:#b91c1c; font-weight:bold; }
.muted{ color:#9ca3af; }
.bold { font-weight:bold; }
.sm   { font-size:7.5pt; }
.xs   { font-size:7pt; }
.right{ text-align:{{ $isAr?'left':'right' }}; }
.center { text-align:center; }
.nowrap { white-space:nowrap; }
.overdue-row td { background:#fff1f2 !important; }
.page-break { page-break-before:always; }

/* Trend bar chart (ASCII-style using table) */
.chart-wrap  { margin:10px 0 16px; }
.chart-bar   { display:inline-block; min-width:2px; }
</style>
</head>
<body>

{{-- ══════════════════════════════════════════════════════════════════════════
     COVER PAGE
══════════════════════════════════════════════════════════════════════════ --}}
<div class="cover">
    @if(file_exists(public_path('img/logo.png')))
    <img src="{{ public_path('img/logo.png') }}" class="cover-logo" alt="logo">
    @endif
    <div class="cover-title">
        {{ $tr('التقرير الشامل لجمعية الملاك','Comprehensive Owners Association Report') }}
    </div>
    <div class="cover-sub">{{ $reportTitle }}</div>
    <table class="cover-meta" cellspacing="0">
        @if($isMulti)
        <tr>
            <td class="cover-lbl">{{ $tr('عدد الجمعيات','Associations') }}</td>
            <td class="cover-val">{{ $agg['count'] }} {{ $tr('جمعية','associations') }}</td>
        </tr>
        <tr>
            <td class="cover-lbl">{{ $tr('إجمالي الوحدات','Total Units') }}</td>
            <td class="cover-val">{{ $agg['totalUnits'] }}</td>
        </tr>
        @else
        @php $singleAssoc = $allData[0]['association']; @endphp
        <tr>
            <td class="cover-lbl">{{ $tr('العقار','Property') }}</td>
            <td class="cover-val">{{ $allData[0]['property']?->name ?? '—' }}</td>
        </tr>
        <tr>
            <td class="cover-lbl">{{ $tr('الحالة','Status') }}</td>
            <td class="cover-val">{{ $singleAssoc->status === 'active' ? $tr('نشطة','Active') : $tr('غير نشطة','Inactive') }}</td>
        </tr>
        @endif
        <tr>
            <td class="cover-lbl">{{ $tr('فترة التقرير','Report Period') }}</td>
            <td class="cover-val">{{ $from->format('Y/m/d') }} — {{ $to->format('Y/m/d') }}</td>
        </tr>
        <tr>
            <td class="cover-lbl">{{ $tr('تاريخ الإصدار','Issued On') }}</td>
            <td class="cover-val">{{ now()->format('Y/m/d H:i') }}</td>
        </tr>
    </table>
</div>
<div class="cover-divider"></div>

{{-- ══ MULTI-ASSOCIATION: GLOBAL AGGREGATE SUMMARY ══ --}}
@if($isMulti)
@php
$aggCollColor = $agg['collectionRate'] >= 90 ? 'kpi-green' : ($agg['collectionRate'] >= 70 ? 'kpi-amber' : 'kpi-red');
$aggCollVColor= $agg['collectionRate'] >= 90 ? 'v-green'   : ($agg['collectionRate'] >= 70 ? 'v-amber'   : 'v-red');
@endphp
<div class="sec-title">{{ $tr('الملخص الموحد لجميع الجمعيات','Combined Summary — All Associations') }}</div>
<table class="kpi-row">
<tr>
    <td class="kpi kpi-gray" style="width:15%;"><div class="kpi-lbl">{{ $tr('عدد الجمعيات','Associations') }}</div><div class="kpi-val v-gray">{{ $agg['count'] }}</div></td>
    <td width="4"></td>
    <td class="kpi kpi-gray" style="width:15%;"><div class="kpi-lbl">{{ $tr('إجمالي الوحدات','Total Units') }}</div><div class="kpi-val v-gray">{{ $agg['totalUnits'] }}</div></td>
    <td width="4"></td>
    <td class="kpi kpi-blue" style="width:15%;"><div class="kpi-lbl">{{ $tr('إجمالي الاشتراكات','Total Dues') }}</div><div class="kpi-val v-blue">{{ number_format($agg['totalDues']) }}</div><div class="kpi-unit">{{ $cur }}</div></td>
    <td width="4"></td>
    <td class="kpi kpi-green" style="width:15%;"><div class="kpi-lbl">{{ $tr('المحصّل','Collected') }}</div><div class="kpi-val v-green">{{ number_format($agg['paidDues']) }}</div><div class="kpi-unit">{{ $cur }}</div></td>
    <td width="4"></td>
    <td class="kpi kpi-red" style="width:15%;"><div class="kpi-lbl">{{ $tr('غير محصّل','Outstanding') }}</div><div class="kpi-val v-red">{{ number_format($agg['unpaidDues']) }}</div><div class="kpi-unit">{{ $cur }}</div></td>
    <td width="4"></td>
    <td class="kpi {{ $aggCollColor }}" style="width:15%;"><div class="kpi-lbl">{{ $tr('نسبة التحصيل','Collection Rate') }}</div><div class="kpi-val {{ $aggCollVColor }}">{{ $agg['collectionRate'] }}%</div><div class="kpi-unit"><div class="pbar-wrap" style="margin-top:4px;"><div class="pbar-fill {{ $agg['collectionRate']>=90?'pbar-green':($agg['collectionRate']>=70?'pbar-amber':'pbar-red') }}" style="width:{{ min(100,$agg['collectionRate']) }}%;"></div></div></div></td>
</tr>
</table>
<table class="kpi-row" style="margin-top:6px;">
<tr>
    <td class="kpi kpi-amber" style="width:30%;"><div class="kpi-lbl">{{ $tr('إجمالي المصروفات','Total Expenses') }}</div><div class="kpi-val v-amber">{{ number_format($agg['totalExpenses']) }}</div><div class="kpi-unit">{{ $cur }}</div></td>
    <td width="4"></td>
    <td class="kpi {{ $agg['netBalance']>=0?'kpi-green':'kpi-red' }}" style="width:30%;"><div class="kpi-lbl">{{ $tr('صافي الرصيد','Net Balance') }}</div><div class="kpi-val {{ $agg['netBalance']>=0?'v-green':'v-red' }}">{{ number_format($agg['netBalance']) }}</div><div class="kpi-unit">{{ $cur }} — {{ $agg['netBalance']>=0?$tr('فائض','Surplus'):$tr('عجز','Deficit') }}</div></td>
    <td width="4"></td>
    <td class="kpi kpi-gray" style="width:30%;"><div class="kpi-lbl">{{ $tr('إجمالي الملاك','Total Owners') }}</div><div class="kpi-val v-gray">{{ $agg['ownersCount'] }}</div></td>
</tr>
</table>

{{-- Per-association quick overview table --}}
<div class="sub-title">{{ $tr('مؤشرات كل جمعية','Per-Association Overview') }}</div>
<table class="tbl tbl-teal">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ $tr('الجمعية','Association') }}</th>
            <th>{{ $tr('العقار','Property') }}</th>
            <th>{{ $tr('الوحدات','Units') }}</th>
            <th>{{ $tr('الملاك','Owners') }}</th>
            <th>{{ $tr('الاشتراكات','Dues') }}</th>
            <th>{{ $tr('المحصّل','Collected') }}</th>
            <th>{{ $tr('غير محصّل','Outstanding') }}</th>
            <th>{{ $tr('نسبة التحصيل','Coll. %') }}</th>
            <th>{{ $tr('المصروفات','Expenses') }}</th>
            <th>{{ $tr('الصافي','Net') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($allData as $i => $ad)
        <tr>
            <td class="muted xs">{{ $i+1 }}</td>
            <td class="bold">{{ $isAr ? ($ad['association']->name_ar ?? $ad['association']->name_en) : ($ad['association']->name_en ?? $ad['association']->name_ar) }}</td>
            <td class="sm">{{ $ad['property']?->name ?? '—' }}</td>
            <td class="center">{{ $ad['totalUnits'] }}</td>
            <td class="center">{{ $ad['owners']->count() }}</td>
            <td>{{ number_format($ad['totalDues']) }}</td>
            <td class="pos">{{ number_format($ad['paidDues']) }}</td>
            <td class="{{ $ad['unpaidDues']>0?'neg':'muted' }}">{{ number_format($ad['unpaidDues']) }}</td>
            <td class="center {{ $ad['collectionRate']>=90?'pos':($ad['collectionRate']>=70?'v-amber':'neg') }}">{{ $ad['collectionRate'] }}%</td>
            <td class="neg">{{ number_format($ad['totalExpenses']) }}</td>
            <td class="{{ $ad['netBalance']>=0?'pos':'neg' }}">{{ number_format($ad['netBalance']) }}</td>
        </tr>
        @endforeach
        <tr class="tr-total">
            <td colspan="5">{{ $tr('الإجمالي','Total') }}</td>
            <td>{{ number_format($agg['totalDues']) }}</td>
            <td>{{ number_format($agg['paidDues']) }}</td>
            <td>{{ number_format($agg['unpaidDues']) }}</td>
            <td>{{ $agg['collectionRate'] }}%</td>
            <td>{{ number_format($agg['totalExpenses']) }}</td>
            <td>{{ number_format($agg['netBalance']) }}</td>
        </tr>
    </tbody>
</table>
@endif

{{-- ══════════════════════════════════════════════════════════════════════════
     PER-ASSOCIATION LOOP — Sections 1–12
══════════════════════════════════════════════════════════════════════════ --}}
@foreach($allData as $loopData)
@php
// Unpack per-association data for sections below
$assoc          = $loopData['association'];
$property       = $loopData['property'];
$units          = $loopData['units'];
$owners         = $loopData['owners'];
$totalUnits     = $loopData['totalUnits'];
$occupiedUnits  = $loopData['occupiedUnits'];
$vacantUnits    = $loopData['vacantUnits'];
$dues           = $loopData['dues'];
$allDues        = $loopData['allDues'];
$allOutstanding = $loopData['allOutstanding'];
$expenses       = $loopData['expenses'];
$expByCat       = $loopData['expensesByCategory'];
$maintenance    = $loopData['maintenance'];
$meetings       = $loopData['meetings'];
$totalDues      = $loopData['totalDues'];
$paidDues       = $loopData['paidDues'];
$unpaidDues     = $loopData['unpaidDues'];
$waivedDues     = $loopData['waivedDues'];
$overdueDues    = $loopData['overdueDues'];
$totalExpenses  = $loopData['totalExpenses'];
$netBalance     = $loopData['netBalance'];
$collRate       = $loopData['collectionRate'];
$aging          = $loopData['aging'];
$ownerStmts     = $loopData['ownerStatements'];
$unitFeeMap     = $loopData['unitFeeMap'];
$trends         = $loopData['monthlyTrends'];
$ownersCount    = $owners->count();
$tenantsCount   = $units->filter(fn($u) => $u->activeRentalContract)->count();
$assocName      = $isAr ? ($assoc->name_ar ?? $assoc->name_en ?? '—') : ($assoc->name_en ?? $assoc->name_ar ?? '—');
$propName       = $property?->name ?? '—';
@endphp

{{-- Association heading (shown in multi mode only) --}}
@if($isMulti)
<div style="background:#0f766e; color:#fff; padding:8px 12px; margin:0 -10mm 10px; font-size:11pt; font-weight:bold;">
    {{ $loop->iteration }}. {{ $assocName }}
    @if($property) &nbsp;&bull;&nbsp; {{ $propName }} @endif
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════════════
     SECTION 1: EXECUTIVE SUMMARY
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('executive_summary'))
<div class="sec-title">1. {{ $tr('الملخص التنفيذي','Executive Summary') }}</div>

@php
$unitFee    = (float) $assoc->monthly_fee_per_unit;
$expectedMo = $unitFee * $totalUnits;
$collRateColor = $collRate >= 90 ? 'kpi-green' : ($collRate >= 70 ? 'kpi-amber' : 'kpi-red');
$collRateVColor= $collRate >= 90 ? 'v-green'   : ($collRate >= 70 ? 'v-amber'   : 'v-red');
@endphp

<table class="kpi-row">
<tr>
    <td class="kpi kpi-gray" style="width:14%;">
        <div class="kpi-lbl">{{ $tr('إجمالي الوحدات','Total Units') }}</div>
        <div class="kpi-val v-gray">{{ $totalUnits }}</div>
        <div class="kpi-unit">{{ $tr('وحدة','units') }}</div>
    </td>
    <td width="4"></td>
    <td class="kpi kpi-teal" style="width:14%;">
        <div class="kpi-lbl">{{ $tr('الوحدات المشغولة','Occupied') }}</div>
        <div class="kpi-val v-teal">{{ $occupiedUnits }}</div>
        <div class="kpi-unit">{{ $totalUnits>0 ? round($occupiedUnits/$totalUnits*100).'%' : '—' }}</div>
    </td>
    <td width="4"></td>
    <td class="kpi kpi-amber" style="width:14%;">
        <div class="kpi-lbl">{{ $tr('الوحدات الشاغرة','Vacant') }}</div>
        <div class="kpi-val v-amber">{{ $vacantUnits }}</div>
        <div class="kpi-unit">{{ $tr('وحدة','units') }}</div>
    </td>
    <td width="4"></td>
    <td class="kpi kpi-gray" style="width:14%;">
        <div class="kpi-lbl">{{ $tr('عدد الملاك','Owners') }}</div>
        <div class="kpi-val v-gray">{{ $ownersCount }}</div>
        <div class="kpi-unit">{{ $tr('مالك','owners') }}</div>
    </td>
    <td width="4"></td>
    <td class="kpi kpi-blue" style="width:14%;">
        <div class="kpi-lbl">{{ $tr('الرسوم الشهرية/وحدة','Monthly Fee/Unit') }}</div>
        <div class="kpi-val v-blue">{{ $fmtN($unitFee) }}</div>
        <div class="kpi-unit">{{ $cur }}</div>
    </td>
    <td width="4"></td>
    <td class="kpi kpi-violet" style="width:14%;">
        <div class="kpi-lbl">{{ $tr('عدد المستأجرين','Tenants') }}</div>
        <div class="kpi-val v-violet">{{ $tenantsCount }}</div>
        <div class="kpi-unit">{{ $tr('مستأجر','tenants') }}</div>
    </td>
</tr>
</table>

<table class="kpi-row" style="margin-top:6px;">
<tr>
    <td class="kpi kpi-blue" style="width:18%;">
        <div class="kpi-lbl">{{ $tr('إجمالي الاشتراكات المستحقة','Total Dues') }}</div>
        <div class="kpi-val v-blue">{{ $fmtN($totalDues) }}</div>
        <div class="kpi-unit">{{ $cur }}</div>
    </td>
    <td width="4"></td>
    <td class="kpi kpi-green" style="width:18%;">
        <div class="kpi-lbl">{{ $tr('المحصّل','Collected') }}</div>
        <div class="kpi-val v-green">{{ $fmtN($paidDues) }}</div>
        <div class="kpi-unit">{{ $cur }}</div>
    </td>
    <td width="4"></td>
    <td class="kpi kpi-red" style="width:18%;">
        <div class="kpi-lbl">{{ $tr('غير محصّل','Outstanding') }}</div>
        <div class="kpi-val v-red">{{ $fmtN($unpaidDues) }}</div>
        <div class="kpi-unit">{{ $cur }}</div>
    </td>
    <td width="4"></td>
    <td class="kpi {{ $collRateColor }}" style="width:18%;">
        <div class="kpi-lbl">{{ $tr('نسبة التحصيل','Collection Rate') }}</div>
        <div class="kpi-val {{ $collRateVColor }}">{{ $collRate }}%</div>
        <div class="kpi-unit">
            <div class="pbar-wrap" style="margin-top:4px;">
                <div class="pbar-fill {{ $collRate>=90?'pbar-green':($collRate>=70?'pbar-amber':'pbar-red') }}"
                     style="width:{{ min(100,$collRate) }}%;"></div>
            </div>
        </div>
    </td>
    <td width="4"></td>
    <td class="kpi kpi-amber" style="width:18%;">
        <div class="kpi-lbl">{{ $tr('إجمالي المصروفات','Total Expenses') }}</div>
        <div class="kpi-val v-amber">{{ $fmtN($totalExpenses) }}</div>
        <div class="kpi-unit">{{ $cur }}</div>
    </td>
    <td width="4"></td>
    <td class="kpi {{ $netBalance>=0?'kpi-green':'kpi-red' }}" style="width:18%;">
        <div class="kpi-lbl">{{ $tr('صافي الفائض/العجز','Net Surplus/Deficit') }}</div>
        <div class="kpi-val {{ $netBalance>=0?'v-green':'v-red' }}">{{ $fmtN($netBalance) }}</div>
        <div class="kpi-unit">{{ $cur }} — {{ $netBalance>=0?$tr('فائض','Surplus'):$tr('عجز','Deficit') }}</div>
    </td>
</tr>
</table>

{{-- Monthly trend mini-table --}}
@if(count($trends) > 1)
<div class="sub-title">{{ $tr('الاتجاه الشهري','Monthly Trend') }}</div>
<table class="tbl tbl-teal">
    <thead>
        <tr>
            <th>{{ $tr('الشهر','Month') }}</th>
            <th>{{ $tr('المحصّل','Collected') }}</th>
            <th>{{ $tr('المصروفات','Expenses') }}</th>
            <th>{{ $tr('غير محصّل','Outstanding') }}</th>
            <th>{{ $tr('الصافي','Net') }}</th>
            <th>{{ $tr('نسبة التحصيل','Coll. Rate') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($trends as $t)
        @php
            $tTotal  = $t['collected'] + $t['outstanding'];
            $tRate   = $tTotal > 0 ? round($t['collected']/$tTotal*100) : 0;
            $tNetPos = ($t['net'] ?? 0) >= 0;
        @endphp
        <tr>
            <td class="bold">{{ $isAr ? $t['label'] : $t['label_en'] }}</td>
            <td class="pos">{{ $fmtN($t['collected']) }}</td>
            <td class="neg">{{ $fmtN($t['expenses']) }}</td>
            <td class="{{ $t['outstanding']>0?'neg':'muted' }}">{{ $fmtN($t['outstanding']) }}</td>
            <td class="{{ $tNetPos?'pos':'neg' }}">{{ $fmtN($t['net']) }}</td>
            <td>
                <div class="pbar-wrap" style="width:70px;display:inline-block;vertical-align:middle;">
                    <div class="pbar-fill {{ $tRate>=90?'pbar-green':($tRate>=70?'pbar-amber':'pbar-red') }}"
                         style="width:{{ min(100,$tRate) }}%;"></div>
                </div>
                <span class="sm" style="margin-{{ $isAr?'right':'left' }}:4px;">{{ $tRate }}%</span>
            </td>
        </tr>
        @endforeach
        <tr class="tr-total">
            <td>{{ $tr('الإجمالي','Total') }}</td>
            <td>{{ $fmtN(collect($trends)->sum('collected')) }}</td>
            <td>{{ $fmtN(collect($trends)->sum('expenses')) }}</td>
            <td>{{ $fmtN(collect($trends)->sum('outstanding')) }}</td>
            <td>{{ $fmtN(collect($trends)->sum('net')) }}</td>
            <td>{{ $collRate }}%</td>
        </tr>
    </tbody>
</table>
@endif
@endif {{-- executive_summary --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     SECTION 2: ASSOCIATION INFORMATION
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('association_info'))
<div class="sec-title">2. {{ $tr('معلومات الجمعية','Association Information') }}</div>
<table class="info-grid">
    <tr>
        <td class="lbl">{{ $tr('اسم الجمعية (عربي)','Association Name (AR)') }}</td>
        <td class="val">{{ $assoc->name_ar ?? '—' }}</td>
        <td class="lbl">{{ $tr('اسم الجمعية (إنجليزي)','Association Name (EN)') }}</td>
        <td class="val">{{ $assoc->name_en ?? '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('العقار','Property') }}</td>
        <td class="val">{{ $property?->name ?? '—' }}</td>
        <td class="lbl">{{ $tr('نوع العقار','Property Type') }}</td>
        <td class="val">{{ $property?->typeLabel() ?? '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('العنوان','Address') }}</td>
        <td class="val" colspan="3">{{ $property?->address ?? '—' }}@if($property?->city), {{ $property->city }}@endif</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('تاريخ التأسيس','Established Date') }}</td>
        <td class="val">{{ $assoc->established_date ? $assoc->established_date->format('Y/m/d') : '—' }}</td>
        <td class="lbl">{{ $tr('الحالة','Status') }}</td>
        <td class="val">
            <span class="bg {{ $assoc->status==='active'?'bg-green':'bg-gray' }}">
                {{ $assoc->status === 'active' ? $tr('نشطة','Active') : $tr('غير نشطة','Inactive') }}
            </span>
        </td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('الرسوم الشهرية/وحدة','Monthly Fee/Unit') }}</td>
        <td class="val bold v-blue">{{ $fmt($assoc->monthly_fee_per_unit) }} {{ $cur }}</td>
        <td class="lbl">{{ $tr('عدد الوحدات','Total Units') }}</td>
        <td class="val bold">{{ $totalUnits }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('عدد الملاك','Owners Count') }}</td>
        <td class="val bold">{{ $ownersCount }}</td>
        <td class="lbl">{{ $tr('عدد المستأجرين','Tenants Count') }}</td>
        <td class="val bold">{{ $tenantsCount }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('حساب الكهرباء','Electricity Acct.') }}</td>
        <td class="val">{{ $assoc->electricity_account_number ?? '—' }}</td>
        <td class="lbl">{{ $tr('حساب الماء','Water Acct.') }}</td>
        <td class="val">{{ $assoc->water_account_number ?? '—' }}</td>
    </tr>
    @if($assoc->description)
    <tr>
        <td class="lbl">{{ $tr('الوصف','Description') }}</td>
        <td class="val" colspan="3">{{ $assoc->description }}</td>
    </tr>
    @endif
</table>

{{-- Owners list --}}
@if($owners->count())
<div class="sub-title">{{ $tr('قائمة الملاك','Owners List') }}</div>
<table class="tbl">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ $tr('اسم المالك','Owner Name') }}</th>
            <th>{{ $tr('رقم الهوية','National ID') }}</th>
            <th>{{ $tr('الهاتف','Phone') }}</th>
            <th>{{ $tr('نسبة الملكية','Ownership %') }}</th>
            <th>{{ $tr('الحساب البنكي','Bank Acct.') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($owners as $i => $owner)
        <tr>
            <td class="muted sm">{{ $i+1 }}</td>
            <td class="bold">{{ $owner->user?->name ?? '—' }}</td>
            <td class="sm">{{ $owner->national_id ?? '—' }}</td>
            <td class="sm">{{ $owner->phone ?? $owner->user?->phone ?? '—' }}</td>
            <td class="center bold">{{ $owner->pivot->ownership_percentage ?? '—' }}%</td>
            <td class="sm muted">{{ $owner->bank_account ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Units summary --}}
@if($units->count())
<div class="sub-title">{{ $tr('ملخص الوحدات','Units Summary') }}</div>
<table class="tbl">
    <thead>
        <tr>
            <th>{{ $tr('رقم الوحدة','Unit No.') }}</th>
            <th>{{ $tr('الطابق','Floor') }}</th>
            <th>{{ $tr('النوع','Type') }}</th>
            <th>{{ $tr('المساحة','Area') }}</th>
            <th>{{ $tr('الغرف','BR') }}</th>
            <th>{{ $tr('الحالة','Status') }}</th>
            <th>{{ $tr('المستأجر الحالي','Current Tenant') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($units->sortBy('unit_number') as $unit)
        @php
            $uc = $unit->activeRentalContract;
            $sbg = match($unit->status){'rented'=>'bg-blue','sold'=>'bg-green','available'=>'bg-gray','maintenance'=>'bg-amber',default=>'bg-gray'};
        @endphp
        <tr>
            <td class="bold">{{ $unit->unit_number ?? '—' }}</td>
            <td class="center">{{ $unit->floor ?? '—' }}</td>
            <td>{{ $unit->typeLabel() }}</td>
            <td class="nowrap">{{ $unit->area ? number_format($unit->area).' م²' : '—' }}</td>
            <td class="center">{{ $unit->bedrooms ?? '—' }}</td>
            <td><span class="bg {{ $sbg }}">{{ $unit->statusLabel() }}</span></td>
            <td>{{ $uc?->tenant?->user?->name ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endif {{-- association_info --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     SECTION 3: FINANCIAL SUMMARY
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('financial_summary'))
<div class="sec-title">3. {{ $tr('الملخص المالي','Financial Summary') }}</div>

<table class="info-grid" style="margin-bottom:12px;">
    <tr>
        <td class="lbl">{{ $tr('إجمالي الاشتراكات المستحقة','Total Dues Expected') }}</td>
        <td class="val bold v-blue">{{ $fmt($totalDues) }} {{ $cur }}</td>
        <td class="lbl">{{ $tr('إجمالي الاشتراكات المحصّلة','Total Collected') }}</td>
        <td class="val bold pos">{{ $fmt($paidDues) }} {{ $cur }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('إجمالي المبالغ غير المحصّلة','Total Outstanding') }}</td>
        <td class="val bold neg">{{ $fmt($unpaidDues) }} {{ $cur }}</td>
        <td class="lbl">{{ $tr('المبالغ المتأخرة','Overdue Amount') }}</td>
        <td class="val bold neg">{{ $fmt($overdueDues) }} {{ $cur }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('المبالغ المعفاة','Waived Amount') }}</td>
        <td class="val">{{ $fmt($waivedDues) }} {{ $cur }}</td>
        <td class="lbl">{{ $tr('نسبة التحصيل','Collection Rate') }}</td>
        <td class="val bold {{ $collRate>=90?'v-green':($collRate>=70?'v-amber':'v-red') }}">{{ $collRate }}%</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('إجمالي المصروفات','Total Expenses') }}</td>
        <td class="val bold neg">{{ $fmt($totalExpenses) }} {{ $cur }}</td>
        <td class="lbl">{{ $tr('صافي الرصيد (محصّل − مصروفات)','Net Balance (Collected − Expenses)') }}</td>
        <td class="val bold {{ $netBalance>=0?'pos':'neg' }}">
            {{ $fmt($netBalance) }} {{ $cur }}
            — {{ $netBalance>=0?$tr('فائض','Surplus'):$tr('عجز','Deficit') }}
        </td>
    </tr>
</table>

{{-- Expense breakdown by category --}}
@if($expByCat->count())
<div class="sub-title">{{ $tr('توزيع المصروفات حسب الفئة','Expense Breakdown by Category') }}</div>
<table class="tbl tbl-amber">
    <thead>
        <tr>
            <th style="width:40%;">{{ $tr('الفئة','Category') }}</th>
            <th>{{ $tr('عدد البنود','Items') }}</th>
            <th>{{ $tr('الإجمالي','Total') }}</th>
            <th>{{ $tr('النسبة','% of Total') }}</th>
            <th style="width:25%;">{{ $tr('التوزيع','Distribution') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expByCat->sortByDesc(fn($g)=>$g->sum('amount')) as $cat => $catExp)
        @php
            $catTotal = (float) $catExp->sum('amount');
            $catPct   = $totalExpenses > 0 ? round($catTotal/$totalExpenses*100,1) : 0;
        @endphp
        <tr>
            <td class="bold">
                @php
                $catLabels = ['utilities'=>$tr('مرافق','Utilities'),'maintenance'=>$tr('صيانة','Maintenance'),'salaries'=>$tr('رواتب','Salaries'),'marketing'=>$tr('تسويق','Marketing'),'taxes'=>$tr('ضرائب','Taxes'),'supplies'=>$tr('مستلزمات','Supplies'),'insurance'=>$tr('تأمين','Insurance'),'legal'=>$tr('قانوني','Legal'),'other'=>$tr('أخرى','Other')];
                @endphp
                {{ $catLabels[$cat] ?? $cat }}
            </td>
            <td class="center">{{ $catExp->count() }}</td>
            <td class="neg bold">{{ $fmt($catTotal) }} {{ $cur }}</td>
            <td class="center">{{ $catPct }}%</td>
            <td>
                <div class="pbar-wrap">
                    <div class="pbar-fill pbar-amber" style="width:{{ min(100,$catPct) }}%;"></div>
                </div>
            </td>
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
@endif
@endif {{-- financial_summary --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     SECTION 4: DETAILED CONTRIBUTIONS
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('contributions') && $dues->count())
<div class="sec-title">4. {{ $tr('تقرير الاشتراكات التفصيلي','Detailed Contribution Report') }}</div>

<table class="tbl tbl-teal">
    <thead>
        <tr>
            <th style="width:18%;">{{ $tr('اسم المالك','Owner Name') }}</th>
            <th style="width:12%;">{{ $tr('فترة الاشتراك','Period') }}</th>
            <th style="width:10%;">{{ $tr('تاريخ الاستحقاق','Due Date') }}</th>
            <th style="width:10%;">{{ $tr('المبلغ المستحق','Amount Due') }}</th>
            <th style="width:10%;">{{ $tr('المدفوع','Paid') }}</th>
            <th style="width:10%;">{{ $tr('تاريخ الدفع','Pay Date') }}</th>
            <th style="width:9%;">{{ $tr('الحالة','Status') }}</th>
            <th>{{ $tr('ملاحظات','Notes') }}</th>
        </tr>
    </thead>
    <tbody>
        @php $prevOwner = null; $ownerSubTotal = 0; @endphp
        @foreach($dues->sortBy(['owner_id','due_date']) as $due)
        @php
            $ownerName = $due->owner?->user?->name ?? '—';
            $dbg = match($due->status){'paid'=>'bg-green','overdue'=>'bg-red','waived'=>'bg-teal',default=>'bg-amber'};
            if ($prevOwner !== null && $prevOwner !== $due->owner_id) {
                // subtotal row handled below
            }
        @endphp
        @if($prevOwner !== null && $prevOwner !== $due->owner_id)
        <tr class="tr-sub">
            <td colspan="3" class="bold sm">{{ $tr('مجموع','Subtotal') }} — {{ $dues->where('owner_id',$prevOwner)->first()?->owner?->user?->name }}</td>
            <td class="bold">{{ $fmt($dues->where('owner_id',$prevOwner)->sum('amount')) }}</td>
            <td class="pos bold">{{ $fmt($dues->where('owner_id',$prevOwner)->where('status','paid')->sum('amount')) }}</td>
            <td colspan="3"></td>
        </tr>
        @endif
        @php $prevOwner = $due->owner_id; @endphp
        <tr class="{{ $due->status==='overdue'?'overdue-row':'' }}">
            <td class="bold">{{ $ownerName }}</td>
            <td>{{ $due->periodLabel() }}</td>
            <td class="sm nowrap">{{ $due->due_date?->format('Y/m/d') ?? '—' }}</td>
            <td class="bold">{{ $fmt($due->amount) }}</td>
            <td class="{{ in_array($due->status,['paid','waived'])?'pos':'muted' }}">
                {{ in_array($due->status,['paid','waived']) ? $fmt($due->amount) : '—' }}
            </td>
            <td class="sm">{{ $due->paid_at?->format('Y/m/d') ?? '—' }}</td>
            <td><span class="bg {{ $dbg }}">{{ $due->statusLabel() }}</span></td>
            <td class="sm muted">{{ mb_substr($due->notes ?? '', 0, 40) }}</td>
        </tr>
        @endforeach
        {{-- Last owner subtotal --}}
        @if($prevOwner)
        <tr class="tr-sub">
            <td colspan="3" class="bold sm">{{ $tr('مجموع','Subtotal') }} — {{ $dues->where('owner_id',$prevOwner)->first()?->owner?->user?->name }}</td>
            <td class="bold">{{ $fmt($dues->where('owner_id',$prevOwner)->sum('amount')) }}</td>
            <td class="pos bold">{{ $fmt($dues->where('owner_id',$prevOwner)->where('status','paid')->sum('amount')) }}</td>
            <td colspan="3"></td>
        </tr>
        @endif
        <tr class="tr-total">
            <td colspan="3">{{ $tr('الإجمالي الكلي','Grand Total') }}</td>
            <td>{{ $fmt($totalDues) }} {{ $cur }}</td>
            <td>{{ $fmt($paidDues) }} {{ $cur }}</td>
            <td colspan="2">{{ $tr('غير محصّل:','Outstanding:') }} {{ $fmt($unpaidDues) }}</td>
            <td>{{ $collRate }}%</td>
        </tr>
    </tbody>
</table>
@elseif($has('contributions'))
<div class="sec-title">4. {{ $tr('تقرير الاشتراكات التفصيلي','Detailed Contribution Report') }}</div>
<p class="muted sm">{{ $tr('لا توجد اشتراكات في الفترة المحددة.','No contributions found in the selected period.') }}</p>
@endif {{-- contributions --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     SECTION 5: OWNER ACCOUNT STATEMENTS
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('owner_statements') && count($ownerStmts))
<div class="sec-title">5. {{ $tr('كشوف حساب الملاك','Owner Account Statements') }}</div>
@foreach($ownerStmts as $ownerId => $stmt)
@php
    $ow = $stmt['owner'];
    $owBalance = $stmt['balance'];
    $owPct     = $stmt['payment_pct'];
    $owPctColor= $owPct>=90?'v-green':($owPct>=70?'v-amber':'v-red');
@endphp

<div class="sub-title">
    {{ $ow->user?->name ?? '—' }}
    <span class="sm muted">
        — {{ $tr('الهوية:','ID:') }} {{ $ow->national_id ?? '—' }}
        &bull; {{ $tr('الهاتف:','Phone:') }} {{ $ow->phone ?? $ow->user?->phone ?? '—' }}
        &bull; {{ $tr('نسبة الملكية:','Ownership:') }} {{ $ow->pivot->ownership_percentage ?? '—' }}%
    </span>
</div>

<table class="kpi-row" style="margin-bottom:10px;">
    <tr>
        <td class="kpi kpi-blue" style="width:22%;">
            <div class="kpi-lbl">{{ $tr('إجمالي المستحق (الفترة)','Period Total Due') }}</div>
            <div class="kpi-val v-blue" style="font-size:11pt;">{{ $fmt($stmt['total_due']) }}</div>
            <div class="kpi-unit">{{ $cur }}</div>
        </td>
        <td width="4"></td>
        <td class="kpi kpi-green" style="width:22%;">
            <div class="kpi-lbl">{{ $tr('المدفوع (الفترة)','Period Paid') }}</div>
            <div class="kpi-val v-green" style="font-size:11pt;">{{ $fmt($stmt['total_paid']) }}</div>
            <div class="kpi-unit">{{ $cur }}</div>
        </td>
        <td width="4"></td>
        <td class="kpi {{ $stmt['outstanding']>0?'kpi-red':'kpi-green' }}" style="width:22%;">
            <div class="kpi-lbl">{{ $tr('المتبقي (الفترة)','Period Outstanding') }}</div>
            <div class="kpi-val {{ $stmt['outstanding']>0?'v-red':'v-green' }}" style="font-size:11pt;">{{ $fmt($stmt['outstanding']) }}</div>
            <div class="kpi-unit">{{ $cur }}</div>
        </td>
        <td width="4"></td>
        <td class="kpi {{ $owBalance>0?'kpi-red':'kpi-green' }}" style="width:22%;">
            <div class="kpi-lbl">{{ $tr('الرصيد الكلي','Total Balance') }}</div>
            <div class="kpi-val {{ $owBalance>0?'v-red':'v-green' }}" style="font-size:11pt;">{{ $fmt(abs($owBalance)) }}</div>
            <div class="kpi-unit">{{ $owBalance>0?$tr('مدين','Debit'):$tr('دائن','Credit') }}</div>
        </td>
        <td width="4"></td>
        <td class="kpi {{ $owPct>=90?'kpi-green':($owPct>=70?'kpi-amber':'kpi-red') }}" style="width:22%;">
            <div class="kpi-lbl">{{ $tr('نسبة الدفع الكلية','Overall Payment %') }}</div>
            <div class="kpi-val {{ $owPctColor }}" style="font-size:11pt;">{{ $owPct }}%</div>
            <div class="kpi-unit">
                <div class="pbar-wrap" style="margin-top:3px;">
                    <div class="pbar-fill {{ $owPct>=90?'pbar-green':($owPct>=70?'pbar-amber':'pbar-red') }}"
                         style="width:{{ min(100,$owPct) }}%;"></div>
                </div>
            </div>
        </td>
    </tr>
</table>

@if($stmt['transactions']->count())
<table class="tbl tbl-gray" style="font-size:7.5pt;">
    <thead>
        <tr>
            <th style="width:11%;">{{ $tr('التاريخ','Date') }}</th>
            <th style="width:35%;">{{ $tr('البيان','Description') }}</th>
            <th style="width:13%;">{{ $tr('مدين','Debit') }}</th>
            <th style="width:13%;">{{ $tr('دائن','Credit') }}</th>
            <th style="width:14%;">{{ $tr('الرصيد','Balance') }}</th>
            <th style="width:14%;">{{ $tr('الحالة','Status') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($stmt['transactions'] as $tx)
        @php
            $txBg = $tx['type']==='credit' ? 'bg-green' : ($tx['status']==='overdue'?'bg-red':($tx['status']==='waived'?'bg-teal':'bg-amber'));
        @endphp
        <tr class="{{ $tx['type']==='credit'?'':''.($tx['status']==='overdue'?'overdue-row':'') }}">
            <td class="nowrap">{{ $tx['date'] instanceof \Carbon\Carbon ? $tx['date']->format('Y/m/d') : ($tx['date'] ? \Carbon\Carbon::parse($tx['date'])->format('Y/m/d') : '—') }}</td>
            <td>{{ $tx['description'] }}</td>
            <td class="{{ $tx['debit']>0?'neg':'' }}">{{ $tx['debit']>0 ? $fmt($tx['debit']) : '—' }}</td>
            <td class="{{ $tx['credit']>0?'pos':'' }}">{{ $tx['credit']>0 ? $fmt($tx['credit']) : '—' }}</td>
            <td class="{{ $tx['balance']>0?'neg':($tx['balance']<0?'pos':'muted') }} bold">{{ $fmt(abs($tx['balance'])) }}</td>
            <td><span class="bg {{ $txBg }} xs">{{ $tx['status']==='credit'?$tr('دفعة','Payment'):$due->statusLabel() }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@if(!$loop->last) <div style="border-top:1px dashed #e2e8f0; margin:10px 0;"></div> @endif
@endforeach
@endif {{-- owner_statements --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     SECTION 6: AGING REPORT
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('aging'))
<div class="sec-title">6. {{ $tr('تقرير تقادم الأرصدة المتأخرة','Outstanding Balances Aging Report') }}</div>

@php
$agingTotals = [
    'not_due' => ['label_ar'=>'لم تحل بعد', 'label_en'=>'Not Yet Due', 'dues'=>$aging['not_due'], 'color'=>'kpi-blue','vcolor'=>'v-blue'],
    '0_30'    => ['label_ar'=>'0–30 يوم',   'label_en'=>'0–30 Days',   'dues'=>$aging['0_30'],  'color'=>'kpi-amber','vcolor'=>'v-amber'],
    '31_60'   => ['label_ar'=>'31–60 يوم',  'label_en'=>'31–60 Days',  'dues'=>$aging['31_60'], 'color'=>'kpi-amber','vcolor'=>'v-amber'],
    '61_90'   => ['label_ar'=>'61–90 يوم',  'label_en'=>'61–90 Days',  'dues'=>$aging['61_90'], 'color'=>'kpi-red',  'vcolor'=>'v-red'],
    'over_90' => ['label_ar'=>'أكثر من 90', 'label_en'=>'Over 90 Days','dues'=>$aging['over_90'],'color'=>'kpi-red','vcolor'=>'v-red'],
];
$agingTotal = $allOutstanding->sum('amount');
@endphp

<table class="kpi-row">
@foreach($agingTotals as $key => $ag)
@php $agAmt = (float) $ag['dues']->sum('amount'); $agPct = $agingTotal>0 ? round($agAmt/$agingTotal*100) : 0; @endphp
<tr><td class="kpi {{ $ag['color'] }}" style="padding:8px;">
    <div class="kpi-lbl">{{ $tr($ag['label_ar'],$ag['label_en']) }}</div>
    <div class="kpi-val {{ $ag['vcolor'] }}" style="font-size:11pt;">{{ $fmt($agAmt) }}</div>
    <div class="kpi-unit">{{ $ag['dues']->count() }} {{ $tr('اشتراك','dues') }} — {{ $agPct }}%</div>
</td></tr>
@endforeach
</table>

@if($allOutstanding->count())
<table class="tbl tbl-red" style="margin-top:8px;">
    <thead>
        <tr>
            <th>{{ $tr('المالك','Owner') }}</th>
            <th>{{ $tr('الفترة','Period') }}</th>
            <th>{{ $tr('تاريخ الاستحقاق','Due Date') }}</th>
            <th>{{ $tr('المبلغ','Amount') }}</th>
            <th>{{ $tr('أيام التأخر','Days Late') }}</th>
            <th>{{ $tr('الفئة','Bucket') }}</th>
            <th>{{ $tr('الحالة','Status') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($allOutstanding->sortBy('due_date') as $od)
        @php
            $daysLate = $od->due_date ? (int) $od->due_date->diffInDays(now(), false) : 0;
            $bucket = $daysLate <= 0 ? $tr('لم تحل','Not Due')
                    : ($daysLate <= 30 ? '0–30'
                    : ($daysLate <= 60 ? '31–60'
                    : ($daysLate <= 90 ? '61–90'
                    : $tr('أكثر من 90','Over 90'))));
        @endphp
        <tr class="{{ $daysLate>30?'overdue-row':'' }}">
            <td class="bold">{{ $od->owner?->user?->name ?? '—' }}</td>
            <td>{{ $od->periodLabel() }}</td>
            <td class="sm nowrap">{{ $od->due_date?->format('Y/m/d') ?? '—' }}</td>
            <td class="neg bold">{{ $fmt($od->amount) }} {{ $cur }}</td>
            <td class="center {{ $daysLate>0?'neg':'' }}">{{ $daysLate > 0 ? $daysLate : '—' }}</td>
            <td class="center"><span class="bg {{ $daysLate<=0?'bg-blue':($daysLate<=30?'bg-amber':($daysLate<=60?'bg-amber':'bg-red')) }}">{{ $bucket }}</span></td>
            <td><span class="bg {{ $od->status==='overdue'?'bg-red':'bg-amber' }}">{{ $od->statusLabel() }}</span></td>
        </tr>
        @endforeach
        <tr class="tr-total">
            <td colspan="3">{{ $tr('إجمالي المتأخرات','Total Outstanding') }}</td>
            <td>{{ $fmt($agingTotal) }} {{ $cur }}</td>
            <td colspan="3">{{ $allOutstanding->count() }} {{ $tr('اشتراك','due(s)') }}</td>
        </tr>
    </tbody>
</table>
@else
<p class="muted sm">{{ $tr('لا توجد أرصدة متأخرة — جيد!','No outstanding balances — great!') }}</p>
@endif
@endif {{-- aging --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     SECTION 7: UNIT COLLECTION STATUS
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('unit_status') && count($unitFeeMap))
<div class="sec-title">7. {{ $tr('حالة التحصيل لكل مالك','Owner Collection Status Report') }}</div>
<table class="tbl tbl-violet">
    <thead>
        <tr>
            <th>{{ $tr('المالك','Owner') }}</th>
            <th>{{ $tr('نسبة الملكية','Share %') }}</th>
            <th>{{ $tr('الرسوم الشهرية','Monthly Fee') }}</th>
            <th>{{ $tr('إجمالي المستحق','Total Due') }}</th>
            <th>{{ $tr('المدفوع','Paid') }}</th>
            <th>{{ $tr('المتبقي','Outstanding') }}</th>
            <th>{{ $tr('نسبة التحصيل','Coll. %') }}</th>
            <th>{{ $tr('الحالة','Status') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($unitFeeMap as $row)
        @php
            $feeForOwner = $assoc->monthly_fee_per_unit * $totalUnits * (($row['share_pct'] ?? 100) / 100);
            $pctColor = $row['pct']>=100?'bg-green':($row['pct']>=70?'bg-amber':($row['has_overdue']?'bg-red':'bg-amber'));
        @endphp
        <tr class="{{ $row['has_overdue']?'overdue-row':'' }}">
            <td class="bold">{{ $row['owner']->user?->name ?? '—' }}</td>
            <td class="center">{{ $row['share_pct'] ?? '—' }}%</td>
            <td class="nowrap">{{ $fmt($feeForOwner) }} {{ $cur }}</td>
            <td class="bold">{{ $fmt($row['total_due']) }} {{ $cur }}</td>
            <td class="pos bold">{{ $fmt($row['total_paid']) }} {{ $cur }}</td>
            <td class="{{ $row['outstanding']>0?'neg':'muted' }}">{{ $fmt($row['outstanding']) }} {{ $cur }}</td>
            <td>
                <div class="pbar-wrap" style="width:60px;display:inline-block;vertical-align:middle;">
                    <div class="pbar-fill {{ $row['pct']>=90?'pbar-green':($row['pct']>=70?'pbar-amber':'pbar-red') }}"
                         style="width:{{ min(100,$row['pct']) }}%;"></div>
                </div>
                <span class="sm" style="margin-{{ $isAr?'right':'left' }}:3px;">{{ $row['pct'] }}%</span>
            </td>
            <td><span class="bg {{ $pctColor }}">
                {{ $row['pct']>=100?$tr('مكتمل','Complete'):($row['has_overdue']?$tr('متأخر','Overdue'):$tr('جزئي','Partial')) }}
            </span></td>
        </tr>
        @endforeach
        <tr class="tr-total">
            <td colspan="3">{{ $tr('الإجمالي','Total') }}</td>
            <td>{{ $fmt(collect($unitFeeMap)->sum('total_due')) }} {{ $cur }}</td>
            <td>{{ $fmt(collect($unitFeeMap)->sum('total_paid')) }} {{ $cur }}</td>
            <td>{{ $fmt(collect($unitFeeMap)->sum('outstanding')) }} {{ $cur }}</td>
            <td colspan="2">{{ $collRate }}%</td>
        </tr>
    </tbody>
</table>
@endif {{-- unit_status --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     SECTION 8: EXPENSE MANAGEMENT
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('expenses'))
<div class="sec-title">8. {{ $tr('تقرير المصروفات','Expense Management Report') }}</div>
@if($expenses->count())
<table class="tbl tbl-amber">
    <thead>
        <tr>
            <th style="width:11%;">{{ $tr('التاريخ','Date') }}</th>
            <th style="width:25%;">{{ $tr('البيان','Title') }}</th>
            <th style="width:13%;">{{ $tr('الفئة','Category') }}</th>
            <th style="width:12%;">{{ $tr('المبلغ','Amount') }}</th>
            <th style="width:15%;">{{ $tr('المدفوع بواسطة','Paid By') }}</th>
            <th>{{ $tr('الوصف','Description') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenses->sortBy('expense_date') as $exp)
        @php $catLabels2 = ['utilities'=>$tr('مرافق','Utilities'),'maintenance'=>$tr('صيانة','Maintenance'),'salaries'=>$tr('رواتب','Salaries'),'marketing'=>$tr('تسويق','Marketing'),'taxes'=>$tr('ضرائب','Taxes'),'supplies'=>$tr('مستلزمات','Supplies'),'insurance'=>$tr('تأمين','Insurance'),'legal'=>$tr('قانوني','Legal'),'other'=>$tr('أخرى','Other')]; @endphp
        <tr>
            <td class="sm nowrap">{{ $exp->expense_date->format('Y/m/d') }}</td>
            <td class="bold">{{ $exp->title }}</td>
            <td>{{ $catLabels2[$exp->category] ?? $exp->category }}</td>
            <td class="neg bold nowrap">{{ $fmt($exp->amount) }} {{ $cur }}</td>
            <td class="sm">{{ $exp->paidByUser?->name ?? '—' }}</td>
            <td class="sm muted">{{ mb_substr($exp->description ?? '', 0, 55) }}</td>
        </tr>
        @endforeach
        <tr class="tr-total">
            <td colspan="3">{{ $tr('إجمالي المصروفات','Total Expenses') }}</td>
            <td>{{ $fmt($totalExpenses) }} {{ $cur }}</td>
            <td colspan="2">{{ $expenses->count() }} {{ $tr('بند','items') }}</td>
        </tr>
    </tbody>
</table>
@else
<p class="muted sm">{{ $tr('لا توجد مصروفات مسجّلة في هذه الفترة.','No expenses recorded for this period.') }}</p>
@endif
@endif {{-- expenses --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     SECTION 9: MAINTENANCE
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('maintenance'))
<div class="sec-title">9. {{ $tr('الصيانة وأوامر العمل','Maintenance & Work Orders') }}</div>

@php
$mTotal  = $maintenance->count();
$mOpen   = $maintenance->whereIn('status',['pending','in_progress'])->count();
$mClosed = $maintenance->where('status','completed')->count();
$mInProg = $maintenance->where('status','in_progress')->count();
@endphp

<table class="kpi-row">
    <tr>
        <td class="kpi kpi-gray" style="width:22%;">
            <div class="kpi-lbl">{{ $tr('إجمالي الطلبات','Total Requests') }}</div>
            <div class="kpi-val v-gray">{{ $mTotal }}</div>
        </td>
        <td width="4"></td>
        <td class="kpi kpi-amber" style="width:22%;">
            <div class="kpi-lbl">{{ $tr('مفتوح / معلق','Open / Pending') }}</div>
            <div class="kpi-val v-amber">{{ $maintenance->where('status','pending')->count() }}</div>
        </td>
        <td width="4"></td>
        <td class="kpi kpi-blue" style="width:22%;">
            <div class="kpi-lbl">{{ $tr('قيد التنفيذ','In Progress') }}</div>
            <div class="kpi-val v-blue">{{ $mInProg }}</div>
        </td>
        <td width="4"></td>
        <td class="kpi kpi-green" style="width:22%;">
            <div class="kpi-lbl">{{ $tr('مكتمل','Completed') }}</div>
            <div class="kpi-val v-green">{{ $mClosed }}</div>
        </td>
    </tr>
</table>

@if($maintenance->count())
<table class="tbl tbl-gray" style="margin-top:8px;">
    <thead>
        <tr>
            <th style="width:6%;">#</th>
            <th style="width:9%;">{{ $tr('الوحدة','Unit') }}</th>
            <th style="width:18%;">{{ $tr('العنوان','Title') }}</th>
            <th style="width:14%;">{{ $tr('المبلّغ','Reported By') }}</th>
            <th style="width:8%;">{{ $tr('الأولوية','Priority') }}</th>
            <th style="width:9%;">{{ $tr('الحالة','Status') }}</th>
            <th style="width:10%;">{{ $tr('تاريخ الفتح','Opened') }}</th>
            <th>{{ $tr('الوصف','Description') }}</th>
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
            <td class="bold">{{ $mr->unit?->unit_number ?? '—' }}</td>
            <td class="bold">{{ $mr->title }}</td>
            <td class="sm">{{ $mr->tenant?->user?->name ?? '—' }}</td>
            <td><span class="bg {{ $priBg }} xs">{{ $mr->priorityLabel() }}</span></td>
            <td><span class="bg {{ $stsBg }} xs">{{ $mr->statusLabel() }}</span></td>
            <td class="sm">{{ $mr->created_at->format('Y/m/d') }}</td>
            <td class="sm muted">{{ mb_substr($mr->description ?? '', 0, 55) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p class="muted sm">{{ $tr('لا توجد طلبات صيانة.','No maintenance requests.') }}</p>
@endif
@endif {{-- maintenance --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     SECTION 10: MEETINGS & GOVERNANCE
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('meetings'))
<div class="sec-title">10. {{ $tr('الاجتماعات والحوكمة','Meetings & Governance') }}</div>
@if($meetings->count())
@foreach($meetings as $mtg)
@php $mtgBg = match($mtg->status){'completed'=>'bg-green','cancelled'=>'bg-red',default=>'bg-blue'}; @endphp
<div class="sub-title">
    {{ $mtg->title }}
    <span class="sm">
        — {{ $mtg->scheduled_at->format('Y/m/d H:i') }}
        @if($mtg->location) &bull; {{ $mtg->location }} @endif
        &nbsp;<span class="bg {{ $mtgBg }}">{{ $mtg->statusLabel() }}</span>
    </span>
</div>
@if($mtg->agenda)
<table class="info-grid" style="margin-bottom:6px;">
    <tr>
        <td class="lbl" style="width:15%;">{{ $tr('جدول الأعمال','Agenda') }}</td>
        <td class="val" style="white-space:pre-wrap;">{{ $mtg->agenda }}</td>
    </tr>
    @if($mtg->status==='completed' && $mtg->minutes)
    <tr>
        <td class="lbl">{{ $tr('محضر الاجتماع','Minutes') }}</td>
        <td class="val" style="white-space:pre-wrap;">{{ $mtg->minutes }}</td>
    </tr>
    @endif
</table>
@endif
@endforeach
@else
<p class="muted sm">{{ $tr('لا توجد اجتماعات في هذه الفترة.','No meetings in this period.') }}</p>
@endif
@endif {{-- meetings --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     SECTION 11: RESERVE FUND
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('reserve_fund'))
<div class="sec-title">11. {{ $tr('صندوق الاحتياطي','Reserve Fund Report') }}</div>
@php
$reserveContrib = $paidDues * 0.10;
$reserveBalance = $reserveContrib - ($totalExpenses * 0.10);
@endphp
<table class="info-grid">
    <tr>
        <td class="lbl">{{ $tr('رصيد الافتتاح','Opening Balance') }}</td>
        <td class="val">{{ $fmt(0) }} {{ $cur }}</td>
        <td class="lbl">{{ $tr('الاشتراكات المستلمة','Contributions Received') }}</td>
        <td class="val pos bold">{{ $fmt($paidDues) }} {{ $cur }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('مدفوعات إلى صندوق الاحتياطي (10%)','Reserve Fund Contributions (10%)') }}</td>
        <td class="val bold v-teal">{{ $fmt($reserveContrib) }} {{ $cur }}</td>
        <td class="lbl">{{ $tr('مصروفات من الصندوق','Reserve Fund Expenses') }}</td>
        <td class="val neg">{{ $fmt($totalExpenses * 0.10) }} {{ $cur }}</td>
    </tr>
    <tr>
        <td class="lbl bold">{{ $tr('رصيد الصندوق التقديري','Estimated Reserve Balance') }}</td>
        <td class="val bold {{ $reserveBalance>=0?'pos':'neg' }}">{{ $fmt(max(0,$reserveBalance)) }} {{ $cur }}</td>
        <td class="lbl">{{ $tr('الفائدة المكتسبة','Interest Earned') }}</td>
        <td class="val muted">{{ $tr('غير مطبق','N/A') }}</td>
    </tr>
</table>
<p class="xs muted" style="margin-top:4px;">
    {{ $tr('* الأرقام تقديرية بناءً على تخصيص 10% من المحصّل. يُنصح بالرجوع إلى السجلات البنكية الفعلية.','* Figures are estimated based on 10% allocation of collected dues. Refer to actual bank records for accuracy.') }}
</p>
@endif {{-- reserve_fund --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     SECTION 12 (OPTIONAL): VIOLATIONS — NOT IMPLEMENTED
══════════════════════════════════════════════════════════════════════════ --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     SECTION 13: ATTACHMENTS & DOCUMENTS
══════════════════════════════════════════════════════════════════════════ --}}
@if($has('attachments'))
<div class="sec-title">12. {{ $tr('المرفقات والوثائق الداعمة','Attachments & Supporting Documents') }}</div>
@php
$hasAnyDoc = $assoc->no_objection_certificate_path
    || $assoc->sketch_path
    || $assoc->association_certificate_path
    || $assoc->personal_id_path
    || $assoc->manager_id_path;
$nocs     = $data['association']->noObjectionCertificates ?? collect();
$nocSales = $data['association']->noObjectionSaleCertificates ?? collect();
@endphp
<table class="info-grid">
    <tr>
        <td class="lbl">{{ $tr('شهادة عدم الممانعة','No Objection Certificate') }}</td>
        <td class="val">{{ $assoc->no_objection_certificate_path ? $tr('متوفر ✓','Available ✓') : $tr('غير متوفر','Not Available') }}</td>
        <td class="lbl">{{ $tr('المخطط','Sketch') }}</td>
        <td class="val">{{ $assoc->sketch_path ? $tr('متوفر ✓','Available ✓') : $tr('غير متوفر','Not Available') }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('شهادة جمعية الملاك','Association Certificate') }}</td>
        <td class="val">{{ $assoc->association_certificate_path ? $tr('متوفر ✓','Available ✓') : $tr('غير متوفر','Not Available') }}</td>
        <td class="lbl">{{ $tr('بطاقة هوية شخصية','Personal ID') }}</td>
        <td class="val">{{ $assoc->personal_id_path ? $tr('متوفر ✓','Available ✓') : $tr('غير متوفر','Not Available') }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('هوية مدير الجمعية','Manager ID') }}</td>
        <td class="val">{{ $assoc->manager_id_path ? $tr('متوفر ✓','Available ✓') : $tr('غير متوفر','Not Available') }}</td>
        <td class="lbl">{{ $tr('عدد شهادات عدم الممانعة','NOC Certificates Issued') }}</td>
        <td class="val bold">{{ $nocs->count() }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tr('عدد شهادات البيع','NOC Sale Certificates Issued') }}</td>
        <td class="val bold">{{ $nocSales->count() }}</td>
        <td class="lbl"></td>
        <td class="val"></td>
    </tr>
</table>

@if($nocs->count())
<div class="sub-title">{{ $tr('شهادات عدم الممانعة للإيجار','Rental NOC Certificates') }}</div>
<table class="tbl">
    <thead><tr>
        <th>{{ $tr('رقم المرجع','Ref No.') }}</th>
        <th>{{ $tr('اسم المؤجر','Lessor Name') }}</th>
        <th>{{ $tr('هاتف المؤجر','Lessor Phone') }}</th>
        <th>{{ $tr('تاريخ الإصدار','Issue Date') }}</th>
    </tr></thead>
    <tbody>
        @foreach($nocs as $noc)
        <tr>
            <td class="bold sm">{{ $noc->ref_number }}</td>
            <td>{{ $noc->lessor_name ?? '—' }}</td>
            <td class="sm">{{ $noc->lessor_phone ?? '—' }}</td>
            <td class="sm">{{ $noc->created_at->format('Y/m/d') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@if($nocSales->count())
<div class="sub-title">{{ $tr('شهادات عدم الممانعة للبيع','Sale NOC Certificates') }}</div>
<table class="tbl">
    <thead><tr>
        <th>{{ $tr('رقم المرجع','Ref No.') }}</th>
        <th>{{ $tr('اسم المشتري','Buyer Name') }}</th>
        <th>{{ $tr('هاتف المشتري','Buyer Phone') }}</th>
        <th>{{ $tr('تاريخ الإصدار','Issue Date') }}</th>
    </tr></thead>
    <tbody>
        @foreach($nocSales as $noc)
        <tr>
            <td class="bold sm">{{ $noc->ref_number }}</td>
            <td>{{ $noc->buyer_name ?? '—' }}</td>
            <td class="sm">{{ $noc->buyer_phone ?? '—' }}</td>
            <td class="sm">{{ $noc->created_at->format('Y/m/d') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endif {{-- attachments --}}


{{-- Association footer --}}
<div style="margin-top:24px; border-top:2px solid #1e3a8a; padding-top:10px;">
    <table width="100%" border="0" cellspacing="0">
        <tr>
            <td style="font-size:7.5pt; color:#1e3a8a; font-weight:bold;">
                {{ $tr('شركة ثروة للتطوير العقاري','Tharwa Real Estate') }}
            </td>
            <td style="text-align:center; font-size:7pt; color:#9ca3af;">
                {{ $tr('تقرير شامل لجمعية الملاك','Comprehensive HOA Report') }}
                — {{ $assocName }}
            </td>
            <td style="text-align:{{ $isAr?'left':'right' }}; font-size:7pt; color:#9ca3af;">
                {{ now()->format('Y/m/d H:i') }}
            </td>
        </tr>
    </table>
</div>

{{-- Page break between associations (not after the last one) --}}
@if(!$loop->last)
<div style="page-break-after:always;"></div>
@endif

@endforeach {{-- end per-association loop --}}

</body>
</html>
