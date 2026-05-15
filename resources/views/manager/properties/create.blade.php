<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('إضافة عقار', 'Add Property') }}</x-slot>

    <div class="max-w-3xl mx-auto">
        <a href="{{ route('manager.properties.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">← {{ $tr('رجوع', 'Back') }}</a>

        <form method="POST" action="{{ route('manager.properties.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            @csrf

            <h2 class="text-lg font-bold text-gray-800 mb-2">{{ $tr('إضافة عقار جديد', 'Create New Property') }}</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('اسم العقار (عربي)', 'Property Name (Arabic)') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name_ar" value="{{ old('name_ar') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    @error('name_ar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Property Name (English)</label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    @error('name_en') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الكود (اختياري)', 'Code (optional)') }}</label>
                    <input type="text" name="code" value="{{ old('code') }}" placeholder="{{ $tr('مثل: TH-B-003', 'Example: TH-B-003') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <p class="text-xs text-gray-400 mt-1">{{ $tr('سيُولّد تلقائياً إذا تُرك فارغاً', 'Will be generated automatically if left empty') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('نوع العقار', 'Property Type') }} <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" onchange="toggleFloors(this.value)">
                        <option value="apartment_building" @selected(old('type')==='apartment_building')>{{ $tr('عمارة', 'Apartment Building') }}</option>
                        <option value="villa" @selected(old('type')==='villa')>{{ $tr('فيلا', 'Villa') }}</option>
                        <option value="farm" @selected(old('type')==='farm')>{{ $tr('مزرعة', 'Farm') }}</option>
                        <option value="chalet" @selected(old('type')==='chalet')>{{ $tr('شاليه', 'Chalet') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الغرض', 'Purpose') }} <span class="text-red-500">*</span></label>
                    <select name="purpose" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="rent" @selected(old('purpose')==='rent')>{{ $tr('إيجار', 'Rent') }}</option>
                        <option value="sale" @selected(old('purpose')==='sale')>{{ $tr('بيع', 'Sale') }}</option>
                        <option value="both" @selected(old('purpose')==='both')>{{ $tr('إيجار وبيع', 'Rent & Sale') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('القسم', 'Section') }} <span class="text-red-500">*</span></label>
                    @php $oldSection = old('section', request('section', 'management')); @endphp
                    <select name="section" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="management" @selected($oldSection==='management')>{{ $tr('إدارة المباني', 'Building Management') }}</option>
                        <option value="hoa" @selected($oldSection==='hoa')>{{ $tr('جمعية الملاك', 'Owners Association') }}</option>
                        <option value="external" @selected($oldSection==='external')>{{ $tr('عقار خارجي', 'External Property') }}</option>
                    </select>
                    @error('section') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('العنوان (عربي)', 'Address (Arabic)') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="address_ar" value="{{ old('address_ar') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    @error('address_ar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address (English)</label>
                    <input type="text" name="address_en" value="{{ old('address_en') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    @error('address_en') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المدينة (عربي)', 'City (Arabic)') }}</label>
                    <input type="text" name="city_ar" value="{{ old('city_ar', 'الرياض') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City (English)</label>
                    <input type="text" name="city_en" value="{{ old('city_en') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المالك', 'Owner') }}</label>
                    <select name="owner_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">{{ $tr('الشركة (لا يوجد مالك خارجي)', 'Company (no external owner)') }}</option>
                        @foreach($owners as $owner)
                        <option value="{{ $owner->id }}" @selected(old('owner_id')==$owner->id)>
                            {{ $owner->user->name }} — {{ $tr('عمولة', 'Commission') }} {{ $owner->commission_rate }}%
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الموظف المسؤول', 'Assigned Employee') }}</label>
                    <select name="employee_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">— {{ $tr('غير مُسنَد', 'Unassigned') }} —</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(old('employee_id')==$emp->id)>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="floors-field">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('عدد الطوابق', 'Floors') }}</label>
                    <input type="number" name="floors" value="{{ old('floors', 1) }}" min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المساحة الإجمالية (م²)', 'Total Area (m²)') }}</label>
                    <input type="number" step="0.01" name="total_area" value="{{ old('total_area') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('غرف النوم', 'Bedrooms') }}</label>
                    <input type="number" name="bedrooms" value="{{ old('bedrooms') }}" min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحمامات', 'Bathrooms') }}</label>
                    <input type="number" name="bathrooms" value="{{ old('bathrooms') }}" min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الوصف (عربي)', 'Description (Arabic)') }}</label>
                <textarea name="description_ar" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('description_ar') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description (English)</label>
                <textarea name="description_en" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('description_en') }}</textarea>
            </div>

            <div id="unit-price-fields" class="border-t border-gray-100 pt-4 hidden">
                <p class="text-xs text-gray-500 mb-3">{{ $tr('هذه الأسعار تُستخدم لإنشاء الوحدة الواحدة تلقائياً (للفيلا/المزرعة/الشاليه):', 'These prices are used to auto-create the single unit (villa/farm/chalet):') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('سعر الإيجار (للوحدة الواحدة)', 'Rent price (single unit)') }}</label>
                        <input type="number" step="0.01" name="rent_price" value="{{ old('rent_price') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('سعر البيع (للوحدة الواحدة)', 'Sale price (single unit)') }}</label>
                        <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ $tr('حفظ', 'Save') }}</button>
                <a href="{{ route('manager.properties.index') }}" class="bg-gray-100 text-gray-700 px-5 py-2 rounded-lg text-sm">{{ $tr('إلغاء', 'Cancel') }}</a>
            </div>
        </form>
    </div>

    <script>
        function toggleFloors(type) {
            const floorsField = document.getElementById('floors-field');
            const priceFields = document.getElementById('unit-price-fields');
            if (type === 'apartment_building') {
                floorsField.style.display = '';
                priceFields.classList.add('hidden');
            } else {
                floorsField.style.display = 'none';
                priceFields.classList.remove('hidden');
            }
        }
        toggleFloors(document.querySelector('[name=type]').value);
    </script>
</x-app-layout>
