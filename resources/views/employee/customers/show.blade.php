<x-app-layout>
@php
    $isAr = app()->getLocale() === 'ar';
    $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    $locale = $isAr ? 'ar' : 'en';
    $waMsg = $tr('مرحباً ' . $customer->name . '، لدينا عروض عقارية قد تناسب متطلباتك.', 'Hello ' . $customer->name . ', we have property offers that may match your requirements.');
    $waUrl = $customer->whatsappUrl($waMsg);
@endphp
<x-slot name="title">{{ $customer->name }}</x-slot>

<div class="max-w-3xl mx-auto py-4">
    <a href="{{ route('employee.customers.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-5 inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        {{ $tr('رجوع إلى العملاء', 'Back to Customers') }}
    </a>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Header card --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $customer->name }}</h2>
                <span class="inline-block mt-2 text-xs font-semibold px-2.5 py-1 rounded-full {{ $customer->statusColor() }}">
                    {{ $customer->statusLabel($locale) }}
                </span>
            </div>
            <div class="flex items-center gap-2">
                @if($waUrl)
                <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold px-3 py-2 rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.71.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.999-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    {{ $tr('واتساب', 'WhatsApp') }}
                </a>
                @endif
                <a href="{{ route('employee.customers.edit', $customer) }}"
                   class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-3 py-2 rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    {{ $tr('تعديل', 'Edit') }}
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <div>
                <p class="text-xs text-gray-400 mb-1">{{ $tr('رقم الهاتف / واتساب', 'Mobile / WhatsApp') }}</p>
                <p class="text-sm font-medium text-gray-800">{{ $customer->mobile ?: '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">{{ $tr('البريد الإلكتروني', 'Email') }}</p>
                <p class="text-sm font-medium text-gray-800">{{ $customer->email ?: '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">{{ $tr('المنطقة / الموقع المطلوب', 'Desired Area / Location') }}</p>
                <p class="text-sm font-medium text-gray-800">{{ $customer->location ?: '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">{{ $tr('مصدر العميل', 'Customer Source') }}</p>
                @php $sourceLabel = $customer->source ? (\App\Models\Customer::$sources[$customer->source][$locale] ?? $customer->source) : null; @endphp
                @if($sourceLabel)
                <span class="inline-block bg-indigo-50 text-indigo-700 text-xs px-2.5 py-1 rounded-full font-medium">{{ $sourceLabel }}</span>
                @else
                <p class="text-sm font-medium text-gray-800">—</p>
                @endif
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">{{ $tr('تاريخ الإضافة', 'Added On') }}</p>
                <p class="text-sm font-medium text-gray-800">{{ $customer->created_at?->format('Y/m/d') ?: '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Requirements --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-5">
        <h3 class="text-sm font-bold text-gray-800 mb-4">{{ $tr('المتطلبات', 'Requirements') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-400 mb-1">{{ $tr('نوع العقار المطلوب', 'Property Type Needed') }}</p>
                <span class="inline-block bg-blue-50 text-blue-700 text-xs px-2.5 py-1 rounded-full font-medium">{{ $customer->typeLabel($locale) }}</span>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">{{ $tr('الغرض', 'Purpose') }}</p>
                <span class="inline-block bg-purple-50 text-purple-700 text-xs px-2.5 py-1 rounded-full font-medium">{{ $customer->purposeLabel($locale) }}</span>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">{{ $tr('عدد غرف النوم المطلوب', 'Required Bedrooms') }}</p>
                <p class="text-sm font-medium text-gray-800">{{ $customer->bedrooms ?: '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">{{ $tr('الميزانية', 'Budget') }}</p>
                <p class="text-sm font-medium text-gray-800">
                    @if($customer->min_budget || $customer->max_budget)
                        @if($customer->min_budget){{ number_format($customer->min_budget) }}@endif
                        @if($customer->min_budget && $customer->max_budget) — @endif
                        @if($customer->max_budget){{ number_format($customer->max_budget) }}@endif
                        <span class="text-gray-400">{{ $tr('ريال', 'OMR') }}</span>
                    @else —
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Notes history --}}
    @if($customer->notes)
    <div class="bg-amber-50/60 border border-amber-100 rounded-xl p-6 mb-5">
        <h3 class="text-sm font-bold text-gray-800 mb-2">{{ $tr('ملاحظات وردود', 'Notes & Replies') }}</h3>
        <p class="text-sm text-gray-600 whitespace-pre-line">{{ $customer->notes }}</p>
    </div>
    @endif

    {{-- Quick Reply --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            {{ $tr('رد سريع / تحديث الحالة', 'Quick Reply / Status Update') }}
        </h3>
        <form method="POST" action="{{ route('employee.customers.reply', $customer) }}" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('تحديث الحالة', 'Update Status') }}</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-300">
                    @foreach(\App\Models\Customer::$statuses as $val => $labels)
                    <option value="{{ $val }}" @selected($customer->status === $val)>{{ $labels[$locale] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('إضافة ملاحظة / رد (اختياري)', 'Add Note / Reply (optional)') }}</label>
                <textarea name="reply_note" rows="3"
                          placeholder="{{ $tr('اكتب ملاحظتك أو ردك على متطلبات العميل…', 'Write your note or reply to the customer requirements…') }}"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-300 resize-none"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition">
                    {{ $tr('حفظ', 'Save') }}
                </button>
                @if($waUrl)
                <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1.5 bg-green-500 hover:bg-green-600 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.71.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.999-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    {{ $tr('رد عبر واتساب', 'Reply via WhatsApp') }}
                </a>
                @endif
            </div>
        </form>
    </div>
</div>
</x-app-layout>
