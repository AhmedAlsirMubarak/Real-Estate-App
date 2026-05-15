<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('تعديل بيانات المستأجر', 'Edit Tenant') }}</x-slot>
    <div class="py-4 max-w-2xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('manager.tenants.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('تعديل', 'Edit') }}: {{ $tenant->user->name ?? '' }}</h2>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <form method="POST" action="{{ route('manager.tenants.update', $tenant) }}" class="space-y-5">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الاسم (عربي)', 'Name (Arabic)') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name_ar" value="{{ old('name_ar', ($tenant->user->name_ar ?: $tenant->user->getRawOriginal('name'))) }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الاسم (إنجليزي)', 'Name (English)') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name_en" value="{{ old('name_en', ($tenant->user->name_en ?: $tenant->user->getRawOriginal('name'))) }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('جهة الاتصال للطوارئ', 'Emergency Contact') }}</label>
                        <input type="text" name="emergency_contact" value="{{ old('emergency_contact', $tenant->emergency_contact) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition">{{ $tr('حفظ التعديلات', 'Save Changes') }}</button>
                    <a href="{{ route('manager.tenants.show', $tenant) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg font-medium transition">{{ $tr('إلغاء', 'Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
