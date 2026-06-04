<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr   = fn(string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('أصول الشركة', 'Company Assets') }} — {{ $tr('إضافة', 'Add') }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">{{ $tr('إضافة', 'Add') }} — {{ $tr('أصول الشركة', 'Company Assets') }}</h2>
        <a href="{{ route('manager.assets.index') }}" class="text-sm text-gray-600">{{ $tr('رجوع', 'Back') }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <form method="POST" action="{{ route('manager.assets.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الاسم', 'Name') }}</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الفئة', 'Category') }}</label>
                    <select name="category" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="laptop"           @selected(old('category')==='laptop')>{{ $tr('أجهزة كمبيوتر محمول', 'Laptops') }}</option>
                        <option value="mobile"           @selected(old('category')==='mobile')>{{ $tr('هواتف', 'Mobiles') }}</option>
                        <option value="office_equipment" @selected(old('category','office_equipment')==='office_equipment')>{{ $tr('معدات مكتبية', 'Office Equipment') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الرقم التسلسلي', 'Serial Number') }} ({{ $tr('اختياري', 'Optional') }})</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحالة', 'Status') }}</label>
                    <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="available"    @selected(old('status','available')==='available')>{{ $tr('متاح', 'Available') }}</option>
                        <option value="assigned"     @selected(old('status')==='assigned')>{{ $tr('مخصص', 'Assigned') }}</option>
                        <option value="under_repair" @selected(old('status')==='under_repair')>{{ $tr('تحت الإصلاح', 'Under Repair') }}</option>
                        <option value="retired"      @selected(old('status')==='retired')>{{ $tr('متقاعد', 'Retired') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('مخصص لـ', 'Assigned To') }} ({{ $tr('اختياري', 'Optional') }})</label>
                    <select name="assigned_to" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">— {{ $tr('غير مخصص', 'Unassigned') }} —</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(old('assigned_to')==$emp->id)>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('تاريخ الشراء', 'Purchase Date') }} ({{ $tr('اختياري', 'Optional') }})</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('سعر الشراء', 'Purchase Price') }} ({{ $tr('اختياري', 'Optional') }})</label>
                    <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('ملاحظات', 'Notes') }}</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-2 pt-3">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ $tr('حفظ', 'Save') }}</button>
                <a href="{{ route('manager.assets.index') }}" class="text-gray-600 px-3 py-2 text-sm">{{ $tr('إلغاء', 'Cancel') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>
