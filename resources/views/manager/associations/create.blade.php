<x-app-layout>
    <x-slot name="title">{{ __('Owners Association') }} — {{ __('Add') }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">{{ __('Add') }} — {{ __('Owners Association') }}</h2>
        <a href="{{ route('manager.associations.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Back') }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-3xl">
        <form method="POST" action="{{ route('manager.associations.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Property') }}</label>
                <select name="property_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">--</option>
                    @foreach($properties as $p)
                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code }})</option>
                    @endforeach
                </select>
                @error('property_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Name') }} (AR)</label>
                    <input type="text" name="name_ar" value="{{ old('name_ar') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Name') }} (EN)</label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Established Date') }}</label>
                    <input type="date" name="established_date" value="{{ old('established_date') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Monthly Fee per Unit') }}</label>
                    <input type="number" step="0.01" name="monthly_fee_per_unit" value="{{ old('monthly_fee_per_unit', 0) }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }} (AR)</label>
                    <textarea name="description_ar" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('description_ar') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }} (EN)</label>
                    <textarea name="description_en" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('description_en') }}</textarea>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <option value="active">{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
            </div>

            <div class="flex gap-2 pt-3">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ __('Save') }}</button>
                <a href="{{ route('manager.associations.index') }}" class="text-gray-600 px-3 py-2 text-sm">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>
