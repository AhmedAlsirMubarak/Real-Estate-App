<x-app-layout>
@php
    $isAr = app()->getLocale() === 'ar';
    $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
@endphp
<x-slot name="title">{{ $tr('تعديل بيانات العميل', 'Edit Customer') }}</x-slot>

<div class="max-w-2xl mx-auto py-4">
    <a href="{{ route('manager.customers.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        {{ $tr('رجوع إلى العملاء', 'Back to Customers') }}
    </a>

    <form method="POST" action="{{ route('manager.customers.update', $customer) }}"
          class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        @csrf @method('PATCH')
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-gray-800">{{ $customer->name }}</h2>
                @if($customer->mobile)
                <p class="text-xs text-gray-500 mt-0.5">{{ $customer->mobile }}</p>
                @endif
            </div>
            @php
            $editWaUrl = $customer->whatsappUrl($isAr ? 'مرحباً ' . $customer->name . '، لدينا عروض عقارية قد تناسبك.' : 'Hello ' . $customer->name . ', we have offers that may suit you.');
        @endphp
        @if($editWaUrl)
            <a href="{{ $editWaUrl }}"
               target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition flex-shrink-0">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.71.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.999-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                {{ $tr('إرسال واتساب', 'Send WhatsApp') }}
            </a>
            @endif
        </div>

        @include('manager.customers._form')

        <div class="flex gap-2 pt-2">
            <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                {{ $tr('حفظ التعديلات', 'Save Changes') }}
            </button>
            <a href="{{ route('manager.customers.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm transition">
                {{ $tr('إلغاء', 'Cancel') }}
            </a>
        </div>
    </form>
</div>
</x-app-layout>
