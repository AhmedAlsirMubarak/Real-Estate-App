<x-app-layout>
    <x-slot name="title">{{ __('Owners Association') }} — {{ __('Add') }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">{{ __('Add') }} — {{ __('Owners Association') }}</h2>
        <a href="{{ route('manager.associations.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Back') }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-3xl">
        <form method="POST" action="{{ route('manager.associations.store') }}" enctype="multipart/form-data" class="space-y-4">
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

            <div class="border-t border-dashed border-gray-200 pt-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">{{ __('Utility Account Numbers') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                {{ __('Electricity Account No.') }}
                            </span>
                        </label>
                        <input type="text" name="electricity_account_number" value="{{ old('electricity_account_number') }}"
                               placeholder="{{ __('Optional') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        @error('electricity_account_number')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-6 8-12a8 8 0 1 0-16 0c0 6 8 12 8 12z"/></svg>
                                {{ __('Water Account No.') }}
                            </span>
                        </label>
                        <input type="text" name="water_account_number" value="{{ old('water_account_number') }}"
                               placeholder="{{ __('Optional') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        @error('water_account_number')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Documents --}}
            <div class="border-t border-dashed border-gray-200 pt-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">{{ __('Documents') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('No Objection Certificate') }}</label>
                        <input type="file" name="no_objection_certificate" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:bg-gray-100 file:text-gray-700">
                        <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG — {{ __('Max') }} 5MB</p>
                        @error('no_objection_certificate')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Sketch') }}</label>
                        <input type="file" name="sketch" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:bg-gray-100 file:text-gray-700">
                        <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG — {{ __('Max') }} 5MB</p>
                        @error('sketch')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Owners Association Certificate') }}</label>
                        <input type="file" name="association_certificate" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:bg-gray-100 file:text-gray-700">
                        <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG — {{ __('Max') }} 5MB</p>
                        @error('association_certificate')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Personal ID') }}</label>
                        <input type="file" name="personal_id" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:bg-gray-100 file:text-gray-700">
                        <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG — {{ __('Max') }} 5MB</p>
                        @error('personal_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __("Association Manager's ID") }}</label>
                        <input type="file" name="manager_id" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:bg-gray-100 file:text-gray-700">
                        <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG — {{ __('Max') }} 5MB</p>
                        @error('manager_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="flex gap-2 pt-3">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ __('Save') }}</button>
                <a href="{{ route('manager.associations.index') }}" class="text-gray-600 px-3 py-2 text-sm">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>
