<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
    @endphp
    <x-slot name="title">{{ $tr('تسجيل مصروف', 'Add Expense') }}</x-slot>

    <div class="max-w-2xl mx-auto">
        <a href="{{ route('manager.expenses.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">← {{ $tr('رجوع إلى المصروفات', 'Back to expenses') }}</a>

        <form method="POST" action="{{ route('manager.expenses.store') }}" enctype="multipart/form-data"
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
                        <option value="utilities"    @selected(old('category')==='utilities')>{{ $tr('مرافق (كهرباء، ماء، إنترنت)', 'Utilities') }}</option>
                        <option value="maintenance"  @selected(old('category')==='maintenance')>{{ $tr('صيانة دورية', 'Maintenance') }}</option>
                        <option value="salaries"     @selected(old('category')==='salaries')>{{ $tr('رواتب', 'Salaries') }}</option>
                        <option value="marketing"    @selected(old('category')==='marketing')>{{ $tr('تسويق وإعلانات', 'Marketing & Ads') }}</option>
                        <option value="taxes"        @selected(old('category')==='taxes')>{{ $tr('ضرائب ورسوم', 'Taxes & Fees') }}</option>
                        <option value="supplies"     @selected(old('category')==='supplies')>{{ $tr('مستلزمات', 'Supplies') }}</option>
                        <option value="insurance"    @selected(old('category')==='insurance')>{{ $tr('تأمين', 'Insurance') }}</option>
                        <option value="legal"        @selected(old('category')==='legal')>{{ $tr('قانوني', 'Legal') }}</option>
                        <option value="other"        @selected(old('category')==='other')>{{ $tr('أخرى', 'Other') }}</option>
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

            {{-- Invoice PDFs --}}
            <div x-data="{ files: [] }">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ $tr('فواتير PDF', 'Invoice PDFs') }}
                    <span class="text-gray-400 font-normal text-xs">{{ $tr('(اختياري — يمكن رفع أكثر من فاتورة)', '(optional — multiple files allowed)') }}</span>
                </label>
                @error('invoices.*') <p class="text-red-500 text-xs mb-1">{{ $message }}</p> @enderror
                <label class="flex items-center gap-3 border-2 border-dashed border-gray-300 hover:border-blue-400 hover:bg-gray-50 rounded-xl px-4 py-4 cursor-pointer transition">
                    <svg class="w-8 h-8 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-600">
                            <span x-show="files.length === 0">{{ $tr('انقر لإرفاق فواتير PDF', 'Click to attach PDF invoices') }}</span>
                            <span x-show="files.length > 0" x-text="files.length + ' {{ $tr('ملف محدد', 'file(s) selected') }}'"></span>
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">PDF — {{ $tr('بحد أقصى 10 ميجابايت لكل ملف', 'max 10 MB per file') }}</p>
                    </div>
                    <input type="file" name="invoices[]" accept="application/pdf,.pdf" multiple class="hidden"
                           @change="files = Array.from($event.target.files)">
                </label>
                <ul x-show="files.length > 0" class="mt-2 space-y-1">
                    <template x-for="f in files" :key="f.name">
                        <li class="flex items-center gap-2 text-xs text-gray-600 bg-gray-50 border border-gray-100 rounded px-3 py-1.5">
                            <svg class="w-3.5 h-3.5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span x-text="f.name"></span>
                        </li>
                    </template>
                </ul>
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
