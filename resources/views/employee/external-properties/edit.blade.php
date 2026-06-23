<x-app-layout>
@php
    $isAr = app()->getLocale() === 'ar';
    $tr   = fn(string $ar, string $en) => $isAr ? $ar : $en;
@endphp
<x-slot name="title">{{ $tr('تعديل العقار الخارجي', 'Edit External Property') }} — {{ $property->name }}</x-slot>

<div class="max-w-3xl mx-auto py-4">
    <a href="{{ route('employee.external-properties.show', $property) }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-5 inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        {{ $tr('رجوع', 'Back') }}
    </a>

    <form method="POST" action="{{ route('employee.external-properties.update', $property) }}" enctype="multipart/form-data"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
        @csrf @method('PUT')
        <input type="hidden" name="section" value="external">

        <h2 class="text-lg font-bold text-gray-800 mb-2">{{ $tr('تعديل', 'Edit') }}: {{ $property->name }}</h2>

        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('اسم العقار (عربي)', 'Property Name (Arabic)') }} <span class="text-red-500">*</span></label>
                <input type="text" name="name_ar" value="{{ old('name_ar', $property->name_ar) }}" required
                       @class(['w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none', 'border-red-400' => $errors->has('name_ar'), 'border-gray-200' => !$errors->has('name_ar')])
                @error('name_ar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Property Name (English)</label>
                <input type="text" name="name_en" value="{{ old('name_en', $property->name_en) }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الكود', 'Code') }}</label>
                <input type="text" name="code" value="{{ old('code', $property->code) }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('نوع العقار', 'Property Type') }} <span class="text-red-500">*</span></label>
                <select name="type" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-300"
                        onchange="toggleFloors(this.value)">
                    <option value="apartment_building" @selected(old('type', $property->type)==='apartment_building')>{{ $tr('عمارة', 'Apartment Building') }}</option>
                    <option value="flat"    @selected(old('type', $property->type)==='flat')>{{ $tr('شقة', 'Flat') }}</option>
                    <option value="villa"   @selected(old('type', $property->type)==='villa')>{{ $tr('فيلا', 'Villa') }}</option>
                    <option value="farm"    @selected(old('type', $property->type)==='farm')>{{ $tr('مزرعة', 'Farm') }}</option>
                    <option value="chalet"  @selected(old('type', $property->type)==='chalet')>{{ $tr('شاليه', 'Chalet') }}</option>
                    <option value="land"    @selected(old('type', $property->type)==='land')>{{ $tr('أرض', 'Land') }}</option>
                    <option value="office" @selected(old('type', $property->type) === 'office')>{{ $tr('مكتب', 'Office') }}</option>
                    <option value="shop"   @selected(old('type', $property->type) === 'shop')>{{ $tr('محل', 'Shop') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الغرض', 'Purpose') }} <span class="text-red-500">*</span></label>
                <select name="purpose" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="rent"           @selected(old('purpose', $property->purpose)==='rent')>{{ $tr('إيجار', 'Rent') }}</option>
                    <option value="sale"           @selected(old('purpose', $property->purpose)==='sale')>{{ $tr('بيع', 'Sale') }}</option>
                    <option value="both"           @selected(old('purpose', $property->purpose)==='both')>{{ $tr('إيجار وبيع', 'Rent & Sale') }}</option>
                    <option value="exclusive_rent" @selected(old('purpose', $property->purpose)==='exclusive_rent')>{{ $tr('ايجار حصري', 'Exclusive Rent') }}</option>
                    <option value="exclusive_sale" @selected(old('purpose', $property->purpose)==='exclusive_sale')>{{ $tr('بيع حصري', 'Exclusive Sale') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('العنوان (عربي)', 'Address (Arabic)') }} <span class="text-red-500">*</span></label>
                <input type="text" name="address_ar" value="{{ old('address_ar', $property->address_ar) }}" required
                       @class(['w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none', 'border-red-400' => $errors->has('address_ar'), 'border-gray-200' => !$errors->has('address_ar')])
                @error('address_ar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address (English)</label>
                <input type="text" name="address_en" value="{{ old('address_en', $property->address_en) }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المدينة (عربي)', 'City (Arabic)') }}</label>
                <input type="text" name="city_ar" value="{{ old('city_ar', $property->city_ar) }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">City (English)</label>
                <input type="text" name="city_en" value="{{ old('city_en', $property->city_en) }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المالك', 'Owner') }}</label>
                <select name="owner_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">{{ $tr('الشركة (لا يوجد مالك خارجي)', 'Company (no external owner)') }}</option>
                    @foreach($owners as $owner)
                    <option value="{{ $owner->id }}" @selected(old('owner_id', $property->owner_id)==$owner->id)>
                        {{ $owner->user->name }} — {{ $tr('عمولة', 'Commission') }} {{ $owner->commission_rate }}%
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الموظف المسؤول', 'Assigned Employee') }}</label>
                <select name="employee_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">— {{ $tr('غير مُسنَد', 'Unassigned') }} —</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(old('employee_id', $property->employee_id)==$emp->id)>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="floors-field">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('عدد الطوابق', 'Floors') }}</label>
                <input type="number" name="floors" value="{{ old('floors', $property->floors) }}" min="1"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المساحة الإجمالية (م²)', 'Total Area (m²)') }}</label>
                <input type="number" step="0.01" name="total_area" value="{{ old('total_area', $property->total_area) }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('غرف النوم', 'Bedrooms') }}</label>
                <input type="number" name="bedrooms" value="{{ old('bedrooms', $property->bedrooms) }}" min="0"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحمامات', 'Bathrooms') }}</label>
                <input type="number" name="bathrooms" value="{{ old('bathrooms', $property->bathrooms) }}" min="0"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none">
            </div>
        </div>

        {{-- Commission --}}
        <div class="border-t border-dashed border-gray-200 pt-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">{{ $tr('عمولة الأعمال', 'Business Commission') }}</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('عمولة الإيجار (%)', 'Rent Commission (%)') }}</label>
                    <input type="number" step="0.01" min="0" max="100" name="rent_commission_rate"
                           value="{{ old('rent_commission_rate', $property->rent_commission_rate) }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('عمولة البيع (%)', 'Sale Commission (%)') }}</label>
                    <input type="number" step="0.01" min="0" max="100" name="sale_commission_rate"
                           value="{{ old('sale_commission_rate', $property->sale_commission_rate) }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('يدفع العمولة', 'Commission Paid By') }}</label>
                    <select name="commission_payer" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">{{ $tr('-- اختر --', '-- Select --') }}</option>
                        <option value="owner"  @selected(old('commission_payer', $property->commission_payer)==='owner')>{{ $tr('المالك', 'Owner') }}</option>
                        <option value="tenant" @selected(old('commission_payer', $property->commission_payer)==='tenant')>{{ $tr('المستأجر', 'Tenant') }}</option>
                        <option value="buyer"  @selected(old('commission_payer', $property->commission_payer)==='buyer')>{{ $tr('المشتري', 'Buyer') }}</option>
                        <option value="shared" @selected(old('commission_payer', $property->commission_payer)==='shared')>{{ $tr('مشترك', 'Shared') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('ملاحظات العمولة', 'Commission Notes') }}</label>
                    <textarea name="commission_notes" rows="2"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none resize-none">{{ old('commission_notes', $property->commission_notes) }}</textarea>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الوصف (عربي)', 'Description (Arabic)') }}</label>
            <textarea name="description_ar" rows="3"
                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none resize-none">{{ old('description_ar', $property->description_ar) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description (English)</label>
            <textarea name="description_en" rows="3"
                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none resize-none">{{ old('description_en', $property->description_en) }}</textarea>
        </div>

        {{-- Existing images --}}
        @if($property->images->count())
        <div class="border-t border-gray-100 pt-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">{{ $tr('الصور الحالية', 'Current Images') }}</p>
            <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                @foreach($property->images as $img)
                <div class="relative rounded-lg overflow-hidden border border-gray-200 aspect-square">
                    <img src="{{ asset('storage/' . $img->path) }}" class="w-full h-full object-cover">
                    @if($img->is_primary)
                    <div class="absolute top-0.5 start-0.5 bg-teal-600 text-white text-[9px] font-bold px-1 rounded">{{ $tr('رئيسية', 'Main') }}</div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- New images --}}
        <div class="border-t border-gray-100 pt-4"
             x-data="{
                 previews: [],
                 sizeErrors: [],
                 pick(e) {
                     this.previews = [];
                     this.sizeErrors = [];
                     const max = 2 * 1024 * 1024;
                     Array.from(e.target.files).forEach(f => {
                         if (f.size > max) { this.sizeErrors.push(f.name); return; }
                         const r = new FileReader();
                         r.onload = ev => this.previews.push(ev.target.result);
                         r.readAsDataURL(f);
                     });
                 }
             }">
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $tr('إضافة صور جديدة', 'Add New Images') }}</label>
            <label class="flex items-center gap-2 cursor-pointer border border-dashed border-gray-300 bg-gray-50 hover:border-teal-400 rounded-lg px-4 py-3 text-sm text-gray-500 transition w-full">
                <svg class="w-5 h-5 text-teal-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                <span x-text="previews.length ? `${previews.length} {{ $tr('صورة محددة', 'image(s) selected') }}` : '{{ $tr('اختر صوراً…', 'Choose images…') }}'"></span>
                <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="hidden" @change="pick($event)">
            </label>
            <p class="text-xs text-gray-400 mt-1">{{ $tr('JPG، PNG، WebP — حد أقصى 2 ميجا للصورة', 'JPG, PNG, WebP — max 2 MB each') }}</p>
            <div x-show="previews.length > 0" class="mt-3 grid grid-cols-4 sm:grid-cols-6 gap-2">
                <template x-for="(src, i) in previews" :key="i">
                    <div class="relative rounded-lg overflow-hidden border border-gray-200 aspect-square">
                        <img :src="src" class="w-full h-full object-cover">
                    </div>
                </template>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('employee.external-properties.show', $property) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-medium transition">{{ $tr('إلغاء', 'Cancel') }}</a>
            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition">{{ $tr('حفظ التعديلات', 'Save Changes') }}</button>
        </div>
    </form>
</div>

<script>
function toggleFloors(type) {
    document.getElementById('floors-field').style.display = type === 'apartment_building' ? '' : 'none';
}
toggleFloors(document.querySelector('[name=type]').value);
</script>
</x-app-layout>
