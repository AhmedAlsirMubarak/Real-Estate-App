<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $numLocale = $isAr ? 'ar-OM' : 'en-US';
        $currency = $isAr ? 'ر.ع' : 'OMR';
        $unitsChartLabels = [$tr('مؤجرة', 'Rented'), $tr('متاحة', 'Available')];
        $revenueDatasetLabel = $tr('الإيرادات', 'Revenue');
        $displayName = function (?string $name, string $fallback = 'User') use ($isAr) {
            if ($name === null || $name === '') {
                return '-';
            }

            if ($isAr || ! preg_match('/\p{Arabic}/u', $name)) {
                return $name;
            }

            return $fallback;
        };
    @endphp
    <x-slot name="title">{{ $tr('لوحة التحكم', 'Dashboard') }}</x-slot>

    @php
        $totalUnits = (int) ($stats['total_units'] ?? 0);
        $rentedUnits = (int) ($stats['rented_units'] ?? 0);
        $availableUnits = (int) ($stats['available_units'] ?? 0);
        $occupancyRate = $totalUnits > 0 ? round(($rentedUnits / $totalUnits) * 100) : 0;
        $monthlyRevenue = (float) ($stats['monthly_revenue'] ?? 0);
        $monthlyExpenses = (float) ($stats['monthly_expenses'] ?? 0);
        $monthlyNet = $monthlyRevenue - $monthlyExpenses;
    @endphp

    <div class="py-4 space-y-6">
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-blue-900 to-indigo-900 p-6 sm:p-8 text-white shadow-2xl">
            <div class="absolute -top-20 -left-10 h-56 w-56 rounded-full bg-cyan-400/20 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-6 h-64 w-64 rounded-full bg-fuchsia-500/20 blur-3xl"></div>

            <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-xs font-medium">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        {{ $tr('لوحة إدارة حديثة', 'Modern Admin Panel') }}
                    </p>
                    <h2 class="mt-3 text-2xl sm:text-3xl font-black tracking-tight">{{ $tr('مرحباً بك في مركز التحكم الذكي', 'Welcome to your smart control center') }}</h2>
                    <p class="mt-2 text-sm sm:text-base text-blue-100/90 max-w-2xl">
                        {{ $tr('متابعة الأداء المالي، العقارات، الصيانة، والمدفوعات من واجهة احترافية متقدمة.', 'Track financial performance, properties, maintenance, and payments from a premium professional interface.') }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:gap-4 min-w-[250px]">
                    <div class="rounded-2xl bg-white/10 border border-white/20 p-4 backdrop-blur">
                        <p class="text-xs text-blue-100/80">{{ $tr('إيراد الشهر', 'Monthly Revenue') }}</p>
                        <p class="text-xl font-extrabold mt-1">{{ number_format($monthlyRevenue) }}</p>
                        <p class="text-[11px] text-blue-100/70">{{ $currency }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 border border-white/20 p-4 backdrop-blur">
                        <p class="text-xs text-blue-100/80">{{ $tr('صافي الشهر', 'Monthly Net') }}</p>
                        <p class="text-xl font-extrabold mt-1 {{ $monthlyNet >= 0 ? 'text-emerald-300' : 'text-red-300' }}">{{ number_format($monthlyNet) }}</p>
                        <p class="text-[11px] text-blue-100/70">{{ $tr('بعد المصروفات', 'After expenses') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="group rounded-2xl bg-white shadow-lg border border-slate-100 p-5 hover:shadow-xl transition">
                <p class="text-xs text-slate-500">{{ $tr('إجمالي العقارات', 'Total Properties') }}</p>
                <p class="text-3xl font-black text-slate-800 mt-2">{{ $stats['total_properties'] ?? 0 }}</p>
                <p class="mt-2 text-xs text-slate-500">
                    <span class="text-blue-600 font-semibold">{{ $stats['rent_properties'] ?? 0 }} {{ $tr('إيجار', 'Rent') }}</span>
                    ·
                    <span class="text-amber-600 font-semibold">{{ $stats['sale_properties'] ?? 0 }} {{ $tr('بيع', 'Sale') }}</span>
                </p>
            </div>

            <div class="group rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-lg p-5 hover:shadow-xl transition">
                <p class="text-xs text-blue-100">{{ $tr('إجمالي الوحدات', 'Total Units') }}</p>
                <p class="text-3xl font-black mt-2">{{ $totalUnits }}</p>
                <p class="mt-2 text-xs text-blue-100/90">{{ $tr('إدارة وحدات متعددة', 'Manage multiple units') }}</p>
            </div>

            <div class="group rounded-2xl bg-white shadow-lg border border-slate-100 p-5 hover:shadow-xl transition">
                <p class="text-xs text-slate-500">{{ $tr('نسبة الإشغال', 'Occupancy Rate') }}</p>
                <p class="text-3xl font-black text-emerald-600 mt-2">{{ $occupancyRate }}%</p>
                <div class="mt-3 h-2 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-cyan-500" style="width: {{ $occupancyRate }}%"></div>
                </div>
            </div>

            <div class="group rounded-2xl bg-white shadow-lg border border-slate-100 p-5 hover:shadow-xl transition">
                <p class="text-xs text-slate-500">{{ $tr('المدفوعات المتأخرة', 'Overdue Payments') }}</p>
                <p class="text-3xl font-black text-red-600 mt-2">{{ $stats['overdue_payments'] ?? 0 }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ $tr('تحتاج متابعة عاجلة', 'Needs immediate follow-up') }}</p>
            </div>

            <div class="group rounded-2xl bg-white shadow-lg border border-slate-100 p-5 hover:shadow-xl transition">
                <p class="text-xs text-slate-500">{{ $tr('المستأجرون', 'Tenants') }}</p>
                <p class="text-3xl font-black text-slate-800 mt-2">{{ $stats['total_tenants'] ?? 0 }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ $tr('قاعدة عملاء نشطة', 'Active client base') }}</p>
            </div>

            <div class="group rounded-2xl bg-white shadow-lg border border-slate-100 p-5 hover:shadow-xl transition">
                <p class="text-xs text-slate-500">{{ $tr('الموظفون', 'Employees') }}</p>
                <p class="text-3xl font-black text-slate-800 mt-2">{{ $stats['total_employees'] ?? 0 }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ $tr('فرق التشغيل', 'Operations teams') }}</p>
            </div>

            <div class="group rounded-2xl bg-white shadow-lg border border-slate-100 p-5 hover:shadow-xl transition">
                <p class="text-xs text-slate-500">{{ $tr('طلبات الصيانة المعلقة', 'Pending Maintenance Requests') }}</p>
                <p class="text-3xl font-black text-amber-600 mt-2">{{ $stats['pending_maintenance'] ?? 0 }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ $tr('حالات تحتاج جدولة', 'Cases requiring scheduling') }}</p>
            </div>

            <div class="group rounded-2xl bg-white shadow-lg border border-slate-100 p-5 hover:shadow-xl transition">
                <p class="text-xs text-slate-500">{{ $tr('مصروفات الشهر', 'Monthly Expenses') }}</p>
                <p class="text-3xl font-black text-violet-600 mt-2">{{ number_format($monthlyExpenses) }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ $currency }}</p>
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 rounded-2xl bg-white border border-slate-100 shadow-lg p-5 sm:p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-extrabold text-slate-800 text-lg">{{ $tr('الإيرادات الشهرية (آخر 6 أشهر)', 'Monthly Revenue (Last 6 Months)') }}</h3>
                    <span class="text-xs px-3 py-1 rounded-full bg-blue-50 text-blue-600 border border-blue-100">{{ $tr('تحليل الاتجاه', 'Trend Analysis') }}</span>
                </div>
                <div class="relative h-[320px] sm:h-[360px]">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-100 shadow-lg p-5 sm:p-6 flex flex-col">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-extrabold text-slate-800 text-lg">{{ $tr('توزيع حالة الوحدات', 'Units Status Distribution') }}</h3>
                    <span class="text-xs px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100">{{ $tr('مباشر', 'Live') }}</span>
                </div>
                <div class="flex-1 h-[240px] flex items-center justify-center">
                    <canvas id="unitsChart"></canvas>
                </div>
                <div class="grid grid-cols-2 gap-3 mt-5">
                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-2 text-sm">
                        <p class="text-slate-500 text-xs">{{ $tr('مؤجرة', 'Rented') }}</p>
                        <p class="font-bold text-slate-800">{{ $rentedUnits }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-2 text-sm">
                        <p class="text-slate-500 text-xs">{{ $tr('متاحة', 'Available') }}</p>
                        <p class="font-bold text-slate-800">{{ $availableUnits }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="rounded-2xl bg-white border border-slate-100 shadow-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80">
                    <h3 class="font-extrabold text-slate-800">{{ $tr('آخر طلبات الصيانة', 'Latest Maintenance Requests') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-500">
                            <tr>
                                <th class="px-4 py-3 text-right font-semibold">{{ $tr('العنوان', 'Title') }}</th>
                                <th class="px-4 py-3 text-right font-semibold">{{ $tr('المستأجر', 'Tenant') }}</th>
                                <th class="px-4 py-3 text-right font-semibold">{{ $tr('الأولوية', 'Priority') }}</th>
                                <th class="px-4 py-3 text-right font-semibold">{{ $tr('الحالة', 'Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentMaintenance ?? [] as $req)
                                @php
                                    $priorityClasses = ['low' => 'bg-slate-100 text-slate-700', 'medium' => 'bg-blue-100 text-blue-700', 'high' => 'bg-orange-100 text-orange-700', 'urgent' => 'bg-red-100 text-red-700'];
                                    $priorityLabels = $isAr
                                        ? ['low' => 'منخفضة', 'medium' => 'متوسطة', 'high' => 'عالية', 'urgent' => 'عاجل']
                                        : ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'];
                                    $statusClasses = ['pending' => 'bg-amber-100 text-amber-700', 'in_progress' => 'bg-blue-100 text-blue-700', 'completed' => 'bg-emerald-100 text-emerald-700', 'rejected' => 'bg-red-100 text-red-700'];
                                    $statusLabels = $isAr
                                        ? ['pending' => 'معلق', 'in_progress' => 'جاري', 'completed' => 'مكتمل', 'rejected' => 'مرفوض']
                                        : ['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'rejected' => 'Rejected'];
                                @endphp
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3 font-semibold text-slate-700">{{ $req->title }}</td>
                                    <td class="px-4 py-3 text-slate-500">{{ $displayName($req->tenant->user->name ?? null, $tr('مستأجر', 'Tenant')) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $priorityClasses[$req->priority] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ $priorityLabels[$req->priority] ?? $req->priority }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$req->status] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ $statusLabels[$req->status] ?? $req->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-slate-400">{{ $tr('لا توجد طلبات صيانة', 'No maintenance requests') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-100 shadow-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80">
                    <h3 class="font-extrabold text-slate-800">{{ $tr('آخر المدفوعات', 'Latest Payments') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-500">
                            <tr>
                                <th class="px-4 py-3 text-right font-semibold">{{ $tr('المستأجر', 'Tenant') }}</th>
                                <th class="px-4 py-3 text-right font-semibold">{{ $tr('المبلغ', 'Amount') }}</th>
                                <th class="px-4 py-3 text-right font-semibold">{{ $tr('الشهر', 'Month') }}</th>
                                <th class="px-4 py-3 text-right font-semibold">{{ $tr('الحالة', 'Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentPayments ?? [] as $pay)
                                @php
                                    $paymentClasses = ['pending' => 'bg-amber-100 text-amber-700', 'paid' => 'bg-emerald-100 text-emerald-700', 'overdue' => 'bg-red-100 text-red-700'];
                                    $paymentLabels = $isAr
                                        ? ['pending' => 'معلق', 'paid' => 'مدفوع', 'overdue' => 'متأخر']
                                        : ['pending' => 'Pending', 'paid' => 'Paid', 'overdue' => 'Overdue'];
                                @endphp
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3 font-semibold text-slate-700">{{ $displayName($pay->tenant->user->name ?? null, $tr('مستأجر', 'Tenant')) }}</td>
                                    <td class="px-4 py-3 font-bold text-slate-800">{{ number_format($pay->amount) }} {{ $currency }}</td>
                                    <td class="px-4 py-3 text-slate-500">{{ $pay->month }}/{{ $pay->year }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $paymentClasses[$pay->status] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ $paymentLabels[$pay->status] ?? $pay->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-slate-400">{{ $tr('لا توجد مدفوعات', 'No payments') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const revenueLabels = @json($revenueChart->pluck('label'));
        const revenueAmounts = @json($revenueChart->pluck('amount'));

        const isArLocale = @json($isAr);
        const numberLocale = @json($numLocale);
        const currency = @json($currency);

        Chart.defaults.font.family = isArLocale ? 'Cairo, sans-serif' : 'Sora, sans-serif';
        Chart.defaults.color = '#64748b';

        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueGradient = revenueCtx.createLinearGradient(0, 0, 0, 260);
        revenueGradient.addColorStop(0, 'rgba(37, 99, 235, 0.35)');
        revenueGradient.addColorStop(1, 'rgba(37, 99, 235, 0.02)');

        if (window.revenueChartInstance) {
            window.revenueChartInstance.destroy();
        }

        window.revenueChartInstance = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: @json($revenueDatasetLabel),
                    data: revenueAmounts,
                    borderColor: '#2563eb',
                    backgroundColor: revenueGradient,
                    fill: true,
                    borderWidth: 3,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#2563eb',
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
                            label: (ctx) => `${Number(ctx.parsed.y).toLocaleString(numberLocale)} ${currency}`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148,163,184,0.15)' },
                        ticks: {
                            callback: value => `${Number(value).toLocaleString(numberLocale)} ${currency}`
                        }
                    }
                }
            }
        });

        const unitsCanvas = document.getElementById('unitsChart');
        if (window.unitsChartInstance) {
            window.unitsChartInstance.destroy();
        }

        window.unitsChartInstance = new Chart(unitsCanvas, {
            type: 'doughnut',
            data: {
                labels: @json($unitsChartLabels),
                datasets: [{
                    data: [{{ $rentedUnits }}, {{ $availableUnits }}],
                    backgroundColor: ['#2563eb', '#cbd5e1'],
                    borderColor: ['#ffffff', '#ffffff'],
                    borderWidth: 3,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.label}: ${Number(ctx.parsed).toLocaleString(numberLocale)}`
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
