<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
    @endphp
    <x-slot name="title">{{ $tr('إضافة مستأجر جديد', 'Add New Tenant') }}</x-slot>
    <div class="py-4 max-w-3xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('manager.tenants.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('إضافة مستأجر جديد', 'Add New Tenant') }}</h2>
        </div>
        <form method="POST" action="{{ route('manager.tenants.store') }}" class="space-y-6">
            @csrf
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">{{ $tr('بيانات المستأجر', 'Tenant Details') }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('رقم الهاتف', 'Phone Number') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('رقم الهوية الوطنية', 'National ID') }}</label>
                        <input type="text" name="national_id" value="{{ old('national_id') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('جهة الاتصال في حالات الطوارئ', 'Emergency Contact') }}</label>
                        <input type="text" name="emergency_contact" value="{{ old('emergency_contact') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('كلمة المرور', 'Password') }} <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">{{ $tr('بيانات العقد', 'Contract Details') }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الوحدة', 'Unit') }} <span class="text-red-500">*</span></label>
                        <select name="unit_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('unit_id') border-red-500 @enderror">
                            <option value="">{{ $tr('-- اختر الوحدة --', '-- Select Unit --') }}</option>
                            @foreach($availableUnits as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id')==$unit->id?'selected':'' }}>
                                {{ $unit->property->name }} — {{ $tr('وحدة', 'Unit') }} {{ $unit->unit_number ?? '—' }} ({{ $unit->typeLabel() }})
                            </option>
                            @endforeach
                        </select>
                        @error('unit_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('تاريخ بدء العقد', 'Contract Start Date') }} <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('تاريخ انتهاء العقد', 'Contract End Date') }} <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الإيجار الشهري', 'Monthly Rent') }} ({{ $currency }}) <span class="text-red-500">*</span></label>
                        <input type="number" name="monthly_rent" value="{{ old('monthly_rent') }}" min="0" step="0.01" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('مبلغ التأمين', 'Deposit') }} ({{ $currency }})</label>
                        <input type="number" name="deposit" value="{{ old('deposit', 0) }}" min="0" step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition">{{ $tr('حفظ المستأجر والعقد', 'Save Tenant and Contract') }}</button>
                <a href="{{ route('manager.tenants.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg font-medium transition">{{ $tr('إلغاء', 'Cancel') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>
