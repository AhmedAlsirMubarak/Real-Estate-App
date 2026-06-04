<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
        $currentPropertyId = $expense->expensable_type === \App\Models\Property::class
            ? $expense->expensable_id : null;
    @endphp
    <x-slot name="title">{{ $tr('تعديل مصروف', 'Edit Expense') }}</x-slot>

    <div class="max-w-2xl mx-auto">
        <a href="{{ route('manager.expenses.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">← {{ $tr('رجوع إلى المصروفات', 'Back to expenses') }}</a>

        <form method="POST" action="{{ route('manager.expenses.update', $expense) }}" enctype="multipart/form-data"
              class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            @csrf
            @method('PATCH')

            <h2 class="text-lg font-bold text-gray-800 mb-2">{{ $tr('تعديل المصروف', 'Edit Expense') }}</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('عنوان المصروف (عربي)', 'Expense Title (Arabic)') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="title_ar" value="{{ old('title_ar', $expense->title_ar ?? $expense->title) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('title_ar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('عنوان المصروف (إنجليزي)', 'Expense Title (English)') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="title_en" value="{{ old('title_en', $expense->title_en ?? $expense->title) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('title_en') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('النطاق', 'Scope') }} <span class="text-red-500">*</span></label>
                    <select name="scope" id="scope-select" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"
                            onchange="togglePropertyField(this.value)">
                        <option value="company" @selected(old('scope', $expense->scope) === 'company')>{{ $tr('مصروف الشركة', 'Company Expense') }}</option>
                        <option value="property" @selected(old('scope', $expense->scope) === 'property')>{{ $tr('مصروف عقار محدد', 'Property Expense') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الفئة', 'Category') }} <span class="text-red-500">*</span></label>
                    <select name="category" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="utilities"    @selected(old('category', $expense->category) === 'utilities')>{{ $tr('مرافق (كهرباء، ماء، إنترنت)', 'Utilities') }}</option>
                        <option value="maintenance"  @selected(old('category', $expense->category) === 'maintenance')>{{ $tr('صيانة دورية', 'Maintenance') }}</option>
                        <option value="salaries"     @selected(old('category', $expense->category) === 'salaries')>{{ $tr('رواتب', 'Salaries') }}</option>
                        <option value="marketing"    @selected(old('category', $expense->category) === 'marketing')>{{ $tr('تسويق وإعلانات', 'Marketing & Ads') }}</option>
                        <option value="taxes"        @selected(old('category', $expense->category) === 'taxes')>{{ $tr('ضرائب ورسوم', 'Taxes & Fees') }}</option>
                        <option value="supplies"     @selected(old('category', $expense->category) === 'supplies')>{{ $tr('مستلزمات', 'Supplies') }}</option>
                        <option value="insurance"    @selected(old('category', $expense->category) === 'insurance')>{{ $tr('تأمين', 'Insurance') }}</option>
                        <option value="legal"        @selected(old('category', $expense->category) === 'legal')>{{ $tr('قانوني', 'Legal') }}</option>
                        <option value="other"        @selected(old('category', $expense->category) === 'other')>{{ $tr('أخرى', 'Other') }}</option>
                    </select>
                </div>

                <div id="property-field" style="{{ old('scope', $expense->scope) === 'property' ? '' : 'display:none' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('العقار', 'Property') }} <span class="text-red-500">*</span></label>
                    <select name="property_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">{{ $tr('— اختر عقاراً —', '— Select a property —') }}</option>
                        @foreach($properties as $p)
                        <option value="{{ $p->id }}" @selected(old('property_id', $currentPropertyId) == $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                    @error('property_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المبلغ', 'Amount') }} ({{ $currency }}) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount', $expense->amount) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('تاريخ المصروف', 'Expense Date') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="expense_date" value="{{ old('expense_date', $expense->expense_date->toDateString()) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('دُفع بواسطة', 'Paid by') }}</label>
                    <select name="paid_by" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">{{ $tr('— المستخدم الحالي —', '— Current user —') }}</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(old('paid_by', $expense->paid_by) == $emp->id)>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('ملاحظات (عربي)', 'Notes (Arabic)') }}</label>
                    <textarea name="description_ar" rows="3"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('description_ar', $expense->description_ar) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('ملاحظات (إنجليزي)', 'Notes (English)') }}</label>
                    <textarea name="description_en" rows="3"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('description_en', $expense->description_en) }}</textarea>
                </div>
            </div>

            {{-- Add more invoices --}}
            <div x-data="{ files: [] }">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ $tr('إضافة فواتير جديدة', 'Add New Invoices') }}
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
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ $tr('حفظ التعديلات', 'Save Changes') }}</button>
                <a href="{{ route('manager.expenses.index') }}" class="bg-gray-100 text-gray-700 px-5 py-2 rounded-lg text-sm">{{ $tr('إلغاء', 'Cancel') }}</a>
            </div>
        </form>

        {{-- Attached invoices — separate card below the form to avoid nested-form issues --}}
        @php
            $existingInvoices = $expense->invoices()->get();
            $legacyPath = $expense->receipt_path && ! $existingInvoices->contains('file_path', $expense->receipt_path)
                ? $expense->receipt_path : null;
        @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mt-4">
            <p class="text-sm font-semibold text-gray-700 mb-3">{{ $tr('الفواتير المرفقة', 'Attached Invoices') }}</p>
            @if($existingInvoices->count() || $legacyPath)
            <div class="space-y-2">
                @foreach($existingInvoices as $inv)
                <div class="flex items-center gap-3 p-2 bg-green-50 border border-green-200 rounded-lg">
                    <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="text-xs text-gray-700 flex-1 truncate">{{ $inv->original_name ?? basename($inv->file_path) }}</span>
                    <a href="{{ asset('storage/' . $inv->file_path) }}" target="_blank"
                       class="text-xs text-blue-600 hover:text-blue-800 font-medium">{{ $tr('عرض', 'View') }}</a>
                    <form method="POST" action="{{ route('manager.expenses.invoices.destroy', $inv) }}"
                          onsubmit="return confirm('{{ $tr('حذف هذه الفاتورة؟', 'Delete this invoice?') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">{{ $tr('حذف', 'Delete') }}</button>
                    </form>
                </div>
                @endforeach
                @if($legacyPath)
                <div class="flex items-center gap-3 p-2 bg-green-50 border border-green-200 rounded-lg">
                    <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="text-xs text-gray-700 flex-1 truncate">{{ basename($legacyPath) }}</span>
                    <a href="{{ asset('storage/' . $legacyPath) }}" target="_blank"
                       class="text-xs text-blue-600 hover:text-blue-800 font-medium">{{ $tr('عرض', 'View') }}</a>
                    <form method="POST" action="{{ route('manager.expenses.receipt.destroy', $expense) }}"
                          onsubmit="return confirm('{{ $tr('حذف هذه الفاتورة؟', 'Delete this invoice?') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">{{ $tr('حذف', 'Delete') }}</button>
                    </form>
                </div>
                @endif
            </div>
            @else
            <p class="text-xs text-gray-400">{{ $tr('لا توجد فواتير مرفقة بعد', 'No invoices attached yet') }}</p>
            @endif
        </div>
    </div>

    <script>
    function togglePropertyField(scope) {
        document.getElementById('property-field').style.display = scope === 'property' ? '' : 'none';
    }
    </script>
</x-app-layout>
