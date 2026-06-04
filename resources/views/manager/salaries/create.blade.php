<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr   = fn(string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('الرواتب', 'Salaries') }} — {{ $tr('إضافة', 'Add') }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">{{ $tr('إضافة', 'Add') }} — {{ $tr('الرواتب', 'Salaries') }}</h2>
        <a href="{{ route('manager.salaries.index') }}" class="text-sm text-gray-600">{{ $tr('رجوع', 'Back') }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <form method="POST" action="{{ route('manager.salaries.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الموظفون', 'Employees') }}</label>
                <select name="employee_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">--</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(old('employee_id')==$emp->id)>{{ $emp->name }} ({{ $emp->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الشهر', 'Month') }}</label>
                    <select name="period_month" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        @for($m=1;$m<=12;$m++)<option value="{{ $m }}" @selected(old('period_month',$m===now()->month?$m:null)==$m)>{{ $m }}</option>@endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('السنة', 'Year') }}</label>
                    <input type="number" name="period_year" value="{{ old('period_year', now()->year) }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 border-b pb-1">{{ $tr('الاستحقاقات', 'Earnings') }}</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الراتب الأساسي', 'Base Salary') }}</label>
                        <input type="number" step="0.01" name="base_salary" value="{{ old('base_salary', 0) }}" required min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('بدل السكن', 'Housing Allowance') }}</label>
                        <input type="number" step="0.01" name="housing_allowance" value="{{ old('housing_allowance', 0) }}" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('بدل المواصلات', 'Transport Allowance') }}</label>
                        <input type="number" step="0.01" name="transport_allowance" value="{{ old('transport_allowance', 0) }}" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('بدل الطعام', 'Food Allowance') }}</label>
                        <input type="number" step="0.01" name="food_allowance" value="{{ old('food_allowance', 0) }}" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('بدلات أخرى', 'Other Allowances') }}</label>
                        <input type="number" step="0.01" name="other_allowances" value="{{ old('other_allowances', 0) }}" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المكافآت', 'Bonuses') }}</label>
                        <input type="number" step="0.01" name="bonuses" value="{{ old('bonuses', 0) }}" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                </div>
            </div>

            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 border-b pb-1">{{ $tr('الاستقطاعات', 'Deductions') }}</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الاستقطاعات', 'Deductions') }}</label>
                        <input type="number" step="0.01" name="deductions" value="{{ old('deductions', 0) }}" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-blue-50 border border-blue-100 px-4 py-3 flex items-center justify-between">
                <span class="text-sm font-semibold text-blue-800">{{ $tr('صافي المدفوع', 'Net Paid') }}</span>
                <span id="net-display" class="text-xl font-black text-blue-900">0.00</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحالة', 'Status') }}</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <option value="draft"   @selected(old('status')==='draft')>{{ $tr('مسودة', 'Draft') }}</option>
                    <option value="pending" @selected(old('status','pending')==='pending')>{{ $tr('معلق', 'Pending') }}</option>
                    <option value="paid"    @selected(old('status')==='paid')>{{ $tr('مدفوع', 'Paid') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('ملاحظات', 'Notes') }}</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-2 pt-3">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ $tr('حفظ', 'Save') }}</button>
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
    calcNet();
    </script>
    @endpush
</x-app-layout>
