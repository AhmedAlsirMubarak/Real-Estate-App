<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr   = fn(string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('الرواتب', 'Salaries') }} — {{ $tr('تعديل', 'Edit') }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">{{ $tr('تعديل', 'Edit') }} — {{ $salary->employee?->name }} / {{ $salary->periodLabel() }}</h2>
        <a href="{{ route('manager.salaries.index') }}" class="text-sm text-gray-600">{{ $tr('رجوع', 'Back') }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <form method="POST" action="{{ route('manager.salaries.update', $salary) }}" class="space-y-5">
            @csrf @method('PATCH')

            <input type="hidden" name="employee_id"  value="{{ $salary->employee_id }}">
            <input type="hidden" name="period_month" value="{{ $salary->period_month }}">
            <input type="hidden" name="period_year"  value="{{ $salary->period_year }}">

            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 border-b pb-1">{{ $tr('الاستحقاقات', 'Earnings') }}</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الراتب الأساسي', 'Base Salary') }}</label>
                        <input type="number" step="0.01" name="base_salary" value="{{ old('base_salary', $salary->base_salary) }}" required min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('بدل السكن', 'Housing Allowance') }}</label>
                        <input type="number" step="0.01" name="housing_allowance" value="{{ old('housing_allowance', $salary->housing_allowance) }}" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('بدل المواصلات', 'Transport Allowance') }}</label>
                        <input type="number" step="0.01" name="transport_allowance" value="{{ old('transport_allowance', $salary->transport_allowance) }}" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('بدل الطعام', 'Food Allowance') }}</label>
                        <input type="number" step="0.01" name="food_allowance" value="{{ old('food_allowance', $salary->food_allowance) }}" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('بدلات أخرى', 'Other Allowances') }}</label>
                        <input type="number" step="0.01" name="other_allowances" value="{{ old('other_allowances', $salary->other_allowances) }}" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المكافآت', 'Bonuses') }}</label>
                        <input type="number" step="0.01" name="bonuses" value="{{ old('bonuses', $salary->bonuses) }}" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                </div>
            </div>

            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 border-b pb-1">{{ $tr('الاستقطاعات', 'Deductions') }}</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الاستقطاعات', 'Deductions') }}</label>
                        <input type="number" step="0.01" name="deductions" value="{{ old('deductions', $salary->deductions) }}" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-blue-50 border border-blue-100 px-4 py-3 flex items-center justify-between">
                <span class="text-sm font-semibold text-blue-800">{{ $tr('صافي المدفوع', 'Net Paid') }}</span>
                <span id="net-display" class="text-xl font-black text-blue-900">{{ number_format($salary->net_paid, 2) }}</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحالة', 'Status') }}</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <option value="draft"   @selected(old('status',$salary->status)==='draft')>{{ $tr('مسودة', 'Draft') }}</option>
                    <option value="pending" @selected(old('status',$salary->status)==='pending')>{{ $tr('معلق', 'Pending') }}</option>
                    <option value="paid"    @selected(old('status',$salary->status)==='paid')>{{ $tr('مدفوع', 'Paid') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('ملاحظات', 'Notes') }}</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('notes', $salary->notes) }}</textarea>
            </div>

            <div class="flex gap-2 pt-3">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ $tr('تحديث', 'Update') }}</button>
                <a href="{{ route('manager.salaries.index') }}" class="text-gray-600 px-3 py-2 text-sm">{{ $tr('إلغاء', 'Cancel') }}</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
    function calcNet() {
        const get = id => parseFloat(document.querySelector('[name="'+id+'"]')?.value) || 0;
        const net = get('base_salary') + get('housing_allowance') + get('transport_allowance')
                  + get('food_allowance') + get('other_allowances') + get('bonuses') - get('deductions');
        document.getElementById('net-display').textContent = net.toFixed(2);
    }
    </script>
    @endpush
</x-app-layout>
