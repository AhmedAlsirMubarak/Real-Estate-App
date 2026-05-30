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
        $months = $isAr
            ? [1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس', 9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر']
            : [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];
    @endphp

    <x-slot name="title">{{ $tr('لوحة الموظف', 'Employee Dashboard') }}</x-slot>

    <div class="py-4 space-y-6">
        <div class="bg-white rounded-xl shadow p-4 flex flex-wrap items-end gap-3">
            <form method="GET" action="{{ route('employee.dashboard') }}" class="flex items-end gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">{{ $tr('سنة المتابعة', 'Tracking Year') }}</label>
                    <select name="year" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ (int) ($year ?? now()->year) === $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    {{ $tr('تحديث', 'Refresh') }}
                </button>
            </form>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-white rounded-xl shadow p-4">
                <p class="text-xs text-gray-500">{{ $tr('العقارات المسندة', 'Assigned Properties') }}</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_properties'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4">
                <p class="text-xs text-gray-500">{{ $tr('إجمالي الوحدات', 'Total Units') }}</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_units'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4">
                <p class="text-xs text-gray-500">{{ $tr('إيجار محصل', 'Collected Rent') }} ({{ $year }})</p>
                <p class="text-2xl font-bold text-green-700 mt-1">{{ number_format($stats['year_paid_rent'] ?? 0) }} {{ $currency }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4">
                <p class="text-xs text-gray-500">{{ $tr('إيجار متأخر', 'Overdue Rent') }} ({{ $year }})</p>
                <p class="text-2xl font-bold text-red-700 mt-1">{{ number_format($stats['year_overdue_rent'] ?? 0) }} {{ $currency }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4">
                <p class="text-xs text-gray-500">{{ $tr('عمولتك الإجمالية', 'Your Total Commission') }} ({{ $year }})</p>
                <p class="text-2xl font-bold text-indigo-700 mt-1">{{ number_format($commissionStats['total'] ?? 0) }} {{ $currency }}</p>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-xl shadow p-4">
                <p class="text-xs text-blue-600">{{ $tr('عمولة الإحالة', 'Referral Commission') }} ({{ $year }})</p>
                <p class="text-2xl font-bold {{ $referralCommissionTotal > 0 ? 'text-blue-700' : 'text-gray-400' }} mt-1">
                    {{ number_format($referralCommissionTotal, 2) }} {{ $currency }}
                </p>
                @if($referredProperties->isNotEmpty())
                <p class="text-xs text-blue-400 mt-1">{{ $referredProperties->count() }} {{ $tr('عقار محال', 'referred props') }}</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800">{{ $tr('تحصيل الإيجار حسب العقار (يناير - ديسمبر)', 'Rent Collection by Property (Jan - Dec)') }} ({{ $year }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 text-xs">
                            <tr>
                                <th class="px-4 py-3 text-right">{{ $tr('العقار', 'Property') }}</th>
                                <th class="px-4 py-3 text-right">{{ $tr('محصل', 'Collected') }}</th>
                                <th class="px-4 py-3 text-right">{{ $tr('معلق', 'Pending') }}</th>
                                <th class="px-4 py-3 text-right">{{ $tr('متأخر', 'Overdue') }}</th>
                                <th class="px-4 py-3 text-right">{{ $tr('عدد الدفعات', 'Payments Count') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($propertyRentSummary ?? [] as $row)
                                <tr>
                                    <td class="px-4 py-3 font-medium">{{ $row['property']->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-green-700 font-semibold">{{ number_format($row['paid']) }} {{ $currency }}</td>
                                    <td class="px-4 py-3 text-yellow-700 font-semibold">{{ number_format($row['pending']) }} {{ $currency }}</td>
                                    <td class="px-4 py-3 text-red-700 font-semibold">{{ number_format($row['overdue']) }} {{ $currency }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $row['count'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">{{ $tr('لا توجد بيانات تحصيل', 'No collection data') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800">{{ $tr('آخر العمولات المحتسبة', 'Latest Calculated Commissions') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 text-xs">
                            <tr>
                                <th class="px-4 py-3 text-right">{{ $tr('النوع', 'Type') }}</th>
                                <th class="px-4 py-3 text-right">{{ $tr('العقار', 'Property') }}</th>
                                <th class="px-4 py-3 text-right">{{ $tr('النسبة', 'Rate') }}</th>
                                <th class="px-4 py-3 text-right">{{ $tr('العمولة', 'Commission') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentCommissions as $commission)
                                <tr>
                                    <td class="px-4 py-3">{{ $commission->typeLabel() }}</td>
                                    <td class="px-4 py-3">{{ $commission->property->name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ number_format($commission->rate, 2) }}%</td>
                                    <td class="px-4 py-3 font-semibold text-indigo-700">{{ number_format($commission->commission_amount, 2) }} {{ $currency }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">{{ $tr('لا توجد عمولات حتى الآن', 'No commissions yet') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Referral commission details --}}
        @if($referredProperties->isNotEmpty())
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $tr('عمولة الإحالة — العقارات التي أحلتها', 'Referral Commission — Properties You Referred') }}
                </h3>
                @if($referralCommissionTotal > 0)
                <span class="text-sm font-bold text-green-700 bg-green-50 border border-green-200 px-3 py-1 rounded-full">
                    {{ $tr('الإجمالي', 'Total') }}: {{ number_format($referralCommissionTotal, 2) }} {{ $currency }}
                </span>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs">
                        <tr>
                            <th class="px-4 py-3 text-right">{{ $tr('العقار', 'Property') }}</th>
                            <th class="px-4 py-3 text-right">{{ $tr('النوع', 'Type') }}</th>
                            <th class="px-4 py-3 text-right">{{ $tr('نسبة العمولة', 'Commission Rate') }}</th>
                            <th class="px-4 py-3 text-right">{{ $tr('الإيجار المحصَّل', 'Collected Rent') }} ({{ $year }})</th>
                            <th class="px-4 py-3 text-right">{{ $tr('العمولة المستحقة', 'Commission Earned') }} ({{ $year }})</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($referredProperties as $rp)
                        @php
                            $rpCollected = $referralPropertyRevenue[$rp->id] ?? 0;
                            $rpEarned    = ($rp->referral_commission_rate ?? 0) / 100 * $rpCollected;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $rp->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $rp->typeLabel() }}</td>
                            <td class="px-4 py-3">
                                @if($rp->referral_commission_rate)
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-semibold">{{ $rp->referral_commission_rate }}%</span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ number_format($rpCollected, 2) }} {{ $currency }}</td>
                            <td class="px-4 py-3 font-semibold {{ $rpEarned > 0 ? 'text-green-700' : 'text-gray-400' }}">
                                {{ number_format($rpEarned, 2) }} {{ $currency }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">{{ $tr('العقارات المسندة إليك', 'Properties Assigned to You') }}</h3>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($properties as $property)
                    @php
                        $supportsSale = in_array($property->purpose, ['sale', 'both'], true)
                            || $property->units->contains(fn($unit) => in_array($unit->listing_type, ['sale', 'both'], true));
                        $suggestedSaleAmount = (float) $property->units
                            ->filter(fn($unit) => in_array($unit->listing_type, ['sale', 'both'], true))
                            ->whereNotNull('sale_price')
                            ->sum('sale_price');
                    @endphp
                    <div class="border border-gray-100 rounded-xl p-4">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h4 class="font-bold text-gray-800">{{ $property->name }}</h4>
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $property->status === 'sold' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $property->statusLabel() }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-2">{{ $property->address }}</p>
                        <p class="text-xs text-gray-500 mb-2">
                            {{ $tr('المالك', 'Owner') }}:
                            {{ $displayName($property->owner->user->name ?? null, $tr('مملوك للشركة', 'Company Owned')) }}
                        </p>
                        <div class="flex gap-3 text-xs mb-3">
                            <span class="text-green-600">{{ $property->units->where('status', 'rented')->count() }} {{ $tr('مؤجرة', 'rented') }}</span>
                            <span class="text-gray-500">{{ $property->units->where('status', 'available')->count() }} {{ $tr('متاحة', 'available') }}</span>
                            <span class="text-gray-500">{{ $property->units->count() }} {{ $tr('إجمالاً', 'total') }}</span>
                        </div>

                        @if($property->status !== 'sold' && $supportsSale)
                            <form method="POST" action="{{ route('employee.properties.mark-sold', $property) }}" class="space-y-2">
                                @csrf
                                @method('PATCH')
                                <label class="block text-xs text-gray-500">{{ $tr('قيمة البيع', 'Sale Amount') }} ({{ $currency }})</label>
                                <input type="number"
                                       step="0.01"
                                       min="0"
                                       name="sale_amount"
                                       value="{{ old('sale_amount', $suggestedSaleAmount > 0 ? number_format($suggestedSaleAmount, 2, '.', '') : '') }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                <input type="text"
                                       name="notes"
                                       value="{{ old('notes') }}"
                                       placeholder="{{ $tr('ملاحظة (اختياري)', 'Note (optional)') }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition">
                                    {{ $tr('تسجيل العقار كمباع + احتساب العمولة', 'Mark as Sold + Calculate Commission') }}
                                </button>
                            </form>
                        @elseif(! $supportsSale)
                            <p class="text-xs text-blue-700 bg-blue-50 border border-blue-100 rounded-lg p-2">
                                {{ $tr('هذا العقار مخصص للإيجار فقط', 'This property is rent-only') }}
                            </p>
                        @else
                            <p class="text-xs text-green-700 bg-green-50 border border-green-100 rounded-lg p-2">
                                {{ $tr('تم تسجيل العقار كمباع', 'This property is already marked as sold') }}
                            </p>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-400">{{ $tr('لا توجد عقارات مسندة لك حالياً', 'No properties currently assigned to you') }}</p>
                @endforelse
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800">{{ $tr('طلبات الصيانة المعلقة', 'Pending Maintenance Requests') }}</h3>
                    <a href="{{ route('employee.maintenance.index') }}" class="text-sm text-blue-600 hover:underline">{{ $tr('عرض الكل', 'View All') }}</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($pendingMaintenance as $requestItem)
                        <a href="{{ route('employee.maintenance.show', $requestItem) }}" class="block px-5 py-3 hover:bg-gray-50">
                            <p class="text-sm font-medium text-gray-800">{{ $requestItem->title }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $requestItem->unit->property->name ?? '-' }} / {{ $requestItem->unit->unit_number ?? '-' }}</p>
                        </a>
                    @empty
                        <p class="px-5 py-6 text-center text-gray-400 text-sm">{{ $tr('لا توجد طلبات صيانة معلقة', 'No pending maintenance requests') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl shadow">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800">{{ $tr('دفعات تحتاج تأكيد', 'Payments Waiting Confirmation') }}</h3>
                    <a href="{{ route('employee.payments.index') }}" class="text-sm text-blue-600 hover:underline">{{ $tr('عرض الكل', 'View All') }}</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($pendingPayments as $paymentItem)
                        <div class="px-5 py-3">
                            <p class="text-sm font-medium text-gray-800">{{ $displayName($paymentItem->tenant->user->name ?? null, $tr('مستأجر', 'Tenant')) }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $paymentItem->rentalContract->unit->property->name ?? '-' }} / {{ $paymentItem->rentalContract->unit->unit_number ?? '-' }}</p>
                            <p class="text-xs text-blue-700 mt-1">{{ number_format($paymentItem->amount) }} {{ $currency }} · {{ $months[$paymentItem->month] ?? $paymentItem->month }} {{ $paymentItem->year }}</p>
                        </div>
                    @empty
                        <p class="px-5 py-6 text-center text-gray-400 text-sm">{{ $tr('لا توجد دفعات معلقة', 'No pending payments') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
