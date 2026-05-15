<x-app-layout>
    <x-slot name="title">{{ __('Salaries') }} — {{ __('Edit') }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">{{ __('Edit') }} — {{ $salary->employee?->name }}</h2>
        <a href="{{ route('manager.salaries.index') }}" class="text-sm text-gray-600">{{ __('Back') }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <form method="POST" action="{{ route('manager.salaries.update', $salary) }}" class="space-y-4">
            @csrf @method('PATCH')

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Base Salary') }}</label>
                    <input type="number" step="0.01" name="base_salary" value="{{ $salary->base_salary }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Bonuses') }}</label>
                    <input type="number" step="0.01" name="bonuses" value="{{ $salary->bonuses }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Deductions') }}</label>
                    <input type="number" step="0.01" name="deductions" value="{{ $salary->deductions }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <input type="hidden" name="employee_id" value="{{ $salary->employee_id }}">
            <input type="hidden" name="period_month" value="{{ $salary->period_month }}">
            <input type="hidden" name="period_year" value="{{ $salary->period_year }}">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <option value="draft" @selected($salary->status==='draft')>{{ __('Draft') }}</option>
                    <option value="pending" @selected($salary->status==='pending')>{{ __('Pending') }}</option>
                    <option value="paid" @selected($salary->status==='paid')>{{ __('Paid') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Notes') }}</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ $salary->notes }}</textarea>
            </div>

            <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ __('Update') }}</button>
        </form>
    </div>
</x-app-layout>
