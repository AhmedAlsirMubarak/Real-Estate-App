<x-app-layout>
@php
    $isAr = app()->getLocale() === 'ar';
    $tr   = fn(string $ar, string $en) => $isAr ? $ar : $en;
@endphp
<x-slot name="title">{{ $tr('إضافة جمعية ملاك', 'Add Owners Association') }}</x-slot>

<div class="max-w-3xl mx-auto py-4">
    <a href="{{ route('employee.associations.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-5 inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        {{ $tr('رجوع إلى الجمعيات', 'Back to Associations') }}
    </a>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-5">{{ $tr('إضافة جمعية ملاك جديدة', 'Add New Owners Association') }}</h2>

        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('employee.associations.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('العقار', 'Property') }} <span class="text-red-500">*</span></label>
                <select name="property_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none">
                    <option value="">--</option>
                    @foreach($properties as $p)
                    <option value="{{ $p->id }}" @selected(old('property_id') == $p->id)>{{ $p->name }} ({{ $p->code }})</option>
                    @endforeach
                </select>
                @error('property_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الاسم', 'Name') }} (AR) <span class="text-red-500">*</span></label>
                    <input type="text" name="name_ar" value="{{ old('name_ar') }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none @error('name_ar') border-red-400 @enderror">
                    @error('name_ar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الاسم', 'Name') }} (EN) <span class="text-red-500">*</span></label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none @error('name_en') border-red-400 @enderror">
                    @error('name_en')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('تاريخ التأسيس', 'Established Date') }}</label>
                    <input type="date" name="established_date" value="{{ old('established_date') }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الرسوم الشهرية لكل وحدة', 'Monthly Fee per Unit') }} <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="monthly_fee_per_unit" value="{{ old('monthly_fee_per_unit', 0) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none @error('monthly_fee_per_unit') border-red-400 @enderror">
                    @error('monthly_fee_per_unit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحالة', 'Status') }}</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none">
                    <option value="active" @selected(old('status','active')==='active')>{{ $tr('نشط', 'Active') }}</option>
                    <option value="inactive" @selected(old('status')==='inactive')>{{ $tr('غير نشط', 'Inactive') }}</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الوصف (عربي)', 'Description (AR)') }}</label>
                    <textarea name="description_ar" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none resize-none">{{ old('description_ar') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الوصف (إنجليزي)', 'Description (EN)') }}</label>
                    <textarea name="description_en" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none resize-none">{{ old('description_en') }}</textarea>
                </div>
            </div>

            <div class="border-t border-dashed border-gray-200 pt-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">{{ $tr('أرقام حسابات المرافق', 'Utility Account Numbers') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('رقم حساب الكهرباء', 'Electricity Account No.') }}</label>
                        <input type="text" name="electricity_account_number" value="{{ old('electricity_account_number') }}"
                               placeholder="{{ $tr('اختياري', 'Optional') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('رقم حساب الماء', 'Water Account No.') }}</label>
                        <input type="text" name="water_account_number" value="{{ old('water_account_number') }}"
                               placeholder="{{ $tr('اختياري', 'Optional') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none">
                    </div>
                </div>
            </div>

            <div class="border-t border-dashed border-gray-200 pt-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">{{ $tr('المستندات', 'Documents') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach([
                        ['no_objection_certificate', $tr('ملكية', 'Ownership')],
                        ['sketch',                   $tr('المخطط', 'Sketch')],
                        ['association_certificate',  $tr('شهادة جمعية الملاك', 'Owners Association Certificate')],
                        ['personal_id',              $tr('الهوية الشخصية', 'Personal ID')],
                        ['manager_id',               $tr('هوية مدير الجمعية', "Association Manager's ID")],
                    ] as [$field, $label])
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                        <input type="file" name="{{ $field }}" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:bg-gray-100 file:text-gray-700">
                        <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG — {{ $tr('الحد الأقصى', 'Max') }} 5MB</p>
                        @error($field)<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('employee.associations.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-medium transition">{{ $tr('إلغاء', 'Cancel') }}</a>
                <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition">{{ $tr('حفظ الجمعية', 'Save Association') }}</button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
