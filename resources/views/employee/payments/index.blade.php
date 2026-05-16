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

    <x-slot name="title">{{ $tr('دفعات الإيجار', 'Rent Payments') }}</x-slot>

    <div class="py-4 space-y-6">
        <div class="bg-white rounded-xl shadow p-4">
            <form method="GET" action="{{ route('employee.payments.index') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">{{ $tr('السنة', 'Year') }}</label>
                    <select name="year" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ (int) ($year ?? now()->year) === $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 mb-1">{{ $tr('الحالة', 'Status') }}</label>
                    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="all" {{ ($status ?? 'all') === 'all' ? 'selected' : '' }}>{{ $tr('الكل', 'All') }}</option>
                        <option value="pending" {{ ($status ?? 'all') === 'pending' ? 'selected' : '' }}>{{ $tr('معلقة', 'Pending') }}</option>
                        <option value="paid" {{ ($status ?? 'all') === 'paid' ? 'selected' : '' }}>{{ $tr('مدفوعة', 'Paid') }}</option>
                        <option value="overdue" {{ ($status ?? 'all') === 'overdue' ? 'selected' : '' }}>{{ $tr('متأخرة', 'Overdue') }}</option>
                    </select>
                </div>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    {{ $tr('تطبيق', 'Apply') }}
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">{{ $tr('ملخص التحصيل من يناير إلى ديسمبر', 'Collection Summary from Jan to Dec') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs">
                        <tr>
                            <th class="px-4 py-3 text-right">{{ $tr('الشهر', 'Month') }}</th>
                            <th class="px-4 py-3 text-right">{{ $tr('محصل', 'Collected') }}</th>
                            <th class="px-4 py-3 text-right">{{ $tr('معلق', 'Pending') }}</th>
                            <th class="px-4 py-3 text-right">{{ $tr('متأخر', 'Overdue') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($monthlySummary ?? [] as $monthRow)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-700">{{ $months[$monthRow['month']] ?? $monthRow['month'] }}</td>
                                <td class="px-4 py-3 text-green-700 font-semibold">{{ number_format($monthRow['paid']) }} {{ $currency }}</td>
                                <td class="px-4 py-3 text-yellow-700 font-semibold">{{ number_format($monthRow['pending']) }} {{ $currency }}</td>
                                <td class="px-4 py-3 text-red-700 font-semibold">{{ number_format($monthRow['overdue']) }} {{ $currency }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('المستأجر', 'Tenant') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('العقار / الوحدة', 'Property / Unit') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('المبلغ', 'Amount') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الشهر / السنة', 'Month / Year') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الحالة', 'Status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الإجراءات', 'Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($payments as $pay)
                        @php
                            $pc = ['pending' => 'bg-yellow-100 text-yellow-700', 'paid' => 'bg-green-100 text-green-700', 'overdue' => 'bg-red-100 text-red-700'];
                            $pl = $isAr
                                ? ['pending' => 'معلق', 'paid' => 'مدفوع', 'overdue' => 'متأخر']
                                : ['pending' => 'Pending', 'paid' => 'Paid', 'overdue' => 'Overdue'];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $displayName($pay->tenant->user->name ?? null, $tr('مستأجر', 'Tenant')) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $pay->rentalContract->unit->property->name ?? '-' }} / {{ $pay->rentalContract->unit->unit_number ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium">{{ number_format($pay->amount) }} {{ $currency }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $months[$pay->month] ?? $pay->month }} / {{ $pay->year }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $pc[$pay->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $pl[$pay->status] ?? $pay->status }}</span></td>
                            <td class="px-4 py-3">
                                @if($pay->status === 'pending')
                                    <div class="flex flex-col gap-2">
                                        <form method="POST" action="{{ route('employee.payments.confirm', $pay) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text"
                                                   name="notes"
                                                   placeholder="{{ $tr('ملاحظة (اختياري)', 'Note (optional)') }}"
                                                   class="border border-gray-300 rounded-lg px-2 py-1 text-xs w-44">
                                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg text-xs font-medium transition">
                                                {{ $tr('تأكيد استلام الإيجار', 'Confirm Rent Received') }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('employee.payments.overdue', $pay) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-xs font-medium transition">
                                                {{ $tr('تعليم كمتأخر', 'Mark Overdue') }}
                                            </button>
                                        </form>
                                    </div>
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
