<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $development->name }} — {{ app()->getLocale() === 'ar' ? 'تقرير المشروع' : 'Project Report' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: {{ app()->getLocale() === 'ar' ? "'Cairo'" : "'Sora'" }}, sans-serif; color: #1e293b; background: #f8fafc; font-size: 13px; line-height: 1.6; }
        .page { max-width: 960px; margin: 0 auto; padding: 40px 36px; background: #fff; }

        /* ── Toolbar ── */
        .toolbar { position: fixed; top: 0; left: 0; right: 0; z-index: 100; background: #0f172a; display: flex; align-items: center; gap: 8px; padding: 10px 24px; box-shadow: 0 2px 8px rgba(0,0,0,.3); }
        .toolbar a { color: #94a3b8; font-size: 12px; text-decoration: none; padding: 4px 10px; border-radius: 6px; border: 1px solid #334155; transition: background .15s; }
        .toolbar a:hover { background: #334155; color: #fff; }
        .toolbar button { background: #0d9488; color: #fff; border: none; padding: 6px 16px; border-radius: 8px; cursor: pointer; font-size: 12px; font-weight: 600; font-family: inherit; }
        .toolbar button:hover { background: #0f766e; }
        .toolbar .spacer { flex: 1; }

        /* ── Cover header ── */
        .cover { display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 3px solid #0d9488; }
        .cover-left h1 { font-size: 28px; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
        .cover-left .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 24px; margin-top: 10px; }
        .cover-left .meta-item { font-size: 12px; color: #64748b; }
        .cover-left .meta-item strong { color: #334155; }
        .cover-right { text-align: end; flex-shrink: 0; }
        .cover-right .report-date { font-size: 12px; color: #94a3b8; }
        .cover-right .project-id { font-size: 11px; color: #cbd5e1; background: #f1f5f9; border-radius: 6px; padding: 3px 8px; display: inline-block; margin-top: 4px; }

        /* ── KPIs ── */
        .kpis { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 32px; }
        .kpi { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; text-align: center; }
        .kpi .val { font-size: 18px; font-weight: 700; color: #0f172a; }
        .kpi .lbl { font-size: 10px; color: #94a3b8; margin-top: 3px; text-transform: uppercase; letter-spacing: .4px; }
        .kpi.accent-teal { border-top: 3px solid #0d9488; }
        .kpi.accent-orange { border-top: 3px solid #f97316; }
        .kpi.accent-blue { border-top: 3px solid #3b82f6; }
        .kpi.accent-red { border-top: 3px solid #ef4444; }
        .kpi.accent-green { border-top: 3px solid #22c55e; }
        .kpi.accent-purple { border-top: 3px solid #a855f7; }

        /* ── Health alert banner ── */
        .health-banner { border-radius: 10px; padding: 12px 16px; margin-bottom: 28px; display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; }
        .health-banner.on_track  { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .health-banner.at_risk   { background: #fef9c3; color: #854d0e; border: 1px solid #fde68a; }
        .health-banner.over_budget { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* ── Section headings ── */
        section { margin-bottom: 32px; }
        h2 { font-size: 15px; font-weight: 700; padding-bottom: 8px; border-bottom: 2px solid #e2e8f0; color: #0f172a; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        h2 .num { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; background: #0d9488; color: #fff; border-radius: 50%; font-size: 11px; font-weight: 700; margin-inline-end: 8px; flex-shrink: 0; }

        /* ── Tables ── */
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { background: #f1f5f9; padding: 8px 12px; text-align: start; font-weight: 600; color: #475569; border-bottom: 2px solid #e2e8f0; font-size: 11px; text-transform: uppercase; letter-spacing: .3px; }
        td { padding: 8px 12px; border-bottom: 1px solid #f1f5f9; color: #374151; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: #f8fafc; }
        .tfoot-row td { font-weight: 700; background: #f1f5f9; border-top: 2px solid #e2e8f0; }

        /* ── Badges ── */
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; }
        .badge-teal   { background: #ccfbf1; color: #0f766e; }
        .badge-blue   { background: #dbeafe; color: #1d4ed8; }
        .badge-orange { background: #ffedd5; color: #c2410c; }
        .badge-yellow { background: #fef9c3; color: #92400e; }
        .badge-red    { background: #fee2e2; color: #991b1b; }
        .badge-green  { background: #dcfce7; color: #166534; }
        .badge-gray   { background: #f1f5f9; color: #475569; }
        .badge-purple { background: #f3e8ff; color: #7e22ce; }

        /* ── Progress bars ── */
        .bar-bg { background: #e2e8f0; height: 7px; border-radius: 999px; overflow: hidden; flex: 1; }
        .bar-fill { height: 100%; border-radius: 999px; }
        .bar-teal   { background: #0d9488; }
        .bar-orange { background: #f97316; }
        .bar-red    { background: #ef4444; }

        /* ── Phase timeline ── */
        .phases { display: flex; align-items: flex-start; margin-bottom: 8px; }
        .phase-item { flex: 1; display: flex; flex-direction: column; align-items: center; }
        .phase-dot { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; margin-bottom: 4px; }
        .phase-done    { background: #0d9488; color: #fff; }
        .phase-current { background: #2563eb; color: #fff; box-shadow: 0 0 0 4px #dbeafe; }
        .phase-pending { background: #e2e8f0; color: #94a3b8; }
        .phase-label { font-size: 10px; text-align: center; }
        .phase-line { flex: 1; height: 2px; margin-top: 15px; margin-inline-start: -8px; margin-inline-end: -8px; }
        .line-done { background: #0d9488; }
        .line-pending { background: #e2e8f0; }

        /* ── Budget visual ── */
        .budget-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .budget-row .cat-name { width: 190px; font-size: 12px; color: #374151; flex-shrink: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .budget-row .amounts { width: 150px; text-align: end; font-size: 11px; color: #64748b; flex-shrink: 0; }

        /* ── Document list ── */
        .doc-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
        .doc-row:last-child { border-bottom: none; }
        .doc-icon { width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 16px; }
        .doc-info { flex: 1; }
        .doc-info .title { font-weight: 600; font-size: 13px; color: #1e293b; }
        .doc-info .sub { font-size: 11px; color: #94a3b8; margin-top: 1px; }

        /* ── 2-col layout ── */
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }

        /* ── Separator ── */
        .page-break { page-break-before: always; }

        /* ── Print styles ── */
        @media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .page { padding: 20px; max-width: 100%; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body>
@php
    $isAr     = app()->getLocale() === 'ar';
    $tr       = fn (string $ar, string $en) => $isAr ? $ar : $en;
    $currency = $isAr ? 'ر.ع' : 'OMR';
    $totalSpent  = $development->totalSpent();
    $health      = $development->budgetHealth();
    $days        = $development->daysToCompletion();
    $phases      = \App\Models\DevelopmentProject::phases();
    $pIdx        = $development->phaseIndex();
    $phaseLabels = $isAr
        ? ['planning' => 'تخطيط', 'foundation' => 'أساسات', 'structure' => 'هيكل', 'finishing' => 'تشطيب', 'handover' => 'تسليم', 'completed' => 'مكتمل']
        : ['planning' => 'Planning', 'foundation' => 'Foundation', 'structure' => 'Structure', 'finishing' => 'Finishing', 'handover' => 'Handover', 'completed' => 'Completed'];

    $healthMessages = [
        'on_track'   => [$tr('المشروع في المسار الصحيح — الإنفاق متوافق مع تقدم الأعمال', 'Project is on track — spending aligns with progress')],
        'at_risk'    => [$tr('تحذير: الإنفاق يتجاوز تقدم الأعمال بأكثر من 5%', 'Warning: spending exceeds progress by more than 5%')],
        'over_budget'=> [$tr('تنبيه: الإنفاق يتجاوز تقدم الأعمال بأكثر من 15%', 'Alert: spending exceeds progress by more than 15%')],
    ];
    $docTypeColors = ['contract' => 'badge-blue', 'invoice' => 'badge-orange', 'other' => 'badge-gray'];
    $docTypeBg     = ['contract' => 'background:#dbeafe;', 'invoice' => 'background:#ffedd5;', 'other' => 'background:#f1f5f9;'];
@endphp

{{-- Toolbar (no-print) --}}
<div class="toolbar">
    <a href="{{ route('manager.development.show', $development) }}">← {{ $tr('العودة للمشروع', 'Back to Project') }}</a>
    <span class="spacer"></span>
    <button onclick="window.print()">🖨 {{ $tr('طباعة / PDF', 'Print / PDF') }}</button>
</div>

<div class="page" style="margin-top:52px;">

    {{-- ══ COVER HEADER ══ --}}
    <div class="cover">
        <div class="cover-left">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                @php
                    $typeColors  = ['residential' => 'badge-blue', 'commercial' => 'badge-purple', 'mixed' => 'badge-teal'];
                    $statusBadge = ['planning' => 'badge-gray', 'foundation' => 'badge-orange', 'structure' => 'badge-yellow', 'finishing' => 'badge-blue', 'handover' => 'badge-teal', 'completed' => 'badge-green'];
                @endphp
                <span class="badge {{ $typeColors[$development->type] ?? 'badge-gray' }}">{{ $development->typeLabel($isAr) }}</span>
                <span class="badge {{ $statusBadge[$development->status] ?? 'badge-gray' }}">{{ $development->statusLabel($isAr) }}</span>
            </div>
            <h1>{{ $development->name }}</h1>
            <div class="meta-grid">
                <div class="meta-item"><strong>{{ $tr('الموقع', 'Location') }}:</strong> {{ $development->location }}</div>
                <div class="meta-item"><strong>{{ $tr('مدير المشروع', 'Project Manager') }}:</strong> {{ $development->project_manager_name }}</div>
                <div class="meta-item"><strong>{{ $tr('تاريخ البدء', 'Start Date') }}:</strong> {{ $development->start_date?->format('d M Y') }}</div>
                <div class="meta-item"><strong>{{ $tr('تاريخ الإنجاز المتوقع', 'Est. Completion') }}:</strong> {{ $development->estimated_completion_date?->format('d M Y') }}</div>
            </div>
        </div>
        <div class="cover-right">
            <div style="font-size:24px;font-weight:800;color:#0d9488;">{{ $development->progress_percentage }}%</div>
            <div style="font-size:11px;color:#94a3b8;">{{ $tr('نسبة الإنجاز', 'Progress') }}</div>
            <div class="report-date" style="margin-top:12px;">{{ $tr('تاريخ التقرير:', 'Report Date:') }} {{ now()->format('d M Y') }}</div>
            <div class="project-id">{{ $tr('مشروع', 'Project') }} #{{ $development->id }}</div>
        </div>
    </div>

    {{-- ══ KPI GRID ══ --}}
    <div class="kpis">
        <div class="kpi accent-teal">
            <div class="val">{{ number_format($development->total_budget) }}</div>
            <div style="font-size:10px;color:#0d9488;font-weight:600;">{{ $currency }}</div>
            <div class="lbl">{{ $tr('إجمالي الميزانية', 'Total Budget') }}</div>
        </div>
        <div class="kpi accent-orange">
            <div class="val" style="color:#f97316;">{{ number_format($totalSpent) }}</div>
            <div style="font-size:10px;color:#f97316;font-weight:600;">{{ $currency }}</div>
            <div class="lbl">{{ $tr('إجمالي المصروف', 'Total Spent') }}</div>
        </div>
        <div class="kpi accent-green">
            <div class="val" style="color:{{ $development->remaining() >= 0 ? '#16a34a' : '#dc2626' }};">{{ number_format($development->remaining()) }}</div>
            <div style="font-size:10px;color:#94a3b8;font-weight:600;">{{ $currency }}</div>
            <div class="lbl">{{ $tr('المتبقي', 'Remaining') }}</div>
        </div>
        <div class="kpi accent-blue">
            <div class="val">{{ $development->budgetUsedPercent() }}%</div>
            <div style="font-size:10px;color:#94a3b8;font-weight:600;">&nbsp;</div>
            <div class="lbl">{{ $tr('الميزانية المستهلكة', 'Budget Used') }}</div>
        </div>
        <div class="kpi accent-purple">
            <div class="val">{{ $development->expenses->count() }}</div>
            <div style="font-size:10px;color:#94a3b8;font-weight:600;">&nbsp;</div>
            <div class="lbl">{{ $tr('إجمالي المصروفات', 'Expense Entries') }}</div>
        </div>
        <div class="kpi accent-teal">
            <div class="val">{{ $development->contractors->count() }}</div>
            <div style="font-size:10px;color:#94a3b8;font-weight:600;">&nbsp;</div>
            <div class="lbl">{{ $tr('المقاولون', 'Contractors') }}</div>
        </div>
        <div class="kpi {{ $days < 0 ? 'accent-red' : 'accent-green' }}">
            <div class="val" style="color:{{ $days < 0 ? '#dc2626' : '#16a34a' }};">{{ abs($days) }}</div>
            <div style="font-size:10px;color:#94a3b8;font-weight:600;">&nbsp;</div>
            <div class="lbl">{{ $days < 0 ? $tr('يوم تأخر', 'Days Overdue') : $tr('يوم متبقي', 'Days Remaining') }}</div>
        </div>
        <div class="kpi {{ $health === 'on_track' ? 'accent-green' : ($health === 'at_risk' ? 'accent-orange' : 'accent-red') }}">
            <div class="val" style="font-size:13px;color:{{ $health === 'on_track' ? '#16a34a' : ($health === 'at_risk' ? '#ea580c' : '#dc2626') }};">
                {{ $tr(...match($health) {
                    'on_track'    => ['في المسار', 'On Track'],
                    'at_risk'     => ['في خطر', 'At Risk'],
                    'over_budget' => ['تجاوز الميزانية', 'Over Budget'],
                }) }}
            </div>
            <div style="font-size:10px;color:#94a3b8;font-weight:600;">&nbsp;</div>
            <div class="lbl">{{ $tr('صحة الميزانية', 'Budget Health') }}</div>
        </div>
    </div>

    {{-- ══ HEALTH BANNER ══ --}}
    <div class="health-banner {{ $health }}">
        <span style="font-size:18px;">{{ $health === 'on_track' ? '✅' : ($health === 'at_risk' ? '⚠️' : '🚨') }}</span>
        {{ $healthMessages[$health][0] }}
        <span style="margin-inline-start:auto;font-size:11px;opacity:.7;">{{ $tr('تقدم الإنجاز:','Progress:') }} {{ $development->progress_percentage }}% · {{ $tr('الميزانية المستهلكة:','Budget used:') }} {{ $development->budgetUsedPercent() }}%</span>
    </div>

    {{-- ══ 1: PHASE TIMELINE ══ --}}
    <section>
        <h2><span class="num">1</span>{{ $tr('مراحل المشروع', 'Project Phases') }}</h2>
        <div class="phases">
            @foreach($phases as $i => $phase)
            <div class="phase-item">
                <div class="phase-dot {{ $pIdx > $i ? 'phase-done' : ($pIdx === $i ? 'phase-current' : 'phase-pending') }}">
                    @if($pIdx > $i) ✓ @else {{ $i + 1 }} @endif
                </div>
                <span class="phase-label" style="color:{{ $pIdx > $i ? '#0d9488' : ($pIdx === $i ? '#2563eb' : '#94a3b8') }};">{{ $phaseLabels[$phase] }}</span>
            </div>
            @if($i < count($phases) - 1)
            <div class="phase-line {{ $pIdx > $i ? 'line-done' : 'line-pending' }}"></div>
            @endif
            @endforeach
        </div>
        @if($development->notes)
        <div style="margin-top:12px;background:#f8fafc;border-radius:8px;padding:10px 14px;font-size:12px;color:#475569;border-left:3px solid #0d9488;">
            <strong>{{ $tr('ملاحظات المشروع:', 'Project Notes:') }}</strong> {{ $development->notes }}
        </div>
        @endif
    </section>

    {{-- ══ 2: BUDGET ANALYSIS ══ --}}
    <section>
        <h2><span class="num">2</span>{{ $tr('تحليل الميزانية', 'Budget Analysis') }}</h2>

        @php
            $totalAllocated      = $development->totalSpent();   // sum of category_budgets
            $totalActualExpenses = (float)$development->expenses->sum('amount');
            $totalVariance       = $totalAllocated - $totalActualExpenses;
            $unallocated         = max(0, (float)$development->total_budget - $totalAllocated);
        @endphp

        {{-- 4-box summary --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px;">
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-top:3px solid #0d9488;border-radius:10px;padding:12px;text-align:center;">
                <div style="font-size:15px;font-weight:700;color:#0f172a;">{{ number_format($development->total_budget) }}</div>
                <div style="font-size:10px;color:#0d9488;font-weight:600;">{{ $currency }}</div>
                <div style="font-size:10px;color:#64748b;text-transform:uppercase;letter-spacing:.3px;margin-top:3px;">{{ $tr('إجمالي الميزانية','Total Budget') }}</div>
            </div>
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-top:3px solid #3b82f6;border-radius:10px;padding:12px;text-align:center;">
                <div style="font-size:15px;font-weight:700;color:#3b82f6;">{{ number_format($totalAllocated) }}</div>
                <div style="font-size:10px;color:#3b82f6;font-weight:600;">{{ $currency }}</div>
                <div style="font-size:10px;color:#64748b;text-transform:uppercase;letter-spacing:.3px;margin-top:3px;">{{ $tr('الميزانية المخصصة','Budget Allocated') }}</div>
            </div>
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-top:3px solid #f97316;border-radius:10px;padding:12px;text-align:center;">
                <div style="font-size:15px;font-weight:700;color:#f97316;">{{ number_format($totalActualExpenses) }}</div>
                <div style="font-size:10px;color:#f97316;font-weight:600;">{{ $currency }}</div>
                <div style="font-size:10px;color:#64748b;text-transform:uppercase;letter-spacing:.3px;margin-top:3px;">{{ $tr('المصروفات الفعلية','Actual Expenses') }}</div>
            </div>
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-top:3px solid #22c55e;border-radius:10px;padding:12px;text-align:center;">
                <div style="font-size:15px;font-weight:700;color:{{ $development->remaining() >= 0 ? '#16a34a' : '#dc2626' }};">{{ number_format($development->remaining()) }}</div>
                <div style="font-size:10px;color:#94a3b8;font-weight:600;">{{ $currency }}</div>
                <div style="font-size:10px;color:#64748b;text-transform:uppercase;letter-spacing:.3px;margin-top:3px;">{{ $tr('الرصيد المتبقي','Remaining Balance') }}</div>
            </div>
        </div>

        {{-- Overall usage bar --}}
        <div style="background:#f8fafc;border-radius:10px;padding:14px 16px;margin-bottom:20px;">
            <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                <span style="font-weight:600;font-size:12px;color:#374151;">{{ $tr('الاستخدام الكلي للميزانية','Overall Budget Utilization') }}</span>
                <span style="font-weight:700;font-size:12px;color:{{ $development->budgetUsedPercent() > 100 ? '#dc2626' : '#0d9488' }};">{{ $development->budgetUsedPercent() }}%</span>
            </div>
            <div class="bar-bg" style="height:10px;margin-bottom:8px;">
                <div class="bar-fill {{ $development->budgetUsedPercent() > 100 ? 'bar-red' : ($development->budgetUsedPercent() > 90 ? 'bar-orange' : 'bar-teal') }}"
                     style="width:{{ min(100,$development->budgetUsedPercent()) }}%;"></div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:10px;color:#94a3b8;">
                <span>{{ $tr('المخصص:','Allocated:') }} <strong style="color:#3b82f6;">{{ number_format($totalAllocated) }} {{ $currency }}</strong></span>
                <span>{{ $tr('المصروف الفعلي:','Actual Spent:') }} <strong style="color:#f97316;">{{ number_format($totalActualExpenses) }} {{ $currency }}</strong></span>
                <span>{{ $tr('الميزانية الكلية:','Total Budget:') }} <strong style="color:#0f172a;">{{ number_format($development->total_budget) }} {{ $currency }}</strong></span>
            </div>
        </div>

        {{-- Per-category blocks: budget header + item table --}}
        @php $expGroups = $development->expenses->groupBy('category'); @endphp
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
        <div style="margin-bottom:18px;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;">

            {{-- Category header --}}
            <div style="background:#f1f5f9;padding:10px 14px;display:flex;align-items:center;flex-wrap:wrap;gap:8px;border-bottom:1px solid #e2e8f0;">
                <span style="font-size:11px;font-weight:700;color:#64748b;min-width:18px;">{{ $catIdx + 1 }}.</span>
                <span class="badge badge-blue" style="font-size:11px;">{{ $catLabels[$cat] }}</span>
                @if(!empty($budgetItems))
                <span style="font-size:10px;color:#94a3b8;">{{ count($budgetItems) }} {{ $tr('بند مخطط','planned item(s)') }}</span>
                @endif
                @if($catItems->isNotEmpty())
                <span style="font-size:10px;color:#f97316;">{{ $catItems->count() }} {{ $tr('مصروف فعلي','actual expense(s)') }}</span>
                @endif
                <div style="margin-inline-start:auto;display:flex;gap:14px;font-size:11px;flex-wrap:wrap;align-items:center;">
                    @if($allocated > 0)
                    <span>{{ $tr('المخصص:','Allocated:') }} <strong style="color:#3b82f6;">{{ number_format($allocated, 2) }} {{ $currency }}</strong></span>
                    @endif
                    @if($actualSpent > 0)
                    <span>{{ $tr('الفعلي:','Actual:') }} <strong style="color:#f97316;">{{ number_format($actualSpent, 2) }} {{ $currency }}</strong></span>
                    @endif
                    @if($allocated > 0)
                    <span style="font-weight:700;color:{{ $variance >= 0 ? '#16a34a' : '#dc2626' }};">
                        {{ $variance >= 0 ? '+' : '' }}{{ number_format($variance, 2) }} {{ $currency }}
                    </span>
                    @endif
                    @if($allocated > 0)
                    <div style="display:flex;align-items:center;gap:4px;width:90px;">
                        <div class="bar-bg" style="flex:1;">
                            <div class="bar-fill" style="width:{{ min(100,$utilization) }}%;background:{{ $utilColor }};"></div>
                        </div>
                        <span style="font-size:10px;color:{{ $utilColor }};width:30px;flex-shrink:0;">{{ $utilization }}%</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Budget allocation items (from category_budgets) --}}
            @if(!empty($budgetItems))
            <div style="padding:6px 14px;font-size:10px;font-weight:700;color:#1d4ed8;background:#eff6ff;border-bottom:1px solid #dbeafe;text-transform:uppercase;letter-spacing:.4px;">
                {{ $tr('تفصيل الميزانية المخططة','Planned Budget Breakdown') }}
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width:28px;">#</th>
                        <th>{{ $tr('اسم البند','Item Name') }}</th>
                        <th style="text-align:end;">{{ $tr('المبلغ المخصص','Allocated Amount') }}</th>
                        <th style="text-align:end;">{{ $tr('% من الفئة','% of Category') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($budgetItems as $bIdx => $bItem)
                    <tr>
                        <td style="color:#94a3b8;font-size:11px;">{{ $bIdx + 1 }}</td>
                        <td style="font-weight:600;color:#0f172a;">{{ $bItem['name'] ?: ('—') }}</td>
                        <td style="text-align:end;font-weight:600;color:#3b82f6;white-space:nowrap;">{{ number_format((float)$bItem['amount'], 2) }} {{ $currency }}</td>
                        <td style="text-align:end;color:#64748b;font-size:11px;">
                            {{ $allocated > 0 ? round(((float)$bItem['amount'] / $allocated) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="tfoot-row">
                        <td colspan="2" style="text-align:end;">{{ $tr('إجمالي التخصيص','Total Allocated') }}</td>
                        <td style="text-align:end;color:#3b82f6;">{{ number_format($allocated, 2) }} {{ $currency }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            @endif

            {{-- Actual expense records --}}
            @if($catItems->isNotEmpty())
            @if(!empty($budgetItems))
            <div style="padding:6px 14px;font-size:10px;font-weight:700;color:#c2410c;background:#fff7ed;border-top:1px solid #fed7aa;border-bottom:1px solid #fed7aa;text-transform:uppercase;letter-spacing:.4px;">
                {{ $tr('المصروفات الفعلية المسجلة','Actual Recorded Expenses') }}
            </div>
            @endif
            <table>
                <thead>
                    <tr>
                        <th style="width:28px;">#</th>
                        <th>{{ $tr('الصنف / البند', 'Item / Description') }}</th>
                        <th style="text-align:end;">{{ $tr('الكمية', 'Qty') }}</th>
                        <th style="text-align:end;">{{ $tr('الوحدة', 'Unit') }}</th>
                        <th style="text-align:end;">{{ $tr('سعر الوحدة', 'Unit Cost') }}</th>
                        <th style="text-align:end;">{{ $tr('الإجمالي', 'Total') }}</th>
                        <th>{{ $tr('التاريخ', 'Date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($catItems as $idx => $exp)
                    <tr>
                        <td style="color:#94a3b8;font-size:11px;">{{ $idx + 1 }}</td>
                        <td>
                            <div style="font-weight:600;color:#0f172a;">{{ $exp->item_name }}</div>
                            @if($exp->description)
                            <div style="font-size:10px;color:#94a3b8;margin-top:1px;">{{ $exp->description }}</div>
                            @endif
                        </td>
                        <td style="text-align:end;white-space:nowrap;color:#475569;">{{ number_format((float)$exp->quantity, 2) }}</td>
                        <td style="text-align:end;white-space:nowrap;color:#94a3b8;font-size:11px;">{{ $exp->unit ?: '—' }}</td>
                        <td style="text-align:end;white-space:nowrap;color:#475569;">{{ number_format((float)$exp->unit_cost, 2) }} {{ $currency }}</td>
                        <td style="text-align:end;font-weight:700;color:#f97316;white-space:nowrap;">{{ number_format((float)$exp->amount, 2) }} {{ $currency }}</td>
                        <td style="color:#94a3b8;font-size:11px;white-space:nowrap;">{{ $exp->expense_date?->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="tfoot-row">
                        <td colspan="5" style="text-align:end;">{{ $tr('مجموع المصروفات الفعلية','Actual Expenses Subtotal') }}</td>
                        <td style="text-align:end;color:#f97316;">{{ number_format($actualSpent, 2) }} {{ $currency }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            @elseif(empty($budgetItems))
            <div style="padding:10px 14px;font-size:11px;color:#94a3b8;font-style:italic;">
                {{ $tr('لم يتم تسجيل أي بنود لهذه الفئة بعد','No items logged for this category yet') }}
            </div>
            @endif
        </div>
        @endif
        @endforeach

        {{-- Grand total + unallocated note --}}
        <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;padding:12px 16px;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-weight:700;color:#c2410c;font-size:13px;">{{ $tr('الإجمالي الكلي للمصروفات','Grand Total — All Expenses') }}</span>
            <span style="font-weight:800;color:#f97316;font-size:15px;">{{ number_format($totalActualExpenses, 2) }} {{ $currency }}</span>
        </div>
        @if($unallocated > 0)
        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:10px 14px;margin-top:8px;display:flex;align-items:center;justify-content:space-between;font-size:12px;color:#1d4ed8;">
            <span>ℹ️ {{ $tr('الميزانية غير الموزعة على الفئات:','Budget not yet allocated to any category:') }}</span>
            <strong>{{ number_format($unallocated, 2) }} {{ $currency }}
            ({{ $development->total_budget > 0 ? round(($unallocated / $development->total_budget) * 100, 1) : 0 }}%)</strong>
        </div>
        @endif
    </section>

    {{-- ══ 3: MONTHLY SPENDING ══ --}}
    @if($monthlyBreakdown->isNotEmpty())
    <section>
        <h2><span class="num">3</span>{{ $tr('الصرف الشهري', 'Monthly Spending') }}</h2>
        <table>
            <thead>
                <tr>
                    <th>{{ $tr('الشهر', 'Month') }}</th>
                    <th style="text-align:end;">{{ $tr('المبلغ', 'Amount') }}</th>
                    <th>{{ $tr('توزيع النسبة', 'Share') }}</th>
                    <th style="text-align:end;">{{ $tr('% من المصروف', '% of Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyBreakdown as $row)
                @php $mpct = $totalActualExpenses > 0 ? round(($row->total / $totalActualExpenses) * 100) : 0; @endphp
                <tr>
                    <td style="font-weight:600;">{{ \Carbon\Carbon::createFromDate($row->yr, $row->mo, 1)->format('F Y') }}</td>
                    <td style="text-align:end;font-weight:600;color:#f97316;">{{ number_format($row->total) }} {{ $currency }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="bar-bg"><div class="bar-fill bar-teal" style="width:{{ $mpct }}%;"></div></div>
                        </div>
                    </td>
                    <td style="text-align:end;color:#64748b;">{{ $mpct }}%</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="tfoot-row">
                    <td>{{ $tr('الإجمالي', 'Total') }}</td>
                    <td style="text-align:end;color:#f97316;">{{ number_format($totalActualExpenses) }} {{ $currency }}</td>
                    <td></td><td></td>
                </tr>
            </tfoot>
        </table>
    </section>
    @endif

    {{-- ══ 4: CONTRACTORS & PAYMENTS ══ --}}
    @if($development->contractors->isNotEmpty())
    <section>
        @php $secNum = $monthlyBreakdown->isNotEmpty() ? '4' : '3'; @endphp
        <h2><span class="num">{{ $secNum }}</span>{{ $tr('المقاولون والمدفوعات', 'Contractors & Payments') }}</h2>
        @foreach($development->contractors as $c)
        @php
            $paid    = $c->totalPaid();
            $rem     = $c->remaining();
            $paidPct = $c->paidPercent();
        @endphp
        <div style="border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;margin-bottom:16px;">
            {{-- Contractor header --}}
            <div style="background:#f8fafc;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;gap:16px;">
                <div>
                    <div style="font-weight:700;font-size:14px;color:#0f172a;">{{ $c->name }}</div>
                    <div style="font-size:11px;color:#64748b;margin-top:2px;">{{ Str::limit($c->scope_of_work, 80) }}</div>
                </div>
                <div style="display:flex;gap:16px;align-items:center;flex-shrink:0;">
                    <div style="text-align:center;">
                        <div style="font-size:11px;color:#94a3b8;">{{ $tr('قيمة العقد','Contract') }}</div>
                        <div style="font-weight:700;">{{ number_format($c->contract_value) }} {{ $currency }}</div>
                    </div>
                    <div style="text-align:center;">
                        <div style="font-size:11px;color:#94a3b8;">{{ $tr('المدفوع','Paid') }}</div>
                        <div style="font-weight:700;color:#0d9488;">{{ number_format($paid) }} {{ $currency }}</div>
                    </div>
                    <div style="text-align:center;">
                        <div style="font-size:11px;color:#94a3b8;">{{ $tr('المتبقي','Remaining') }}</div>
                        <div style="font-weight:700;color:{{ $rem > 0 ? '#f97316' : '#16a34a' }};">{{ number_format($rem) }} {{ $currency }}</div>
                    </div>
                    <span class="badge {{ $paidPct >= 100 ? 'badge-green' : ($paidPct >= 50 ? 'badge-teal' : 'badge-yellow') }}">{{ $paidPct }}% {{ $tr('مدفوع','paid') }}</span>
                </div>
            </div>
            {{-- Payment progress bar --}}
            <div style="padding:8px 16px;background:#fff;border-top:1px solid #f1f5f9;">
                <div class="bar-bg" style="height:6px;">
                    <div class="bar-fill {{ $paidPct >= 100 ? 'bar-teal' : ($paidPct >= 80 ? 'bar-orange' : 'bar-teal') }}" style="width:{{ min(100,$paidPct) }}%;"></div>
                </div>
            </div>
            {{-- Payment history --}}
            @if($c->payments->isNotEmpty())
            <table style="border-top:1px solid #f1f5f9;">
                <thead>
                    <tr>
                        <th>{{ $tr('تاريخ الدفع','Payment Date') }}</th>
                        <th>{{ $tr('الوصف','Description') }}</th>
                        <th style="text-align:end;">{{ $tr('المبلغ','Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($c->payments->sortByDesc('paid_at') as $pmt)
                    <tr>
                        <td style="color:#64748b;white-space:nowrap;">{{ \Carbon\Carbon::parse($pmt->paid_at)->format('d M Y') }}</td>
                        <td style="color:#475569;">{{ $pmt->description ?: '—' }}</td>
                        <td style="text-align:end;font-weight:600;color:#0d9488;white-space:nowrap;">{{ number_format($pmt->amount) }} {{ $currency }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="tfoot-row">
                        <td colspan="2">{{ $tr('إجمالي المدفوع','Total Paid') }}</td>
                        <td style="text-align:end;color:#0d9488;">{{ number_format($paid) }} {{ $currency }}</td>
                    </tr>
                </tfoot>
            </table>
            @else
            <div style="padding:10px 16px;font-size:11px;color:#94a3b8;">{{ $tr('لا توجد مدفوعات مسجلة','No payments recorded yet') }}</div>
            @endif
        </div>
        @endforeach

        {{-- Contractors summary --}}
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 16px;display:flex;justify-content:space-between;">
            <span style="font-size:12px;font-weight:600;color:#166534;">{{ $tr('إجمالي المدفوع للمقاولين','Total Paid to Contractors') }}</span>
            <span style="font-weight:700;color:#16a34a;">{{ number_format($development->contractors->sum(fn($c) => $c->totalPaid())) }} {{ $currency }}</span>
        </div>
    </section>
    @endif

    {{-- ══ 6: DOCUMENTS ══ --}}
    @if($development->documents->isNotEmpty())
    @php
        $docSecNum = 3;
        if ($monthlyBreakdown->isNotEmpty()) $docSecNum++;
        if ($development->contractors->isNotEmpty()) $docSecNum++;
    @endphp
    <section>
        <h2><span class="num">{{ $docSecNum }}</span>{{ $tr('المستندات والمرفقات', 'Documents & Attachments') }}</h2>
        <div>
            @foreach($development->documents->sortBy('type') as $doc)
            <div class="doc-row">
                <div class="doc-icon" style="{{ $docTypeBg[$doc->type] ?? 'background:#f1f5f9;' }}">
                    @if($doc->isPdf()) 📄
                    @elseif(in_array($doc->extension(), ['jpg','jpeg','png'])) 🖼️
                    @else 📎 @endif
                </div>
                <div class="doc-info">
                    <div class="title">{{ $doc->title }}</div>
                    <div class="sub">
                        <span class="badge {{ $docTypeColors[$doc->type] ?? 'badge-gray' }}">{{ $doc->typeLabel($isAr) }}</span>
                        · {{ $doc->original_name }} · {{ $tr('رُفع في','Uploaded') }} {{ $doc->created_at->format('d M Y') }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ══ FOOTER ══ --}}
    <div style="margin-top:40px;padding-top:16px;border-top:2px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
        <div>
            <div style="font-weight:700;font-size:13px;color:#0f172a;">{{ $tr('نظام ثروة العقارية', 'Tharwa Real Estate System') }}</div>
            <div style="font-size:11px;color:#94a3b8;margin-top:2px;">{{ $tr('تقرير مُنشأ تلقائياً', 'Auto-generated report') }} · {{ now()->format('d M Y, H:i') }}</div>
        </div>
        <div style="text-align:end;">
            <div style="font-size:12px;font-weight:600;color:#0d9488;">{{ $development->name }}</div>
            <div style="font-size:11px;color:#94a3b8;">{{ $tr('مشروع', 'Project') }} #{{ $development->id }} · {{ $development->typeLabel($isAr) }}</div>
        </div>
    </div>

</div>
</body>
</html>
