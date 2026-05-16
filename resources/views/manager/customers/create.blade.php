<x-app-layout>
@php
    $isAr = app()->getLocale() === 'ar';
    $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
@endphp
<x-slot name="title">{{ $tr('إضافة عميل', 'Add Customer') }}</x-slot>

<div class="max-w-2xl mx-auto py-4">
    <a href="{{ route('manager.customers.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        {{ $tr('رجوع إلى العملاء', 'Back to Customers') }}
    </a>

    <form method="POST" action="{{ route('manager.customers.store') }}"
          class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        @csrf
        <h2 class="text-lg font-bold text-gray-800">{{ $tr('إضافة عميل جديد', 'Add New Customer') }}</h2>
        <p class="text-xs text-gray-500">{{ $tr('سجّل متطلبات العميل الباحث عن عقار', 'Record the requirements of a property seeker') }}</p>

        @include('manager.customers._form')

        <div class="flex gap-2 pt-2">
            <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                {{ $tr('حفظ', 'Save') }}
            </button>
            <a href="{{ route('manager.customers.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm transition">
                {{ $tr('إلغاء', 'Cancel') }}
            </a>
        </div>
    </form>
</div>
</x-app-layout>
