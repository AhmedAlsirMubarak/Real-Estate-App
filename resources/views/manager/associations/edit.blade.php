<x-app-layout>
    <x-slot name="title">{{ __('Edit') }} — {{ __('Owners Association') }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">{{ __('Edit') }} — {{ $association->name }}</h2>
        <a href="{{ route('manager.associations.show', $association) }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Back') }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-3xl">
        <form method="POST" action="{{ route('manager.associations.update', $association) }}" class="space-y-4">
            @csrf @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Name') }} (AR)</label>
                    <input type="text" name="name_ar" value="{{ old('name_ar', $association->getRawOriginal('name_ar')) }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Name') }} (EN)</label>
                    <input type="text" name="name_en" value="{{ old('name_en', $association->getRawOriginal('name_en')) }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Established Date') }}</label>
                    <input type="date" name="established_date" value="{{ old('established_date', $association->established_date?->format('Y-m-d')) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Monthly Fee per Unit') }}</label>
                    <input type="number" step="0.01" name="monthly_fee_per_unit" value="{{ old('monthly_fee_per_unit', $association->monthly_fee_per_unit) }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }} (AR)</label>
                    <textarea name="description_ar" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('description_ar', $association->getRawOriginal('description_ar')) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }} (EN)</label>
                    <textarea name="description_en" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('description_en', $association->getRawOriginal('description_en')) }}</textarea>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <option value="active" @selected($association->status==='active')>{{ __('Active') }}</option>
                    <option value="inactive" @selected($association->status==='inactive')>{{ __('Inactive') }}</option>
                </select>
            </div>

            <div class="flex gap-2 pt-3">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ __('Update') }}</button>
                <a href="{{ route('manager.associations.show', $association) }}" class="text-gray-600 px-3 py-2 text-sm">{{ __('Cancel') }}</a>
            </div>
        </form>

        <form method="POST" action="{{ route('manager.associations.destroy', $association) }}" class="mt-8 pt-6 border-t border-red-100"
              onsubmit="return confirm('Delete?')">
            @csrf @method('DELETE')
            <button class="text-red-600 hover:text-red-800 text-sm">{{ __('Delete') }}</button>
        </form>
    </div>
</x-app-layout>
