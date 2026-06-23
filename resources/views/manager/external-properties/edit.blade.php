<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $typeValue        = old('type', $property->type);
        $purposeValue     = old('purpose', $property->purpose);
        $statusValue      = old('status', $property->status);
        $ownerValue       = old('owner_id', $property->owner_id);
        $employeeValue    = old('employee_id', $property->employee_id);
        $nameArValue      = old('name_ar', $property->name_ar ?: $property->getRawOriginal('name'));
        $nameEnValue      = old('name_en', $property->name_en ?: '');
        $addressArValue   = old('address_ar', $property->address_ar ?: $property->getRawOriginal('address'));
        $addressEnValue   = old('address_en', $property->address_en ?: '');
        $cityArValue      = old('city_ar', $property->city_ar ?: $property->getRawOriginal('city'));
        $cityEnValue      = old('city_en', $property->city_en ?: '');
        $descriptionArValue = old('description_ar', $property->description_ar ?: $property->getRawOriginal('description'));
        $descriptionEnValue = old('description_en', $property->description_en ?: '');
    @endphp
    <x-slot name="title">{{ $tr('تعديل عقار خارجي', 'Edit External Property') }}</x-slot>

    <div class="max-w-3xl mx-auto">
        <a href="{{ route('manager.external-properties.show', $property) }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">← {{ $tr('رجوع', 'Back') }}</a>

        <form method="POST" action="{{ route('manager.external-properties.update', $property) }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            @csrf @method('PATCH')
            <input type="hidden" name="section" value="external">

            <h2 class="text-lg font-bold text-gray-800 mb-2">{{ $tr('تعديل', 'Edit') }} {{ $property->name }}</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('القسم', 'Section') }}</label>
                    <div class="inline-flex items-center gap-2 px-3 py-2 bg-teal-50 border border-teal-200 rounded-lg text-sm font-medium text-teal-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $tr('عقار خارجي', 'External Properties') }}
                    </div>
                </div>
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
                        <option value="flat"    @selected($typeValue === 'flat')>{{ $tr('شقة', 'Flat') }}</option>
                        <option value="villa"   @selected($typeValue === 'villa')>{{ $tr('فيلا', 'Villa') }}</option>
                        <option value="farm"    @selected($typeValue === 'farm')>{{ $tr('مزرعة', 'Farm') }}</option>
                        <option value="chalet"  @selected($typeValue === 'chalet')>{{ $tr('شاليه', 'Chalet') }}</option>
                        <option value="land"    @selected($typeValue === 'land')>{{ $tr('أرض', 'Land') }}</option>
                        <option value="office" @selected($typeValue === 'office')>{{ $tr('مكتب', 'Office') }}</option>
                        <option value="shop"   @selected($typeValue === 'shop')>{{ $tr('محل', 'Shop') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الغرض', 'Purpose') }}</label>
                    <select name="purpose" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="rent"           @selected($purposeValue === 'rent')>{{ $tr('إيجار', 'Rent') }}</option>
                        <option value="sale"           @selected($purposeValue === 'sale')>{{ $tr('بيع', 'Sale') }}</option>
                        <option value="both"           @selected($purposeValue === 'both')>{{ $tr('إيجار وبيع', 'Rent & Sale') }}</option>
                        <option value="exclusive_rent" @selected($purposeValue === 'exclusive_rent')>{{ $tr('ايجار حصري', 'Exclusive Rent') }}</option>
                        <option value="exclusive_sale" @selected($purposeValue === 'exclusive_sale')>{{ $tr('بيع حصري', 'Exclusive Sale') }}</option>
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

                {{-- Map Coordinates --}}
                <div class="col-span-2 bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-blue-700"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0z"/></svg>
                        <span class="text-sm font-bold text-blue-900">{{ $tr('إحداثيات الخريطة', 'Map Coordinates') }}</span>
                        <span class="text-xs text-blue-600">({{ $tr('اختياري', 'Optional') }})</span>
                    </div>
                    @php
                        $coordsVal = ($property->latitude && $property->longitude)
                            ? old('latitude', $property->latitude) . ', ' . old('longitude', $property->longitude)
                            : old('_coords', '');
                    @endphp
                    <input type="text" id="coords-input" value="{{ $coordsVal }}"
                           placeholder="23.64017916319336, 58.24496583677259"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono"
                           oninput="parseCoords(this.value)" autocomplete="off">
                    <input type="hidden" name="latitude"  id="lat-hidden" value="{{ old('latitude',  $property->latitude) }}">
                    <input type="hidden" name="longitude" id="lng-hidden" value="{{ old('longitude', $property->longitude) }}">
                    <div id="coords-feedback" class="text-xs mt-1.5" style="display:none"></div>
                    <p class="text-xs text-blue-600 mt-2">{{ $tr('انسخ الإحداثيات من خرائط جوجل والصقها مباشرةً', 'Copy coordinates from Google Maps and paste directly here') }}</p>
                    <script>
                    function parseCoords(val) {
                        var fb = document.getElementById('coords-feedback');
                        var parts = val.trim().split(/[\s,،]+/).filter(Boolean);
                        if (parts.length >= 2) {
                            var lat = parseFloat(parts[0]), lng = parseFloat(parts[1]);
                            if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                                document.getElementById('lat-hidden').value = lat;
                                document.getElementById('lng-hidden').value = lng;
                                fb.style.display = 'block'; fb.style.color = '#16a34a';
                                fb.textContent = '✓ {{ $tr("تم التعرف على الإحداثيات", "Coordinates recognised") }}: ' + lat.toFixed(6) + ', ' + lng.toFixed(6);
                                return;
                            }
                        }
                        document.getElementById('lat-hidden').value = '';
                        document.getElementById('lng-hidden').value = '';
                        if (val.trim().length > 3) {
                            fb.style.display = 'block'; fb.style.color = '#dc2626';
                            fb.textContent = '{{ $tr("صيغة غير صحيحة", "Invalid format — example: 23.640, 58.244") }}';
                        } else { fb.style.display = 'none'; }
                    }
                    </script>
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

                {{-- Referral employee --}}
                <div class="col-span-2 border-t border-dashed border-gray-200 pt-4 mt-1">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">{{ $tr('موظف الإحالة', 'Referral Employee') }}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('موظف الإحالة', 'Referral Employee') }}</label>
                            <select name="referral_employee_id" id="referral-employee-select" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                                <option value="" data-rate="">— {{ $tr('لا يوجد', 'None') }} —</option>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" data-rate="{{ $emp->commission_rate }}" @selected((string) old('referral_employee_id', $property->referral_employee_id) === (string) $emp->id)>{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('نسبة عمولة الإحالة %', 'Referral Commission %') }}</label>
                            <div class="relative">
                                <input type="number" name="referral_commission_rate" id="referral-commission-rate"
                                       value="{{ old('referral_commission_rate', $property->referral_commission_rate) }}"
                                       step="0.01" min="0" max="100" placeholder="0.00"
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm pr-8">
                                <span class="absolute inset-y-0 {{ app()->getLocale() === 'ar' ? 'left-3' : 'right-3' }} flex items-center text-gray-400 text-sm">%</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Utility accounts --}}
                <div class="col-span-2 border-t border-dashed border-gray-200 pt-4 mt-1">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">{{ $tr('أرقام حسابات الخدمات', 'Utility Account Numbers') }}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    {{ $tr('رقم حساب الكهرباء', 'Electricity Account No.') }}
                                </span>
                            </label>
                            <input type="text" name="electricity_account_number"
                                   value="{{ old('electricity_account_number', $property->electricity_account_number) }}"
                                   placeholder="{{ $tr('اختياري', 'Optional') }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-6 8-12a8 8 0 1 0-16 0c0 6 8 12 8 12z"/></svg>
                                    {{ $tr('رقم حساب الماء', 'Water Account No.') }}
                                </span>
                            </label>
                            <input type="text" name="water_account_number"
                                   value="{{ old('water_account_number', $property->water_account_number) }}"
                                   placeholder="{{ $tr('اختياري', 'Optional') }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>
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
                        <option value="active"            @selected($statusValue === 'active')>{{ $tr('نشط', 'Active') }}</option>
                        <option value="sold"              @selected($statusValue === 'sold')>{{ $tr('مباع', 'Sold') }}</option>
                        <option value="rented"            @selected($statusValue === 'rented')>{{ $tr('مؤجر', 'Rented') }}</option>
                        <option value="under_maintenance" @selected($statusValue === 'under_maintenance')>{{ $tr('قيد الصيانة', 'Under Maintenance') }}</option>
                        <option value="archived"          @selected($statusValue === 'archived')>{{ $tr('مؤرشف', 'Archived') }}</option>
                    </select>
                </div>
            </div>

            {{-- Business Commission --}}
            <div class="col-span-2 border-t border-dashed border-gray-200 pt-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">{{ $tr('عمولة الأعمال', 'Business Commission') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('عمولة الإيجار (%)', 'Rent Commission (%)') }}</label>
                        <input type="number" step="0.01" min="0" max="100" name="rent_commission_rate"
                               value="{{ old('rent_commission_rate', $property->rent_commission_rate) }}"
                               placeholder="{{ $tr('مثال: 5', 'e.g. 5') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('عمولة البيع (%)', 'Sale Commission (%)') }}</label>
                        <input type="number" step="0.01" min="0" max="100" name="sale_commission_rate"
                               value="{{ old('sale_commission_rate', $property->sale_commission_rate) }}"
                               placeholder="{{ $tr('مثال: 2.5', 'e.g. 2.5') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('يدفع العمولة', 'Commission Paid By') }}</label>
                        <select name="commission_payer" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-300">
                            <option value="">{{ $tr('-- اختر --', '-- Select --') }}</option>
                            <option value="owner"  @selected(old('commission_payer', $property->commission_payer) === 'owner')>{{ $tr('المالك', 'Owner') }}</option>
                            <option value="tenant" @selected(old('commission_payer', $property->commission_payer) === 'tenant')>{{ $tr('المستأجر', 'Tenant') }}</option>
                            <option value="buyer"  @selected(old('commission_payer', $property->commission_payer) === 'buyer')>{{ $tr('المشتري', 'Buyer') }}</option>
                            <option value="shared" @selected(old('commission_payer', $property->commission_payer) === 'shared')>{{ $tr('مشترك', 'Shared') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('ملاحظات العمولة', 'Commission Notes') }}</label>
                        <textarea name="commission_notes" rows="2"
                                  placeholder="{{ $tr('أي تفاصيل إضافية...', 'Any additional details...') }}"
                                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none resize-none">{{ old('commission_notes', $property->commission_notes) }}</textarea>
                    </div>
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
                             if (f.size > max) { this.sizeErrors.push(f.name); return; }
                             const r = new FileReader();
                             r.onload = ev => this.previews.push(ev.target.result);
                             r.readAsDataURL(f);
                         });
                     }
                 }">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ $tr('إضافة صور جديدة', 'Add New Images') }}</label>
                <label :class="sizeErrors.length ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50 hover:border-teal-400'"
                       class="flex items-center gap-2 cursor-pointer border border-dashed rounded-lg px-4 py-3 text-sm text-gray-500 transition w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0" :class="sizeErrors.length ? 'text-red-500' : 'text-teal-500'"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                    <span x-text="previews.length ? `${previews.length} {{ $tr('صورة محددة', 'image(s) selected') }}` : '{{ $tr('اختر صوراً…', 'Choose images…') }}'"></span>
                    <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="hidden" @change="pick($event)">
                </label>
                <template x-if="sizeErrors.length > 0">
                    <div class="mt-2 rounded-lg bg-red-50 border border-red-200 px-3 py-2">
                        <p class="text-red-600 text-xs font-semibold">{{ $tr('الملفات التالية تتجاوز الحد الأقصى (2 ميجا):', 'The following files exceed the 2 MB limit:') }}</p>
                        <template x-for="name in sizeErrors" :key="name">
                            <p class="text-red-500 text-xs" x-text="name"></p>
                        </template>
                    </div>
                </template>
                @php $imgErrors = collect($errors->getMessages())->filter(fn($m,$k) => str_starts_with($k,'images.'))->flatten(); @endphp
                @if($imgErrors->isNotEmpty())
                <div class="mt-2 rounded-lg bg-red-50 border border-red-200 px-3 py-2">
                    @foreach($imgErrors as $msg)
                    <p class="text-red-500 text-xs">{{ $msg }}</p>
                    @endforeach
                </div>
                @endif
                <p class="text-xs text-gray-400 mt-1">{{ $tr('JPG، PNG، WebP — حد أقصى 2 ميجا للصورة', 'JPG, PNG, WebP — max 2 MB each') }}</p>
                <div x-show="previews.length > 0" class="mt-3 grid grid-cols-4 sm:grid-cols-6 gap-2">
                    <template x-for="(src, i) in previews" :key="i">
                        <div class="relative rounded-lg overflow-hidden border border-gray-200 aspect-square">
                            <img :src="src" class="w-full h-full object-cover">
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ $tr('حفظ التعديلات', 'Save Changes') }}</button>
                <a href="{{ route('manager.external-properties.show', $property) }}" class="bg-gray-100 text-gray-700 px-5 py-2 rounded-lg text-sm">{{ $tr('إلغاء', 'Cancel') }}</a>
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
                <div class="relative group rounded-xl overflow-hidden border-2 {{ $image->is_primary ? 'border-teal-500' : 'border-gray-200' }} bg-gray-50">
                    <img src="{{ $image->url() }}" alt="" class="w-full h-28 object-cover">
                    @if($image->is_primary)
                    <span class="absolute top-1.5 start-1.5 bg-teal-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-md">{{ $tr('رئيسية', 'Primary') }}</span>
                    @endif
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                        @if(!$image->is_primary)
                        <form method="POST" action="{{ route('manager.properties.images.primary', [$property, $image]) }}">
                            @csrf @method('PATCH')
                            <button class="bg-white text-teal-700 rounded-lg px-2 py-1 text-xs font-bold hover:bg-teal-50">{{ $tr('رئيسية', 'Primary') }}</button>
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

    <script>
        document.getElementById('referral-employee-select').addEventListener('change', function () {
            const rate = this.options[this.selectedIndex].dataset.rate;
            document.getElementById('referral-commission-rate').value = rate || '';
        });
    </script>
</x-app-layout>
