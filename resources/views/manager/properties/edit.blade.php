<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $typeValue = old('type', $property->type);
        $purposeValue = old('purpose', $property->purpose);
        $sectionValue = old('section', $property->section ?? 'management');
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

        <form method="POST" action="{{ route('manager.properties.update', $property) }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('القسم', 'Section') }} <span class="text-red-500">*</span></label>
                    <select name="section" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="management" @selected($sectionValue === 'management')>{{ $tr('إدارة المباني', 'Building Management') }}</option>
                        <option value="hoa" @selected($sectionValue === 'hoa')>{{ $tr('جمعية الملاك', 'Owners Association') }}</option>
                        <option value="external" @selected($sectionValue === 'external')>{{ $tr('عقار خارجي', 'External Property') }}</option>
                    </select>
                    @error('section') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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

            {{-- Upload new images --}}
            <div class="border-t border-gray-100 pt-4"
                 x-data="{
                     previews: [],
                     sizeErrors: [],
                     pick(e) {
                         this.previews = [];
                         this.sizeErrors = [];
                         const max = 2 * 1024 * 1024;
                         Array.from(e.target.files).forEach(f => {
                             if (f.size > max) {
                                 this.sizeErrors.push(f.name);
                                 return;
                             }
                             const r = new FileReader();
                             r.onload = ev => this.previews.push(ev.target.result);
                             r.readAsDataURL(f);
                         });
                     }
                 }">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ $tr('إضافة صور جديدة', 'Add New Images') }}</label>
                <label :class="sizeErrors.length ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50 hover:border-blue-400'"
                       class="flex items-center gap-2 cursor-pointer border border-dashed rounded-lg px-4 py-3 text-sm text-gray-500 transition w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0" :class="sizeErrors.length ? 'text-red-500' : 'text-blue-500'"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                    <span x-text="previews.length ? `${previews.length} {{ $tr('صورة محددة', 'image(s) selected') }}` : '{{ $tr('اختر صوراً…', 'Choose images…') }}'"></span>
                    <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="hidden" @change="pick($event)">
                </label>
                {{-- Client-side size errors --}}
                <template x-if="sizeErrors.length > 0">
                    <div class="mt-2 rounded-lg bg-red-50 border border-red-200 px-3 py-2 space-y-1">
                        <p class="text-red-600 text-xs font-semibold">{{ $tr('الملفات التالية تتجاوز الحد الأقصى (2 ميجا):', 'The following files exceed the 2 MB limit:') }}</p>
                        <template x-for="name in sizeErrors" :key="name">
                            <p class="text-red-500 text-xs flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5 flex-shrink-0"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                                <span x-text="name"></span>
                            </p>
                        </template>
                    </div>
                </template>
                {{-- Server-side validation errors --}}
                @php $imgErrors = collect($errors->getMessages())->filter(fn($m,$k) => str_starts_with($k,'images.'))->flatten(); @endphp
                @if($imgErrors->isNotEmpty())
                <div class="mt-2 rounded-lg bg-red-50 border border-red-200 px-3 py-2 space-y-1">
                    @foreach($imgErrors as $msg)
                    <p class="text-red-500 text-xs flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5 flex-shrink-0"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                        {{ $msg }}
                    </p>
                    @endforeach
                </div>
                @endif
                <p class="text-xs text-gray-400 mt-1">{{ $tr('JPG، PNG، WebP — حد أقصى 2 ميجا للصورة', 'JPG, PNG, WebP — max 2 MB each') }}</p>
                {{-- Preview grid --}}
                <div x-show="previews.length > 0" x-transition class="mt-3 grid grid-cols-4 sm:grid-cols-6 gap-2">
                    <template x-for="(src, i) in previews" :key="i">
                        <div class="relative rounded-lg overflow-hidden border border-gray-200 aspect-square">
                            <img :src="src" class="w-full h-full object-cover">
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ $tr('حفظ التعديلات', 'Save Changes') }}</button>
                <a href="{{ route('manager.properties.show', $property) }}" class="bg-gray-100 text-gray-700 px-5 py-2 rounded-lg text-sm">{{ $tr('إلغاء', 'Cancel') }}</a>
            </div>
        </form>

        {{-- Existing images gallery --}}
        @if($property->images->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-5">
            <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-sm">{{ $tr('الصور الحالية', 'Current Images') }} ({{ $property->images->count() }})</h3>
            </div>
            <div class="p-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                @foreach($property->images as $image)
                <div class="relative group rounded-xl overflow-hidden border-2 {{ $image->is_primary ? 'border-blue-500' : 'border-gray-200' }} bg-gray-50">
                    <img src="{{ $image->url() }}" alt="" class="w-full h-28 object-cover">
                    @if($image->is_primary)
                    <span class="absolute top-1.5 start-1.5 bg-blue-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-md">{{ $tr('رئيسية', 'Primary') }}</span>
                    @endif
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                        @if(!$image->is_primary)
                        <form method="POST" action="{{ route('manager.properties.images.primary', [$property, $image]) }}">
                            @csrf @method('PATCH')
                            <button class="bg-white text-blue-700 rounded-lg px-2 py-1 text-xs font-bold hover:bg-blue-50">{{ $tr('رئيسية', 'Primary') }}</button>
                        </form>
                        @endif
                        <form method="POST" action="{{ route('manager.properties.images.destroy', [$property, $image]) }}" onsubmit="return confirm('{{ $tr('حذف الصورة؟', 'Delete image?') }}')">
                            @csrf @method('DELETE')
                            <button class="bg-white text-red-600 rounded-lg px-2 py-1 text-xs font-bold hover:bg-red-50">{{ $tr('حذف', 'Delete') }}</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
