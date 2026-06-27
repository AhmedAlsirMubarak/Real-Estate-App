<x-app-layout>
    <x-slot name="title">تعديل وحدة</x-slot>

    <div class="max-w-2xl mx-auto">
        <a href="{{ route('manager.properties.show', $property) }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">← رجوع</a>

        <form method="POST" action="{{ route('manager.units.update', [$property, $unit]) }}"
              class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4"
              x-data="{ unitType: '{{ old('type', $unit->type) }}',
                        isResidential() { return !['office','shop'].includes(this.unitType); } }">
            @csrf @method('PATCH')

            <h2 class="text-lg font-bold text-gray-800 mb-1">تعديل وحدة {{ $unit->unit_number ?? '#' . $unit->id }}</h2>
            <p class="text-xs text-gray-500 mb-3">في: <strong>{{ $property->name }}</strong></p>

            @if($owners->isNotEmpty())
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">المالك (جمعية الملاك) <span class="text-red-500">*</span></label>
                <select name="owner_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">-- اختر المالك --</option>
                    @foreach($owners as $owner)
                    <option value="{{ $owner->id }}" @selected(old('owner_id', $unit->owner_id) == $owner->id)>
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
                    <input type="text" name="unit_number" value="{{ old('unit_number', $unit->unit_number) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الطابق</label>
                    <input type="number" name="floor" value="{{ old('floor', $unit->floor) }}" min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">النوع</label>
                    <select name="type" required x-model="unitType" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        @foreach(['apartment'=>'شقة سكنية','studio'=>'استوديو','office'=>'مكتب','shop'=>'محل تجاري','villa_unit'=>'وحدة فيلا','farm_unit'=>'وحدة مزرعة','chalet_unit'=>'وحدة شاليه'] as $v=>$l)
                        <option value="{{ $v }}" @selected(old('type', $unit->type)===$v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">غرض العرض</label>
                    <select name="listing_type" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="rent" @selected($unit->listing_type==='rent')>إيجار</option>
                        <option value="sale" @selected($unit->listing_type==='sale')>بيع</option>
                        <option value="both" @selected($unit->listing_type==='both')>إيجار أو بيع</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">المساحة (م²)</label>
                    <input type="number" step="0.01" name="area" value="{{ old('area', $unit->area) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <select name="status" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="available" @selected($unit->status==='available')>متاح</option>
                        <option value="rented" @selected($unit->status==='rented')>مؤجر</option>
                        <option value="sold" @selected($unit->status==='sold')>مباع</option>
                        <option value="reserved" @selected($unit->status==='reserved')>محجوز</option>
                        <option value="maintenance" @selected($unit->status==='maintenance')>صيانة</option>
                    </select>
                </div>

                {{-- Residential-only fields --}}
                <div x-show="isResidential()" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-1">غرف النوم</label>
                    <input type="number" name="bedrooms" value="{{ old('bedrooms', $unit->bedrooms) }}" min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div x-show="isResidential()" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الحمامات</label>
                    <input type="number" name="bathrooms" value="{{ old('bathrooms', $unit->bathrooms) }}" min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">سعر الإيجار</label>
                    <input type="number" step="0.01" name="rent_price" value="{{ old('rent_price', $unit->rent_price) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">سعر البيع</label>
                    <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $unit->sale_price) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('notes', $unit->notes) }}</textarea>
            </div>

            <div class="flex gap-2 pt-2">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">حفظ التعديلات</button>
                <a href="{{ route('manager.properties.show', $property) }}" class="bg-gray-100 text-gray-700 px-5 py-2 rounded-lg text-sm">إلغاء</a>
            </div>
        </form>

        {{-- Unit Images --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-5">
            <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-sm">صور الوحدة ({{ $unit->images->count() }})</h3>
            </div>

            <div class="px-4 py-4 border-b border-gray-50 bg-gray-50"
                 x-data="{
                     label: 'اختر صور',
                     sizeErrors: [],
                     pick(e) {
                         this.sizeErrors = [];
                         const max = 2 * 1024 * 1024;
                         const ok = Array.from(e.target.files).filter(f => {
                             if (f.size > max) { this.sizeErrors.push(f.name); return false; }
                             return true;
                         });
                         this.label = ok.length ? ok.length + ' ملف(ات) محددة' : 'اختر صور';
                     }
                 }">
                <form method="POST" action="{{ route('manager.units.images.store', [$property, $unit]) }}" enctype="multipart/form-data" class="flex items-center gap-3 flex-wrap">
                    @csrf
                    <label :class="sizeErrors.length ? 'border-red-400 bg-red-50 text-red-600' : 'border-gray-200 bg-white text-gray-600 hover:border-blue-400'"
                           class="flex items-center gap-2 cursor-pointer border rounded-lg px-3 py-2 text-sm transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                        <span x-text="label"></span>
                        <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="hidden" @change="pick($event)">
                    </label>
                    <button type="submit" :disabled="sizeErrors.length > 0" class="bg-blue-900 hover:bg-blue-800 disabled:opacity-50 text-white px-4 py-2 rounded-lg text-sm font-medium">رفع الصور</button>
                    <span class="text-xs text-gray-400">JPG, PNG, WebP — حد أقصى 2 ميجا للصورة</span>
                </form>
                {{-- Client-side size errors --}}
                <template x-if="sizeErrors.length > 0">
                    <div class="mt-2 rounded-lg bg-red-50 border border-red-200 px-3 py-2 space-y-1">
                        <p class="text-red-600 text-xs font-semibold">الملفات التالية تتجاوز الحد الأقصى (2 ميجا):</p>
                        <template x-for="name in sizeErrors" :key="name">
                            <p class="text-red-500 text-xs flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5 flex-shrink-0"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                                <span x-text="name"></span>
                            </p>
                        </template>
                    </div>
                </template>
                {{-- Server-side errors --}}
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
            </div>

            @if($unit->images->isEmpty())
            <div class="py-8 text-center text-gray-400 text-sm">لا توجد صور مضافة بعد</div>
            @else
            <div class="p-4 grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($unit->images as $image)
                <div class="relative group rounded-xl overflow-hidden border-2 {{ $image->is_primary ? 'border-blue-500' : 'border-gray-200' }} bg-gray-50">
                    <img src="{{ $image->url() }}" alt="" class="w-full h-28 object-cover">
                    @if($image->is_primary)
                    <span class="absolute top-1.5 start-1.5 bg-blue-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-md">رئيسية</span>
                    @endif
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                        @if(!$image->is_primary)
                        <form method="POST" action="{{ route('manager.units.images.primary', [$property, $unit, $image]) }}">
                            @csrf @method('PATCH')
                            <button class="bg-white text-blue-700 rounded-lg px-2 py-1 text-xs font-bold hover:bg-blue-50">رئيسية</button>
                        </form>
                        @endif
                        <form method="POST" action="{{ route('manager.units.images.destroy', [$property, $unit, $image]) }}" onsubmit="return confirm('حذف الصورة؟')">
                            @csrf @method('DELETE')
                            <button class="bg-white text-red-600 rounded-lg px-2 py-1 text-xs font-bold hover:bg-red-50">حذف</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
