<!DOCTYPE html>
<html lang="{{ ($isAr ?? (app()->getLocale() === 'ar')) ? 'ar' : 'en' }}" dir="{{ ($isAr ?? (app()->getLocale() === 'ar')) ? 'rtl' : 'ltr' }}">
<head>
<meta charset="utf-8">
<title>{{ $development->name }}</title>
<style>
    @page { margin: 0 0 12mm 0; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: dejavusans, sans-serif; color: #1e293b; background: #fff; font-size: 9pt; line-height: 1.5; }

    /* ── Header ── */
    .hdr       { background: #fff; border-bottom: 3px solid #0d9488; padding: 14px 20px 12px; }
    .hdr-tbl   { width: 100%; border-collapse: collapse; }
    .hdr-logo  { max-height: 48px; max-width: 68px; }
    .hdr-title { font-size: 14pt; font-weight: bold; color: #0f172a; }
    .hdr-sub   { font-size: 8pt; color: #64748b; margin-top: 2px; }
    .hdr-co    { font-size: 9pt; font-weight: bold; color: #0f172a; }
    .hdr-date  { font-size: 7.5pt; color: #94a3b8; margin-top: 3px; }

    /* ── Body ── */
    .page { padding: 14px 20px; }

    /* ── Section heading ── */
    .sec { font-size: 10.5pt; font-weight: bold; color: #0d9488; border-bottom: 2px solid #0d9488; padding-bottom: 4px; margin: 16px 0 10px; }

    /* ── Tables ── */
    .tbl          { width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 10px; }
    .tbl thead th { background: #0d9488; color: #fff; padding: 6px 8px; text-align: right; font-weight: bold; font-size: 7.5pt; }
    .tbl tbody td { padding: 5px 8px; border-bottom: 1px solid #f1f5f9; }
    .tbl tbody tr:nth-child(even) td { background: #f8fafc; }
    .tbl .tr-tot td { background: #f0fdfa; font-weight: bold; border-top: 2px solid #99f6e4; }
    .al { text-align: left; }

    /* ── Badges ── */
    .badge { display: inline-block; padding: 1px 6px; border-radius: 3px; font-size: 7pt; font-weight: bold; }
    .b-teal   { background: #ccfbf1; color: #0f766e; }
    .b-blue   { background: #dbeafe; color: #1d4ed8; }
    .b-orange { background: #ffedd5; color: #c2410c; }
    .b-yellow { background: #fef9c3; color: #92400e; }
    .b-red    { background: #fee2e2; color: #991b1b; }
    .b-green  { background: #dcfce7; color: #166534; }
    .b-gray   { background: #f1f5f9; color: #475569; }
    .b-purple { background: #f3e8ff; color: #7e22ce; }

    /* ── Bar ── */
    .bar-bg   { background: #e2e8f0; height: 6px; border-radius: 3px; overflow: hidden; width: 100%; }
    .bar-fill { height: 6px; border-radius: 3px; }
    .bar-teal   { background: #0d9488; }
    .bar-orange { background: #f97316; }
    .bar-red    { background: #ef4444; }

    /* ── KPI table ── */
    .kpi-cell { border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; }
    .kpi-val  { font-size: 11pt; font-weight: bold; }
    .kpi-cur  { font-size: 7pt; font-weight: bold; margin-top: 1px; }
    .kpi-lbl  { font-size: 6.5pt; color: #94a3b8; text-transform: uppercase; letter-spacing: .3px; margin-top: 3px; }

    /* ── Health banner ── */
    .health { border-radius: 4px; padding: 8px 12px; margin-bottom: 14px; font-size: 8.5pt; font-weight: bold; }
    .on_track    { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .at_risk     { background: #fef9c3; color: #854d0e; border: 1px solid #fde68a; }
    .over_budget { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    /* ── Category block ── */
    .cat-block  { border: 1px solid #e2e8f0; border-radius: 4px; margin-bottom: 12px; overflow: hidden; }
    .cat-hdr    { background: #f1f5f9; padding: 7px 11px; border-bottom: 1px solid #e2e8f0; font-size: 8pt; }
    .cat-sub-b  { padding: 4px 11px; font-size: 7pt; font-weight: bold; text-transform: uppercase; background: #eff6ff; color: #1d4ed8; border-bottom: 1px solid #dbeafe; letter-spacing: .4px; }
    .cat-sub-o  { padding: 4px 11px; font-size: 7pt; font-weight: bold; text-transform: uppercase; background: #fff7ed; color: #c2410c; border-top: 1px solid #fed7aa; border-bottom: 1px solid #fed7aa; letter-spacing: .4px; }

    /* ── Contractor ── */
    .con-block { border: 1px solid #e2e8f0; border-radius: 4px; margin-bottom: 10px; overflow: hidden; }
    .con-hdr   { background: #f8fafc; padding: 9px 13px; border-bottom: 1px solid #f1f5f9; font-size: 8pt; }

    /* ── Utilities ── */
    .bold  { font-weight: bold; }
    .muted { color: #94a3b8; }
    .sm    { font-size: 7.5pt; }
    .pos   { color: #16a34a; font-weight: bold; }
    .neg   { color: #dc2626; font-weight: bold; }
    .ok    { color: #0d9488; }

    /* ── Footer ── */
    .footer     { margin-top: 14px; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    .footer-tbl { width: 100%; border-collapse: collapse; }
    .footer-co  { font-size: 8pt; font-weight: bold; color: #0d9488; }
    .footer-txt { font-size: 7pt; color: #9ca3af; margin-top: 2px; }
    .footer-logo{ max-height: 24px; max-width: 44px; opacity: .55; }
</style>
</head>
<body>
@php
    $isAr     = $isAr ?? (app()->getLocale() === 'ar');
    $tr       = fn (string $ar, string $en) => $isAr ? $ar : $en;
    $currency = $isAr ? 'ر.ع' : 'OMR';
    $totalSpent          = $development->totalSpent();
    $health              = $development->budgetHealth();
    $days                = $development->daysToCompletion();
    $phases              = \App\Models\DevelopmentProject::phases();
    $pIdx                = $development->phaseIndex();
    $totalActualExpenses = (float)$development->expenses->sum('amount');
    $unallocated         = max(0, (float)$development->total_budget - $totalSpent);
    $expGroups           = $development->expenses->groupBy('category');
    $logoPath            = public_path('img/logo.png');

    $phaseLabels = $isAr
        ? ['planning'=>'تخطيط','foundation'=>'أساسات','structure'=>'هيكل','finishing'=>'تشطيب','handover'=>'تسليم','completed'=>'مكتمل']
        : ['planning'=>'Planning','foundation'=>'Foundation','structure'=>'Structure','finishing'=>'Finishing','handover'=>'Handover','completed'=>'Completed'];

    $statusBadge = ['planning'=>'b-gray','foundation'=>'b-orange','structure'=>'b-yellow','finishing'=>'b-blue','handover'=>'b-teal','completed'=>'b-green'];
    $docBadge    = ['contract'=>'b-blue','invoice'=>'b-orange','other'=>'b-gray'];

    $healthMsg = [
        'on_track'    => $tr('المشروع في المسار الصحيح', 'Project is on track'),
        'at_risk'     => $tr('تحذير: الإنفاق يتجاوز تقدم الأعمال', 'Warning: spending exceeds progress'),
        'over_budget' => $tr('تنبيه: الإنفاق يتجاوز الميزانية', 'Alert: spending exceeds budget'),
    ];
@endphp

{{-- ══ HEADER ══ --}}
<div class="hdr">
    <table class="hdr-tbl">
        <tr>
            <td style="vertical-align:middle; width:62%;">
                <div class="hdr-title">{{ $development->name }}</div>
                <div class="hdr-sub">{{ $tr('تقرير المشروع', 'Project Report') }} &bull; {{ $development->typeLabel($isAr) }} &bull; {{ $development->location }}</div>
            </td>
            <td style="text-align:left; vertical-align:middle; white-space:nowrap;">
                @if(file_exists($logoPath))
                <img src="{{ $logoPath }}" class="hdr-logo"><br>
                @endif
                <div class="hdr-co" style="margin-top:4px;">{{ $tr('شركة ثروة للعقارات','Tharwa Real Estate') }}</div>
                <div class="hdr-date">{{ now()->format('Y/m/d') }}</div>
            </td>
        </tr>
    </table>
</div>

<div class="page">

{{-- ══ META ROW ══ --}}
<table style="width:100%;border-collapse:collapse;font-size:8pt;margin-bottom:12px;background:#f8fafc;border:1px solid #e2e8f0;">
    <tr>
        <td style="padding:7px 11px;border-right:1px solid #e2e8f0;">
            <div class="muted sm">{{ $tr('مدير المشروع','Project Manager') }}</div>
            <div class="bold">{{ $development->project_manager_name ?: '—' }}</div>
        </td>
        <td style="padding:7px 11px;border-right:1px solid #e2e8f0;">
            <div class="muted sm">{{ $tr('تاريخ البدء','Start Date') }}</div>
            <div class="bold">{{ $development->start_date?->format('Y/m/d') ?? '—' }}</div>
        </td>
        <td style="padding:7px 11px;border-right:1px solid #e2e8f0;">
            <div class="muted sm">{{ $tr('الإنجاز المتوقع','Est. Completion') }}</div>
            <div class="bold">{{ $development->estimated_completion_date?->format('Y/m/d') ?? '—' }}</div>
        </td>
        <td style="padding:7px 11px;border-right:1px solid #e2e8f0;">
            <div class="muted sm">{{ $tr('الحالة','Status') }}</div>
            <span class="badge {{ $statusBadge[$development->status] ?? 'b-gray' }}">{{ $development->statusLabel($isAr) }}</span>
        </td>
        <td style="padding:7px 11px;text-align:center;">
            <div style="font-size:13pt;font-weight:bold;color:#0d9488;">{{ $development->progress_percentage }}%</div>
            <div class="muted sm">{{ $tr('نسبة الإنجاز','Progress') }}</div>
        </td>
    </tr>
</table>

{{-- ══ KPI CARDS ══ --}}
<table style="width:100%;border-collapse:collapse;margin-bottom:12px;">
    <tr>
        <td class="kpi-cell" style="border-top:3px solid #0d9488;background:#f0fdfa;">
            <div class="kpi-val">{{ number_format($development->total_budget) }}</div>
            <div class="kpi-cur ok">{{ $currency }}</div>
            <div class="kpi-lbl">{{ $tr('إجمالي الميزانية','Total Budget') }}</div>
        </td>
        <td width="3"></td>
        <td class="kpi-cell" style="border-top:3px solid #f97316;background:#fff7ed;">
            <div class="kpi-val" style="color:#f97316;">{{ number_format($totalActualExpenses) }}</div>
            <div class="kpi-cur" style="color:#f97316;">{{ $currency }}</div>
            <div class="kpi-lbl">{{ $tr('إجمالي المصروف','Total Spent') }}</div>
        </td>
        <td width="3"></td>
        <td class="kpi-cell" style="border-top:3px solid {{ $development->remaining() >= 0 ? '#22c55e' : '#ef4444' }};background:{{ $development->remaining() >= 0 ? '#f0fdf4' : '#fff1f2' }};">
            <div class="kpi-val" style="color:{{ $development->remaining() >= 0 ? '#16a34a' : '#dc2626' }};">{{ number_format(abs($development->remaining())) }}</div>
            <div class="kpi-cur muted">{{ $currency }}</div>
            <div class="kpi-lbl">{{ $development->remaining() >= 0 ? $tr('المتبقي','Remaining') : $tr('تجاوز','Over Budget') }}</div>
        </td>
        <td width="3"></td>
        <td class="kpi-cell" style="border-top:3px solid #3b82f6;background:#eff6ff;">
            <div class="kpi-val" style="color:#3b82f6;">{{ $development->budgetUsedPercent() }}%</div>
            <div class="kpi-cur muted">&nbsp;</div>
            <div class="kpi-lbl">{{ $tr('الميزانية المستهلكة','Budget Used') }}</div>
        </td>
        <td width="3"></td>
        <td class="kpi-cell" style="border-top:3px solid #a855f7;background:#faf5ff;">
            <div class="kpi-val">{{ $development->expenses->count() }}</div>
            <div class="kpi-cur muted">&nbsp;</div>
            <div class="kpi-lbl">{{ $tr('بنود المصروفات','Expense Items') }}</div>
        </td>
        <td width="3"></td>
        <td class="kpi-cell" style="border-top:3px solid #0d9488;background:#f0fdfa;">
            <div class="kpi-val">{{ $development->contractors->count() }}</div>
            <div class="kpi-cur muted">&nbsp;</div>
            <div class="kpi-lbl">{{ $tr('المقاولون','Contractors') }}</div>
        </td>
        <td width="3"></td>
        <td class="kpi-cell" style="border-top:3px solid {{ $days < 0 ? '#ef4444' : '#22c55e' }};background:{{ $days < 0 ? '#fff1f2' : '#f0fdf4' }};">
            <div class="kpi-val" style="color:{{ $days < 0 ? '#dc2626' : '#16a34a' }};">{{ abs($days) }}</div>
            <div class="kpi-cur muted">&nbsp;</div>
            <div class="kpi-lbl">{{ $days < 0 ? $tr('يوم تأخر','Days Overdue') : $tr('يوم متبقي','Days Left') }}</div>
        </td>
    </tr>
</table>

{{-- ══ HEALTH BANNER ══ --}}
<div class="health {{ $health }}">
    {{ $healthMsg[$health] }}
    &nbsp;&nbsp;&bull;&nbsp;&nbsp;
    {{ $tr('الإنجاز:','Progress:') }} {{ $development->progress_percentage }}%
    &nbsp;&bull;&nbsp;
    {{ $tr('الميزانية المستهلكة:','Budget used:') }} {{ $development->budgetUsedPercent() }}%
</div>

{{-- ══ 1: PHASE TIMELINE ══ --}}
<div class="sec">1 — {{ $tr('مراحل المشروع','Project Phases') }}</div>
<table style="width:100%;border-collapse:collapse;margin-bottom:14px;font-size:8pt;">
    <tr>
        @foreach($phases as $i => $phase)
        <td style="text-align:center;vertical-align:top;padding:4px 3px;">
            <div style="width:24px;height:24px;margin:0 auto 4px;border-radius:4px;
                background:{{ $pIdx > $i ? '#0d9488' : ($pIdx === $i ? '#2563eb' : '#e2e8f0') }};
                color:{{ ($pIdx > $i || $pIdx === $i) ? '#fff' : '#94a3b8' }};
                font-weight:bold;font-size:9pt;text-align:center;line-height:24px;">
                {{ $pIdx > $i ? '✓' : $i + 1 }}
            </div>
            <div style="font-size:7pt;color:{{ $pIdx > $i ? '#0d9488' : ($pIdx === $i ? '#2563eb' : '#94a3b8') }};">{{ $phaseLabels[$phase] }}</div>
        </td>
        @if($i < count($phases) - 1)
        <td style="vertical-align:middle;padding-bottom:16px;width:20px;">
            <div style="height:2px;background:{{ $pIdx > $i ? '#0d9488' : '#e2e8f0' }};margin:0 1px;"></div>
        </td>
        @endif
        @endforeach
    </tr>
</table>
@if($development->notes)
<div style="background:#f0fdfa;border-right:3px solid #0d9488;padding:7px 11px;font-size:8pt;color:#0f766e;margin-bottom:12px;">
    <strong>{{ $tr('ملاحظات:','Notes:') }}</strong> {{ $development->notes }}
</div>
@endif

{{-- ══ 2: BUDGET ANALYSIS ══ --}}
<div class="sec">2 — {{ $tr('تحليل الميزانية','Budget Analysis') }}</div>

{{-- Overall utilization --}}
<table style="width:100%;border-collapse:collapse;background:#f8fafc;border:1px solid #e2e8f0;margin-bottom:4px;font-size:8pt;">
    <tr>
        <td style="padding:8px 12px;border-right:1px solid #e2e8f0;">
            {{ $tr('الميزانية الكلية:','Total Budget:') }}
            <strong style="color:#0f172a;">{{ number_format($development->total_budget) }} {{ $currency }}</strong>
        </td>
        <td style="padding:8px 12px;border-right:1px solid #e2e8f0;">
            {{ $tr('المخصص:','Allocated:') }}
            <strong style="color:#3b82f6;">{{ number_format($totalSpent) }} {{ $currency }}</strong>
        </td>
        <td style="padding:8px 12px;border-right:1px solid #e2e8f0;">
            {{ $tr('الفعلي:','Actual:') }}
            <strong style="color:#f97316;">{{ number_format($totalActualExpenses) }} {{ $currency }}</strong>
        </td>
        <td style="padding:8px 12px;text-align:left;font-weight:bold;color:{{ $development->budgetUsedPercent() > 100 ? '#dc2626' : '#0d9488' }};">
            {{ $development->budgetUsedPercent() }}%
        </td>
    </tr>
</table>
<div style="margin-bottom:14px;">
    <div class="bar-bg" style="height:8px;">
        <div class="bar-fill {{ $development->budgetUsedPercent() > 100 ? 'bar-red' : ($development->budgetUsedPercent() > 90 ? 'bar-orange' : 'bar-teal') }}"
             style="width:{{ min(100,$development->budgetUsedPercent()) }}%;"></div>
    </div>
</div>

{{-- Per-category --}}
@foreach($categories as $catIdx => $cat)
@php
    $allocated   = $development->categoryBudget($cat);
    $actualSpent = (float)($expenseByCategory->get($cat)?->total ?? 0);
    $catItems    = $expGroups->get($cat, collect())->sortByDesc('expense_date');
    $budgetItems = $development->categoryItems($cat);
    $variance    = $allocated - $actualSpent;
    $utilization = $allocated > 0 ? min(200, round(($actualSpent / $allocated) * 100)) : ($actualSpent > 0 ? 100 : 0);
    $utilColor   = $utilization > 100 ? '#dc2626' : ($utilization > 90 ? '#ea580c' : '#0d9488');
@endphp
@if($allocated > 0 || $catItems->isNotEmpty())
<div class="cat-block">
    <div class="cat-hdr">
        <table style="width:100%;border-collapse:collapse;font-size:8pt;">
            <tr>
                <td>
                    <span class="muted sm">{{ $catIdx + 1 }}.</span>
                    <span class="badge b-blue" style="margin:0 4px;">{{ $catLabels[$cat] }}</span>
                    @if(!empty($budgetItems))
                    <span class="muted sm">{{ count($budgetItems) }} {{ $tr('بند مخطط','planned') }}</span>
                    @endif
                </td>
                <td style="text-align:left;white-space:nowrap;font-size:7.5pt;">
                    @if($allocated > 0)
                    <span style="color:#3b82f6;">{{ $tr('مخصص:','Alloc:') }} <strong>{{ number_format($allocated,2) }}</strong></span>&nbsp;
                    @endif
                    @if($actualSpent > 0)
                    <span style="color:#f97316;">{{ $tr('فعلي:','Actual:') }} <strong>{{ number_format($actualSpent,2) }}</strong></span>&nbsp;
                    @endif
                    @if($allocated > 0)
                    <strong style="color:{{ $variance >= 0 ? '#16a34a' : '#dc2626' }};">{{ $variance >= 0 ? '+' : '' }}{{ number_format($variance,2) }} {{ $currency }}</strong>
                    @endif
                </td>
            </tr>
        </table>
        @if($allocated > 0)
        <table style="width:100%;border-collapse:collapse;margin-top:5px;font-size:7.5pt;">
            <tr>
                <td style="width:85%;">
                    <div class="bar-bg"><div class="bar-fill" style="width:{{ min(100,$utilization) }}%;background:{{ $utilColor }};"></div></div>
                </td>
                <td style="text-align:left;padding-left:6px;font-weight:bold;color:{{ $utilColor }};width:35px;">{{ $utilization }}%</td>
            </tr>
        </table>
        @endif
    </div>

    @if(!empty($budgetItems))
    <div class="cat-sub-b">{{ $tr('تفصيل الميزانية المخططة','Planned Budget Breakdown') }}</div>
    <table class="tbl">
        <thead>
            <tr>
                <th style="width:22px;">#</th>
                <th>{{ $tr('اسم البند','Item Name') }}</th>
                <th class="al">{{ $tr('المبلغ المخصص','Allocated Amount') }}</th>
                <th class="al">{{ $tr('% من الفئة','% of Category') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($budgetItems as $bIdx => $bItem)
            <tr>
                <td class="muted sm">{{ $bIdx + 1 }}</td>
                <td class="bold">{{ $bItem['name'] ?: '—' }}</td>
                <td class="al bold" style="color:#3b82f6;">{{ number_format((float)$bItem['amount'],2) }} {{ $currency }}</td>
                <td class="al muted sm">{{ $allocated > 0 ? round(((float)$bItem['amount']/$allocated)*100,1) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="tr-tot">
                <td colspan="2" class="al">{{ $tr('إجمالي التخصيص','Total Allocated') }}</td>
                <td class="al" style="color:#3b82f6;">{{ number_format($allocated,2) }} {{ $currency }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @endif

    @if($catItems->isNotEmpty())
    @if(!empty($budgetItems))<div class="cat-sub-o">{{ $tr('المصروفات الفعلية المسجلة','Actual Recorded Expenses') }}</div>@endif
    <table class="tbl">
        <thead>
            <tr>
                <th style="width:22px;">#</th>
                <th>{{ $tr('الصنف / البند','Item / Description') }}</th>
                <th class="al">{{ $tr('الكمية','Qty') }}</th>
                <th class="al">{{ $tr('سعر الوحدة','Unit Cost') }}</th>
                <th class="al">{{ $tr('الإجمالي','Total') }}</th>
                <th>{{ $tr('التاريخ','Date') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($catItems as $idx => $exp)
            <tr>
                <td class="muted sm">{{ $idx + 1 }}</td>
                <td>
                    <div class="bold">{{ $exp->item_name }}</div>
                    @if($exp->description)<div class="muted sm">{{ $exp->description }}</div>@endif
                </td>
                <td class="al">{{ number_format((float)$exp->quantity,2) }}</td>
                <td class="al">{{ number_format((float)$exp->unit_cost,2) }} {{ $currency }}</td>
                <td class="al bold" style="color:#f97316;">{{ number_format((float)$exp->amount,2) }} {{ $currency }}</td>
                <td class="muted sm">{{ $exp->expense_date?->format('Y/m/d') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="tr-tot">
                <td colspan="4" class="al">{{ $tr('مجموع المصروفات الفعلية','Actual Expenses Subtotal') }}</td>
                <td class="al" style="color:#f97316;">{{ number_format($actualSpent,2) }} {{ $currency }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @elseif(empty($budgetItems))
    <div style="padding:8px 12px;" class="muted sm">{{ $tr('لم يتم تسجيل أي بنود لهذه الفئة بعد','No items logged for this category yet') }}</div>
    @endif
</div>
@endif
@endforeach

<table style="width:100%;border-collapse:collapse;background:#fff7ed;border:1px solid #fed7aa;margin-bottom:4px;">
    <tr>
        <td style="padding:9px 13px;font-weight:bold;color:#c2410c;font-size:9pt;">{{ $tr('الإجمالي الكلي للمصروفات','Grand Total — All Expenses') }}</td>
        <td style="padding:9px 13px;text-align:left;font-weight:bold;color:#f97316;font-size:11pt;">{{ number_format($totalActualExpenses,2) }} {{ $currency }}</td>
    </tr>
</table>
@if($unallocated > 0)
<div style="background:#eff6ff;border:1px solid #bfdbfe;padding:7px 12px;font-size:8pt;color:#1d4ed8;margin-bottom:14px;">
    {{ $tr('ميزانية غير موزعة:','Budget not yet allocated:') }}
    <strong>{{ number_format($unallocated,2) }} {{ $currency }} ({{ $development->total_budget > 0 ? round(($unallocated/$development->total_budget)*100,1) : 0 }}%)</strong>
</div>
@endif

{{-- ══ 3: MONTHLY SPENDING ══ --}}
@if($monthlyBreakdown->isNotEmpty())
<div class="sec">3 — {{ $tr('الصرف الشهري','Monthly Spending') }}</div>
<table class="tbl">
    <thead>
        <tr>
            <th>{{ $tr('الشهر','Month') }}</th>
            <th class="al">{{ $tr('المبلغ','Amount') }}</th>
            <th style="width:30%;">{{ $tr('التوزيع النسبي','Share') }}</th>
            <th class="al">{{ $tr('% من الإجمالي','% of Total') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($monthlyBreakdown as $row)
        @php $mpct = $totalActualExpenses > 0 ? round(($row->total / $totalActualExpenses) * 100) : 0; @endphp
        <tr>
            <td class="bold">{{ \Carbon\Carbon::createFromDate($row->yr, $row->mo, 1)->format('F Y') }}</td>
            <td class="al bold" style="color:#f97316;">{{ number_format($row->total) }} {{ $currency }}</td>
            <td><div class="bar-bg"><div class="bar-fill bar-teal" style="width:{{ $mpct }}%;"></div></div></td>
            <td class="al muted">{{ $mpct }}%</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="tr-tot">
            <td>{{ $tr('الإجمالي','Total') }}</td>
            <td class="al" style="color:#f97316;">{{ number_format($totalActualExpenses) }} {{ $currency }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>
@endif

{{-- ══ 4: CONTRACTORS & PAYMENTS ══ --}}
@if($development->contractors->isNotEmpty())
@php $secN = $monthlyBreakdown->isNotEmpty() ? 4 : 3; @endphp
<div class="sec">{{ $secN }} — {{ $tr('المقاولون والمدفوعات','Contractors & Payments') }}</div>
@foreach($development->contractors as $c)
@php $paid=$c->totalPaid(); $rem=$c->remaining(); $paidPct=$c->paidPercent(); @endphp
<div class="con-block">
    <div class="con-hdr">
        <table style="width:100%;border-collapse:collapse;font-size:8.5pt;">
            <tr>
                <td>
                    <div class="bold" style="font-size:9.5pt;">{{ $c->name }}</div>
                    <div class="muted sm">{{ Str::limit($c->scope_of_work, 90) }}</div>
                </td>
                <td style="text-align:center;width:80px;">
                    <div class="muted sm">{{ $tr('قيمة العقد','Contract') }}</div>
                    <div class="bold">{{ number_format($c->contract_value) }}</div>
                </td>
                <td style="text-align:center;width:80px;">
                    <div class="muted sm">{{ $tr('المدفوع','Paid') }}</div>
                    <div class="bold pos">{{ number_format($paid) }}</div>
                </td>
                <td style="text-align:center;width:80px;">
                    <div class="muted sm">{{ $tr('المتبقي','Remaining') }}</div>
                    <div class="bold" style="color:{{ $rem > 0 ? '#f97316' : '#16a34a' }};">{{ number_format($rem) }}</div>
                </td>
                <td style="text-align:center;width:60px;">
                    <span class="badge {{ $paidPct >= 100 ? 'b-green' : ($paidPct >= 50 ? 'b-teal' : 'b-yellow') }}">{{ $paidPct }}%</span>
                </td>
            </tr>
        </table>
        <div style="margin-top:5px;"><div class="bar-bg"><div class="bar-fill bar-teal" style="width:{{ min(100,$paidPct) }}%;"></div></div></div>
    </div>
    @if($c->payments->isNotEmpty())
    <table class="tbl">
        <thead>
            <tr>
                <th>{{ $tr('تاريخ الدفع','Payment Date') }}</th>
                <th>{{ $tr('الوصف','Description') }}</th>
                <th class="al">{{ $tr('المبلغ','Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($c->payments->sortByDesc('paid_at') as $pmt)
            <tr>
                <td class="muted sm">{{ \Carbon\Carbon::parse($pmt->paid_at)->format('Y/m/d') }}</td>
                <td>{{ $pmt->description ?: '—' }}</td>
                <td class="al bold pos">{{ number_format($pmt->amount) }} {{ $currency }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="tr-tot">
                <td colspan="2">{{ $tr('إجمالي المدفوع','Total Paid') }}</td>
                <td class="al pos">{{ number_format($paid) }} {{ $currency }}</td>
            </tr>
        </tfoot>
    </table>
    @else
    <div style="padding:8px 12px;" class="muted sm">{{ $tr('لا توجد مدفوعات مسجلة','No payments recorded yet') }}</div>
    @endif
</div>
@endforeach
<table style="width:100%;border-collapse:collapse;background:#f0fdf4;border:1px solid #bbf7d0;margin-bottom:14px;">
    <tr>
        <td style="padding:8px 13px;font-weight:bold;color:#166534;font-size:8.5pt;">{{ $tr('إجمالي المدفوع للمقاولين','Total Paid to Contractors') }}</td>
        <td style="padding:8px 13px;text-align:left;" class="pos">{{ number_format($development->contractors->sum(fn($c) => $c->totalPaid())) }} {{ $currency }}</td>
    </tr>
</table>
@endif

{{-- ══ 5: DOCUMENTS ══ --}}
@if($development->documents->isNotEmpty())
@php
    $docSecN = ($monthlyBreakdown->isNotEmpty() ? 4 : 3) + ($development->contractors->isNotEmpty() ? 1 : 0) + 1;
    $attachedCount = $development->documents->filter(fn($d) => in_array($d->type,['contract','invoice']) && $d->isPdf() && file_exists(storage_path('app/public/'.$d->file_path)))->count();
@endphp
<div class="sec">{{ $docSecN }} — {{ $tr('المستندات والعقود والفواتير','Documents, Contracts & Invoices') }}</div>
<table class="tbl">
    <thead>
        <tr>
            <th style="width:22px;">#</th>
            <th>{{ $tr('العنوان','Title') }}</th>
            <th>{{ $tr('النوع','Type') }}</th>
            <th>{{ $tr('اسم الملف','File Name') }}</th>
            <th>{{ $tr('تاريخ الرفع','Uploaded') }}</th>
            <th class="al">{{ $tr('مضمّن','Appended') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($development->documents->sortBy('type') as $dIdx => $doc)
        @php $isAttached = in_array($doc->type,['contract','invoice']) && $doc->isPdf() && file_exists(storage_path('app/public/'.$doc->file_path)); @endphp
        <tr>
            <td class="muted sm">{{ $dIdx + 1 }}</td>
            <td class="bold">{{ $doc->title }}</td>
            <td><span class="badge {{ $docBadge[$doc->type] ?? 'b-gray' }}">{{ $doc->typeLabel($isAr) }}</span></td>
            <td class="muted sm">{{ $doc->original_name }}</td>
            <td class="muted sm">{{ $doc->created_at->format('Y/m/d') }}</td>
            <td class="al bold" style="color:{{ $isAttached ? '#0d9488' : '#94a3b8' }};">{{ $isAttached ? '✓' : '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@if($attachedCount > 0)
<div style="background:#f0fdfa;border:1px solid #99f6e4;padding:7px 12px;font-size:8pt;color:#0f766e;margin-bottom:12px;">
    ✓ {{ $attachedCount }} {{ $tr('ملف(ات) عقد/فاتورة مضمّن(ة) بعد هذه الصفحة','contract/invoice file(s) appended after this page') }}
</div>
@endif
@endif

{{-- ══ FOOTER ══ --}}
<div class="footer">
    <table class="footer-tbl">
        <tr>
            <td>
                <div class="footer-co">{{ $tr('شركة ثروة للعقارات','Tharwa Real Estate') }}</div>
                <div class="footer-txt">{{ $development->name }} &mdash; {{ $tr('تقرير المشروع','Project Report') }} &mdash; {{ now()->format('Y/m/d H:i') }}</div>
            </td>
            @if(file_exists($logoPath))
            <td style="text-align:left;vertical-align:middle;width:54px;">
                <img src="{{ $logoPath }}" class="footer-logo">
            </td>
            @endif
        </tr>
    </table>
</div>

</div>
</body>
</html>
