<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $typeValue = old('type', $property->type);
        $purposeValue = old('purpose', $property->purpose);
        $statusValue = old('status', $property->status);
        $ownerValue = old('owner_id', $property->owner_id);
        $employeeValue = old('employee_id', $property->employee_id);
        $nameArValue = old('name_ar', $property->name_ar ?: $property->getRawOriginal('name'));
        $nameEnValue = old('name_en', $property->name_en ?: '');
        $addressArValue = old('address_ar', $property->address_ar ?: $property->getRawOriginal('address'));
        $addressEnValue = old('address_en', $property->address_en ?: '');
        $cityArValue = old('city_ar', $property->city_ar ?: $property->getRawOriginal('city'));
        $cityEnValue = old('city_en', $property->city_en ?: '');
        $descriptionArValue = old('description_ar', $property->description_ar ?: $property->getRawOriginal('description'));
        $descriptionEnValue = old('description_en', $property->description_en ?: '');
    @endphp
    <x-slot name="title">{{ $tr('تعديل عقار', 'Edit Property') }}</x-slot>

    <div class="max-w-3xl mx-auto">
        <a href="{{ route('manager.properties.show', $property) }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">← {{ $tr('رجوع', 'Back') }}</a>

        <form method="POST" action="{{ route('manager.properties.update', $property) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            @csrf @method('PATCH')

            <h2 class="text-lg font-bold text-gray-800 mb-2">{{ $tr('تعديل', 'Edit') }} {{ $property->name }}</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('اسم العقار (عربي)', 'Property Name (Arabic)') }}</label>
                    <input type="text" name="name_ar" value="{{ $nameArValue }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Property Name (English)</label>
                    <input type="text" name="name_en" value="{{ $nameEnValue }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الكود', 'Code') }}</label>
                    <input type="text" name="code" value="{{ old('code', $property->code) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('نوع العقار', 'Property Type') }}</label>
                    <select name="type" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="apartment_building" @selected($typeValue === 'apartment_building')>{{ $tr('عمارة', 'Apartment Building') }}</option>
                        <option value="villa" @selected($typeValue === 'villa')>{{ $tr('فيلا', 'Villa') }}</option>
                        <option value="farm" @selected($typeValue === 'farm')>{{ $tr('مزرعة', 'Farm') }}</option>
                        <option value="chalet" @selected($typeValue === 'chalet')>{{ $tr('شاليه', 'Chalet') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الغرض', 'Purpose') }}</label>
                    <select name="purpose" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="rent" @selected($purposeValue === 'rent')>{{ $tr('إيجار', 'Rent') }}</option>
                        <option value="sale" @selected($purposeValue === 'sale')>{{ $tr('بيع', 'Sale') }}</option>
                        <option value="both" @selected($purposeValue === 'both')>{{ $tr('إيجار وبيع', 'Rent & Sale') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('العنوان (عربي)', 'Address (Arabic)') }}</label>
                    <input type="text" name="address_ar" value="{{ $addressArValue }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address (English)</label>
                    <input type="text" name="address_en" value="{{ $addressEnValue }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المدينة (عربي)', 'City (Arabic)') }}</label>
                    <input type="text" name="city_ar" value="{{ $cityArValue }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City (English)</label>
                    <input type="text" name="city_en" value="{{ $cityEnValue }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المالك', 'Owner') }}</label>
                    <select name="owner_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">{{ $tr('الشركة', 'Company') }}</option>
                        @foreach($owners as $owner)
                        <option value="{{ $owner->id }}" @selected((string) $ownerValue === (string) $owner->id)>
                            {{ $owner->user->name }} — {{ $owner->commission_rate }}%
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الموظف المسؤول', 'Assigned Employee') }}</label>
                    <select name="employee_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">— {{ $tr('غير مُسنَد', 'Unassigned') }} —</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected((string) $employeeValue === (string) $emp->id)>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('عدد الطوابق', 'Floors') }}</label>
                    <input type="number" name="floors" value="{{ old('floors', $property->floors) }}" min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المساحة الإجمالية (م²)', 'Total Area (m²)') }}</label>
                    <input type="number" step="0.01" name="total_area" value="{{ old('total_area', $property->total_area) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('غرف النوم', 'Bedrooms') }}</label>
                    <input type="number" name="bedrooms" value="{{ old('bedrooms', $property->bedrooms) }}" min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحمامات', 'Bathrooms') }}</label>
                    <input type="number" name="bathrooms" value="{{ old('bathrooms', $property->bathrooms) }}" min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحالة', 'Status') }}</label>
                    <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="active" @selected($statusValue === 'active')>{{ $tr('نشط', 'Active') }}</option>
                        <option value="sold" @selected($statusValue === 'sold')>{{ $tr('مباع', 'Sold') }}</option>
                        <option value="under_maintenance" @selected($statusValue === 'under_maintenance')>{{ $tr('قيد الصيانة', 'Under Maintenance') }}</option>
                        <option value="archived" @selected($statusValue === 'archived')>{{ $tr('مؤرشف', 'Archived') }}</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الوصف (عربي)', 'Description (Arabic)') }}</label>
                <textarea name="description_ar" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ $descriptionArValue }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description (English)</label>
                <textarea name="description_en" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ $descriptionEnValue }}</textarea>
            </div>

            <div class="flex gap-2 pt-2">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ $tr('حفظ التعديلات', 'Save Changes') }}</button>
                <a href="{{ route('manager.properties.show', $property) }}" class="bg-gray-100 text-gray-700 px-5 py-2 rounded-lg text-sm">{{ $tr('إلغاء', 'Cancel') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>
