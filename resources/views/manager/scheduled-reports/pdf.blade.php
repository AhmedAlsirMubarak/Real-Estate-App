<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<title>{{ $report->name }}</title>
<style>
    @page { margin: 14mm 12mm; }
    body  { font-family: dejavusans, sans-serif; font-size: 9pt; color: #1e293b; direction: rtl; margin:0; padding:0; }

    /* ── Header ── */
    .hdr      { background:#1e3a8a; color:#fff; padding:16px 20px 13px; margin-bottom:14px; }
    .hdr-tbl  { width:100%; border-collapse:collapse; }
    .hdr-title{ font-size:16pt; font-weight:bold; margin:0 0 3px; }
    .hdr-sub  { font-size:8.5pt; color:#bfdbfe; margin:0; }
    .hdr-right{ font-size:8.5pt; color:#bfdbfe; text-align:left; vertical-align:top; white-space:nowrap; }

    /* ── Section / Block titles ── */
    .sec      { font-size:11pt; font-weight:bold; color:#1e3a8a; border-bottom:2px solid #1e3a8a; padding-bottom:3px; margin:14px 0 9px; }
    .blk-hdr  { font-size:10.5pt; font-weight:bold; color:#1e40af; background:#eff6ff; border-right:4px solid #1e3a8a; padding:6px 10px; margin:14px 0 8px; }
    .blk-sub  { font-size:9pt; font-weight:bold; color:#1e3a8a; margin:8px 0 4px; }

    /* ── Report meta ── */
    .meta   { width:100%; border-collapse:collapse; margin-bottom:14px; font-size:9pt; }
    .meta td{ padding:3px 7px; }
    .mlbl   { font-weight:bold; color:#1e293b; width:110px; }
    .mval   { color:#475569; }
    .badge-green { background:#dcfce7; color:#166534; padding:1px 9px; border-radius:9px; font-size:8.5pt; }
    .badge-amber { background:#fef9c3; color:#854d0e; padding:1px 9px; border-radius:9px; font-size:8.5pt; }
    .divider{ border:none; border-top:1px solid #e2e8f0; margin:0 0 14px; }

    /* ── Summary Cards ── */
    .cards  { width:100%; border-collapse:collapse; margin-bottom:12px; }
    .card   { border:1px solid #e2e8f0; padding:9px 7px; text-align:center; vertical-align:top; }
    .cblue  { background:#eff6ff; border-color:#bfdbfe; }
    .cgreen { background:#f0fdf4; border-color:#bbf7d0; }
    .cred   { background:#fff1f2; border-color:#fecdd3; }
    .camber { background:#fffbeb; border-color:#fde68a; }
    .cgray  { background:#f9fafb; border-color:#e5e7eb; }
    .clbl   { font-size:7.5pt; color:#64748b; margin-bottom:4px; }
    .cval   { font-size:13pt; font-weight:bold; }
    .cunit  { font-size:7.5pt; color:#94a3b8; margin-top:2px; }
    .vblue  { color:#1d4ed8; }
    .vgreen { color:#15803d; }
    .vred   { color:#b91c1c; }
    .vamber { color:#b45309; }
    .vgray  { color:#374151; }

    /* ── Data tables ── */
    .tbl          { width:100%; border-collapse:collapse; margin-bottom:11px; font-size:8.5pt; }
    .tbl thead th { background:#1e3a8a; color:#fff; padding:6px 7px; text-align:right; font-weight:bold; }
    .tbl tbody td { padding:5px 7px; border-bottom:1px solid #f1f5f9; }
    .tbl tbody tr:nth-child(even) td { background:#f8fafc; }
    .tbl .tr-tot td { background:#eff6ff; font-weight:bold; border-top:2px solid #bfdbfe; }
    .tbl-dark thead th { background:#334155; }
    .tbl-teal thead th { background:#0f766e; }

    /* ── Inline badges ── */
    .bg  { padding:1px 6px; border-radius:5px; font-size:7.5pt; white-space:nowrap; }
    .bg-green { background:#dcfce7; color:#166534; }
    .bg-blue  { background:#dbeafe; color:#1d4ed8; }
    .bg-amber { background:#fef9c3; color:#92400e; }
    .bg-red   { background:#fee2e2; color:#b91c1c; }
    .bg-gray  { background:#f3f4f6; color:#374151; }
    .bg-teal  { background:#ccfbf1; color:#0f766e; }

    /* ── Utilities ── */
    .pos  { color:#15803d; font-weight:bold; }
    .neg  { color:#b91c1c; font-weight:bold; }
    .muted{ color:#9ca3af; }
    .bold { font-weight:bold; }
    .sm   { font-size:7.5pt; }
    .sep  { border-top:3px dashed #cbd5e1; margin:16px 0; }

    /* ── Footer ── */
    .footer { margin-top:20px; text-align:center; font-size:7.5pt; color:#9ca3af; border-top:1px solid #e2e8f0; padding-top:8px; }
</style>
</head>
<body>

{{-- ══════════════════════════════════════════════
     HEADER
══════════════════════════════════════════════ --}}
<div class="hdr">
    <table class="hdr-tbl">
        <tr>
            <td>
                <p class="hdr-title">{{ $report->name }}</p>
                <p class="hdr-sub">{{ $report->section === 'hoa' ? 'جمعية الملاك' : 'إدارة المباني' }} &mdash; تقرير دوري</p>
            </td>
            <td class="hdr-right">
                <div>شركة ثروة للعقارات</div>
                <div style="margin-top:3px;">{{ now()->format('Y/m/d H:i') }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ══════════════════════════════════════════════
     REPORT META
══════════════════════════════════════════════ --}}
@php
    $isManagement = $report->section !== 'hoa';

    $scopeLabel = $report->association
        ? ('جمعية: ' . ($report->association->name_ar ?? $report->association->name_en ?? '—'))
        : ($report->property
            ? ('عقار: ' . $report->property->name)
            : ($isManagement ? 'جميع المباني المُدارة' : 'جميع الجمعيات'));
@endphp

<table class="meta">
    <tr>
        <td class="mlbl">الفترة:</td>
        <td class="mval">{{ $start->format('Y/m/d') }} &mdash; {{ $end->format('Y/m/d') }}
            <span class="muted">({{ $report->period_months }} {{ $report->period_months === 1 ? 'شهر' : 'أشهر' }})</span>
        </td>
        <td class="mlbl" style="padding-right:20px;">النطاق:</td>
        <td class="mval">{{ $scopeLabel }}</td>
    </tr>
    <tr>
        <td class="mlbl">الحالة:</td>
        <td colspan="3">
            <span class="{{ $report->status === 'active' ? 'badge-green' : 'badge-amber' }}">{{ $report->statusLabel() }}</span>
        </td>
    </tr>
</table>
<hr class="divider">

@if($isManagement)
{{-- ══════════════════════════════════════════════════════════════════
     BUILDING MANAGEMENT SECTION
══════════════════════════════════════════════════════════════════ --}}
@php
    $mgRevenue    = (float) ($data['total_revenue']    ?? 0);
    $mgExpenses   = (float) ($data['total_expenses']   ?? 0);
    $mgNet        = (float) ($data['net_income']       ?? $mgRevenue - $mgExpenses);
    $mgPending    = (int)   ($data['pending_payments'] ?? 0);
    $mgOverdue    = (int)   ($data['overdue_payments'] ?? 0);
    $mgMaintTotal = (int)   ($data['maintenance_total']?? 0);
    $mgMaintDone  = (int)   ($data['maintenance_done'] ?? 0);
    $mgPropCount  = (int)   ($data['properties_count'] ?? 0);
    $properties   = $data['properties'] ?? collect();
@endphp

{{-- Global Summary --}}
<div class="sec">الملخص المالي الإجمالي للفترة</div>
<table class="cards">
    <tr>
        <td class="card cgray" style="width:16%;">
            <div class="clbl">عدد العقارات</div>
            <div class="cval vgray">{{ $mgPropCount }}</div>
            <div class="cunit">مبنى مشمول</div>
        </td>
        <td width="5"></td>
        <td class="card cgreen" style="width:16%;">
            <div class="clbl">الإيرادات المحصّلة</div>
            <div class="cval vgreen">{{ number_format($mgRevenue) }}</div>
            <div class="cunit">ر.ع</div>
        </td>
        <td width="5"></td>
        <td class="card cred" style="width:16%;">
            <div class="clbl">إجمالي المصروفات</div>
            <div class="cval vred">{{ number_format($mgExpenses) }}</div>
            <div class="cunit">ر.ع</div>
        </td>
        <td width="5"></td>
        <td class="card {{ $mgNet >= 0 ? 'cgreen' : 'cred' }}" style="width:16%;">
            <div class="clbl">صافي الربح</div>
            <div class="cval {{ $mgNet >= 0 ? 'vgreen' : 'vred' }}">{{ number_format($mgNet) }}</div>
            <div class="cunit">ر.ع</div>
        </td>
        <td width="5"></td>
        <td class="card camber" style="width:16%;">
            <div class="clbl">متأخر / معلق</div>
            <div class="cval vamber">{{ $mgOverdue + $mgPending }}</div>
            <div class="cunit">{{ $mgOverdue }} متأخرة &bull; {{ $mgPending }} معلقة</div>
        </td>
        <td width="5"></td>
        <td class="card cblue" style="width:16%;">
            <div class="clbl">الصيانة</div>
            <div class="cval vblue">{{ $mgMaintTotal }}</div>
            <div class="cunit">{{ $mgMaintDone }} مكتملة</div>
        </td>
    </tr>
</table>

{{-- ── Per-Property Blocks ── --}}
@foreach($properties as $property)
@php
    $propPayments    = $data['payments_by_property'][$property->id]    ?? collect();
    $propExpenses    = $data['expenses_by_property'][$property->id]    ?? collect();
    $propMaintenance = $data['maintenance_by_property'][$property->id] ?? collect();

    $propRevenue  = $propPayments->where('status','paid')->sum('amount');
    $propExpTotal = $propExpenses->sum('amount');
    $propNet      = $propRevenue - $propExpTotal;
    $propPaid     = $propPayments->where('status','paid')->count();
    $propPending  = $propPayments->where('status','pending')->count();
    $propOverdue  = $propPayments->where('status','overdue')->count();

    $totalUnits     = $property->units->count();
    $rentedUnits    = $property->units->where('status','rented')->count();
    $soldUnits      = $property->units->where('status','sold')->count();
    $availableUnits = $property->units->where('status','available')->count();
    $occupancyPct   = $totalUnits > 0 ? round(($rentedUnits + $soldUnits) / $totalUnits * 100, 1) : 0;
    $monthlyRentTotal = $property->units->filter(fn($u) => $u->activeRentalContract)->sum(fn($u) => $u->activeRentalContract->monthly_rent);
    $activeContracts  = $property->units->filter(fn($u) => $u->activeRentalContract)->map(fn($u) => $u->activeRentalContract);
@endphp

{{-- Property Block Header --}}
<div class="blk-hdr">
    {{ $property->name }}
    @if($property->code) &nbsp;&bull;&nbsp; {{ $property->code }} @endif
    &nbsp;&bull;&nbsp; {{ $property->typeLabel() }}
    &nbsp;&bull;&nbsp; {{ $property->address }}@if($property->city), {{ $property->city }}@endif
    &nbsp;&bull;&nbsp; المالك: {{ $property->owner?->user?->name ?? 'الشركة' }}
    &nbsp;&bull;&nbsp; الموظف: {{ $property->employee?->name ?? '—' }}
</div>

@if($property->electricity_account_number || $property->water_account_number)
<p class="sm" style="margin:0 0 7px; color:#475569;">
    ح. الكهرباء: <strong>{{ $property->electricity_account_number ?? '—' }}</strong>
    &nbsp;&nbsp;&bull;&nbsp;&nbsp;
    ح. الماء: <strong>{{ $property->water_account_number ?? '—' }}</strong>
</p>
@endif

{{-- Property Mini-Summary Cards --}}
<table class="cards" style="margin-bottom:10px;">
    <tr>
        <td class="card cgray" style="width:16%;">
            <div class="clbl">الوحدات</div>
            <div class="cval vgray" style="font-size:12pt;">{{ $totalUnits }}</div>
            <div class="cunit">{{ $rentedUnits }} مؤجرة &bull; {{ $availableUnits }} متاحة</div>
        </td>
        <td width="5"></td>
        <td class="card cblue" style="width:16%;">
            <div class="clbl">الإشغال</div>
            <div class="cval vblue" style="font-size:12pt;">{{ $occupancyPct }}%</div>
            <div class="cunit">{{ $rentedUnits + $soldUnits }} مشغولة</div>
        </td>
        <td width="5"></td>
        <td class="card cgreen" style="width:16%;">
            <div class="clbl">الإيرادات</div>
            <div class="cval vgreen" style="font-size:12pt;">{{ number_format($propRevenue) }}</div>
            <div class="cunit">ر.ع &bull; {{ $propPaid }} دفعة</div>
        </td>
        <td width="5"></td>
        <td class="card cred" style="width:16%;">
            <div class="clbl">المصروفات</div>
            <div class="cval vred" style="font-size:12pt;">{{ number_format($propExpTotal) }}</div>
            <div class="cunit">ر.ع &bull; {{ $propExpenses->count() }} بند</div>
        </td>
        <td width="5"></td>
        <td class="card {{ $propNet >= 0 ? 'cgreen' : 'cred' }}" style="width:16%;">
            <div class="clbl">صافي الربح</div>
            <div class="cval {{ $propNet >= 0 ? 'vgreen' : 'vred' }}" style="font-size:12pt;">{{ number_format($propNet) }}</div>
            <div class="cunit">ر.ع</div>
        </td>
        <td width="5"></td>
        <td class="card camber" style="width:16%;">
            <div class="clbl">متأخر / معلق</div>
            <div class="cval vamber" style="font-size:12pt;">{{ $propOverdue + $propPending }}</div>
            <div class="cunit">{{ $propOverdue }} متأخرة &bull; {{ $propPending }} معلقة</div>
        </td>
    </tr>
</table>

{{-- Units Inventory --}}
<p class="blk-sub">جدول الوحدات والإشغال ({{ $totalUnits }} وحدة)</p>
<table class="tbl tbl-dark">
    <thead>
        <tr>
            <th style="width:9%;">رقم الوحدة</th>
            <th style="width:10%;">النوع</th>
            <th style="width:7%;">المساحة</th>
            <th style="width:8%;">غرف/حمام</th>
            <th style="width:9%;">الحالة</th>
            <th style="width:16%;">المستأجر / المشتري</th>
            <th style="width:10%;">الهاتف</th>
            <th style="width:10%;">بداية العقد</th>
            <th style="width:10%;">انتهاء العقد</th>
            <th style="width:11%;">الإيجار</th>
        </tr>
    </thead>
    <tbody>
        @forelse($property->units->sortBy('unit_number') as $unit)
        @php
            $ac = $unit->activeRentalContract;
            $sc = $unit->activeSaleContract;
            $dLeft = $ac?->end_date ? (int) now()->diffInDays($ac->end_date, false) : null;
        @endphp
        <tr>
            <td class="bold">{{ $unit->unit_number ?? '—' }}</td>
            <td>{{ $unit->typeLabel() }}</td>
            <td>{{ $unit->area ? number_format($unit->area).' م²' : '—' }}</td>
            <td style="text-align:center;">{{ $unit->bedrooms ?? '—' }}/{{ $unit->bathrooms ?? '—' }}</td>
            <td>
                @if($unit->status==='rented')    <span class="bg bg-blue">مؤجرة</span>
                @elseif($unit->status==='sold')   <span class="bg bg-green">مباعة</span>
                @elseif($unit->status==='available') <span class="bg bg-gray">متاحة</span>
                @else <span class="bg bg-amber">{{ $unit->statusLabel() }}</span>
                @endif
            </td>
            <td>
                @if($ac) {{ $ac->tenant?->user?->name ?? '—' }}
                @elseif($sc) {{ $sc->buyer?->user?->name ?? '—' }}
                @else <span class="muted">—</span>
                @endif
            </td>
            <td class="sm">
                @if($ac) {{ $ac->tenant?->user?->phone ?? $ac->tenant?->phone ?? '—' }}
                @elseif($sc) {{ $sc->buyer?->user?->phone ?? '—' }}
                @else <span class="muted">—</span>
                @endif
            </td>
            <td class="sm">{{ $ac?->start_date?->format('Y/m/d') ?? ($sc?->contract_date?->format('Y/m/d') ?? '—') }}</td>
            <td class="sm">
                @if($ac?->end_date)
                    {{ $ac->end_date->format('Y/m/d') }}
                    @if($dLeft!==null && $dLeft>=0 && $dLeft<=30) <span class="bg bg-amber">{{ $dLeft }}ي</span>
                    @elseif($dLeft!==null && $dLeft<0) <span class="bg bg-red">منتهي</span>
                    @endif
                @else <span class="muted">—</span>
                @endif
            </td>
            <td class="bold">
                @if($ac) {{ number_format($ac->monthly_rent) }} ر.ع
                @elseif($sc) {{ number_format($sc->sale_price??0) }} ر.ع
                @else <span class="muted">—</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="10" style="text-align:center;" class="muted">لا توجد وحدات</td></tr>
        @endforelse
        @if($monthlyRentTotal > 0)
        <tr class="tr-tot">
            <td colspan="9">إجمالي الإيجار الشهري الحالي</td>
            <td class="pos">{{ number_format($monthlyRentTotal) }} ر.ع</td>
        </tr>
        @endif
    </tbody>
</table>

{{-- Active Contracts Detail --}}
@if($activeContracts->count())
<p class="blk-sub">تفاصيل عقود الإيجار النشطة</p>
<table class="tbl tbl-dark">
    <thead>
        <tr>
            <th>الوحدة</th><th>المستأجر</th><th>رقم الهوية</th>
            <th>بداية العقد</th><th>انتهاء العقد</th>
            <th>الإيجار الشهري</th><th>التأمين</th>
            <th>ح. كهرباء</th><th>ح. ماء</th>
        </tr>
    </thead>
    <tbody>
        @foreach($activeContracts->sortBy(fn($c)=>$c->unit?->unit_number) as $c)
        @php $dLeft2 = $c->end_date ? (int)now()->diffInDays($c->end_date,false) : null; @endphp
        <tr>
            <td class="bold">{{ $c->unit?->unit_number ?? '—' }}</td>
            <td>{{ $c->tenant?->user?->name ?? '—' }}</td>
            <td class="sm">{{ $c->tenant?->national_id ?? '—' }}</td>
            <td class="sm">{{ $c->start_date?->format('Y/m/d') ?? '—' }}</td>
            <td class="sm">
                {{ $c->end_date?->format('Y/m/d') ?? '—' }}
                @if($dLeft2!==null && $dLeft2>=0 && $dLeft2<=30) <span class="bg bg-amber">{{ $dLeft2 }}ي</span>
                @elseif($dLeft2!==null && $dLeft2<0) <span class="bg bg-red">منتهي</span>
                @endif
            </td>
            <td class="pos bold">{{ number_format($c->monthly_rent) }} ر.ع</td>
            <td>{{ number_format($c->deposit??0) }} ر.ع</td>
            <td class="sm">{{ $c->electricity_account_number ?? '—' }}</td>
            <td class="sm">{{ $c->water_account_number ?? '—' }}</td>
        </tr>
        @endforeach
        <tr class="tr-tot">
            <td colspan="5">إجمالي الإيجار الشهري / التأمينات</td>
            <td class="pos">{{ number_format($activeContracts->sum('monthly_rent')) }} ر.ع</td>
            <td class="bold">{{ number_format($activeContracts->sum('deposit')) }} ر.ع</td>
            <td colspan="2"></td>
        </tr>
    </tbody>
</table>
@endif

{{-- Payments --}}
@if($propPayments->count())
<p class="blk-sub">سجل المدفوعات ({{ $propPayments->count() }} دفعة)</p>
<table class="tbl tbl-dark">
    <thead>
        <tr><th style="width:8%;">الوحدة</th><th>المستأجر</th><th>الشهر/السنة</th><th>المبلغ</th><th>الحالة</th><th>تاريخ الدفع</th></tr>
    </thead>
    <tbody>
        @foreach($propPayments as $p)
        @php $pbg = match($p->status){'paid'=>'bg-green','pending'=>'bg-amber','overdue'=>'bg-red',default=>'bg-gray'}; @endphp
        <tr>
            <td class="bold">{{ $p->rentalContract?->unit?->unit_number ?? '—' }}</td>
            <td>{{ $p->tenant?->user?->name ?? '—' }}</td>
            <td>{{ $p->month }}/{{ $p->year }}</td>
            <td>{{ number_format($p->amount) }} ر.ع</td>
            <td><span class="bg {{ $pbg }}">{{ $p->statusLabel() }}</span></td>
            <td class="sm">{{ $p->paid_at ? $p->paid_at->format('Y/m/d') : '—' }}</td>
        </tr>
        @endforeach
        <tr class="tr-tot">
            <td colspan="3">المحصّل / غير المحصّل</td>
            <td class="pos">{{ number_format($propRevenue) }} ر.ع</td>
            <td colspan="2" class="muted sm">{{ $propPending + $propOverdue }} غير محصّل</td>
        </tr>
    </tbody>
</table>
@else
<p class="sm muted" style="margin:4px 0 8px;">لا توجد مدفوعات في هذه الفترة.</p>
@endif

{{-- Expenses --}}
@if($propExpenses->count())
<p class="blk-sub">المصروفات ({{ $propExpenses->count() }} بند)</p>
<table class="tbl tbl-dark">
    <thead>
        <tr><th>البيان</th><th>الفئة</th><th>التاريخ</th><th>المبلغ</th><th>ملاحظات</th></tr>
    </thead>
    <tbody>
        @foreach($propExpenses->sortBy('expense_date') as $e)
        <tr>
            <td class="bold">{{ $e->title }}</td>
            <td>{{ $e->categoryLabel() }}</td>
            <td class="sm">{{ $e->expense_date->format('Y/m/d') }}</td>
            <td class="neg">{{ number_format($e->amount) }} ر.ع</td>
            <td class="sm muted">{{ mb_substr($e->description ?? '', 0, 55) }}</td>
        </tr>
        @endforeach
        <tr class="tr-tot">
            <td colspan="3">إجمالي المصروفات</td>
            <td class="neg">{{ number_format($propExpTotal) }} ر.ع</td>
            <td></td>
        </tr>
    </tbody>
</table>
@else
<p class="sm muted" style="margin:4px 0 8px;">لا توجد مصروفات في هذه الفترة.</p>
@endif

{{-- Maintenance --}}
@if($propMaintenance->count())
<p class="blk-sub">طلبات الصيانة ({{ $propMaintenance->count() }} طلب)</p>
<table class="tbl tbl-dark">
    <thead>
        <tr><th>العنوان</th><th>الوحدة</th><th>المستأجر</th><th>الأولوية</th><th>الحالة</th><th>التاريخ</th><th>الوصف</th></tr>
    </thead>
    <tbody>
        @foreach($propMaintenance->sortByDesc('created_at') as $mr)
        @php
            $mpb = match($mr->priority){'urgent'=>'bg-red','high'=>'bg-amber','medium'=>'bg-blue',default=>'bg-gray'};
            $msb = match($mr->status){'completed'=>'bg-green','in_progress'=>'bg-blue','rejected'=>'bg-red',default=>'bg-amber'};
        @endphp
        <tr>
            <td class="bold">{{ $mr->title }}</td>
            <td>{{ $mr->unit?->unit_number ?? '—' }}</td>
            <td>{{ $mr->tenant?->user?->name ?? '—' }}</td>
            <td><span class="bg {{ $mpb }}">{{ $mr->priorityLabel() }}</span></td>
            <td><span class="bg {{ $msb }}">{{ $mr->statusLabel() }}</span></td>
            <td class="sm">{{ $mr->created_at->format('Y/m/d') }}</td>
            <td class="sm muted">{{ mb_substr($mr->description ?? '', 0, 50) }}</td>
        </tr>
        @endforeach
        <tr class="tr-tot">
            <td colspan="4">مكتمل: {{ $propMaintenance->where('status','completed')->count() }} &bull; جاري: {{ $propMaintenance->where('status','in_progress')->count() }} &bull; معلق: {{ $propMaintenance->where('status','pending')->count() }}</td>
            <td colspan="3"></td>
        </tr>
    </tbody>
</table>
@else
<p class="sm muted" style="margin:4px 0 8px;">لا توجد طلبات صيانة في هذه الفترة.</p>
@endif

@if(!$loop->last) <div class="sep"></div> @endif
@endforeach

{{-- Overall Final Summary (multi-property) --}}
@if($properties->count() > 1)
<div class="sec">الملخص الختامي الإجمالي</div>
<table class="tbl">
    <tbody>
        <tr><td style="width:55%;">عدد العقارات المشمولة</td><td class="bold">{{ $mgPropCount }}</td><td class="muted sm"></td></tr>
        <tr><td>إجمالي الوحدات (كل العقارات)</td><td class="bold">{{ $properties->sum(fn($p)=>$p->units->count()) }}</td><td class="muted sm">{{ $properties->sum(fn($p)=>$p->units->where('status','rented')->count()) }} مؤجرة</td></tr>
        <tr><td>إجمالي الإيرادات المحصّلة</td><td class="pos">{{ number_format($mgRevenue) }} ر.ع</td><td class="muted sm"></td></tr>
        <tr><td>إجمالي المصروفات</td><td class="neg">{{ number_format($mgExpenses) }} ر.ع</td><td class="muted sm"></td></tr>
        <tr class="tr-tot"><td>صافي الربح الإجمالي</td><td class="{{ $mgNet>=0?'pos':'neg' }}">{{ number_format($mgNet) }} ر.ع</td><td class="muted sm">{{ $mgNet>=0?'فائض':'عجز' }}</td></tr>
        <tr><td>مدفوعات متأخرة / معلقة</td><td class="{{ ($mgOverdue+$mgPending)>0?'neg':'muted' }}">{{ $mgOverdue + $mgPending }}</td><td class="muted sm">{{ $mgOverdue }} متأخرة &bull; {{ $mgPending }} معلقة</td></tr>
        <tr><td>طلبات الصيانة (مكتمل / إجمالي)</td><td class="bold">{{ $mgMaintDone }} / {{ $mgMaintTotal }}</td><td class="muted sm"></td></tr>
    </tbody>
</table>
@endif

@else
{{-- ══════════════════════════════════════════════════════════════════
     OWNERS ASSOCIATION (HOA) SECTION
══════════════════════════════════════════════════════════════════ --}}
@php
    $duesTotal   = (float)($data['dues_total']        ?? 0);
    $duesPaid    = (float)($data['dues_paid']          ?? 0);
    $duesUnpaid  = (float)($data['dues_unpaid']        ?? 0);
    $duesWaived  = (float)($data['dues_waived']        ?? 0);
    $hoaExpenses = (float)($data['total_expenses']     ?? 0);
    $balance     = (float)($data['balance']            ?? 0);
    $assocCount  = (int)  ($data['associations_count'] ?? 0);
    $meetingsAll = (int)  ($data['meetings_count']     ?? 0);
    $paidPct     = $duesTotal > 0 ? round($duesPaid   / $duesTotal * 100, 1) : 0;
    $unpaidPct   = $duesTotal > 0 ? round($duesUnpaid / $duesTotal * 100, 1) : 0;
    $associations = $data['associations'] ?? collect();
    $expByProp    = $data['expenses_by_property'] ?? collect();
@endphp

{{-- Global Summary --}}
<div class="sec">الملخص المالي الإجمالي للفترة</div>
<table class="cards">
    <tr>
        <td class="card cgray" style="width:16%;">
            <div class="clbl">عدد الجمعيات</div>
            <div class="cval vgray">{{ $assocCount }}</div>
            <div class="cunit">جمعية مشمولة</div>
        </td>
        <td width="5"></td>
        <td class="card cblue" style="width:16%;">
            <div class="clbl">إجمالي الاشتراكات</div>
            <div class="cval vblue">{{ number_format($duesTotal) }}</div>
            <div class="cunit">ر.ع</div>
        </td>
        <td width="5"></td>
        <td class="card cgreen" style="width:16%;">
            <div class="clbl">المحصّل</div>
            <div class="cval vgreen">{{ number_format($duesPaid) }}</div>
            <div class="cunit">ر.ع — {{ $paidPct }}%</div>
        </td>
        <td width="5"></td>
        <td class="card cred" style="width:16%;">
            <div class="clbl">غير محصّل</div>
            <div class="cval vred">{{ number_format($duesUnpaid) }}</div>
            <div class="cunit">ر.ع — {{ $unpaidPct }}%</div>
        </td>
        <td width="5"></td>
        <td class="card cred" style="width:16%;">
            <div class="clbl">المصروفات</div>
            <div class="cval vred">{{ number_format($hoaExpenses) }}</div>
            <div class="cunit">ر.ع</div>
        </td>
        <td width="5"></td>
        <td class="card {{ $balance>=0?'cgreen':'cred' }}" style="width:16%;">
            <div class="clbl">صافي الرصيد</div>
            <div class="cval {{ $balance>=0?'vgreen':'vred' }}">{{ number_format($balance) }}</div>
            <div class="cunit">ر.ع — {{ $balance>=0?'فائض':'عجز' }}</div>
        </td>
    </tr>
</table>

{{-- ── Per-Association Blocks ── --}}
@foreach($associations as $assoc)
@php
    $assocDues     = $assoc->dues;
    $assocMeetings = $assoc->meetings;
    $assocExpenses = $expByProp[$assoc->property_id] ?? collect();

    $aDuesTotal  = $assocDues->sum('amount');
    $aDuesPaid   = $assocDues->where('status','paid')->sum('amount');
    $aDuesUnpaid = $assocDues->whereIn('status',['pending','overdue'])->sum('amount');
    $aDuesWaived = $assocDues->where('status','waived')->sum('amount');
    $aExpTotal   = $assocExpenses->sum('amount');
    $aBalance    = $aDuesPaid - $aExpTotal;
    $aPaidPct    = $aDuesTotal > 0 ? round($aDuesPaid / $aDuesTotal * 100, 1) : 0;
@endphp

{{-- Association Block Header --}}
<div class="blk-hdr" style="background:#f0fdfa; border-right-color:#0f766e; color:#0f766e;">
    {{ $assoc->name }}
    @if($assoc->property) &nbsp;&bull;&nbsp; عقار: {{ $assoc->property->name }} @endif
    @if($assoc->monthly_fee_per_unit) &nbsp;&bull;&nbsp; الرسوم الشهرية/وحدة: {{ number_format($assoc->monthly_fee_per_unit) }} ر.ع @endif
    @if($assoc->status === 'active') &nbsp;&bull;&nbsp; <span class="bg bg-green">نشطة</span> @endif
</div>

@if($assoc->electricity_account_number || $assoc->water_account_number)
<p class="sm" style="margin:0 0 7px; color:#475569;">
    ح. الكهرباء: <strong>{{ $assoc->electricity_account_number ?? '—' }}</strong>
    &nbsp;&nbsp;&bull;&nbsp;&nbsp;
    ح. الماء: <strong>{{ $assoc->water_account_number ?? '—' }}</strong>
</p>
@endif

{{-- Association Mini-Summary Cards --}}
<table class="cards" style="margin-bottom:10px;">
    <tr>
        <td class="card cblue" style="width:19%;">
            <div class="clbl">إجمالي الاشتراكات</div>
            <div class="cval vblue" style="font-size:12pt;">{{ number_format($aDuesTotal) }}</div>
            <div class="cunit">ر.ع &bull; {{ $assocDues->count() }} اشتراك</div>
        </td>
        <td width="5"></td>
        <td class="card cgreen" style="width:19%;">
            <div class="clbl">المحصّل</div>
            <div class="cval vgreen" style="font-size:12pt;">{{ number_format($aDuesPaid) }}</div>
            <div class="cunit">ر.ع — {{ $aPaidPct }}%</div>
        </td>
        <td width="5"></td>
        <td class="card cred" style="width:19%;">
            <div class="clbl">غير محصّل</div>
            <div class="cval vred" style="font-size:12pt;">{{ number_format($aDuesUnpaid) }}</div>
            <div class="cunit">ر.ع — {{ $assocDues->whereIn('status',['pending','overdue'])->count() }} اشتراك</div>
        </td>
        <td width="5"></td>
        <td class="card cred" style="width:19%;">
            <div class="clbl">المصروفات</div>
            <div class="cval vred" style="font-size:12pt;">{{ number_format($aExpTotal) }}</div>
            <div class="cunit">ر.ع &bull; {{ $assocExpenses->count() }} بند</div>
        </td>
        <td width="5"></td>
        <td class="card {{ $aBalance>=0?'cgreen':'cred' }}" style="width:19%;">
            <div class="clbl">صافي الرصيد</div>
            <div class="cval {{ $aBalance>=0?'vgreen':'vred' }}" style="font-size:12pt;">{{ number_format($aBalance) }}</div>
            <div class="cunit">ر.ع — {{ $aBalance>=0?'فائض':'عجز' }}</div>
        </td>
    </tr>
</table>

{{-- Dues Detail --}}
@if($assocDues->count())
<p class="blk-sub">تفاصيل الاشتراكات ({{ $assocDues->count() }} اشتراك)</p>
<table class="tbl tbl-teal">
    <thead>
        <tr>
            <th style="width:20%;">المالك</th>
            <th style="width:13%;">الفترة</th>
            <th style="width:11%;">تاريخ الاستحقاق</th>
            <th style="width:11%;">المبلغ</th>
            <th style="width:9%;">الحالة</th>
            <th style="width:11%;">تاريخ الدفع</th>
            <th>ملاحظات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($assocDues->sortBy('due_date') as $due)
        @php $dbg = match($due->status){'paid'=>'bg-green','overdue'=>'bg-red','waived'=>'bg-teal',default=>'bg-amber'}; @endphp
        <tr>
            <td class="bold">{{ $due->owner?->user?->name ?? '—' }}</td>
            <td>{{ $due->periodLabel() }}</td>
            <td class="sm">{{ $due->due_date?->format('Y/m/d') ?? '—' }}</td>
            <td>{{ number_format($due->amount) }} ر.ع</td>
            <td><span class="bg {{ $dbg }}">{{ $due->statusLabel() }}</span></td>
            <td class="sm">{{ $due->paid_at?->format('Y/m/d') ?? '—' }}</td>
            <td class="sm muted">{{ mb_substr($due->notes ?? '', 0, 50) }}</td>
        </tr>
        @endforeach
        <tr class="tr-tot">
            <td colspan="3">الإجمالي (محصّل / غير محصّل / معفى)</td>
            <td>{{ number_format($aDuesTotal) }} ر.ع</td>
            <td colspan="3" class="sm">
                <span class="pos">محصّل: {{ number_format($aDuesPaid) }}</span> &bull;
                <span class="neg">غير محصّل: {{ number_format($aDuesUnpaid) }}</span>
                @if($aDuesWaived > 0) &bull; معفى: {{ number_format($aDuesWaived) }} @endif
            </td>
        </tr>
    </tbody>
</table>
@else
<p class="sm muted" style="margin:4px 0 8px;">لا توجد اشتراكات في هذه الفترة.</p>
@endif

{{-- Owner Payment Status Summary --}}
@php
    $ownerGroups = $assocDues->groupBy(fn($d) => $d->owner?->user?->name ?? 'غير محدد');
@endphp
@if($ownerGroups->count() > 1)
<p class="blk-sub">ملخص حالة الدفع لكل مالك</p>
<table class="tbl tbl-teal">
    <thead>
        <tr><th>المالك</th><th>المبلغ المستحق</th><th>المدفوع</th><th>المتبقي</th><th>نسبة الدفع</th><th>الحالة العامة</th></tr>
    </thead>
    <tbody>
        @foreach($ownerGroups as $ownerName => $ownerDues)
        @php
            $owTotal  = $ownerDues->sum('amount');
            $owPaid   = $ownerDues->where('status','paid')->sum('amount');
            $owUnpaid = $ownerDues->whereIn('status',['pending','overdue'])->sum('amount');
            $owPct    = $owTotal > 0 ? round($owPaid / $owTotal * 100, 1) : 0;
            $hasOverdue = $ownerDues->where('status','overdue')->count() > 0;
        @endphp
        <tr>
            <td class="bold">{{ $ownerName }}</td>
            <td>{{ number_format($owTotal) }} ر.ع</td>
            <td class="pos">{{ number_format($owPaid) }} ر.ع</td>
            <td class="{{ $owUnpaid > 0 ? 'neg' : 'muted' }}">{{ number_format($owUnpaid) }} ر.ع</td>
            <td>{{ $owPct }}%</td>
            <td>
                @if($owPct >= 100)          <span class="bg bg-green">مكتمل</span>
                @elseif($hasOverdue)        <span class="bg bg-red">متأخر</span>
                @elseif($owUnpaid > 0)      <span class="bg bg-amber">جزئي</span>
                @else                       <span class="bg bg-gray">—</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Meetings --}}
@if($assocMeetings->count())
<p class="blk-sub">الاجتماعات ({{ $assocMeetings->count() }} اجتماع)</p>
<table class="tbl tbl-teal">
    <thead>
        <tr><th style="width:30%;">العنوان</th><th style="width:14%;">التاريخ</th><th style="width:16%;">الموقع</th><th style="width:10%;">الحالة</th><th>جدول الأعمال</th></tr>
    </thead>
    <tbody>
        @foreach($assocMeetings as $mtg)
        @php $mbg = match($mtg->status){'completed'=>'bg-green','cancelled'=>'bg-red',default=>'bg-blue'}; @endphp
        <tr>
            <td class="bold">{{ $mtg->title }}</td>
            <td class="sm">{{ $mtg->scheduled_at->format('Y/m/d H:i') }}</td>
            <td class="sm">{{ $mtg->location ?? '—' }}</td>
            <td><span class="bg {{ $mbg }}">{{ $mtg->statusLabel() }}</span></td>
            <td class="sm muted">{{ mb_substr($mtg->agenda ?? '', 0, 70) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p class="sm muted" style="margin:4px 0 8px;">لا توجد اجتماعات في هذه الفترة.</p>
@endif

{{-- Expenses --}}
@if($assocExpenses->count())
<p class="blk-sub">مصروفات العقار المرتبط ({{ $assocExpenses->count() }} بند)</p>
<table class="tbl tbl-teal">
    <thead>
        <tr><th>البيان</th><th>الفئة</th><th>التاريخ</th><th>المبلغ</th><th>ملاحظات</th></tr>
    </thead>
    <tbody>
        @foreach($assocExpenses->sortBy('expense_date') as $e)
        <tr>
            <td class="bold">{{ $e->title }}</td>
            <td>{{ $e->categoryLabel() }}</td>
            <td class="sm">{{ $e->expense_date->format('Y/m/d') }}</td>
            <td class="neg">{{ number_format($e->amount) }} ر.ع</td>
            <td class="sm muted">{{ mb_substr($e->description ?? '', 0, 55) }}</td>
        </tr>
        @endforeach
        <tr class="tr-tot">
            <td colspan="3">إجمالي المصروفات</td>
            <td class="neg">{{ number_format($aExpTotal) }} ر.ع</td>
            <td></td>
        </tr>
    </tbody>
</table>
@else
<p class="sm muted" style="margin:4px 0 8px;">لا توجد مصروفات مرتبطة في هذه الفترة.</p>
@endif

@if(!$loop->last) <div class="sep"></div> @endif
@endforeach

{{-- Overall HOA Final Summary --}}
@if($associations->count() > 1)
<div class="sec">الملخص الختامي لجميع الجمعيات</div>
<table class="tbl">
    <tbody>
        <tr><td style="width:55%;">عدد الجمعيات المشمولة</td><td class="bold">{{ $assocCount }}</td><td class="muted sm"></td></tr>
        <tr><td>إجمالي الاشتراكات المُصدَرة</td><td class="bold">{{ number_format($duesTotal) }} ر.ع</td><td class="muted sm"></td></tr>
        <tr><td>المبالغ المحصّلة</td><td class="pos">{{ number_format($duesPaid) }} ر.ع</td><td class="muted sm">{{ $paidPct }}% من الإجمالي</td></tr>
        <tr><td>المبالغ غير المحصّلة</td><td class="neg">{{ number_format($duesUnpaid) }} ر.ع</td><td class="muted sm">{{ $unpaidPct }}% من الإجمالي</td></tr>
        <tr><td>المبالغ المُعفاة</td><td>{{ number_format($duesWaived) }} ر.ع</td><td class="muted sm"></td></tr>
        <tr><td>إجمالي المصروفات</td><td class="neg">{{ number_format($hoaExpenses) }} ر.ع</td><td class="muted sm"></td></tr>
        <tr><td>عدد الاجتماعات</td><td class="bold">{{ $meetingsAll }}</td><td class="muted sm">خلال الفترة</td></tr>
        <tr class="tr-tot"><td>صافي الرصيد (محصّل – مصروفات)</td><td class="{{ $balance>=0?'pos':'neg' }}">{{ number_format($balance) }} ر.ع</td><td class="muted sm">{{ $balance>=0?'فائض':'عجز' }}</td></tr>
    </tbody>
</table>
@endif

@endif

{{-- ══════════════════════════════════════════════
     FOOTER
══════════════════════════════════════════════ --}}
<div class="footer">
    شركة ثروة للعقارات &mdash;
    تم توليد هذا التقرير تلقائياً بواسطة نظام ثروة
    في {{ now()->format('Y/m/d H:i') }}
</div>

</body>
</html>
