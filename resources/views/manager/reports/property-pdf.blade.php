<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8">
<title>تقرير {{ $property->name }}</title>
<style>
    @page { margin: 14mm 12mm 14mm 12mm; }
    body  { font-family: dejavusans, sans-serif; font-size: 9pt; color: #1e293b; direction: rtl; margin:0; padding:0; }

    /* ── Header ── */
    .hdr       { background:#fff; border-bottom:3px solid #1e3a8a; padding:14px 18px 12px; margin-bottom:14px; }
    .hdr-tbl   { width:100%; border-collapse:collapse; }
    .hdr-logo  { max-height:50px; max-width:70px; }
    .hdr-title { font-size:15pt; font-weight:bold; color:#1e293b; margin:0 0 3px; }
    .hdr-sub   { font-size:8.5pt; color:#64748b; margin:0; }
    .hdr-right { font-size:8.5pt; color:#475569; text-align:left; vertical-align:middle; white-space:nowrap; }
    .hdr-co    { font-size:9.5pt; font-weight:bold; color:#1e293b; margin-bottom:3px; }
    .hdr-date  { font-size:7.5pt; color:#94a3b8; }

    /* ── Section title ── */
    .sec { font-size:11pt; font-weight:bold; color:#1e3a8a; border-bottom:2px solid #1e3a8a; padding-bottom:3px; margin:13px 0 8px; }

    /* ── Info table ── */
    .inf      { width:100%; border-collapse:collapse; margin-bottom:12px; }
    .inf td   { padding:4px 7px; border-bottom:1px solid #f1f5f9; font-size:9pt; }
    .inf .lbl { font-weight:bold; color:#374151; width:130px; }
    .inf .val { color:#4b5563; }

    /* ── Stat cards ── */
    .cards   { width:100%; border-collapse:collapse; margin-bottom:12px; }
    .card    { border:1px solid #e2e8f0; padding:9px 6px; text-align:center; vertical-align:top; }
    .cblue   { background:#eff6ff; border-color:#bfdbfe; }
    .cgreen  { background:#f0fdf4; border-color:#bbf7d0; }
    .cred    { background:#fff1f2; border-color:#fecdd3; }
    .camber  { background:#fffbeb; border-color:#fde68a; }
    .cgray   { background:#f9fafb; border-color:#e5e7eb; }
    .clbl    { font-size:7.5pt; color:#64748b; margin-bottom:4px; }
    .cval    { font-size:13pt; font-weight:bold; }
    .cunit   { font-size:7.5pt; color:#94a3b8; margin-top:2px; }
    .vblue   { color:#1d4ed8; }
    .vgreen  { color:#15803d; }
    .vred    { color:#b91c1c; }
    .vamber  { color:#b45309; }
    .vgray   { color:#374151; }

    /* ── Data tables ── */
    .tbl          { width:100%; border-collapse:collapse; margin-bottom:13px; font-size:8.5pt; }
    .tbl thead th { background:#1e3a8a; color:#fff; padding:6px 7px; text-align:right; font-weight:bold; }
    .tbl tbody td { padding:5px 7px; border-bottom:1px solid #f1f5f9; }
    .tbl tbody tr:nth-child(even) td { background:#f8fafc; }
    .tbl .tr-tot td { background:#eff6ff; font-weight:bold; border-top:2px solid #bfdbfe; }

    /* ── Badges ── */
    .bg  { padding:1px 6px; border-radius:5px; font-size:7.5pt; white-space:nowrap; }
    .bg-green { background:#dcfce7; color:#166534; }
    .bg-blue  { background:#dbeafe; color:#1d4ed8; }
    .bg-amber { background:#fef9c3; color:#92400e; }
    .bg-red   { background:#fee2e2; color:#b91c1c; }
    .bg-gray  { background:#f3f4f6; color:#374151; }

    /* ── Utilities ── */
    .pos  { color:#15803d; font-weight:bold; }
    .neg  { color:#b91c1c; font-weight:bold; }
    .muted{ color:#9ca3af; }
    .bold { font-weight:bold; }
    .sm   { font-size:7.5pt; }
    .divider { border:none; border-top:1px solid #e2e8f0; margin:10px 0; }

    /* ── Footer ── */
    .footer     { margin-top:18px; border-top:1px solid #e2e8f0; padding-top:8px; }
    .footer-tbl { width:100%; border-collapse:collapse; }
    .footer-co  { font-size:8pt; font-weight:bold; color:#1e3a8a; }
    .footer-txt { font-size:7pt; color:#9ca3af; margin-top:2px; }
    .footer-logo{ max-height:26px; max-width:46px; opacity:.55; }
</style>
</head>
<body>
@php
    $totalUnits     = $property->units->count();
    $rentedUnits    = $property->units->where('status','rented')->count();
    $soldUnits      = $property->units->where('status','sold')->count();
    $availableUnits = $property->units->where('status','available')->count();
    $occupancyPct   = $totalUnits > 0 ? round(($rentedUnits + $soldUnits) / $totalUnits * 100, 1) : 0;

    $totalRevenue   = $payments->where('status','paid')->sum('amount');
    $totalExpenses  = $expenses->sum('amount');
    $netIncome      = $totalRevenue - $totalExpenses;
    $paidCount      = $payments->where('status','paid')->count();
    $pendingCount   = $payments->where('status','pending')->count();
    $overdueCount   = $payments->where('status','overdue')->count();

    $monthlyRentTotal = $property->units
        ->filter(fn($u) => $u->activeRentalContract)
        ->sum(fn($u) => $u->activeRentalContract->monthly_rent);

    $activeContracts = $property->units
        ->filter(fn($u) => $u->activeRentalContract)
        ->map(fn($u) => $u->activeRentalContract);
@endphp

{{-- ── Header ── --}}
<div class="hdr">
    <table class="hdr-tbl">
        <tr>
            <td style="vertical-align:middle; width:60%;">
                <p class="hdr-title">تقرير العقار: {{ $property->name }}</p>
                <p class="hdr-sub">{{ $property->code }} &bull; {{ $property->typeLabel() }} &bull; {{ $property->purposeLabel() }} &bull; {{ $property->address }}@if($property->city), {{ $property->city }}@endif</p>
            </td>
            <td class="hdr-right">
                @if(file_exists(public_path('img/logo.png')))
                <img src="{{ public_path('img/logo.png') }}" class="hdr-logo"><br>
                @endif
                <div class="hdr-co" style="margin-top:4px;">شركة ثروة للتطوير العقاري</div>
                <div class="hdr-date">{{ now()->format('Y/m/d H:i') }}</div>
                <div class="hdr-date">الفترة: {{ $year }}@if($month) — شهر {{ $month }}@endif</div>
            </td>
        </tr>
    </table>
</div>

{{-- ── Property Details ── --}}
<div class="sec">بيانات العقار</div>
<table class="inf">
    <tr>
        <td class="lbl">الكود:</td><td class="val">{{ $property->code ?? '—' }}</td>
        <td class="lbl" style="padding-right:16px;">النوع / الغرض:</td><td class="val">{{ $property->typeLabel() }} / {{ $property->purposeLabel() }}</td>
    </tr>
    <tr>
        <td class="lbl">العنوان:</td><td class="val">{{ $property->address }}</td>
        <td class="lbl" style="padding-right:16px;">المدينة:</td><td class="val">{{ $property->city ?? '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">المالك:</td>
        <td class="val">{{ $property->owner?->user?->name ?? 'الشركة' }}
            @if($property->owner?->commission_rate) &nbsp;(عمولة {{ $property->owner->commission_rate }}%)@endif
        </td>
        <td class="lbl" style="padding-right:16px;">الموظف المسؤول:</td>
        <td class="val">{{ $property->employee?->name ?? '—' }}</td>
    </tr>
    @if($property->total_area || $property->floors)
    <tr>
        <td class="lbl">المساحة الكلية:</td><td class="val">{{ $property->total_area ? number_format($property->total_area).' م²' : '—' }}</td>
        <td class="lbl" style="padding-right:16px;">عدد الطوابق:</td><td class="val">{{ $property->floors ?? '—' }}</td>
    </tr>
    @endif
    @if($property->electricity_account_number || $property->water_account_number)
    <tr>
        <td class="lbl">حساب الكهرباء:</td><td class="val bold">{{ $property->electricity_account_number ?? '—' }}</td>
        <td class="lbl" style="padding-right:16px;">حساب الماء:</td><td class="val bold">{{ $property->water_account_number ?? '—' }}</td>
    </tr>
    @endif
    @if($property->description)
    <tr>
        <td class="lbl">الوصف:</td><td class="val" colspan="3">{{ $property->description }}</td>
    </tr>
    @endif
</table>

{{-- ── Summary Cards ── --}}
<div class="sec">الملخص المالي للفترة</div>
<table class="cards">
    <tr>
        <td class="card cgray" style="width:16%;">
            <div class="clbl">إجمالي الوحدات</div>
            <div class="cval vgray">{{ $totalUnits }}</div>
            <div class="cunit">{{ $rentedUnits }} مؤجرة &bull; {{ $availableUnits }} متاحة</div>
        </td>
        <td width="5"></td>
        <td class="card cblue" style="width:16%;">
            <div class="clbl">نسبة الإشغال</div>
            <div class="cval vblue">{{ $occupancyPct }}%</div>
            <div class="cunit">{{ $rentedUnits + $soldUnits }} وحدة مشغولة</div>
        </td>
        <td width="5"></td>
        <td class="card cgreen" style="width:16%;">
            <div class="clbl">الإيرادات المحصّلة</div>
            <div class="cval vgreen">{{ number_format($totalRevenue) }}</div>
            <div class="cunit">ر.ع &bull; {{ $paidCount }} دفعة</div>
        </td>
        <td width="5"></td>
        <td class="card cred" style="width:16%;">
            <div class="clbl">إجمالي المصروفات</div>
            <div class="cval vred">{{ number_format($totalExpenses) }}</div>
            <div class="cunit">ر.ع &bull; {{ $expenses->count() }} بند</div>
        </td>
        <td width="5"></td>
        <td class="card {{ $netIncome >= 0 ? 'cgreen' : 'cred' }}" style="width:16%;">
            <div class="clbl">صافي الربح</div>
            <div class="cval {{ $netIncome >= 0 ? 'vgreen' : 'vred' }}">{{ number_format($netIncome) }}</div>
            <div class="cunit">ر.ع</div>
        </td>
        <td width="5"></td>
        <td class="card camber" style="width:16%;">
            <div class="clbl">متأخر / معلق</div>
            <div class="cval vamber">{{ $overdueCount + $pendingCount }}</div>
            <div class="cunit">{{ $overdueCount }} متأخرة &bull; {{ $pendingCount }} معلقة</div>
        </td>
    </tr>
</table>

{{-- ── Units Inventory ── --}}
<div class="sec">جدول الوحدات والإشغال ({{ $totalUnits }} وحدة)</div>
<table class="tbl">
    <thead>
        <tr>
            <th style="width:8%;">رقم الوحدة</th>
            <th style="width:9%;">النوع</th>
            <th style="width:7%;">المساحة</th>
            <th style="width:8%;">غرف/حمام</th>
            <th style="width:9%;">الحالة</th>
            <th style="width:14%;">المستأجر / المشتري</th>
            <th style="width:11%;">رقم الهاتف</th>
            <th style="width:10%;">بداية العقد</th>
            <th style="width:10%;">انتهاء العقد</th>
            <th style="width:14%;">الإيجار الشهري</th>
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
            <td style="text-align:center;">{{ $unit->bedrooms ?? '—' }} / {{ $unit->bathrooms ?? '—' }}</td>
            <td>
                @if($unit->status === 'rented')    <span class="bg bg-blue">مؤجرة</span>
                @elseif($unit->status === 'sold')   <span class="bg bg-green">مباعة</span>
                @elseif($unit->status === 'available') <span class="bg bg-gray">متاحة</span>
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
                    @if($dLeft !== null && $dLeft >= 0 && $dLeft <= 30)
                        <span class="bg bg-amber">{{ $dLeft }}ي</span>
                    @elseif($dLeft !== null && $dLeft < 0)
                        <span class="bg bg-red">منتهي</span>
                    @endif
                @else <span class="muted">—</span>
                @endif
            </td>
            <td class="bold">
                @if($ac) {{ number_format($ac->monthly_rent) }} ر.ع
                @elseif($sc) {{ number_format($sc->sale_price ?? 0) }} ر.ع <span class="sm muted">(بيع)</span>
                @else <span class="muted">—</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="10" style="text-align:center;" class="muted">لا توجد وحدات</td></tr>
        @endforelse
        @if($monthlyRentTotal > 0)
        <tr class="tr-tot">
            <td colspan="9">إجمالي الإيجار الشهري (الوحدات المؤجرة الحالية)</td>
            <td class="pos">{{ number_format($monthlyRentTotal) }} ر.ع</td>
        </tr>
        @endif
    </tbody>
</table>

{{-- ── Active Rental Contracts Detail ── --}}
@if($activeContracts->count())
<div class="sec">تفاصيل عقود الإيجار النشطة ({{ $activeContracts->count() }} عقد)</div>
<table class="tbl">
    <thead>
        <tr>
            <th>الوحدة</th>
            <th>اسم المستأجر</th>
            <th>رقم الهوية</th>
            <th>الهاتف</th>
            <th>بداية العقد</th>
            <th>انتهاء العقد</th>
            <th>الإيجار الشهري</th>
            <th>التأمين</th>
            <th>ح. كهرباء</th>
            <th>ح. ماء</th>
        </tr>
    </thead>
    <tbody>
        @foreach($activeContracts->sortBy(fn($c) => $c->unit?->unit_number) as $c)
        @php $dLeft2 = $c->end_date ? (int) now()->diffInDays($c->end_date, false) : null; @endphp
        <tr>
            <td class="bold">{{ $c->unit?->unit_number ?? '—' }}</td>
            <td>{{ $c->tenant?->user?->name ?? '—' }}</td>
            <td class="sm">{{ $c->tenant?->national_id ?? '—' }}</td>
            <td class="sm">{{ $c->tenant?->user?->phone ?? $c->tenant?->phone ?? '—' }}</td>
            <td class="sm">{{ $c->start_date?->format('Y/m/d') ?? '—' }}</td>
            <td class="sm">
                {{ $c->end_date?->format('Y/m/d') ?? '—' }}
                @if($dLeft2 !== null && $dLeft2 >= 0 && $dLeft2 <= 30)
                    <span class="bg bg-amber">{{ $dLeft2 }}ي</span>
                @elseif($dLeft2 !== null && $dLeft2 < 0)
                    <span class="bg bg-red">منتهي</span>
                @endif
            </td>
            <td class="pos bold">{{ number_format($c->monthly_rent) }} ر.ع</td>
            <td>{{ number_format($c->deposit ?? 0) }} ر.ع</td>
            <td class="sm">{{ $c->electricity_account_number ?? '—' }}</td>
            <td class="sm">{{ $c->water_account_number ?? '—' }}</td>
        </tr>
        @endforeach
        <tr class="tr-tot">
            <td colspan="6">إجمالي الإيجار الشهري</td>
            <td class="pos">{{ number_format($activeContracts->sum('monthly_rent')) }} ر.ع</td>
            <td class="bold">{{ number_format($activeContracts->sum('deposit')) }} ر.ع</td>
            <td colspan="2"></td>
        </tr>
    </tbody>
</table>
@endif

{{-- ── Payment Records ── --}}
<div class="sec">سجل المدفوعات — {{ $year }}@if($month) / شهر {{ $month }}@endif &nbsp;({{ $payments->count() }} دفعة)</div>
<table class="tbl">
    <thead>
        <tr>
            <th style="width:8%;">الوحدة</th>
            <th style="width:18%;">المستأجر</th>
            <th style="width:10%;">الشهر / السنة</th>
            <th style="width:11%;">المبلغ</th>
            <th style="width:9%;">الحالة</th>
            <th style="width:11%;">تاريخ الدفع</th>
            <th>ملاحظات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($payments as $p)
        @php
            $pbg = match($p->status){ 'paid'=>'bg-green','pending'=>'bg-amber','overdue'=>'bg-red',default=>'bg-gray' };
        @endphp
        <tr>
            <td class="bold">{{ $p->rentalContract?->unit?->unit_number ?? '—' }}</td>
            <td>{{ $p->tenant?->user?->name ?? '—' }}</td>
            <td>{{ $p->month }}/{{ $p->year }}</td>
            <td>{{ number_format($p->amount) }} ر.ع</td>
            <td><span class="bg {{ $pbg }}">{{ $p->statusLabel() }}</span></td>
            <td class="sm">{{ $p->paid_at ? $p->paid_at->format('Y/m/d') : '—' }}</td>
            <td class="sm muted">{{ $p->notes ?? '' }}</td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;" class="muted">لا توجد مدفوعات في هذه الفترة</td></tr>
        @endforelse
        @if($payments->where('status','paid')->count())
        <tr class="tr-tot">
            <td colspan="3">الإجمالي المحصّل ({{ $paidCount }} دفعة)</td>
            <td class="pos">{{ number_format($totalRevenue) }} ر.ع</td>
            <td colspan="3"></td>
        </tr>
        @endif
        @if($overdueCount + $pendingCount > 0)
        <tr style="background:#fff7ed;">
            <td colspan="3" style="color:#92400e; font-weight:bold;">غير محصّل (متأخر + معلق)</td>
            <td style="color:#b91c1c; font-weight:bold;">{{ number_format($payments->whereIn('status',['pending','overdue'])->sum('amount')) }} ر.ع</td>
            <td colspan="3"></td>
        </tr>
        @endif
    </tbody>
</table>

{{-- ── Expenses ── --}}
<div class="sec">المصروفات ({{ $expenses->count() }} بند)</div>
<table class="tbl">
    <thead>
        <tr>
            <th style="width:28%;">البيان</th>
            <th style="width:15%;">الفئة</th>
            <th style="width:12%;">التاريخ</th>
            <th style="width:13%;">المبلغ</th>
            <th>الوصف / ملاحظات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($expenses->sortBy('expense_date') as $e)
        <tr>
            <td class="bold">{{ $e->title }}</td>
            <td>{{ $e->categoryLabel() }}</td>
            <td class="sm">{{ $e->expense_date->format('Y/m/d') }}</td>
            <td class="neg">{{ number_format($e->amount) }} ر.ع</td>
            <td class="sm muted">{{ mb_substr($e->description ?? '', 0, 70) }}</td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;" class="muted">لا توجد مصروفات في هذه الفترة</td></tr>
        @endforelse
        @if($expenses->count())
        <tr class="tr-tot">
            <td colspan="3">إجمالي المصروفات</td>
            <td class="neg">{{ number_format($totalExpenses) }} ر.ع</td>
            <td></td>
        </tr>
        @endif
    </tbody>
</table>

{{-- ── Maintenance Requests ── --}}
<div class="sec">طلبات الصيانة ({{ $maintenanceRequests->count() }} طلب)</div>
<table class="tbl">
    <thead>
        <tr>
            <th style="width:22%;">العنوان</th>
            <th style="width:7%;">الوحدة</th>
            <th style="width:14%;">المستأجر</th>
            <th style="width:9%;">الأولوية</th>
            <th style="width:9%;">الحالة</th>
            <th style="width:10%;">تاريخ الطلب</th>
            <th>الوصف</th>
        </tr>
    </thead>
    <tbody>
        @forelse($maintenanceRequests->sortByDesc('created_at') as $mr)
        @php
            $mpb = match($mr->priority){ 'urgent'=>'bg-red','high'=>'bg-amber','medium'=>'bg-blue',default=>'bg-gray' };
            $msb = match($mr->status){ 'completed'=>'bg-green','in_progress'=>'bg-blue','rejected'=>'bg-red',default=>'bg-amber' };
        @endphp
        <tr>
            <td class="bold">{{ $mr->title }}</td>
            <td>{{ $mr->unit?->unit_number ?? '—' }}</td>
            <td>{{ $mr->tenant?->user?->name ?? '—' }}</td>
            <td><span class="bg {{ $mpb }}">{{ $mr->priorityLabel() }}</span></td>
            <td><span class="bg {{ $msb }}">{{ $mr->statusLabel() }}</span></td>
            <td class="sm">{{ $mr->created_at->format('Y/m/d') }}</td>
            <td class="sm muted">{{ mb_substr($mr->description ?? '', 0, 60) }}</td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;" class="muted">لا توجد طلبات صيانة</td></tr>
        @endforelse
        @if($maintenanceRequests->count())
        <tr class="tr-tot">
            <td colspan="4">مكتمل: {{ $maintenanceRequests->where('status','completed')->count() }} &nbsp;&bull;&nbsp; قيد التنفيذ: {{ $maintenanceRequests->where('status','in_progress')->count() }} &nbsp;&bull;&nbsp; معلق: {{ $maintenanceRequests->where('status','pending')->count() }}</td>
            <td colspan="3"></td>
        </tr>
        @endif
    </tbody>
</table>

{{-- ── Final Summary ── --}}
<div class="sec">الملخص الختامي</div>
<table class="tbl">
    <tbody>
        <tr><td style="width:55%;">إجمالي الوحدات</td><td class="bold">{{ $totalUnits }} وحدة</td><td class="muted sm">{{ $rentedUnits }} مؤجرة &bull; {{ $soldUnits }} مباعة &bull; {{ $availableUnits }} متاحة</td></tr>
        <tr><td>نسبة الإشغال</td><td class="bold vblue">{{ $occupancyPct }}%</td><td class="muted sm">{{ $rentedUnits + $soldUnits }} من أصل {{ $totalUnits }}</td></tr>
        <tr><td>الإيجار الشهري الإجمالي (الوحدات النشطة)</td><td class="bold">{{ number_format($monthlyRentTotal) }} ر.ع</td><td class="muted sm">{{ $rentedUnits }} وحدة مؤجرة</td></tr>
        <tr><td>إجمالي الإيرادات المحصّلة في الفترة</td><td class="pos">{{ number_format($totalRevenue) }} ر.ع</td><td class="muted sm">{{ $paidCount }} دفعة مدفوعة</td></tr>
        <tr><td>المبالغ غير المحصّلة (متأخر + معلق)</td><td class="{{ $overdueCount+$pendingCount>0 ? 'neg' : 'muted' }}">{{ number_format($payments->whereIn('status',['pending','overdue'])->sum('amount')) }} ر.ع</td><td class="muted sm">{{ $overdueCount }} متأخرة &bull; {{ $pendingCount }} معلقة</td></tr>
        <tr><td>إجمالي المصروفات</td><td class="neg">{{ number_format($totalExpenses) }} ر.ع</td><td class="muted sm">{{ $expenses->count() }} بند مصروفات</td></tr>
        <tr class="tr-tot"><td>صافي الربح (الإيرادات – المصروفات)</td><td class="{{ $netIncome >= 0 ? 'pos' : 'neg' }}">{{ number_format($netIncome) }} ر.ع</td><td class="muted sm">{{ $netIncome >= 0 ? 'فائض' : 'عجز' }}</td></tr>
    </tbody>
</table>

<div class="footer">
    <table class="footer-tbl">
        <tr>
            <td>
                <div class="footer-co">شركة ثروة للتطوير العقاري</div>
                <div class="footer-txt">تقرير {{ $property->name }} ({{ $property->code }}) &mdash; الفترة: {{ $year }}@if($month) / شهر {{ $month }}@endif &mdash; تم التوليد: {{ now()->format('Y/m/d H:i') }}</div>
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
