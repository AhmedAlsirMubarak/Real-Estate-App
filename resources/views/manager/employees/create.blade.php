<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('إضافة موظف جديد', 'Add New Employee') }}</x-slot>
    <div class="py-4 max-w-xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('manager.employees.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('إضافة موظف جديد', 'Add New Employee') }}</h2>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <form method="POST" action="{{ route('manager.employees.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الاسم (عربي)', 'Name (Arabic)') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name_ar" value="{{ old('name_ar') }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('name_ar') border-red-500 @enderror">
                    @error('name_ar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الاسم (إنجليزي)', 'Name (English)') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('name_en') border-red-500 @enderror">
                    @error('name_en')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('البريد الإلكتروني', 'Email') }} <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('رقم الهاتف', 'Phone Number') }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الراتب الأساسي (ر.ع)', 'Base Salary (OMR)') }}</label>
                    <input type="number" step="0.01" min="0" name="base_salary" value="{{ old('base_salary', 0) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('base_salary') border-red-500 @enderror">
                    @error('base_salary')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('نسبة عمولة الإحالة الافتراضية (%)', 'Default Referral Commission Rate (%)') }}</label>
                    <div class="relative">
                        <input type="number" step="0.01" min="0" max="100" name="commission_rate" value="{{ old('commission_rate') }}"
                               placeholder="0.00"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('commission_rate') border-red-500 @enderror">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 text-sm">%</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ $tr('تُعبَّأ تلقائيًا عند اختيار هذا الموظف كمُحيل في أي عقار', 'Auto-filled when this employee is selected as referral on any property') }}</p>
                    @error('commission_rate')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الدور', 'Role') }} <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                        <option value="employee" {{ old('role')=='employee'?'selected':'' }}>{{ $tr('موظف', 'Employee') }}</option>
                        <option value="accountant" {{ old('role')=='accountant'?'selected':'' }}>{{ $tr('محاسب', 'Accountant') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('كلمة المرور', 'Password') }} <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition">{{ $tr('حفظ الموظف', 'Save Employee') }}</button>
                    <a href="{{ route('manager.employees.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg font-medium transition">{{ $tr('إلغاء', 'Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
