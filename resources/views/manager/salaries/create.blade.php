<x-app-layout>
    <x-slot name="title">{{ __('Salaries') }} — {{ __('Add') }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">{{ __('Add') }} — {{ __('Salaries') }}</h2>
        <a href="{{ route('manager.salaries.index') }}" class="text-sm text-gray-600">{{ __('Back') }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <form method="POST" action="{{ route('manager.salaries.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Employees') }}</label>
                <select name="employee_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">--</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Month') }}</label>
                    <select name="period_month" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        @for($m=1;$m<=12;$m++)<option value="{{ $m }}" @selected($m===now()->month)>{{ $m }}</option>@endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Year') }}</label>
                    <input type="number" name="period_year" value="{{ now()->year }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Base Salary') }}</label>
                    <input type="number" step="0.01" name="base_salary" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Bonuses') }}</label>
                    <input type="number" step="0.01" name="bonuses" value="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Deductions') }}</label>
                    <input type="number" step="0.01" name="deductions" value="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <option value="draft">{{ __('Draft') }}</option>
                    <option value="pending">{{ __('Pending') }}</option>
                    <option value="paid">{{ __('Paid') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Notes') }}</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"></textarea>
            </div>

            <div class="flex gap-2 pt-3">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ __('Save') }}</button>
                <a href="{{ route('manager.salaries.index') }}" class="text-gray-600 px-3 py-2 text-sm">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>
