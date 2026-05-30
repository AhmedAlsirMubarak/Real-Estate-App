<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('إضافة عقار', 'Add Property') }}</x-slot>

    <div class="max-w-3xl mx-auto">
        <a href="{{ route('manager.properties.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">← {{ $tr('رجوع', 'Back') }}</a>

        <form method="POST" action="{{ route('manager.properties.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
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

                {{-- Referral employee who brought this property --}}
                <div class="col-span-2 border-t border-dashed border-gray-200 pt-4 mt-1">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">{{ $tr('موظف الإحالة (من أحضر العقار)', 'Referral (Who Sourced This Property)') }}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('موظف الإحالة', 'Referral Employee') }}</label>
                            <select name="referral_employee_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                                <option value="">— {{ $tr('لا يوجد', 'None') }} —</option>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" @selected(old('referral_employee_id') == $emp->id)>{{ $emp->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">{{ $tr('الموظف الذي أحضر هذا العقار أو الوحدة للشركة', 'Employee who sourced or referred this property to the company') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('نسبة عمولة الإحالة %', 'Referral Commission %') }}</label>
                            <div class="relative">
                                <input type="number" name="referral_commission_rate"
                                       value="{{ old('referral_commission_rate') }}"
                                       step="0.01" min="0" max="100" placeholder="0.00"
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm pr-8">
                                <span class="absolute inset-y-0 {{ app()->getLocale() === 'ar' ? 'left-3' : 'right-3' }} flex items-center text-gray-400 text-sm">%</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">{{ $tr('تُحتسب من إجمالي الإيجار المحصَّل لهذا العقار', 'Calculated from total collected rent for this property') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Utility account numbers --}}
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
                            <input type="text" name="electricity_account_number" value="{{ old('electricity_account_number') }}"
                                   placeholder="{{ $tr('اختياري', 'Optional') }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                            @error('electricity_account_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-6 8-12a8 8 0 1 0-16 0c0 6 8 12 8 12z"/></svg>
                                    {{ $tr('رقم حساب الماء', 'Water Account No.') }}
                                </span>
                            </label>
                            <input type="text" name="water_account_number" value="{{ old('water_account_number') }}"
                                   placeholder="{{ $tr('اختياري', 'Optional') }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                            @error('water_account_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
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

            {{-- Images --}}
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
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ $tr('صور العقار (اختياري)', 'Property Images (optional)') }}</label>
                <label :class="sizeErrors.length ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50 hover:border-blue-400'"
                       class="flex items-center gap-2 cursor-pointer border border-dashed rounded-lg px-4 py-3 text-sm text-gray-500 transition w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0" :class="sizeErrors.length ? 'text-red-500' : 'text-blue-500'"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                    <span x-text="previews.length ? `${previews.length} {{ $tr('صورة محددة', 'image(s) selected') }}` : '{{ $tr('اختر صوراً للعقار…', 'Choose property images…') }}'"></span>
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
                <p class="text-xs text-gray-400 mt-1">{{ $tr('JPG، PNG، WebP — حد أقصى 2 ميجا للصورة. يمكن إضافة المزيد لاحقاً من صفحة التعديل.', 'JPG, PNG, WebP — max 2 MB each. More can be added later from the edit page.') }}</p>
                {{-- Preview grid --}}
                <div x-show="previews.length > 0" x-transition class="mt-3 grid grid-cols-4 sm:grid-cols-6 gap-2">
                    <template x-for="(src, i) in previews" :key="i">
                        <div class="relative rounded-lg overflow-hidden border border-gray-200 aspect-square">
                            <img :src="src" class="w-full h-full object-cover">
                            <div x-show="i === 0" class="absolute top-0.5 start-0.5 bg-blue-600 text-white text-[9px] font-bold px-1 rounded">{{ $tr('رئيسية', 'Main') }}</div>
                        </div>
                    </template>
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
