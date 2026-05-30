<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr   = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $numLocale = $isAr ? 'ar-OM' : 'en-US';
        $currency  = $isAr ? 'ر.ع' : 'OMR';
        $categoryLabels = [
            'utilities'   => $tr('مرافق',      'Utilities'),
            'maintenance' => $tr('صيانة',       'Maintenance'),
            'salaries'    => $tr('رواتب',        'Salaries'),
            'marketing'   => $tr('تسويق',        'Marketing'),
            'repairs'     => $tr('إصلاحات',      'Repairs'),
            'other'       => $tr('أخرى',         'Other'),
        ];
    @endphp
    <x-slot name="title">{{ $tr('لوحة المالية', 'Finance Dashboard') }}</x-slot>

    {{-- ── Hero Banner ─────────────────────────────────────────────────── --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-950 via-teal-900 to-cyan-900 p-6 sm:p-8 text-white shadow-2xl mb-6">
        <div class="absolute -top-16 -left-8 h-52 w-52 rounded-full bg-emerald-400/20 blur-3xl"></div>
        <div class="absolute -bottom-20 -right-4 h-60 w-60 rounded-full bg-cyan-500/20 blur-3xl"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-xs font-medium">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    {{ $tr('قسم المالية الداخلية', 'Internal Finance Department') }}
                </p>
                <h2 class="mt-3 text-2xl sm:text-3xl font-black tracking-tight">{{ $tr('لوحة تحكم المالية', 'Finance Dashboard') }}</h2>
                <p class="mt-2 text-sm text-emerald-100/80 max-w-xl">
                    {{ $tr('نظرة شاملة على الميزانيات، الإيرادات، والمصروفات الداخلية للشركة.', 'Full overview of budgets, revenue, and internal company expenses.') }}
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 min-w-[240px]">
                <div class="rounded-2xl bg-white/10 border border-white/20 p-4 backdrop-blur">
                    <p class="text-xs text-emerald-100/80">{{ $tr('إيرادات السنة', 'Year Revenue') }}</p>
                    <p class="text-xl font-extrabold mt-1">{{ number_format($revenueYear) }}</p>
                    <p class="text-[11px] text-emerald-100/60">{{ $currency }}</p>
                </div>
                <div class="rounded-2xl bg-white/10 border border-white/20 p-4 backdrop-blur">
                    <p class="text-xs text-emerald-100/80">{{ $tr('مصروفات السنة', 'Year Expenses') }}</p>
                    <p class="text-xl font-extrabold mt-1 text-red-300">{{ number_format($companyExpensesYear) }}</p>
                    <p class="text-[11px] text-emerald-100/60">{{ $currency }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── KPI Cards ────────────────────────────────────────────────────── --}}
    <section class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="rounded-2xl bg-white shadow-lg border border-slate-100 p-5">
            <p class="text-xs text-slate-500">{{ $tr('إجمالي الميزانية المخصصة', 'Total Budget Allocated') }}</p>
            <p class="text-2xl font-black text-slate-800 mt-2">{{ number_format($totalAllocated) }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $currency }}</p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-slate-100 p-5">
            <p class="text-xs text-slate-500">{{ $tr('إجمالي المُنفق من الميزانية', 'Total Budget Spent') }}</p>
            <p class="text-2xl font-black text-red-600 mt-2">{{ number_format($totalSpent) }}</p>
            <div class="mt-3 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-full rounded-full {{ $budgetUsagePct >= 90 ? 'bg-red-500' : ($budgetUsagePct >= 70 ? 'bg-yellow-400' : 'bg-emerald-500') }}" style="width:{{ $budgetUsagePct }}%"></div>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">{{ $budgetUsagePct }}% {{ $tr('مُستخدم', 'used') }}</p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-slate-100 p-5">
            <p class="text-xs text-slate-500">{{ $tr('مصروفات هذا الشهر', 'Expenses This Month') }}</p>
            <p class="text-2xl font-black text-violet-600 mt-2">{{ number_format($companyExpensesMonth) }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $currency }}</p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-slate-100 p-5">
            <p class="text-xs text-slate-500">{{ $tr('رواتب مدفوعة هذا العام', 'Salaries Paid This Year') }}</p>
            <p class="text-2xl font-black text-blue-700 mt-2">{{ number_format($salaryYear) }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $currency }}</p>
        </div>
    </section>

    {{-- ── Chart + Category Breakdown ──────────────────────────────────── --}}
    <section class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        {{-- Monthly Expenses Chart --}}
        <div class="xl:col-span-2 rounded-2xl bg-white border border-slate-100 shadow-lg p-5 sm:p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-extrabold text-slate-800 text-lg">{{ $tr('المصروفات الشهرية (آخر 6 أشهر)', 'Monthly Expenses (Last 6 Months)') }}</h3>
                <span class="text-xs px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100">{{ $tr('مصروفات داخلية', 'Internal') }}</span>
            </div>
            <div class="relative h-[280px] sm:h-[320px]">
                <canvas id="expenseChart"></canvas>
            </div>
        </div>

        {{-- Expense by Category --}}
        <div class="rounded-2xl bg-white border border-slate-100 shadow-lg p-5 sm:p-6">
            <h3 class="font-extrabold text-slate-800 text-lg mb-5">{{ $tr('توزيع المصروفات', 'Expense Breakdown') }}</h3>
            @if($expenseByCategory->isEmpty())
                <div class="flex items-center justify-center h-40 text-slate-400 text-sm">{{ $tr('لا توجد بيانات', 'No data') }}</div>
            @else
                <div class="space-y-3">
                    @php $maxCat = $expenseByCategory->max('total') ?: 1; @endphp
                    @foreach($expenseByCategory as $cat)
                    @php $pct = round(($cat->total / $maxCat) * 100); @endphp
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-slate-600 font-medium">{{ $categoryLabels[$cat->category] ?? $cat->category }}</span>
                            <span class="text-slate-500">{{ number_format($cat->total) }} {{ $currency }}</span>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ── Budget Overview + Recent Expenses ───────────────────────────── --}}
    <section class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
        {{-- Budget Overview --}}
        <div class="rounded-2xl bg-white border border-slate-100 shadow-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between">
                <h3 class="font-extrabold text-slate-800">{{ $tr('نظرة على الميزانيات', 'Budget Overview') }}</h3>
                <div class="flex gap-2 text-xs">
                    <span class="px-2 py-0.5 rounded-full bg-green-50 text-green-700">{{ $tr('معتمد', 'Approved') }}: {{ $budgetCounts['approved'] ?? 0 }}</span>
                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">{{ $tr('مسودة', 'Draft') }}: {{ $budgetCounts['draft'] ?? 0 }}</span>
                    <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">{{ $tr('مغلق', 'Closed') }}: {{ $budgetCounts['closed'] ?? 0 }}</span>
                </div>
            </div>
            @if($budgetsOverview->isEmpty())
                <div class="p-8 text-center text-slate-400 text-sm">{{ $tr('لا توجد ميزانيات', 'No budgets yet') }}</div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($budgetsOverview as $b)
                    @php $pct = $b->usagePercent(); @endphp
                    <div class="px-5 py-3.5 hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between mb-1.5">
                            <div>
                                <p class="text-sm font-semibold text-slate-700">{{ $b->title }}</p>
                                <p class="text-xs text-slate-400">{{ $b->categoryLabel() }} · {{ $b->periodLabel() }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-bold text-slate-800">{{ number_format($b->allocated_amount) }}</p>
                                <p class="text-xs text-red-500">{{ $tr('منفق:', 'Spent:') }} {{ number_format($b->spent_amount) }}</p>
                            </div>
                        </div>
                        <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $pct >= 100 ? 'bg-red-500' : ($pct >= 80 ? 'bg-yellow-400' : 'bg-emerald-500') }}" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/60">
                    <a href="{{ route('manager.budgets.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">{{ $tr('عرض كل الميزانيات ←', 'View all budgets →') }}</a>
                </div>
            @endif
        </div>

        {{-- Recent Company Expenses --}}
        <div class="rounded-2xl bg-white border border-slate-100 shadow-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between">
                <h3 class="font-extrabold text-slate-800">{{ $tr('آخر المصروفات الداخلية', 'Recent Internal Expenses') }}</h3>
                <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">{{ $year }}</span>
            </div>
            @if($recentExpenses->isEmpty())
                <div class="p-8 text-center text-slate-400 text-sm">{{ $tr('لا توجد مصروفات', 'No expenses yet') }}</div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($recentExpenses as $exp)
                    <div class="px-5 py-3.5 hover:bg-slate-50 transition flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-700 truncate">{{ $exp->title }}</p>
                            <p class="text-xs text-slate-400">{{ $categoryLabels[$exp->category] ?? $exp->category }} · {{ $exp->expense_date->format('Y/m/d') }}</p>
                        </div>
                        <p class="text-sm font-bold text-red-600 shrink-0">{{ number_format($exp->amount) }} {{ $currency }}</p>
                    </div>
                    @endforeach
                </div>
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/60">
                    <a href="{{ route('manager.expenses.index') }}?scope=company" class="text-xs text-blue-600 hover:text-blue-800 font-medium">{{ $tr('عرض كل المصروفات ←', 'View all expenses →') }}</a>
                </div>
            @endif
        </div>
    </section>

    {{-- ── Quick Actions ────────────────────────────────────────────────── --}}
    <section class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('manager.expenses.create') }}" class="rounded-2xl bg-white border border-slate-100 shadow p-4 flex items-center gap-3 hover:shadow-md hover:border-emerald-200 transition group">
            <div class="h-10 w-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0 group-hover:bg-emerald-100 transition">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <span class="text-sm font-semibold text-slate-700">{{ $tr('تسجيل مصروف', 'Add Expense') }}</span>
        </a>
        <a href="{{ route('manager.budgets.create') }}" class="rounded-2xl bg-white border border-slate-100 shadow p-4 flex items-center gap-3 hover:shadow-md hover:border-blue-200 transition group">
            <div class="h-10 w-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0 group-hover:bg-blue-100 transition">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <span class="text-sm font-semibold text-slate-700">{{ $tr('إضافة ميزانية', 'Add Budget') }}</span>
        </a>
        <a href="{{ route('manager.expenses.index') }}?scope=company" class="rounded-2xl bg-white border border-slate-100 shadow p-4 flex items-center gap-3 hover:shadow-md hover:border-violet-200 transition group">
            <div class="h-10 w-10 rounded-xl bg-violet-50 flex items-center justify-center shrink-0 group-hover:bg-violet-100 transition">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <span class="text-sm font-semibold text-slate-700">{{ $tr('كل المصروفات', 'All Expenses') }}</span>
        </a>
        <a href="{{ route('manager.reports.index') }}" class="rounded-2xl bg-white border border-slate-100 shadow p-4 flex items-center gap-3 hover:shadow-md hover:border-amber-200 transition group">
            <div class="h-10 w-10 rounded-xl bg-amber-50 flex items-center justify-center shrink-0 group-hover:bg-amber-100 transition">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <span class="text-sm font-semibold text-slate-700">{{ $tr('تقارير الإيرادات', 'Revenue Reports') }}</span>
        </a>
    </section>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const isArLocale  = @json($isAr);
        const numberLocale = @json($numLocale);
        const currencyLabel = @json($currency);

        Chart.defaults.font.family = isArLocale ? 'Cairo, sans-serif' : 'Sora, sans-serif';
        Chart.defaults.color = '#64748b';

        const expCtx = document.getElementById('expenseChart').getContext('2d');
        const expGradient = expCtx.createLinearGradient(0, 0, 0, 240);
        expGradient.addColorStop(0, 'rgba(16, 185, 129, 0.35)');
        expGradient.addColorStop(1, 'rgba(16, 185, 129, 0.02)');

        new Chart(expCtx, {
            type: 'line',
            data: {
                labels: @json($expenseChart->pluck('label')),
                datasets: [{
                    label: @json($tr('المصروفات', 'Expenses')),
                    data: @json($expenseChart->pluck('amount')),
                    borderColor: '#10b981',
                    backgroundColor: expGradient,
                    fill: true,
                    borderWidth: 3,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#e2e8f0',
                        bodyColor: '#e2e8f0',
                        callbacks: {
                            label: (ctx) => `${Number(ctx.parsed.y).toLocaleString(numberLocale)} ${currencyLabel}`
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148,163,184,0.15)' },
                        ticks: {
                            callback: value => `${Number(value).toLocaleString(numberLocale)} ${currencyLabel}`
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
