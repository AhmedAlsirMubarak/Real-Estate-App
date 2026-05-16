<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
    @endphp
    <x-slot name="title">{{ $tr('مستحقاتي', 'My Dues') }}</x-slot>

    <div class="mb-5">
        <h2 class="text-xl font-bold text-gray-800">{{ $tr('مستحقاتي', 'My Dues') }}</h2>
        <p class="text-sm text-gray-500 mt-0.5">{{ $tr('رسوم جمعيات الملاك والفواتير', 'Owners\' association dues and invoices') }}</p>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-yellow-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4.5 h-4.5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $tr('معلّق', 'Pending') }}</p>
                <p class="text-base font-bold text-yellow-700">{{ number_format($totals['pending'], 2) }} {{ $currency }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4.5 h-4.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $tr('مدفوع', 'Paid') }}</p>
                <p class="text-base font-bold text-green-700">{{ number_format($totals['paid'], 2) }} {{ $currency }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4.5 h-4.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $tr('متأخر', 'Overdue') }}</p>
                <p class="text-base font-bold text-red-700">{{ number_format($totals['overdue'], 2) }} {{ $currency }}</p>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5 flex gap-3">
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ $tr('كل الحالات', 'All Statuses') }}</option>
            <option value="pending"  @selected(request('status')==='pending') >{{ $tr('معلّق', 'Pending') }}</option>
            <option value="paid"     @selected(request('status')==='paid')    >{{ $tr('مدفوع', 'Paid') }}</option>
            <option value="overdue"  @selected(request('status')==='overdue') >{{ $tr('متأخر', 'Overdue') }}</option>
        </select>
        <button class="bg-blue-900 text-white px-4 py-2 rounded-lg text-sm">{{ $tr('بحث', 'Search') }}</button>
        @if(request('status'))
        <a href="{{ route('owner.dues.index') }}" class="text-gray-500 text-sm px-3 py-2 hover:text-gray-700">{{ $tr('إعادة', 'Reset') }}</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-600 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">{{ $tr('العقار', 'Property') }}</th>
                        <th class="px-4 py-3 text-left">{{ $tr('الفترة', 'Period') }}</th>
                        <th class="px-4 py-3 text-left">{{ $tr('المبلغ', 'Amount') }}</th>
                        <th class="px-4 py-3 text-left">{{ $tr('تاريخ الاستحقاق', 'Due Date') }}</th>
                        <th class="px-4 py-3 text-left">{{ $tr('الحالة', 'Status') }}</th>
                        <th class="px-4 py-3 text-left">{{ $tr('الفاتورة', 'Invoice') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($dues as $due)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $due->association->property->name }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $due->periodLabel() }}</td>
                        <td class="px-4 py-3 font-semibold text-blue-900">{{ number_format($due->amount, 2) }} {{ $currency }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $due->due_date->format('Y/m/d') }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-medium
                                @if($due->status==='paid')    bg-green-50 text-green-700
                                @elseif($due->status==='overdue') bg-red-50 text-red-700
                                @elseif($due->status==='waived')  bg-gray-100 text-gray-600
                                @else bg-yellow-50 text-yellow-700 @endif">
                                {{ $due->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('owner.dues.invoice', $due) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                </svg>
                                {{ $tr('تحميل', 'PDF Invoice') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400">
                            {{ $tr('لا توجد مستحقات مسجلة', 'No dues found') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">{{ $dues->links() }}</div>
    </div>
</x-app-layout>
