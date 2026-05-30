<x-app-layout>
    <x-slot name="title">{{ __('Edit') }} — {{ __('Owners Association') }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">{{ __('Edit') }} — {{ $association->name }}</h2>
        <a href="{{ route('manager.associations.show', $association) }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Back') }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-3xl">
        <form method="POST" action="{{ route('manager.associations.update', $association) }}" enctype="multipart/form-data" class="space-y-4">
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
                        <input type="text" name="electricity_account_number"
                               value="{{ old('electricity_account_number', $association->electricity_account_number) }}"
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
                        <input type="text" name="water_account_number"
                               value="{{ old('water_account_number', $association->water_account_number) }}"
                               placeholder="{{ __('Optional') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        @error('water_account_number')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Documents --}}
            <div class="border-t border-dashed border-gray-200 pt-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">{{ __('Documents') }}</p>
                @php
                    $docs = [
                        'no_objection_certificate' => ['label' => __('No Objection Certificate'),       'col' => 'no_objection_certificate_path'],
                        'sketch'                   => ['label' => __('Sketch'),                         'col' => 'sketch_path'],
                        'association_certificate'  => ['label' => __('Owners Association Certificate'), 'col' => 'association_certificate_path'],
                        'personal_id'              => ['label' => __('Personal ID'),                    'col' => 'personal_id_path'],
                        'manager_id'               => ['label' => __("Association Manager's ID"),       'col' => 'manager_id_path'],
                    ];
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($docs as $inputName => $doc)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $doc['label'] }}</label>
                        @if($association->{$doc['col']})
                        <div class="flex items-center gap-2 mb-2 p-2 bg-green-50 border border-green-100 rounded-lg">
                            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <a href="{{ asset('storage/' . $association->{$doc['col']}) }}" target="_blank" class="text-xs text-green-700 hover:text-green-900 truncate flex-1">
                                {{ basename($association->{$doc['col']}) }}
                            </a>
                            <form method="POST" action="{{ route('manager.associations.documents.delete', [$association, $doc['col']]) }}" onsubmit="return confirm('{{ __('Delete this document?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">{{ __('Delete') }}</button>
                            </form>
                        </div>
                        @endif
                        <input type="file" name="{{ $inputName }}" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:bg-gray-100 file:text-gray-700">
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $association->{$doc['col']} ? __('Upload new file to replace') : 'PDF, JPG, PNG — '.__('Max').' 5MB' }}
                        </p>
                        @error($inputName)<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    @endforeach
                </div>
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
