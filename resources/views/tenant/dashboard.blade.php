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
    <x-slot name="title">{{ $tr('لوحة التحكم', 'Dashboard') }}</x-slot>
    <div class="py-4">
        @php $contract = auth()->user()->tenant->activeContract ?? null; @endphp

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">{{ $tr('مرحباً،', 'Welcome,') }} {{ $displayName(auth()->user()->name ?? null, $tr('مستأجر', 'Tenant')) }} 👋</h2>
            <p class="text-gray-500 mt-1">{{ $tr('مرحباً بك في بوابة المستأجرين', 'Welcome to the tenant portal') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                </div>
                <div><p class="text-sm text-gray-500">{{ $tr('طلبات الصيانة المعلقة', 'Pending Maintenance Requests') }}</p><p class="text-2xl font-bold text-gray-800">{{ $stats['pending_maintenance'] ?? 0 }}</p></div>
            </div>
            <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><p class="text-sm text-gray-500">{{ $tr('المدفوعات المعلقة', 'Pending Payments') }}</p><p class="text-2xl font-bold text-gray-800">{{ $stats['pending_payments'] ?? 0 }}</p></div>
            </div>
            <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><p class="text-sm text-gray-500">{{ $tr('المدفوعات المتأخرة', 'Overdue Payments') }}</p><p class="text-2xl font-bold text-gray-800">{{ $stats['overdue_payments'] ?? 0 }}</p></div>
            </div>
        </div>

        {{-- Active Contract --}}
        @if($contract)
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl shadow p-6 mb-6">
            <h3 class="font-bold text-lg mb-4">{{ $tr('عقد الإيجار النشط', 'Active Rental Contract') }}</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div><p class="text-blue-200 text-xs mb-1">{{ $tr('العقار', 'Property') }}</p><p class="font-semibold">{{ $contract->unit->property->name ?? '-' }}</p></div>
                <div><p class="text-blue-200 text-xs mb-1">{{ $tr('رقم الوحدة', 'Unit Number') }}</p><p class="font-semibold">{{ $contract->unit->unit_number ?? '-' }}</p></div>
                <div><p class="text-blue-200 text-xs mb-1">{{ $tr('تاريخ البدء', 'Start Date') }}</p><p class="font-semibold">{{ $contract->start_date ? $contract->start_date->format('Y/m/d') : '-' }}</p></div>
                <div><p class="text-blue-200 text-xs mb-1">{{ $tr('تاريخ الانتهاء', 'End Date') }}</p><p class="font-semibold">{{ $contract->end_date ? $contract->end_date->format('Y/m/d') : '-' }}</p></div>
                <div><p class="text-blue-200 text-xs mb-1">{{ $tr('الإيجار الشهري', 'Monthly Rent') }}</p><p class="font-bold text-yellow-300 text-lg">{{ number_format($contract->monthly_rent) }} {{ $currency }}</p></div>
                <div><p class="text-blue-200 text-xs mb-1">{{ $tr('نوع الوحدة', 'Unit Type') }}</p><p class="font-semibold">{{ ['apartment' => $tr('شقة', 'Apartment'), 'shop' => $tr('محل', 'Shop'), 'office' => $tr('مكتب', 'Office')] [$contract->unit->type ?? ''] ?? '-' }}</p></div>
            </div>
        </div>
        @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6 text-yellow-800">{{ $tr('لا يوجد عقد إيجار نشط حالياً', 'No active rental contract right now') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Recent Maintenance --}}
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800">{{ $tr('آخر طلبات الصيانة', 'Recent Maintenance Requests') }}</h3>
                    <a href="{{ route('tenant.maintenance.index') }}" class="text-sm text-blue-600 hover:underline">{{ $tr('عرض الكل', 'View All') }}</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($recentMaintenance ?? [] as $req)
                    @php
                        $sc=['pending'=>'bg-yellow-100 text-yellow-700','in_progress'=>'bg-blue-100 text-blue-700','completed'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'];
                        $sl = $isAr
                            ? ['pending' => 'معلق', 'in_progress' => 'جاري', 'completed' => 'مكتمل', 'rejected' => 'مرفوض']
                            : ['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'rejected' => 'Rejected'];
                    @endphp
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div>
                            <p class="font-medium text-sm">{{ $req->title }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $req->created_at->format('Y/m/d') }}</p>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $sc[$req->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $sl[$req->status] ?? $req->status }}</span>
                    </div>
                    @empty
                    <p class="px-5 py-6 text-center text-gray-400 text-sm">{{ $tr('لا توجد طلبات صيانة', 'No maintenance requests') }}</p>
                    @endforelse
                </div>
            </div>

            {{-- Recent Payments --}}
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800">{{ $tr('آخر المدفوعات', 'Recent Payments') }}</h3>
                    <a href="{{ route('tenant.payments.index') }}" class="text-sm text-blue-600 hover:underline">{{ $tr('عرض الكل', 'View All') }}</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($recentPayments ?? [] as $pay)
                    @php
                        $pc=['pending'=>'bg-yellow-100 text-yellow-700','paid'=>'bg-green-100 text-green-700','overdue'=>'bg-red-100 text-red-700'];
                        $pl = $isAr
                            ? ['pending' => 'معلق', 'paid' => 'مدفوع', 'overdue' => 'متأخر']
                            : ['pending' => 'Pending', 'paid' => 'Paid', 'overdue' => 'Overdue'];
                    @endphp
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div>
                            <p class="font-medium text-sm">{{ $pay->month }}/{{ $pay->year }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ number_format($pay->amount) }} {{ $currency }}</p>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $pc[$pay->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $pl[$pay->status] ?? $pay->status }}</span>
                    </div>
                    @empty
                    <p class="px-5 py-6 text-center text-gray-400 text-sm">{{ $tr('لا توجد مدفوعات', 'No payments') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
