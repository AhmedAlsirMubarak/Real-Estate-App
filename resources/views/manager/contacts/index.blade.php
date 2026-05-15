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
    <x-slot name="title">{{ $tr('رسائل التواصل', 'Contact Messages') }}</x-slot>
    <div class="py-4">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold text-gray-800">{{ $tr('رسائل التواصل', 'Contact Messages') }}</h2>
                @if($unreadCount > 0)
                <span class="bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded-full animate-pulse">
                    {{ $unreadCount }} {{ $tr('جديد', 'new') }}
                </span>
                @endif
            </div>
        </div>

        @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium w-4"></th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('المرسل', 'Sender') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الموضوع', 'Subject') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('التاريخ', 'Date') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الحالة', 'Status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('إجراء', 'Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($messages as $msg)
                        <tr class="hover:bg-gray-50 {{ !$msg->is_read ? 'bg-blue-50/50' : '' }}">
                            <td class="px-4 py-3">
                                @if(!$msg->is_read)
                                <span class="w-2 h-2 bg-blue-500 rounded-full block"></span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-800">{{ $displayText($msg->name, $tr('مرسل', 'Sender')) }}</p>
                                <p class="text-gray-400 text-xs">{{ $msg->email }}</p>
                                @if($msg->phone)
                                <p class="text-gray-400 text-xs">{{ $msg->phone }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="{{ !$msg->is_read ? 'font-semibold text-gray-900' : 'text-gray-600' }}">{{ $displayText($msg->subject, $tr('بدون موضوع', 'No subject')) }}</p>
                                <p class="text-gray-400 text-xs mt-0.5 truncate max-w-xs">{{ $displayText(Str::limit($msg->message, 60), $tr('محتوى الرسالة', 'Message preview')) }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">
                                {{ $msg->created_at->format('Y/m/d') }}<br>
                                {{ $msg->created_at->format('H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                @if($msg->is_read)
                                <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded-full text-xs">{{ $tr('مقروءة', 'Read') }}</span>
                                @else
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">{{ $tr('جديدة', 'New') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('manager.contacts.show', $msg) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">{{ $tr('عرض', 'View') }}</a>
                                    <form method="POST" action="{{ route('manager.contacts.destroy', $msg) }}" onsubmit="return confirm('{{ $tr('حذف الرسالة؟', 'Delete this message?') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">{{ $tr('حذف', 'Delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                {{ $tr('لا توجد رسائل حتى الآن', 'No messages yet') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($messages->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $messages->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
