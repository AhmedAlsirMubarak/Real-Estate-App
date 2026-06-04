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

                <div class="grid grid-cols-2 gap-3 sm:gap-4 w-full lg:w-auto lg:min-w-[250px]">
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

        {{-- Internal Company Departments --}}
        <section>
            <div class="flex items-center gap-4 mb-4">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-1 rounded-full bg-gradient-to-b from-blue-600 to-indigo-600"></div>
                    <h2 class="text-lg font-black text-slate-800">{{ $tr('أقسام الشركة الداخلية', 'Internal Company Departments') }}</h2>
                </div>
                <div class="flex-1 h-px bg-slate-200"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                {{-- HR --}}
                <div class="rounded-2xl bg-white border border-slate-100 shadow-lg p-5 hover:shadow-xl transition">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="h-11 w-11 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base">{{ $tr('الموارد البشرية', 'HR') }}</h3>
                            <p class="text-xs text-slate-500">{{ $tr('الموظفون، العقود، الرواتب', 'Staff, Contracts & Salaries') }}</p>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <a href="{{ route('manager.employees.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-blue-50 text-slate-600 hover:text-blue-700 transition">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-sm font-medium flex-1">{{ $tr('الموظفون', 'Staff') }}</span>
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-semibold">{{ $stats['total_employees'] ?? 0 }}</span>
                        </a>
                        <a href="{{ route('manager.salaries.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-blue-50 text-slate-600 hover:text-blue-700 transition">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium flex-1">{{ $tr('الرواتب', 'Salaries') }}</span>
                        </a>
                        <a href="{{ route('manager.contracts.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-blue-50 text-slate-600 hover:text-blue-700 transition">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-sm font-medium flex-1">{{ $tr('العقود', 'Contracts') }}</span>
                        </a>
                    </div>
                </div>

                {{-- Finance --}}
                <div class="rounded-2xl bg-white border border-slate-100 shadow-lg p-5 hover:shadow-xl transition">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="h-11 w-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base">{{ $tr('المالية', 'Finance') }}</h3>
                            <p class="text-xs text-slate-500">{{ $tr('الميزانية، الإيرادات، المصروفات', 'Budget, Revenue & Expenses') }}</p>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <a href="{{ route('manager.budgets.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 transition">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                            </svg>
                            <span class="text-sm font-medium flex-1">{{ $tr('ميزانية الشركة', 'Company Budget') }}</span>
                        </a>
                        <a href="{{ route('manager.finance.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 transition">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span class="text-sm font-medium flex-1">{{ $tr('لوحة المالية', 'Finance Dashboard') }}</span>
                        </a>
                        <a href="{{ route('manager.expenses.index') }}?scope=company" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 transition">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="text-sm font-medium flex-1">{{ $tr('المصروفات الداخلية', 'Internal Expenses') }}</span>
                            <span class="text-xs bg-violet-100 text-violet-700 px-2 py-0.5 rounded-full font-semibold">{{ number_format($monthlyExpenses) }} {{ $currency }}</span>
                        </a>
                    </div>
                </div>

                {{-- Company Assets --}}
                <a href="{{ route('manager.assets.index') }}" class="rounded-2xl bg-white border border-slate-100 shadow-lg p-5 hover:shadow-xl hover:border-violet-200 transition flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="h-11 w-11 rounded-xl bg-violet-50 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-extrabold text-slate-800 text-base">{{ $tr('أصول الشركة', 'Company Assets') }}</h3>
                            <p class="text-xs text-slate-500">{{ $tr('الأجهزة والمعدات المُخصصة للموظفين', 'Equipment assigned to employees') }}</p>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
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

        {{-- ── Contract Expiry Alerts ────────────────────────────────────── --}}
        @if(isset($expiringContracts) && $expiringContracts->count() > 0)
        <section class="rounded-2xl border border-orange-200 bg-orange-50 shadow-md overflow-hidden">
            <div class="px-5 py-4 border-b border-orange-200 bg-orange-100/60 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <h3 class="font-extrabold text-orange-800">{{ $tr('عقود تنتهي قريباً', 'Contracts Expiring Soon') }}</h3>
                    <span class="bg-orange-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $expiringContracts->count() }}</span>
                </div>
                <a href="{{ route('manager.tenants.index') }}" class="text-orange-700 hover:text-orange-900 text-xs font-medium">{{ $tr('عرض الكل', 'View All') }}</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-orange-100/50 text-orange-700">
                        <tr>
                            <th class="px-4 py-3 text-right font-semibold">{{ $tr('المستأجر', 'Tenant') }}</th>
                            <th class="px-4 py-3 text-right font-semibold">{{ $tr('العقار / الوحدة', 'Property / Unit') }}</th>
                            <th class="px-4 py-3 text-right font-semibold">{{ $tr('تاريخ الانتهاء', 'End Date') }}</th>
                            <th class="px-4 py-3 text-right font-semibold">{{ $tr('متبقي', 'Days Left') }}</th>
                            <th class="px-4 py-3 text-right font-semibold">{{ $tr('تذكير واتساب', 'WhatsApp') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-orange-100">
                        @foreach($expiringContracts as $ec)
                        @php
                            $daysLeft = now()->diffInDays($ec->end_date, false);
                            $urgency  = $daysLeft <= 7 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700';
                            $tenantName = $ec->tenant?->user?->name ?? $tr('مستأجر', 'Tenant');
                            $phone      = $ec->tenant?->user?->phone ?? $ec->tenant?->phone;
                            $endFmt     = $ec->end_date->format('Y/m/d');
                            $waMessage  = $daysLeft <= 1
                                ? "السلام عليكم {$tenantName}،\nتذكير: عقد إيجارك ينتهي *غداً* بتاريخ {$endFmt}. يرجى التواصل مع المكتب لتجديد العقد."
                                : "السلام عليكم {$tenantName}،\nنود تذكيركم بأن عقد الإيجار سينتهي خلال *{$daysLeft} يوماً* بتاريخ {$endFmt}. يرجى التواصل لتجديد العقد أو ترتيب الإخلاء.";
                        @endphp
                        <tr class="hover:bg-orange-50/60">
                            <td class="px-4 py-3 font-medium text-slate-800">
                                <a href="{{ route('manager.tenants.show', $ec->tenant) }}" class="hover:text-blue-700">{{ $tenantName }}</a>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $ec->unit?->property?->name ?? '-' }}
                                @if($ec->unit?->unit_number) / {{ $ec->unit->unit_number }} @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700 font-medium">{{ $endFmt }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $urgency }}">
                                    {{ $daysLeft > 0 ? $daysLeft . ' ' . $tr('يوم', 'days') : $tr('اليوم', 'Today') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <x-whatsapp-button :phone="$phone" :message="$waMessage" size="sm" :label="$tr('تذكير', 'Remind')" />
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
        @endif

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
