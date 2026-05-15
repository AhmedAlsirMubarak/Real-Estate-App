<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.س' : 'SAR';
        $displayName = function (?string $name, string $fallback = 'User') use ($isAr) {
            if ($name === null || $name === '') return $fallback;
            if ($isAr || ! preg_match('/\p{Arabic}/u', $name)) return $name;
            return $fallback;
        };
    @endphp
    <x-slot name="title">{{ $tr('لوحة التحكم - المحاسب', 'Dashboard - Accountant') }}</x-slot>
    <div class="py-4">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
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
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('المستأجر', 'Tenant') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('المبلغ', 'Amount') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('الشهر', 'Month') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('الحالة', 'Status') }}</th>
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
                                <td class="px-4 py-3 font-medium">{{ $displayName($pay->tenant->user->name ?? null, $tr('مستأجر', 'Tenant')) }}</td>
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
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('المستأجر', 'Tenant') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('المبلغ', 'Amount') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('الشهر', 'Month') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($overduePayments ?? [] as $pay)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-red-600">{{ $displayName($pay->tenant->user->name ?? null, $tr('مستأجر', 'Tenant')) }}</td>
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
</x-app-layout>
