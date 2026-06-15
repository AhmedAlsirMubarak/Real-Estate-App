<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
        $displayName = function (?string $name, string $fallback = 'User') use ($isAr) {
            if ($name === null || $name === '') return $fallback;
            if ($isAr || ! preg_match('/\p{Arabic}/u', $name)) return $name;
            return $fallback;
        };
    @endphp
    <x-slot name="title">{{ $tr('لوحة التحكم - المحاسب', 'Dashboard - Accountant') }}</x-slot>

    <div class="py-4 space-y-8">

        {{-- ── Payments KPI cards ────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><p class="text-sm text-gray-500">{{ $tr('إجمالي الإيرادات', 'Total Revenue') }}</p><p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_revenue'] ?? 0) }} {{ $currency }}</p></div>
            </div>
            <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div><p class="text-sm text-gray-500">{{ $tr('إيرادات هذا الشهر', 'This Month Revenue') }}</p><p class="text-2xl font-bold text-gray-800">{{ number_format($stats['monthly_revenue'] ?? 0) }} {{ $currency }}</p></div>
            </div>
            <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><p class="text-sm text-gray-500">{{ $tr('المدفوعات المعلقة', 'Pending Payments') }}</p><p class="text-2xl font-bold text-gray-800">{{ $stats['pending_payments'] ?? 0 }}</p></div>
            </div>
            <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><p class="text-sm text-gray-500">{{ $tr('المدفوعات المتأخرة', 'Overdue Payments') }}</p><p class="text-2xl font-bold text-gray-800">{{ $stats['overdue_payments'] ?? 0 }}</p></div>
            </div>
            <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><p class="text-sm text-gray-500">{{ $tr('العقود النشطة', 'Active Contracts') }}</p><p class="text-2xl font-bold text-gray-800">{{ $stats['active_contracts'] ?? 0 }}</p></div>
            </div>
        </div>

        {{-- ── Finance section header ────────────────────────────────────── --}}
        <div class="flex items-center gap-3">
            <div class="h-px flex-1 bg-gray-200"></div>
            <span class="text-xs font-semibold uppercase tracking-widest text-teal-600 px-3 py-1 bg-teal-50 rounded-full border border-teal-200">
                {{ $tr('قسم المالية', 'Finance Department') }} — {{ $year }}
            </span>
            <div class="h-px flex-1 bg-gray-200"></div>
        </div>

        {{-- ── Finance KPI cards ─────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-xs text-gray-500 mb-1">{{ $tr('إجمالي الميزانية', 'Total Budget') }}</p>
                <p class="text-xl font-bold text-gray-800">{{ number_format($totalAllocated) }} {{ $currency }}</p>
                <div class="mt-2 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-teal-500 rounded-full" style="width: {{ $budgetUsagePct }}%"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1">{{ $budgetUsagePct }}% {{ $tr('مُستخدم', 'used') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-xs text-gray-500 mb-1">{{ $tr('إجمالي الصرف', 'Total Spent') }}</p>
                <p class="text-xl font-bold text-orange-600">{{ number_format($totalSpent) }} {{ $currency }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $tr('متبقي', 'Remaining') }}: {{ number_format($totalRemaining) }} {{ $currency }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-xs text-gray-500 mb-1">{{ $tr('مصاريف هذا الشهر', 'Expenses This Month') }}</p>
                <p class="text-xl font-bold text-red-600">{{ number_format($companyExpensesMonth) }} {{ $currency }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $tr('هذا العام', 'This year') }}: {{ number_format($companyExpensesYear) }} {{ $currency }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-xs text-gray-500 mb-1">{{ $tr('الرواتب المدفوعة', 'Salaries Paid') }}</p>
                <p class="text-xl font-bold text-purple-600">{{ number_format($salaryYear) }} {{ $currency }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $tr('هذا العام', 'This year') }} {{ $year }}</p>
            </div>
        </div>

        {{-- ── Chart + Category breakdown ───────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Monthly expenses chart --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">
                <h3 class="font-bold text-gray-800 mb-4">{{ $tr('المصاريف الشهرية (آخر 6 أشهر)', 'Monthly Expenses (Last 6 Months)') }}</h3>
                <canvas id="expenseChartAccountant" height="120"></canvas>
            </div>

            {{-- Expense by category --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-bold text-gray-800 mb-4">{{ $tr('المصاريف حسب الفئة', 'Expenses by Category') }}</h3>
                @php
                    $maxCat = $expenseByCategory->max('total') ?: 1;
                    $catLabels = $isAr
                        ? ['rent' => 'إيجار', 'utilities' => 'مرافق', 'maintenance' => 'صيانة', 'salary' => 'رواتب', 'marketing' => 'تسويق', 'insurance' => 'تأمين', 'other' => 'أخرى']
                        : ['rent' => 'Rent', 'utilities' => 'Utilities', 'maintenance' => 'Maintenance', 'salary' => 'Salary', 'marketing' => 'Marketing', 'insurance' => 'Insurance', 'other' => 'Other'];
                @endphp
                @forelse($expenseByCategory as $cat)
                <div class="mb-3">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ $catLabels[$cat->category] ?? $cat->category }}</span>
                        <span class="font-medium">{{ number_format($cat->total) }} {{ $currency }}</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-teal-500 rounded-full" style="width: {{ round(($cat->total / $maxCat) * 100) }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-6">{{ $tr('لا توجد بيانات', 'No data') }}</p>
                @endforelse
            </div>
        </div>

        {{-- ── Budget overview + Recent expenses ───────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Budget overview --}}
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800">{{ $tr('نظرة عامة على الميزانية', 'Budget Overview') }}</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($budgetsOverview as $budget)
                    @php
                        $pct = $budget->usagePercent();
                        $barColor = $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-yellow-500' : 'bg-teal-500');
                        $statusColors = ['draft' => 'bg-gray-100 text-gray-600', 'approved' => 'bg-green-100 text-green-700', 'closed' => 'bg-red-100 text-red-700'];
                        $statusLabels = $isAr ? ['draft' => 'مسودة', 'approved' => 'معتمد', 'closed' => 'مغلق'] : ['draft' => 'Draft', 'approved' => 'Approved', 'closed' => 'Closed'];
                    @endphp
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-800">{{ $budget->title }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $statusColors[$budget->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $statusLabels[$budget->status] ?? $budget->status }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full {{ $barColor }} rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500 w-10 text-right">{{ $pct }}%</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-400 mt-1">
                            <span>{{ $tr('مُخصص', 'Allocated') }}: {{ number_format($budget->allocated_amount) }} {{ $currency }}</span>
                            <span>{{ $tr('صُرف', 'Spent') }}: {{ number_format($budget->spent_amount) }} {{ $currency }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-400 text-sm">{{ $tr('لا توجد ميزانيات', 'No budgets found') }}</div>
                    @endforelse
                </div>
            </div>

            {{-- Recent internal expenses --}}
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800">{{ $tr('آخر المصاريف الداخلية', 'Recent Internal Expenses') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-start font-medium">{{ $tr('الوصف', 'Description') }}</th>
                                <th class="px-4 py-3 text-start font-medium">{{ $tr('الفئة', 'Category') }}</th>
                                <th class="px-4 py-3 text-start font-medium">{{ $tr('المبلغ', 'Amount') }}</th>
                                <th class="px-4 py-3 text-start font-medium">{{ $tr('التاريخ', 'Date') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recentExpenses as $exp)
                            @php
                                $expCatLabels = $isAr
                                    ? ['rent' => 'إيجار', 'utilities' => 'مرافق', 'maintenance' => 'صيانة', 'salary' => 'رواتب', 'marketing' => 'تسويق', 'insurance' => 'تأمين', 'other' => 'أخرى']
                                    : ['rent' => 'Rent', 'utilities' => 'Utilities', 'maintenance' => 'Maintenance', 'salary' => 'Salary', 'marketing' => 'Marketing', 'insurance' => 'Insurance', 'other' => 'Other'];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ Str::limit($exp->description, 30) }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $expCatLabels[$exp->category] ?? $exp->category }}</td>
                                <td class="px-4 py-3 font-medium text-red-600">{{ number_format($exp->amount) }} {{ $currency }}</td>
                                <td class="px-4 py-3 text-gray-400">{{ \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">{{ $tr('لا توجد مصاريف', 'No expenses found') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── Payments section header ───────────────────────────────────── --}}
        <div class="flex items-center gap-3">
            <div class="h-px flex-1 bg-gray-200"></div>
            <span class="text-xs font-semibold uppercase tracking-widest text-blue-600 px-3 py-1 bg-blue-50 rounded-full border border-blue-200">
                {{ $tr('المدفوعات', 'Payments') }}
            </span>
            <div class="h-px flex-1 bg-gray-200"></div>
        </div>

        {{-- ── Payment tables ────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800">{{ $tr('آخر المدفوعات', 'Recent Payments') }}</h3>
                    <a href="{{ route('accountant.payments.index') }}" class="text-sm text-blue-600 hover:underline">{{ $tr('عرض الكل', 'View All') }}</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-start font-medium">{{ $tr('المستأجر', 'Tenant') }}</th>
                                <th class="px-4 py-3 text-start font-medium">{{ $tr('المبلغ', 'Amount') }}</th>
                                <th class="px-4 py-3 text-start font-medium">{{ $tr('الشهر', 'Month') }}</th>
                                <th class="px-4 py-3 text-start font-medium">{{ $tr('الحالة', 'Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recentPayments ?? [] as $pay)
                            @php
                                $pc = ['pending' => 'bg-yellow-100 text-yellow-700', 'paid' => 'bg-green-100 text-green-700', 'overdue' => 'bg-red-100 text-red-700'];
                                $pl = $isAr
                                    ? ['pending' => 'معلق', 'paid' => 'مدفوع', 'overdue' => 'متأخر']
                                    : ['pending' => 'Pending', 'paid' => 'Paid', 'overdue' => 'Overdue'];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">{{ $displayName($pay->tenant?->user?->name ?? null, $tr('مستأجر', 'Tenant')) }}</td>
                                <td class="px-4 py-3">{{ number_format($pay->amount) }} {{ $currency }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $pay->month }}/{{ $pay->year }}</td>
                                <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $pc[$pay->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $pl[$pay->status] ?? $pay->status }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">{{ $tr('لا توجد مدفوعات', 'No payments found') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b border-gray-100"><h3 class="font-bold text-gray-800">{{ $tr('المدفوعات المتأخرة', 'Overdue Payments') }}</h3></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-start font-medium">{{ $tr('المستأجر', 'Tenant') }}</th>
                                <th class="px-4 py-3 text-start font-medium">{{ $tr('المبلغ', 'Amount') }}</th>
                                <th class="px-4 py-3 text-start font-medium">{{ $tr('الشهر', 'Month') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($overduePayments ?? [] as $pay)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-red-600">{{ $displayName($pay->tenant?->user?->name ?? null, $tr('مستأجر', 'Tenant')) }}</td>
                                <td class="px-4 py-3">{{ number_format($pay->amount) }} {{ $currency }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $pay->month }}/{{ $pay->year }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">{{ $tr('لا توجد مدفوعات متأخرة', 'No overdue payments') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const chartLabels = @json($expenseChart->pluck('label'));
        const chartData   = @json($expenseChart->pluck('amount'));

        new Chart(document.getElementById('expenseChartAccountant'), {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: '{{ $tr('المصاريف', 'Expenses') }}',
                    data: chartData,
                    borderColor: '#0d9488',
                    backgroundColor: 'rgba(13,148,136,0.08)',
                    borderWidth: 2,
                    pointBackgroundColor: '#0d9488',
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString() } }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
