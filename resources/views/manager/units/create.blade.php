<x-app-layout>
    <x-slot name="title">إضافة وحدة</x-slot>

    <div class="max-w-2xl mx-auto">
        <a href="{{ route('manager.properties.show', $property) }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">← رجوع إلى {{ $property->name }}</a>

        <form method="POST" action="{{ route('manager.units.store', $property) }}"
              class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4"
              x-data="{ unitType: '{{ old('type', 'apartment') }}',
                        isResidential() { return !['office','shop'].includes(this.unitType); } }">
            @csrf

            <h2 class="text-lg font-bold text-gray-800 mb-1">إضافة وحدة جديدة</h2>
            <p class="text-xs text-gray-500 mb-3">ستُضاف إلى: <strong>{{ $property->name }}</strong></p>

            @if($owners->isNotEmpty())
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">المالك (جمعية الملاك) <span class="text-red-500">*</span></label>
                <select name="owner_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">-- اختر المالك --</option>
                    @foreach($owners as $owner)
                    <option value="{{ $owner->id }}" @selected(old('owner_id') == $owner->id)>
                        {{ $owner->user?->name ?? 'مالك #'.$owner->id }}
                    </option>
                    @endforeach
                </select>
                @error('owner_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">رقم الوحدة</label>
                    <input type="text" name="unit_number" value="{{ old('unit_number') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    @error('unit_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الطابق</label>
                    <input type="number" name="floor" value="{{ old('floor', 1) }}" min="0" max="{{ $property->floors ?? 99 }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">النوع <span class="text-red-500">*</span></label>
                    <select name="type" required x-model="unitType" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="apartment">شقة سكنية</option>
                        <option value="studio">استوديو</option>
                        <option value="office">مكتب</option>
                        <option value="shop">محل تجاري</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">غرض العرض <span class="text-red-500">*</span></label>
                    <select name="listing_type" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="rent" @selected(old('listing_type')==='rent')>إيجار</option>
                        <option value="sale" @selected(old('listing_type')==='sale')>بيع</option>
                        <option value="both" @selected(old('listing_type')==='both')>إيجار أو بيع</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">المساحة (م²)</label>
                    <input type="number" step="0.01" name="area" value="{{ old('area') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

                {{-- Residential-only fields --}}
                <div x-show="isResidential()" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-1">غرف النوم</label>
                    <input type="number" name="bedrooms" value="{{ old('bedrooms') }}" min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div x-show="isResidential()" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الحمامات</label>
                    <input type="number" name="bathrooms" value="{{ old('bathrooms') }}" min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">سعر الإيجار الشهري</label>
                    <input type="number" step="0.01" name="rent_price" value="{{ old('rent_price') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    @error('rent_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">سعر البيع</label>
                    <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    @error('sale_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-2 pt-2">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">إضافة الوحدة</button>
                <a href="{{ route('manager.properties.show', $property) }}" class="bg-gray-100 text-gray-700 px-5 py-2 rounded-lg text-sm">إلغاء</a>
            </div>
        </form>
    </div>
</x-app-layout>
