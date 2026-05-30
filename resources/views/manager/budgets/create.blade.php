<x-app-layout>
    <x-slot name="title">{{ __('Company Budget') }} — {{ __('Add') }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">{{ __('Add') }} — {{ __('Company Budget') }}</h2>
        <a href="{{ route('manager.budgets.index') }}" class="text-sm text-gray-600">{{ __('Back') }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <form method="POST" action="{{ route('manager.budgets.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title') }}</label>
                <input type="text" name="title" value="{{ old('title') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Category') }}</label>
                    <select name="category" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="hr"          @selected(old('category')==='hr')>HR</option>
                        <option value="operations"  @selected(old('category')==='operations')>{{ __('Operations') }}</option>
                        <option value="it"          @selected(old('category')==='it')>IT</option>
                        <option value="marketing"   @selected(old('category')==='marketing')>{{ __('Marketing') }}</option>
                        <option value="maintenance" @selected(old('category')==='maintenance')>{{ __('Maintenance') }}</option>
                        <option value="other"       @selected(old('category','other')==='other')>{{ __('Other') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                    <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="draft"    @selected(old('status','draft')==='draft')>{{ __('Draft') }}</option>
                        <option value="approved" @selected(old('status')==='approved')>{{ __('Approved') }}</option>
                        <option value="closed"   @selected(old('status')==='closed')>{{ __('Closed') }}</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Year') }}</label>
                    <input type="number" name="period_year" value="{{ old('period_year', now()->year) }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Month') }} ({{ __('leave blank for annual') }})</label>
                    <select name="period_month" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">— {{ __('Annual') }} —</option>
                        @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}" @selected(old('period_month')==$m)>{{ $m }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Allocated Amount') }}</label>
                    <input type="number" step="0.01" name="allocated_amount" value="{{ old('allocated_amount') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Spent Amount') }}</label>
                    <input type="number" step="0.01" name="spent_amount" value="{{ old('spent_amount', 0) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Notes') }}</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-2 pt-3">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ __('Save') }}</button>
                <a href="{{ route('manager.budgets.index') }}" class="text-gray-600 px-3 py-2 text-sm">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>
