<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
        $contract = $tenant->activeContract;
    @endphp
    <x-slot name="title">{{ $tr('تعديل بيانات المستأجر', 'Edit Tenant') }}</x-slot>
    <div class="py-4 max-w-3xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('manager.tenants.show', $tenant) }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('تعديل', 'Edit') }}: {{ $tenant->user->name ?? '' }}</h2>
        </div>

        <form method="POST" action="{{ route('manager.tenants.update', $tenant) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            {{-- Tenant Details --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">{{ $tr('بيانات المستأجر', 'Tenant Details') }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الاسم (عربي)', 'Name (Arabic)') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name_ar" value="{{ old('name_ar', $tenant->user->name_ar ?: $tenant->user->name) }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('name_ar') border-red-500 @enderror">
                        @error('name_ar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الاسم (إنجليزي)', 'Name (English)') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name_en" value="{{ old('name_en', $tenant->user->name_en ?: $tenant->user->name) }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('name_en') border-red-500 @enderror">
                        @error('name_en')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('البريد الإلكتروني', 'Email') }} <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $tenant->user->email) }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('رقم الهاتف', 'Phone Number') }}</label>
                        <input type="text" name="phone" value="{{ old('phone', $tenant->user->phone) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('رقم الهوية الوطنية', 'National ID') }}</label>
                        <input type="text" name="national_id" value="{{ old('national_id', $tenant->national_id) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('جهة الاتصال في حالات الطوارئ', 'Emergency Contact') }}</label>
                        <input type="text" name="emergency_contact" value="{{ old('emergency_contact', $tenant->emergency_contact) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('كلمة المرور الجديدة', 'New Password') }}</label>
                        <input type="password" name="password"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                               placeholder="{{ $tr('اتركه فارغاً للإبقاء على كلمة المرور الحالية', 'Leave blank to keep current password') }}">
                        @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Contract Details --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-semibold text-gray-700 mb-1 pb-2 border-b border-gray-100">{{ $tr('بيانات العقد', 'Contract Details') }}</h3>
                <p class="text-xs text-gray-400 mb-4">{{ $tr('اترك الوحدة فارغة إذا لم تكن بحاجة لتغيير العقد', 'Leave unit empty if you do not want to change the contract') }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الوحدة', 'Unit') }}</label>
                        <select name="unit_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('unit_id') border-red-500 @enderror">
                            <option value="">{{ $tr('-- بدون تغيير / بدون وحدة --', '-- No change / No unit --') }}</option>
                            @foreach($availableUnits as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id', $contract?->unit_id) == $unit->id ? 'selected' : '' }}>
                                {{ $unit->property->name }} — {{ $tr('وحدة', 'Unit') }} {{ $unit->unit_number ?? '—' }} ({{ $unit->typeLabel() }})
                            </option>
                            @endforeach
                        </select>
                        @error('unit_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('تاريخ بدء العقد', 'Contract Start Date') }}</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $contract?->start_date?->format('Y-m-d')) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('start_date') border-red-500 @enderror">
                        @error('start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('تاريخ انتهاء العقد', 'Contract End Date') }}</label>
                        <input type="date" name="end_date" value="{{ old('end_date', $contract?->end_date?->format('Y-m-d')) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('end_date') border-red-500 @enderror">
                        @error('end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الإيجار الشهري', 'Monthly Rent') }} ({{ $currency }})</label>
                        <input type="number" name="monthly_rent" value="{{ old('monthly_rent', $contract?->monthly_rent) }}" min="0" step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('monthly_rent') border-red-500 @enderror">
                        @error('monthly_rent')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('مبلغ التأمين', 'Deposit') }} ({{ $currency }})</label>
                        <input type="number" name="deposit" value="{{ old('deposit', $contract?->deposit ?? 0) }}" min="0" step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                {{ $tr('رقم حساب الكهرباء', 'Electricity Account No.') }}
                            </span>
                        </label>
                        <input type="text" name="electricity_account_number" value="{{ old('electricity_account_number', $contract?->electricity_account_number) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c-5.33 4.55-8 8.48-8 11.8C4 17.78 7.58 22 12 22s8-4.22 8-8.2C20 10.48 17.33 6.55 12 2z"/></svg>
                                {{ $tr('رقم حساب الماء', 'Water Account No.') }}
                            </span>
                        </label>
                        <input type="text" name="water_account_number" value="{{ old('water_account_number', $contract?->water_account_number) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition">{{ $tr('حفظ التعديلات', 'Save Changes') }}</button>
                <a href="{{ route('manager.tenants.show', $tenant) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg font-medium transition">{{ $tr('إلغاء', 'Cancel') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>
