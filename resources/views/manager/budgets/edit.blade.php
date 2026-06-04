<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr   = fn(string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('ميزانية الشركة', 'Company Budget') }} — {{ $tr('تعديل', 'Edit') }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">{{ $tr('تعديل', 'Edit') }} — {{ $budget->title }}</h2>
        <a href="{{ route('manager.budgets.index') }}" class="text-sm text-gray-600">{{ $tr('رجوع', 'Back') }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <form method="POST" action="{{ route('manager.budgets.update', $budget) }}" class="space-y-4">
            @csrf @method('PATCH')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('العنوان', 'Title') }}</label>
                <input type="text" name="title" value="{{ old('title', $budget->title) }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الفئة', 'Category') }}</label>
                    <select name="category" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="hr"          @selected(old('category',$budget->category)==='hr')>{{ $tr('الموارد البشرية', 'HR') }}</option>
                        <option value="operations"  @selected(old('category',$budget->category)==='operations')>{{ $tr('العمليات', 'Operations') }}</option>
                        <option value="it"          @selected(old('category',$budget->category)==='it')>{{ $tr('تقنية المعلومات', 'IT') }}</option>
                        <option value="marketing"   @selected(old('category',$budget->category)==='marketing')>{{ $tr('التسويق', 'Marketing') }}</option>
                        <option value="maintenance" @selected(old('category',$budget->category)==='maintenance')>{{ $tr('الصيانة', 'Maintenance') }}</option>
                        <option value="other"       @selected(old('category',$budget->category)==='other')>{{ $tr('أخرى', 'Other') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحالة', 'Status') }}</label>
                    <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="draft"    @selected(old('status',$budget->status)==='draft')>{{ $tr('مسودة', 'Draft') }}</option>
                        <option value="approved" @selected(old('status',$budget->status)==='approved')>{{ $tr('معتمد', 'Approved') }}</option>
                        <option value="closed"   @selected(old('status',$budget->status)==='closed')>{{ $tr('مغلق', 'Closed') }}</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('السنة', 'Year') }}</label>
                    <input type="number" name="period_year" value="{{ old('period_year', $budget->period_year) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $tr('الشهر', 'Month') }}
                        <span class="text-gray-400 font-normal text-xs">({{ $tr('اتركه فارغاً للميزانية السنوية', 'leave blank for annual') }})</span>
                    </label>
                    <select name="period_month" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">— {{ $tr('سنوي', 'Annual') }} —</option>
                        @foreach(['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'] as $mi => $mn)
                        <option value="{{ $mi + 1 }}" @selected(old('period_month', $budget->period_month) == $mi + 1)>{{ $isAr ? $mn : \Carbon\Carbon::create()->month($mi + 1)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المبلغ المخصص', 'Allocated Amount') }}</label>
                    <input type="number" step="0.01" name="allocated_amount" value="{{ old('allocated_amount', $budget->allocated_amount) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المبلغ المصروف', 'Spent Amount') }}</label>
                    <input type="number" step="0.01" name="spent_amount" value="{{ old('spent_amount', $budget->spent_amount) }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('ملاحظات', 'Notes') }}</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('notes', $budget->notes) }}</textarea>
            </div>

            <div class="flex gap-2 pt-3">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ $tr('تحديث', 'Update') }}</button>
                <a href="{{ route('manager.budgets.index') }}" class="text-gray-600 px-3 py-2 text-sm">{{ $tr('إلغاء', 'Cancel') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>
