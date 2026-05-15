<x-app-layout>
    <x-slot name="title">تعديل وحدة</x-slot>

    <div class="max-w-2xl mx-auto">
        <a href="{{ route('manager.properties.show', $property) }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">← رجوع</a>

        <form method="POST" action="{{ route('manager.units.update', [$property, $unit]) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            @csrf @method('PATCH')

            <h2 class="text-lg font-bold text-gray-800 mb-1">تعديل وحدة {{ $unit->unit_number ?? '#' . $unit->id }}</h2>
            <p class="text-xs text-gray-500 mb-3">في: <strong>{{ $property->name }}</strong></p>

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
                    <select name="type" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        @foreach(['apartment'=>'شقة','shop'=>'محل','office'=>'مكتب','studio'=>'استوديو','villa_unit'=>'فيلا','farm_unit'=>'مزرعة','chalet_unit'=>'شاليه'] as $v=>$l)
                        <option value="{{ $v }}" @selected($unit->type===$v)>{{ $l }}</option>
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">غرف النوم</label>
                    <input type="number" name="bedrooms" value="{{ old('bedrooms', $unit->bedrooms) }}" min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
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
    </div>
</x-app-layout>
