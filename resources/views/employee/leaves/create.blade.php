<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn(string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('طلب إجازة', 'Request Leave') }}</x-slot>

    <div class="py-4 max-w-xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('employee.leaves.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">← {{ $tr('سجل الإجازات', 'Leave History') }}</a>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('طلب إجازة جديدة', 'Request New Leave') }}</h2>
        </div>

        @if($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm space-y-1">
                @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('employee.leaves.store') }}" class="bg-white rounded-xl shadow p-6 space-y-5">
            @csrf
            <input type="hidden" name="_from" value="create">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('نوع الإجازة', 'Leave Type') }} <span class="text-red-500">*</span></label>
                <select name="type" required class="w-full border {{ $errors->has('type') ? 'border-red-500' : 'border-gray-300' }} rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">{{ $tr('-- اختر النوع --', '-- Select Type --') }}</option>
                    <option value="annual"    {{ old('type') === 'annual'    ? 'selected' : '' }}>{{ $tr('سنوية', 'Annual') }}</option>
                    <option value="sick"      {{ old('type') === 'sick'      ? 'selected' : '' }}>{{ $tr('مرضية', 'Sick') }}</option>
                    <option value="unpaid"    {{ old('type') === 'unpaid'    ? 'selected' : '' }}>{{ $tr('بدون راتب', 'Unpaid') }}</option>
                    <option value="emergency" {{ old('type') === 'emergency' ? 'selected' : '' }}>{{ $tr('طارئة', 'Emergency') }}</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('من', 'From') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', now()->toDateString()) }}" required
                           min="{{ now()->toDateString() }}"
                           class="w-full border {{ $errors->has('start_date') ? 'border-red-500' : 'border-gray-300' }} rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('إلى', 'To') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', now()->toDateString()) }}" required
                           min="{{ now()->toDateString() }}"
                           class="w-full border {{ $errors->has('end_date') ? 'border-red-500' : 'border-gray-300' }} rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('السبب', 'Reason') }}</label>
                <textarea name="reason" rows="3"
                          placeholder="{{ $tr('اكتب سبب طلب الإجازة (اختياري)...', 'Describe the reason for your leave (optional)...') }}"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 resize-none">{{ old('reason') }}</textarea>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition">
                    {{ $tr('إرسال الطلب', 'Submit Request') }}
                </button>
                <a href="{{ route('employee.leaves.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium transition">
                    {{ $tr('إلغاء', 'Cancel') }}
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
