<x-app-layout>
@php
    $isAr = app()->getLocale() === 'ar';
    $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
@endphp
<x-slot name="title">{{ $tr('إضافة عميل', 'Add Customer') }}</x-slot>

<div class="max-w-3xl mx-auto py-4">
    <a href="{{ route('employee.customers.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-5 inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        {{ $tr('رجوع إلى العملاء', 'Back to Customers') }}
    </a>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-5">{{ $tr('إضافة عميل جديد', 'Add New Customer') }}</h2>

        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('employee.customers.store') }}" class="space-y-5">
            @csrf
            @include('manager.customers._form')
            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('employee.customers.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-medium transition">{{ $tr('إلغاء', 'Cancel') }}</a>
                <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition">{{ $tr('حفظ العميل', 'Save Customer') }}</button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
