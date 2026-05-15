<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('تقديم طلب صيانة', 'Submit Maintenance Request') }}</x-slot>
    <div class="py-4 max-w-xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('tenant.maintenance.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('تقديم طلب صيانة جديد', 'Submit New Maintenance Request') }}</h2>
        </div>

        @php $contract = auth()->user()->tenant->activeContract ?? null; @endphp
        @if($contract)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <p class="text-sm text-blue-700">
                <strong>{{ $tr('وحدتك الحالية:', 'Your current unit:') }}</strong>
                {{ $contract->unit->property->name ?? '' }} - {{ $tr('وحدة', 'Unit') }} {{ $contract->unit->unit_number ?? '' }}
            </p>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow p-6">
            <form method="POST" action="{{ route('tenant.maintenance.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('عنوان المشكلة', 'Issue Title') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="{{ $tr('مثال: تسرب في السقف...', 'Example: Roof leakage...') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-right focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('وصف المشكلة', 'Issue Description') }} <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required placeholder="{{ $tr('صف المشكلة بالتفصيل...', 'Describe the issue in detail...') }}"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-right focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('درجة الأولوية', 'Priority') }} <span class="text-red-500">*</span></label>
                    <select name="priority" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-right focus:ring-2 focus:ring-blue-500">
                        <option value="low" {{ old('priority')==='low'?'selected':'' }}>{{ $tr('منخفضة', 'Low') }}</option>
                        <option value="medium" {{ old('priority','medium')==='medium'?'selected':'' }}>{{ $tr('متوسطة', 'Medium') }}</option>
                        <option value="high" {{ old('priority')==='high'?'selected':'' }}>{{ $tr('عالية', 'High') }}</option>
                        <option value="urgent" {{ old('priority')==='urgent'?'selected':'' }}>{{ $tr('عاجلة', 'Urgent') }}</option>
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition">{{ $tr('إرسال الطلب', 'Submit Request') }}</button>
                    <a href="{{ route('tenant.maintenance.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg font-medium transition">{{ $tr('إلغاء', 'Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
