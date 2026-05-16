<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
    @endphp
    <x-slot name="title">{{ $tr('تسجيل مصروف', 'Add Expense') }}</x-slot>

    <div class="max-w-2xl mx-auto">
        <a href="{{ route('manager.expenses.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">← {{ $tr('رجوع إلى المصروفات', 'Back to expenses') }}</a>

        <form method="POST" action="{{ route('manager.expenses.store') }}"
              class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            @csrf

            <h2 class="text-lg font-bold text-gray-800 mb-2">{{ $tr('تسجيل مصروف جديد', 'Create New Expense') }}</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('عنوان المصروف (عربي)', 'Expense Title (Arabic)') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="title_ar" value="{{ old('title_ar') }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="{{ $tr('مثال: صيانة المصعد، رواتب الشهر...', 'Example: Elevator maintenance, monthly salaries...') }}">
                    @error('title_ar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('عنوان المصروف (إنجليزي)', 'Expense Title (English)') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="title_en" value="{{ old('title_en') }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="{{ $tr('مثال: Elevator maintenance, monthly salaries...', 'Example: Elevator maintenance, monthly salaries...') }}">
                    @error('title_en') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('النطاق', 'Scope') }} <span class="text-red-500">*</span></label>
                    <select name="scope" id="scope-select" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"
                            onchange="togglePropertyField(this.value)">
                        <option value="company" @selected(old('scope')==='company')>{{ $tr('مصروف الشركة', 'Company Expense') }}</option>
                        <option value="property" @selected(old('scope')==='property')>{{ $tr('مصروف عقار محدد', 'Property Expense') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الفئة', 'Category') }} <span class="text-red-500">*</span></label>
                    <select name="category" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="utilities" @selected(old('category')==='utilities')>{{ $tr('مرافق (كهرباء، ماء، إنترنت)', 'Utilities (electricity, water, internet)') }}</option>
                        <option value="maintenance" @selected(old('category')==='maintenance')>{{ $tr('صيانة دورية', 'Maintenance') }}</option>
                        <option value="salaries" @selected(old('category')==='salaries')>{{ $tr('رواتب', 'Salaries') }}</option>
                        <option value="marketing" @selected(old('category')==='marketing')>{{ $tr('تسويق وإعلانات', 'Marketing & Ads') }}</option>
                        <option value="repairs" @selected(old('category')==='repairs')>{{ $tr('إصلاحات', 'Repairs') }}</option>
                        <option value="other" @selected(old('category')==='other')>{{ $tr('أخرى', 'Other') }}</option>
                    </select>
                </div>

                <div id="property-field" style="{{ old('scope', 'company') === 'property' ? '' : 'display:none' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('العقار', 'Property') }} <span class="text-red-500">*</span></label>
                    <select name="property_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">{{ $tr('— اختر عقاراً —', '— Select a property —') }}</option>
                        @foreach($properties as $p)
                        <option value="{{ $p->id }}" @selected(old('property_id')==$p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                    @error('property_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المبلغ', 'Amount') }} ({{ $currency }}) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('تاريخ المصروف', 'Expense Date') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('دُفع بواسطة', 'Paid by') }}</label>
                    <select name="paid_by" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">{{ $tr('— المستخدم الحالي —', '— Current user —') }}</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(old('paid_by')==$emp->id)>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('ملاحظات (عربي)', 'Notes (Arabic)') }}</label>
                    <textarea name="description_ar" rows="3"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('description_ar') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('ملاحظات (إنجليزي)', 'Notes (English)') }}</label>
                    <textarea name="description_en" rows="3"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('description_en') }}</textarea>
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ $tr('تسجيل المصروف', 'Save Expense') }}</button>
                <a href="{{ route('manager.expenses.index') }}" class="bg-gray-100 text-gray-700 px-5 py-2 rounded-lg text-sm">{{ $tr('إلغاء', 'Cancel') }}</a>
            </div>
        </form>
    </div>

    <script>
    function togglePropertyField(scope) {
        document.getElementById('property-field').style.display = scope === 'property' ? '' : 'none';
    }
    </script>
</x-app-layout>
