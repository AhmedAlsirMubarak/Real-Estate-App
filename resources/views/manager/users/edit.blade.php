<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('تعديل مستخدم', 'Edit User') }}</x-slot>

    @php
        $roleLabels = [
            'manager' => $tr('مدير', 'Manager'),
            'employee' => $tr('موظف', 'Employee'),
            'accountant' => $tr('محاسب', 'Accountant'),
            'tenant' => $tr('مستأجر', 'Tenant'),
            'owner' => $tr('مالك', 'Owner'),
            'buyer' => $tr('مشتري', 'Buyer'),
        ];
        $currentRole = old('role', $user->roles->first()?->name);
    @endphp

    <div class="py-4 max-w-4xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('manager.users.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('تعديل', 'Edit') }}: {{ $user->name }}</h2>
        </div>

        <form method="POST" action="{{ route('manager.users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">{{ $tr('بيانات الحساب الأساسية', 'Basic Account Details') }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الاسم (عربي)', 'Name (Arabic)') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name_ar" value="{{ old('name_ar', ($user->name_ar ?: $user->getRawOriginal('name'))) }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('name_ar') border-red-500 @enderror">
                        @error('name_ar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الاسم (إنجليزي)', 'Name (English)') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name_en" value="{{ old('name_en', ($user->name_en ?: $user->getRawOriginal('name'))) }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('name_en') border-red-500 @enderror">
                        @error('name_en')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('البريد الإلكتروني', 'Email') }} <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الجوال', 'Phone') }}</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror">
                        @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الدور', 'Role') }} <span class="text-red-500">*</span></label>
                        <select id="role-selector" name="role" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('role') border-red-500 @enderror">
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ $currentRole === $role ? 'selected' : '' }}>
                                    {{ $roleLabels[$role] ?? $role }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div id="tenant-fields" class="bg-white rounded-xl shadow p-6 hidden">
                <h3 class="font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">{{ $tr('بيانات المستأجر', 'Tenant Details') }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('رقم الهوية الوطنية', 'National ID') }}</label>
                        <input type="text" name="tenant_national_id" value="{{ old('tenant_national_id', $user->tenant?->national_id) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('tenant_national_id') border-red-500 @enderror">
                        @error('tenant_national_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('جهة الاتصال في الطوارئ', 'Emergency Contact') }}</label>
                        <input type="text" name="tenant_emergency_contact" value="{{ old('tenant_emergency_contact', $user->tenant?->emergency_contact) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('tenant_emergency_contact') border-red-500 @enderror">
                        @error('tenant_emergency_contact')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div id="owner-fields" class="bg-white rounded-xl shadow p-6 hidden">
                <h3 class="font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">{{ $tr('بيانات المالك', 'Owner Details') }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('رقم الهوية', 'National ID') }}</label>
                        <input type="text" name="owner_national_id" value="{{ old('owner_national_id', $user->owner?->national_id) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('owner_national_id') border-red-500 @enderror">
                        @error('owner_national_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحساب البنكي', 'Bank Account') }}</label>
                        <input type="text" name="owner_bank_account" value="{{ old('owner_bank_account', $user->owner?->bank_account) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('owner_bank_account') border-red-500 @enderror">
                        @error('owner_bank_account')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('نسبة العمولة (%)', 'Commission Rate (%)') }}</label>
                        <input type="number" min="0" max="100" step="0.01" name="owner_commission_rate" value="{{ old('owner_commission_rate', $user->owner?->commission_rate ?? 10) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('owner_commission_rate') border-red-500 @enderror">
                        @error('owner_commission_rate')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('ملاحظات', 'Notes') }}</label>
                        <textarea name="owner_notes" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('owner_notes') border-red-500 @enderror">{{ old('owner_notes', $user->owner?->notes) }}</textarea>
                        @error('owner_notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div id="buyer-fields" class="bg-white rounded-xl shadow p-6 hidden">
                <h3 class="font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">{{ $tr('بيانات المشتري', 'Buyer Details') }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('رقم الهوية', 'National ID') }}</label>
                        <input type="text" name="buyer_national_id" value="{{ old('buyer_national_id', $user->buyer?->national_id) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('buyer_national_id') border-red-500 @enderror">
                        @error('buyer_national_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('العنوان', 'Address') }}</label>
                        <input type="text" name="buyer_address" value="{{ old('buyer_address', $user->buyer?->address) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('buyer_address') border-red-500 @enderror">
                        @error('buyer_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('ملاحظات', 'Notes') }}</label>
                        <textarea name="buyer_notes" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('buyer_notes') border-red-500 @enderror">{{ old('buyer_notes', $user->buyer?->notes) }}</textarea>
                        @error('buyer_notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">{{ $tr('تغيير كلمة المرور', 'Change Password') }}</h3>
                <p class="text-xs text-gray-500 mb-3">{{ $tr('اترك الحقول فارغة إذا لا تريد تغيير كلمة المرور.', 'Leave these fields blank if you do not want to change the password.') }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('كلمة المرور الجديدة', 'New Password') }}</label>
                        <input type="password" name="password"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                        @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('تأكيد كلمة المرور', 'Confirm Password') }}</label>
                        <input type="password" name="password_confirmation"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition">{{ $tr('حفظ التعديلات', 'Save Changes') }}</button>
                <a href="{{ route('manager.users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg font-medium transition">{{ $tr('إلغاء', 'Cancel') }}</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        const roleSelector = document.getElementById('role-selector');
        const tenantFields = document.getElementById('tenant-fields');
        const ownerFields = document.getElementById('owner-fields');
        const buyerFields = document.getElementById('buyer-fields');

        function toggleRoleFields() {
            const role = roleSelector.value;
            tenantFields.classList.toggle('hidden', role !== 'tenant');
            ownerFields.classList.toggle('hidden', role !== 'owner');
            buyerFields.classList.toggle('hidden', role !== 'buyer');
        }

        roleSelector.addEventListener('change', toggleRoleFields);
        toggleRoleFields();
    </script>
    @endpush
</x-app-layout>
