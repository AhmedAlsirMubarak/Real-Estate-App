<x-app-layout>
    @php
        $isAr    = app()->getLocale() === 'ar';
        $tr      = fn(string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
        $fmt     = fn($v) => number_format((float)$v, 2);
    @endphp
    <x-slot name="title">{{ $tr('الرواتب', 'Salaries') }} — {{ $salary->employee?->name }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $salary->employee?->name ?? '—' }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $salary->periodLabel() }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('manager.salaries.edit', $salary) }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium">{{ $tr('تعديل', 'Edit') }}</a>
            <a href="{{ route('manager.salaries.index') }}"
               class="text-sm text-gray-600 border border-gray-200 px-4 py-2 rounded-lg hover:bg-gray-50">{{ $tr('رجوع', 'Back') }}</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <div class="lg:col-span-2 space-y-4">

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3 bg-green-50 border-b border-green-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    <h3 class="text-sm font-bold text-green-800">{{ $tr('الاستحقاقات', 'Earnings') }}</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    <div class="flex items-center justify-between px-5 py-3 text-sm">
                        <span class="text-gray-600">{{ $tr('الراتب الأساسي', 'Base Salary') }}</span>
                        <span class="font-semibold text-gray-800">{{ $fmt($salary->base_salary) }} {{ $currency }}</span>
                    </div>
                    @if((float)$salary->housing_allowance > 0)
                    <div class="flex items-center justify-between px-5 py-3 text-sm">
                        <span class="text-gray-600">{{ $tr('بدل السكن', 'Housing Allowance') }}</span>
                        <span class="font-semibold text-blue-700">{{ $fmt($salary->housing_allowance) }} {{ $currency }}</span>
                    </div>
                    @endif
                    @if((float)$salary->transport_allowance > 0)
                    <div class="flex items-center justify-between px-5 py-3 text-sm">
                        <span class="text-gray-600">{{ $tr('بدل المواصلات', 'Transport Allowance') }}</span>
                        <span class="font-semibold text-blue-700">{{ $fmt($salary->transport_allowance) }} {{ $currency }}</span>
                    </div>
                    @endif
                    @if((float)$salary->food_allowance > 0)
                    <div class="flex items-center justify-between px-5 py-3 text-sm">
                        <span class="text-gray-600">{{ $tr('بدل الطعام', 'Food Allowance') }}</span>
                        <span class="font-semibold text-blue-700">{{ $fmt($salary->food_allowance) }} {{ $currency }}</span>
                    </div>
                    @endif
                    @if((float)$salary->other_allowances > 0)
                    <div class="flex items-center justify-between px-5 py-3 text-sm">
                        <span class="text-gray-600">{{ $tr('بدلات أخرى', 'Other Allowances') }}</span>
                        <span class="font-semibold text-blue-700">{{ $fmt($salary->other_allowances) }} {{ $currency }}</span>
                    </div>
                    @endif
                    @if((float)$salary->bonuses > 0)
                    <div class="flex items-center justify-between px-5 py-3 text-sm">
                        <span class="text-gray-600">{{ $tr('المكافآت', 'Bonuses') }}</span>
                        <span class="font-semibold text-emerald-700">{{ $fmt($salary->bonuses) }} {{ $currency }}</span>
                    </div>
                    @endif
                </div>
            </div>

            @if((float)$salary->deductions > 0)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3 bg-red-50 border-b border-red-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    <h3 class="text-sm font-bold text-red-700">{{ $tr('الاستقطاعات', 'Deductions') }}</h3>
                </div>
                <div class="flex items-center justify-between px-5 py-3 text-sm">
                    <span class="text-gray-600">{{ $tr('الاستقطاعات', 'Deductions') }}</span>
                    <span class="font-semibold text-red-600">{{ $fmt($salary->deductions) }} {{ $currency }}</span>
                </div>
            </div>
            @endif

            <div class="rounded-xl bg-gradient-to-r from-blue-900 to-indigo-800 text-white px-6 py-5 flex items-center justify-between shadow-lg">
                <div>
                    <p class="text-sm text-blue-200">{{ $tr('صافي المدفوع', 'Net Paid') }}</p>
                    <p class="text-3xl font-black mt-0.5">{{ $fmt($salary->net_paid) }}</p>
                    <p class="text-xs text-blue-300 mt-0.5">{{ $currency }}</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                        @if($salary->status==='paid') bg-green-400/20 text-green-200
                        @elseif($salary->status==='pending') bg-yellow-400/20 text-yellow-200
                        @else bg-gray-400/20 text-gray-200 @endif">
                        {{ $salary->statusLabel() }}
                    </span>
                    @if($salary->paid_at)
                    <p class="text-xs text-blue-300 mt-1">{{ $salary->paid_at->format('Y-m-d') }}</p>
                    @endif
                </div>
            </div>

            @if($salary->notes)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ $tr('ملاحظات', 'Notes') }}</p>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $salary->notes }}</p>
            </div>
            @endif
        </div>

        <div class="space-y-4">

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">{{ $tr('بيانات الموظف', 'Employee Info') }}</p>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-black text-sm flex-shrink-0">
                        {{ mb_substr($salary->employee?->name ?? 'E', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">{{ $salary->employee?->name ?? '—' }}</p>
                        <p class="text-xs text-gray-500">{{ $salary->employee?->email ?? '' }}</p>
                    </div>
                </div>
                @if($salary->employee?->phone)
                <div class="flex items-center gap-2 text-xs text-gray-500 mt-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    {{ $salary->employee->phone }}
                </div>
                @endif
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $tr('تفاصيل الراتب', 'Salary Details') }}</p>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">{{ $tr('الفترة', 'Period') }}</span>
                    <span class="font-semibold text-gray-800">{{ $salary->periodLabel() }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">{{ $tr('الحالة', 'Status') }}</span>
                    <span class="font-semibold
                        @if($salary->status==='paid') text-green-700
                        @elseif($salary->status==='pending') text-yellow-700
                        @else text-gray-500 @endif">{{ $salary->statusLabel() }}</span>
                </div>
                @if($salary->paid_at)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">{{ $tr('تاريخ الدفع', 'Paid At') }}</span>
                    <span class="font-semibold text-gray-800">{{ $salary->paid_at->format('Y-m-d') }}</span>
                </div>
                @endif
                @if($salary->paidBy)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">{{ $tr('دُفع بواسطة', 'Paid By') }}</span>
                    <span class="font-semibold text-gray-800">{{ $salary->paidBy->name }}</span>
                </div>
                @endif
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-2">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">{{ $tr('إجراءات', 'Actions') }}</p>

                @if($salary->status !== 'paid')
                <form method="POST" action="{{ route('manager.salaries.pay', $salary) }}">
                    @csrf @method('PATCH')
                    <button class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        {{ $tr('صرف الراتب', 'Pay Salary') }}
                    </button>
                </form>
                @endif

                <x-whatsapp-button
                    :phone="$salary->employee?->phone"
                    :message="$tr('راتب ', 'Salary ') . $salary->periodLabel() . ' — ' . $fmt($salary->net_paid) . ' ' . $currency"
                    class="w-full justify-center" />

                <a href="{{ route('manager.salaries.edit', $salary) }}"
                   class="w-full flex items-center justify-center gap-2 border border-indigo-200 text-indigo-700 hover:bg-indigo-50 px-4 py-2 rounded-lg text-sm font-medium">
                    {{ $tr('تعديل', 'Edit') }}
                </a>

                <form method="POST" action="{{ route('manager.salaries.destroy', $salary) }}"
                      onsubmit="return confirm('{{ $tr('هل أنت متأكد؟', 'Are you sure?') }}')">
                    @csrf @method('DELETE')
                    <button class="w-full border border-red-200 text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-medium">
                        {{ $tr('حذف', 'Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
