<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('إضافة عقار جديد', 'Add New Property') }}</x-slot>

    <div class="max-w-3xl mx-auto py-4">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('employee.dashboard') }}" class="text-gray-500 hover:text-gray-700 text-sm">← {{ $tr('العودة للوحة', 'Back to Dashboard') }}</a>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('إضافة عقار جديد', 'Add New Property') }}</h2>
        </div>

        @if($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm space-y-1">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 mb-5 flex items-center gap-2 text-sm text-blue-700">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $tr('سيتم تعيينك تلقائياً كموظف مسؤول عن هذا العقار', 'You will be automatically assigned as the responsible employee for this property') }}
        </div>

        <form method="POST" action="{{ route('employee.properties.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('اسم العقار (عربي)', 'Property Name (Arabic)') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name_ar" value="{{ old('name_ar') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('name_ar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Property Name (English)</label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الكود (اختياري)', 'Code (optional)') }}</label>
                    <input type="text" name="code" value="{{ old('code') }}" placeholder="{{ $tr('يُولَّد تلقائياً إذا تُرك فارغاً', 'Auto-generated if left empty') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('نوع العقار', 'Property Type') }} <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" onchange="toggleFloors(this.value)">
                        <option value="apartment_building" @selected(old('type')==='apartment_building')>{{ $tr('عمارة', 'Apartment Building') }}</option>
                        <option value="flat" @selected(old('type')==='flat')>{{ $tr('شقة', 'Flat') }}</option>
                        <option value="villa" @selected(old('type')==='villa')>{{ $tr('فيلا', 'Villa') }}</option>
                        <option value="farm" @selected(old('type')==='farm')>{{ $tr('مزرعة', 'Farm') }}</option>
                        <option value="chalet" @selected(old('type')==='chalet')>{{ $tr('شاليه', 'Chalet') }}</option>
                        <option value="land" @selected(old('type')==='land')>{{ $tr('أرض', 'Land') }}</option>
                        <option value="office" @selected(old('type')==='office')>{{ $tr('مكتب', 'Office') }}</option>
                        <option value="shop"   @selected(old('type')==='shop')>{{ $tr('محل', 'Shop') }}</option>

                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الغرض', 'Purpose') }} <span class="text-red-500">*</span></label>
                    <select name="purpose" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="rent" @selected(old('purpose')==='rent')>{{ $tr('إيجار', 'Rent') }}</option>
                        <option value="sale" @selected(old('purpose')==='sale')>{{ $tr('بيع', 'Sale') }}</option>
                        <option value="both" @selected(old('purpose')==='both')>{{ $tr('إيجار وبيع', 'Rent & Sale') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('القسم', 'Section') }} <span class="text-red-500">*</span></label>
                    <select name="section" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="management" @selected(old('section','management')==='management')>{{ $tr('إدارة المباني', 'Building Management') }}</option>
                        <option value="hoa" @selected(old('section')==='hoa')>{{ $tr('جمعية الملاك', 'Owners Association') }}</option>
                        <option value="external" @selected(old('section')==='external')>{{ $tr('عقار خارجي', 'External Property') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('العنوان (عربي)', 'Address (Arabic)') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="address_ar" value="{{ old('address_ar') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('address_ar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address (English)</label>
                    <input type="text" name="address_en" value="{{ old('address_en') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المدينة (عربي)', 'City (Arabic)') }}</label>
                    <input type="text" name="city_ar" value="{{ old('city_ar') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City (English)</label>
                    <input type="text" name="city_en" value="{{ old('city_en') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المالك', 'Owner') }}</label>
                    <select name="owner_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ $tr('الشركة (لا يوجد مالك خارجي)', 'Company (no external owner)') }}</option>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}" @selected(old('owner_id') == $owner->id)>
                                {{ $owner->user->name }} — {{ $tr('عمولة', 'Commission') }} {{ $owner->commission_rate }}%
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="floors-field">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('عدد الطوابق', 'Floors') }}</label>
                    <input type="number" name="floors" value="{{ old('floors', 1) }}" min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المساحة الإجمالية (م²)', 'Total Area (m²)') }}</label>
                    <input type="number" step="0.01" name="total_area" value="{{ old('total_area') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('غرف النوم', 'Bedrooms') }}</label>
                    <input type="number" name="bedrooms" value="{{ old('bedrooms') }}" min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحمامات', 'Bathrooms') }}</label>
                    <input type="number" name="bathrooms" value="{{ old('bathrooms') }}" min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('رقم حساب الكهرباء', 'Electricity Account No.') }}</label>
                    <input type="text" name="electricity_account_number" value="{{ old('electricity_account_number') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('رقم حساب الماء', 'Water Account No.') }}</label>
                    <input type="text" name="water_account_number" value="{{ old('water_account_number') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الوصف (عربي)', 'Description (Arabic)') }}</label>
                <textarea name="description_ar" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">{{ old('description_ar') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description (English)</label>
                <textarea name="description_en" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">{{ old('description_en') }}</textarea>
            </div>

            <div id="unit-price-fields" class="border-t border-gray-100 pt-4 hidden">
                <p class="text-xs text-gray-500 mb-3">{{ $tr('هذه الأسعار للوحدة الواحدة (فيلا/مزرعة/شاليه):', 'Prices for the single unit (villa/farm/chalet):') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('سعر الإيجار', 'Rent Price') }}</label>
                        <input type="number" step="0.01" name="rent_price" value="{{ old('rent_price') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('سعر البيع', 'Sale Price') }}</label>
                        <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium transition">{{ $tr('حفظ', 'Save Property') }}</button>
                <a href="{{ route('employee.dashboard') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium transition">{{ $tr('إلغاء', 'Cancel') }}</a>
            </div>
        </form>
    </div>

    <script>
        function toggleFloors(type) {
            document.getElementById('floors-field').style.display = type === 'apartment_building' ? '' : 'none';
            document.getElementById('unit-price-fields').classList.toggle('hidden', type === 'apartment_building');
        }
        toggleFloors(document.querySelector('[name=type]').value);
    </script>
</x-app-layout>
