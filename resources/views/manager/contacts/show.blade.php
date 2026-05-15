<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $displayText = function (?string $value, string $fallback) use ($isAr) {
            if ($value === null || $value === '') {
                return $fallback;
            }

            if ($isAr || ! preg_match('/\p{Arabic}/u', $value)) {
                return $value;
            }

            return $fallback;
        };
    @endphp
    <x-slot name="title">{{ $tr('رسالة من', 'Message from') }} {{ $displayText($contact->name, $tr('مرسل', 'Sender')) }}</x-slot>
    <div class="py-4 max-w-2xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('manager.contacts.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('تفاصيل الرسالة', 'Message Details') }}</h2>
        </div>

        <div class="bg-white rounded-xl shadow p-6 space-y-5">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $displayText($contact->subject, $tr('بدون موضوع', 'No subject')) }}</h3>
                    <p class="text-gray-400 text-sm mt-1">{{ $contact->created_at->format('Y/m/d H:i') }}</p>
                </div>
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">{{ $tr('مقروءة', 'Read') }}</span>
            </div>

            <div class="border-t border-gray-100 pt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-400 mb-1">{{ $tr('الاسم', 'Name') }}</p>
                    <p class="font-semibold text-gray-800">{{ $displayText($contact->name, $tr('مرسل', 'Sender')) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">{{ $tr('البريد الإلكتروني', 'Email') }}</p>
                    <a href="mailto:{{ $contact->email }}" class="font-semibold text-blue-600 hover:underline">{{ $contact->email }}</a>
                </div>
                @if($contact->phone)
                <div>
                    <p class="text-xs text-gray-400 mb-1">{{ $tr('رقم الجوال', 'Phone') }}</p>
                    <a href="tel:{{ $contact->phone }}" class="font-semibold text-gray-800">{{ $contact->phone }}</a>
                </div>
                @endif
            </div>

            <div class="border-t border-gray-100 pt-5">
                <p class="text-xs text-gray-400 mb-3">{{ $tr('نص الرسالة', 'Message') }}</p>
                <div class="bg-gray-50 rounded-xl p-5 text-gray-700 leading-relaxed text-sm whitespace-pre-line">{{ $displayText($contact->message, $tr('لا يوجد محتوى قابل للعرض في اللغة الحالية', 'No displayable content in current language')) }}</div>
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-2">
                <a href="mailto:{{ $contact->email }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    {{ $tr('رد بالبريد', 'Reply by email') }}
                </a>
                <form method="POST" action="{{ route('manager.contacts.destroy', $contact) }}" onsubmit="return confirm('{{ $tr('هل أنت متأكد من الحذف؟', 'Are you sure you want to delete this message?') }}')">
                    @csrf @method('DELETE')
                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 px-5 py-2.5 rounded-xl text-sm font-medium transition">{{ $tr('حذف الرسالة', 'Delete message') }}</button>
                </form>
                <a href="{{ route('manager.contacts.index') }}" class="text-gray-500 hover:text-gray-700 text-sm px-4 py-2.5">{{ $tr('رجوع', 'Back') }}</a>
            </div>
        </div>
    </div>
</x-app-layout>
