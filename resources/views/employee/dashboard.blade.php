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
        <div class="bg-white rounded-xl shadow p-4 flex flex-wrap items-center justify-between gap-3">
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
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('employee.tenants.create') }}"
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    {{ $tr('إضافة مستأجر', 'Add Tenant') }}
                </a>
                <a href="{{ route('employee.properties.create') }}"
                   class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    {{ $tr('إضافة عقار', 'Add Property') }}
                </a>
            </div>
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

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">{{ $tr('تحصيل الإيجار حسب العقار', 'Rent Collection by Property') }} ({{ $year }})</h3>
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

        {{-- Leave Request & History --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            {{-- Request Leave Form --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <h3 class="font-bold text-gray-800">{{ $tr('طلب إجازة', 'Request Leave') }}</h3>
                </div>
                <div class="p-5">
                    @if(session('leave_success'))
                        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('leave_success') }}</div>
                    @endif
                    @if($errors->has('type') || $errors->has('start_date') || $errors->has('end_date'))
                        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                            @foreach(['type','start_date','end_date','reason'] as $f)
                                @error($f)<p>{{ $message }}</p>@enderror
                            @endforeach
                        </div>
                    @endif
                    <form method="POST" action="{{ route('employee.leaves.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">{{ $tr('نوع الإجازة', 'Leave Type') }}</label>
                            <select name="type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="annual">{{ $tr('سنوية', 'Annual') }}</option>
                                <option value="sick">{{ $tr('مرضية', 'Sick') }}</option>
                                <option value="unpaid">{{ $tr('بدون راتب', 'Unpaid') }}</option>
                                <option value="emergency">{{ $tr('طارئة', 'Emergency') }}</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ $tr('من', 'From') }}</label>
                                <input type="date" name="start_date" required min="{{ now()->toDateString() }}"
                                       value="{{ old('start_date') }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ $tr('إلى', 'To') }}</label>
                                <input type="date" name="end_date" required min="{{ now()->toDateString() }}"
                                       value="{{ old('end_date') }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">{{ $tr('السبب (اختياري)', 'Reason (optional)') }}</label>
                            <textarea name="reason" rows="3" maxlength="1000"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none"
                                      placeholder="{{ $tr('اذكر سبب طلب الإجازة...', 'Briefly explain your leave reason...') }}">{{ old('reason') }}</textarea>
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                            {{ $tr('إرسال الطلب', 'Submit Request') }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Leave History --}}
            <div class="lg:col-span-3 bg-white rounded-xl shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <h3 class="font-bold text-gray-800">{{ $tr('سجل إجازاتي', 'My Leave History') }}</h3>
                    @php $pendingCount = $myLeaves->where('status', 'pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="ml-auto bg-yellow-100 text-yellow-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }} {{ $tr('قيد المراجعة', 'pending') }}</span>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs">
                            <tr>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('النوع', 'Type') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('من', 'From') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('إلى', 'To') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('الأيام', 'Days') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('الحالة', 'Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($myLeaves as $leave)
                            @php
                                $typeColors   = ['annual'=>'bg-blue-100 text-blue-700','sick'=>'bg-orange-100 text-orange-700','unpaid'=>'bg-gray-100 text-gray-700','emergency'=>'bg-red-100 text-red-700'];
                                $statusColors = ['pending'=>'bg-yellow-100 text-yellow-700','approved'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $typeColors[$leave->type] ?? 'bg-gray-100 text-gray-700' }}">{{ $leave->typeLabel() }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $leave->start_date->format('Y/m/d') }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $leave->end_date->format('Y/m/d') }}</td>
                                <td class="px-4 py-3">
                                    <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $leave->days }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$leave->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $leave->statusLabel() }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400 text-xs">{{ $tr('لم تقدم أي طلب إجازة بعد', 'No leave requests yet') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Referral commission details --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            {{-- Header with combined total --}}
            <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $tr('عمولة الإحالة', 'Referral Commission') }} ({{ $year }})
                </h3>
                <div class="flex flex-wrap gap-2 text-xs font-semibold">
                    <span class="bg-blue-50 text-blue-700 border border-blue-100 px-3 py-1 rounded-full">
                        {{ $tr('عقارات', 'Properties') }}: {{ number_format($propertyReferralCommissionTotal, 2) }} {{ $currency }}
                    </span>
                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-100 px-3 py-1 rounded-full">
                        {{ $tr('مستأجرون', 'Tenants') }}: {{ number_format($tenantReferralCommissionTotal, 2) }} {{ $currency }}
                    </span>
                    <span class="bg-green-100 text-green-800 border border-green-200 px-3 py-1 rounded-full font-bold">
                        {{ $tr('الإجمالي', 'Total') }}: {{ number_format($referralCommissionTotal, 2) }} {{ $currency }}
                    </span>
                </div>
            </div>

            {{-- Properties sub-section --}}
            <div class="px-5 pt-4 pb-2">
                <p class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    {{ $tr('العقارات المحالة', 'Referred Properties') }}
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs">
                        <tr>
                            <th class="px-4 py-2.5 text-right font-medium">{{ $tr('العقار', 'Property') }}</th>
                            <th class="px-4 py-2.5 text-right font-medium">{{ $tr('النوع', 'Type') }}</th>
                            <th class="px-4 py-2.5 text-right font-medium">{{ $tr('النسبة', 'Rate') }}</th>
                            <th class="px-4 py-2.5 text-right font-medium">{{ $tr('الإيجار المحصَّل', 'Collected Rent') }}</th>
                            <th class="px-4 py-2.5 text-right font-medium">{{ $tr('العمولة', 'Commission') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($referredProperties as $rp)
                        @php
                            $rpCollected = $referralPropertyRevenue[$rp->id] ?? 0;
                            $rpEarned    = ($rp->referral_commission_rate ?? 0) / 100 * $rpCollected;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $rp->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $rp->typeLabel() }}</td>
                            <td class="px-4 py-3">
                                @if($rp->referral_commission_rate)
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $rp->referral_commission_rate }}%</span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ number_format($rpCollected, 2) }} {{ $currency }}</td>
                            <td class="px-4 py-3 font-semibold {{ $rpEarned > 0 ? 'text-green-700' : 'text-gray-400' }}">{{ number_format($rpEarned, 2) }} {{ $currency }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400 text-xs">{{ $tr('لا توجد عقارات محالة', 'No referred properties') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tenants sub-section --}}
            <div class="px-5 pt-5 pb-2 border-t border-gray-100">
                <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ $tr('المستأجرون المحالون', 'Referred Tenants') }}
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs">
                        <tr>
                            <th class="px-4 py-2.5 text-right font-medium">{{ $tr('المستأجر', 'Tenant') }}</th>
                            <th class="px-4 py-2.5 text-right font-medium">{{ $tr('النسبة', 'Rate') }}</th>
                            <th class="px-4 py-2.5 text-right font-medium">{{ $tr('الإيجار المحصَّل', 'Collected Rent') }}</th>
                            <th class="px-4 py-2.5 text-right font-medium">{{ $tr('العمولة', 'Commission') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($referredTenants as $rt)
                        @php
                            $rtCollected = $referralTenantRevenue[$rt->id] ?? 0;
                            $rtEarned    = ($rt->referral_commission_rate ?? 0) / 100 * $rtCollected;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $rt->user->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if($rt->referral_commission_rate)
                                    <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $rt->referral_commission_rate }}%</span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ number_format($rtCollected, 2) }} {{ $currency }}</td>
                            <td class="px-4 py-3 font-semibold {{ $rtEarned > 0 ? 'text-green-700' : 'text-gray-400' }}">{{ number_format($rtEarned, 2) }} {{ $currency }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">{{ $tr('لا يوجد مستأجرون محالون', 'No referred tenants') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

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

        {{-- Contract Expiry Alerts --}}
        @if($expiringContracts->isNotEmpty())
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-5 mb-6">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 text-orange-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <h3 class="font-bold text-orange-800">
                    {{ $tr('تنبيه: عقود على وشك الانتهاء', 'Alert: Contracts Expiring Soon') }}
                    <span class="ms-2 bg-orange-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $expiringContracts->count() }}</span>
                </h3>
            </div>
            <div class="space-y-2">
                @foreach($expiringContracts as $ec)
                @php $dLeft = (int) now()->diffInDays($ec->end_date, false); @endphp
                <div class="flex items-center justify-between bg-white border border-orange-100 rounded-lg px-4 py-3">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $ec->tenant->user->name ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $ec->unit->property->name ?? '-' }} — {{ $tr('وحدة', 'Unit') }} {{ $ec->unit->unit_number ?? '-' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">{{ $ec->end_date->format('Y/m/d') }}</p>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $dLeft <= 7 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                            @if($dLeft <= 0) {{ $tr('منتهي', 'Expired') }} @else {{ $dLeft }} {{ $tr('يوم', 'days') }} @endif
                        </span>
                    </div>
                    <a href="{{ route('employee.tenants.show', $ec->tenant) }}"
                       class="text-xs text-blue-600 hover:underline ms-3">{{ $tr('عرض', 'View') }}</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

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
