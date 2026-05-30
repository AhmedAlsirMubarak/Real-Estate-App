<x-app-layout>
    <x-slot name="title">{{ __('Salaries') }} — {{ __('Edit') }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">{{ __('Edit') }} — {{ $salary->employee?->name }} / {{ $salary->periodLabel() }}</h2>
        <a href="{{ route('manager.salaries.index') }}" class="text-sm text-gray-600">{{ __('Back') }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <form method="POST" action="{{ route('manager.salaries.update', $salary) }}" class="space-y-5">
            @csrf @method('PATCH')

            {{-- Hidden fields — period & employee are fixed --}}
            <input type="hidden" name="employee_id"  value="{{ $salary->employee_id }}">
            <input type="hidden" name="period_month" value="{{ $salary->period_month }}">
            <input type="hidden" name="period_year"  value="{{ $salary->period_year }}">

            {{-- Earnings --}}
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 border-b pb-1">{{ app()->getLocale()==='ar' ? 'الاستحقاقات' : 'Earnings' }}</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Base Salary') }}</label>
                        <input type="number" step="0.01" name="base_salary" value="{{ old('base_salary', $salary->base_salary) }}" required min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ app()->getLocale()==='ar' ? 'بدل السكن' : 'Housing Allowance' }}</label>
                        <input type="number" step="0.01" name="housing_allowance" value="{{ old('housing_allowance', $salary->housing_allowance) }}" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ app()->getLocale()==='ar' ? 'بدل المواصلات' : 'Transport Allowance' }}</label>
                        <input type="number" step="0.01" name="transport_allowance" value="{{ old('transport_allowance', $salary->transport_allowance) }}" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ app()->getLocale()==='ar' ? 'بدل الطعام' : 'Food Allowance' }}</label>
                        <input type="number" step="0.01" name="food_allowance" value="{{ old('food_allowance', $salary->food_allowance) }}" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ app()->getLocale()==='ar' ? 'بدلات أخرى' : 'Other Allowances' }}</label>
                        <input type="number" step="0.01" name="other_allowances" value="{{ old('other_allowances', $salary->other_allowances) }}" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Bonuses') }}</label>
                        <input type="number" step="0.01" name="bonuses" value="{{ old('bonuses', $salary->bonuses) }}" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                </div>
            </div>

            {{-- Deductions --}}
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 border-b pb-1">{{ app()->getLocale()==='ar' ? 'الاستقطاعات' : 'Deductions' }}</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Deductions') }}</label>
                        <input type="number" step="0.01" name="deductions" value="{{ old('deductions', $salary->deductions) }}" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" oninput="calcNet()">
                    </div>
                </div>
            </div>

            {{-- Net --}}
            <div class="rounded-xl bg-blue-50 border border-blue-100 px-4 py-3 flex items-center justify-between">
                <span class="text-sm font-semibold text-blue-800">{{ __('Net Paid') }}</span>
                <span id="net-display" class="text-xl font-black text-blue-900">{{ number_format($salary->net_paid, 2) }}</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <option value="draft"   @selected(old('status',$salary->status)==='draft')>{{ __('Draft') }}</option>
                    <option value="pending" @selected(old('status',$salary->status)==='pending')>{{ __('Pending') }}</option>
                    <option value="paid"    @selected(old('status',$salary->status)==='paid')>{{ __('Paid') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Notes') }}</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('notes', $salary->notes) }}</textarea>
            </div>

            <div class="flex gap-2 pt-3">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ __('Update') }}</button>
                <a href="{{ route('manager.salaries.index') }}" class="text-gray-600 px-3 py-2 text-sm">{{ __('Cancel') }}</a>
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
