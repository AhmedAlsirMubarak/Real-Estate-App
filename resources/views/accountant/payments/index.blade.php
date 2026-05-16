<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
        $months = $isAr
            ? ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر']
            : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $displayName = function (?string $name, string $fallback = 'User') use ($isAr) {
            if ($name === null || $name === '') return $fallback;
            if ($isAr || ! preg_match('/\p{Arabic}/u', $name)) return $name;
            return $fallback;
        };
    @endphp
    <x-slot name="title">{{ $tr('المدفوعات', 'Payments') }}</x-slot>
    <div class="py-4">
        {{-- Generate Monthly Payments --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6">
            <h3 class="font-bold text-blue-800 mb-4">{{ $tr('توليد مدفوعات شهرية', 'Generate Monthly Payments') }}</h3>
            <form method="POST" action="{{ route('accountant.payments.generate') }}" class="flex gap-4 items-end flex-wrap">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-blue-700 mb-1">{{ $tr('السنة', 'Year') }} <span class="text-red-500">*</span></label>
                    <select name="year" required class="border border-blue-300 rounded-lg px-3 py-2 text-right focus:ring-2 focus:ring-blue-500 bg-white">
                        @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                        <option value="{{ $y }}" {{ date('Y')==$y?'selected':'' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-blue-700 mb-1">{{ $tr('الشهر', 'Month') }} <span class="text-red-500">*</span></label>
                    <select name="month" required class="border border-blue-300 rounded-lg px-3 py-2 text-right focus:ring-2 focus:ring-blue-500 bg-white">
                        @foreach($months as $i => $month)
                        <option value="{{ $i+1 }}" {{ date('n')==$i+1?'selected':'' }}>{{ $month }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium transition">{{ $tr('توليد المدفوعات', 'Generate Payments') }}</button>
            </form>
        </div>

        {{-- Filters + Export --}}
        <div class="bg-white rounded-xl shadow p-4 mb-6">
            <form method="GET" action="{{ route('accountant.payments.index') }}" class="flex gap-4 items-end flex-wrap">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحالة', 'Status') }}</label>
                    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-right focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ $tr('الكل', 'All') }}</option>
                        <option value="pending" {{ request('status')==='pending'?'selected':'' }}>{{ $tr('معلق', 'Pending') }}</option>
                        <option value="paid" {{ request('status')==='paid'?'selected':'' }}>{{ $tr('مدفوع', 'Paid') }}</option>
                        <option value="overdue" {{ request('status')==='overdue'?'selected':'' }}>{{ $tr('متأخر', 'Overdue') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('السنة', 'Year') }}</label>
                    <select name="year" class="border border-gray-300 rounded-lg px-3 py-2 text-right focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ $tr('الكل', 'All') }}</option>
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ request('year')==$y?'selected':'' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الشهر', 'Month') }}</label>
                    <select name="month" class="border border-gray-300 rounded-lg px-3 py-2 text-right focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ $tr('الكل', 'All') }}</option>
                        @foreach($months as $i => $month)
                        <option value="{{ $i+1 }}" {{ request('month')==$i+1?'selected':'' }}>{{ $month }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium transition">{{ $tr('تصفية', 'Filter') }}</button>
                <a href="{{ route('accountant.payments.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg font-medium transition">{{ $tr('إعادة ضبط', 'Reset') }}</a>
            </form>
            <div class="mt-3 border-t border-gray-100 pt-3">
                <a href="{{ route('accountant.payments.export', request()->query()) }}"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    {{ $tr('تصدير PDF', 'Export PDF') }}
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('المستأجر', 'Tenant') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('المبنى / الوحدة', 'Property / Unit') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('المبلغ', 'Amount') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الشهر / السنة', 'Month / Year') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الحالة', 'Status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('إجراء', 'Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($payments as $pay)
                        @php
                            $pc=['pending'=>'bg-yellow-100 text-yellow-700','paid'=>'bg-green-100 text-green-700','overdue'=>'bg-red-100 text-red-700'];
                            $pl = $isAr
                                ? ['pending' => 'معلق', 'paid' => 'مدفوع', 'overdue' => 'متأخر']
                                : ['pending' => 'Pending', 'paid' => 'Paid', 'overdue' => 'Overdue'];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $displayName($pay->tenant->user->name ?? null, $tr('مستأجر', 'Tenant')) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $pay->tenant->activeContract->unit->property->name ?? '-' }} / {{ $pay->tenant->activeContract->unit->unit_number ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium">{{ number_format($pay->amount) }} {{ $currency }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $pay->month }}/{{ $pay->year }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $pc[$pay->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $pl[$pay->status] ?? $pay->status }}</span></td>
                            <td class="px-4 py-3">
                                @if($pay->status === 'pending')
                                <form method="POST" action="{{ route('accountant.payments.confirm', $pay) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg text-xs font-medium transition">{{ $tr('تأكيد الدفع', 'Confirm Payment') }}</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">{{ $tr('لا توجد مدفوعات', 'No payments found') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $payments->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
