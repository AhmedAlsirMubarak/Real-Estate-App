<x-app-layout>
@php
    $isAr = app()->getLocale() === 'ar';
    $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
@endphp
<x-slot name="title">{{ $tr('تعديل المالك', 'Edit Owner') }}</x-slot>

<div class="max-w-3xl mx-auto py-4">
    <a href="{{ route('employee.owner-leads.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-5 inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        {{ $tr('رجوع إلى الملاك', 'Back to Owners') }}
    </a>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-5">{{ $tr('تعديل بيانات المالك', 'Edit Owner') }}: {{ $ownerLead->name }}</h2>

        <form method="POST" action="{{ route('employee.owner-leads.update', $ownerLead) }}">
            @csrf @method('PUT')
            @include('employee.owner-leads._form')
            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                    {{ $tr('حفظ التعديلات', 'Save Changes') }}
                </button>
                <a href="{{ route('employee.owner-leads.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm transition">
                    {{ $tr('إلغاء', 'Cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
