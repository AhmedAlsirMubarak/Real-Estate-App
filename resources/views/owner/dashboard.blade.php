<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.س' : 'SAR';
        $displayName = function (?string $name, string $fallback = 'Owner') use ($isAr) {
            if ($name === null || $name === '') return $fallback;
            if ($isAr || ! preg_match('/\p{Arabic}/u', $name)) return $name;
            return $fallback;
        };
    @endphp
    <x-slot name="title">{{ $tr('لوحة المالك', 'Owner Dashboard') }}</x-slot>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ $tr('مرحباً،', 'Welcome,') }} {{ $displayName(auth()->user()->name ?? null, $tr('مالك', 'Owner')) }}</h2>
        <p class="text-gray-500 mt-1">{{ $tr('ملخص عقاراتك وإيراداتها', 'Summary of your properties and revenue') }} — {{ $tr('سنة', 'Year') }} {{ now()->year }}</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $tr('عقاراتي', 'My Properties') }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_properties'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $stats['total_units'] }} {{ $tr('وحدة إجمالاً', 'units total') }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $tr('وحدات مؤجرة', 'Rented Units') }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['rented_units'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $stats['available_units'] }} {{ $tr('متاحة', 'available') }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $tr('إيرادات هذا الشهر', 'This Month Revenue') }}</p>
                <p class="text-2xl font-bold text-emerald-700">{{ number_format($stats['monthly_revenue']) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $currency }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $tr('مدفوعات معلقة', 'Pending Payments') }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['pending_payments'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $tr('تحتاج متابعة', 'Need follow-up') }}</p>
            </div>
        </div>
    </div>

    {{-- Financial Summary --}}
    <div class="bg-gradient-to-l from-blue-900 to-blue-700 text-white rounded-xl shadow p-6 mb-6">
        <h3 class="font-bold text-lg mb-4">{{ $tr('ملخص مالي', 'Financial Summary') }} — {{ now()->year }}</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-blue-200 text-xs mb-1">{{ $tr('إجمالي الإيرادات', 'Total Revenue') }}</p>
                <p class="text-xl font-bold">{{ number_format($stats['total_revenue']) }} {{ $currency }}</p>
            </div>
            <div>
                <p class="text-blue-200 text-xs mb-1">{{ $tr('عمولة الشركة', 'Company Commission') }} ({{ $stats['commission_rate'] }}%)</p>
                <p class="text-xl font-bold text-red-300">- {{ number_format($stats['commission_amount']) }} {{ $currency }}</p>
            </div>
            <div>
                <p class="text-blue-200 text-xs mb-1">{{ $tr('المصروفات على العقارات', 'Property Expenses') }}</p>
                <p class="text-xl font-bold text-red-300">- {{ number_format($stats['expenses_total']) }} {{ $currency }}</p>
            </div>
            <div>
                <p class="text-blue-200 text-xs mb-1">{{ $tr('صافي أرباحك', 'Your Net Earnings') }}</p>
                <p class="text-2xl font-bold text-yellow-300">{{ number_format($stats['owner_earnings'] - $stats['expenses_total']) }} {{ $currency }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Revenue Chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow p-5">
            <h3 class="font-bold text-gray-800 mb-4">{{ $tr('الإيرادات الشهرية (آخر 6 أشهر)', 'Monthly Revenue (Last 6 Months)') }}</h3>
            <canvas id="revenueChart" height="120"></canvas>
        </div>

        {{-- Properties list --}}
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-bold text-gray-800 mb-3">{{ $tr('عقاراتي', 'My Properties') }}</h3>
            <div class="space-y-3">
                @foreach($properties as $property)
                <div class="border border-gray-100 rounded-lg p-3">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-semibold text-sm text-gray-800">{{ $property->name }}</span>
                        <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full">{{ $property->typeLabel() }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">{{ $property->address }}</p>
                    <div class="flex gap-3 text-xs">
                        <span class="text-green-600">{{ $property->units->where('status','rented')->count() }} {{ $tr('مؤجرة', 'rented') }}</span>
                        <span class="text-gray-400">{{ $property->units->where('status','available')->count() }} {{ $tr('متاحة', 'available') }}</span>
                        <span class="text-gray-400">{{ $property->units->count() }} {{ $tr('إجمالاً', 'total') }}</span>
                    </div>
                    @if($property->employee)
                    <p class="text-xs text-blue-600 mt-1">{{ $tr('مسؤول', 'Assigned') }}: {{ $displayName($property->employee->name ?? null, $tr('موظف', 'Employee')) }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent Payments --}}
    <div class="bg-white rounded-xl shadow">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">{{ $tr('آخر المدفوعات المُستلمة', 'Latest Received Payments') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs">
                    <tr>
                        <th class="px-4 py-3 text-right">{{ $tr('المستأجر', 'Tenant') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('العقار', 'Property') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('الوحدة', 'Unit') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('المبلغ', 'Amount') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('الشهر', 'Month') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('الحالة', 'Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentPayments as $pay)
                    @php
                        $sc = ['pending'=>'bg-yellow-100 text-yellow-700','paid'=>'bg-green-100 text-green-700','overdue'=>'bg-red-100 text-red-700'];
                        $sl = $isAr
                            ? ['pending' => 'معلق', 'paid' => 'مدفوع', 'overdue' => 'متأخر']
                            : ['pending' => 'Pending', 'paid' => 'Paid', 'overdue' => 'Overdue'];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $displayName($pay->tenant->user->name ?? null, $tr('مستأجر', 'Tenant')) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $pay->rentalContract->unit->property->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $pay->rentalContract->unit->unit_number ?? '—' }}</td>
                        <td class="px-4 py-3 font-semibold">{{ number_format($pay->amount) }} {{ $currency }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $pay->month }}/{{ $pay->year }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $sc[$pay->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $sl[$pay->status] ?? $pay->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">{{ $tr('لا توجد مدفوعات بعد', 'No payments yet') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: @json($revenueChart->pluck('label')),
                datasets: [{
                    label: '{{ $tr('الإيرادات', 'Revenue') }} ({{ $currency }})',
                    data: @json($revenueChart->pluck('amount')),
                    backgroundColor: 'rgba(30,58,138,0.8)',
                    borderColor: 'rgba(30,58,138,1)',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString('{{ $isAr ? 'ar-SA' : 'en-US' }}') + ' {{ $currency }}' } } }
            }
        });
    </script>
    @endpush
</x-app-layout>
