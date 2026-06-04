<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr   = fn(string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('الاجتماعات', 'Meetings') }}</x-slot>

    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <h2 class="text-xl font-bold text-gray-800">{{ $tr('الاجتماعات', 'Meetings') }}</h2>
        <a href="{{ route('manager.meetings.create') }}" class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium">{{ $tr('جدولة اجتماع', 'Schedule Meeting') }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-right">{{ $tr('العنوان', 'Title') }}</th>
                    <th class="px-4 py-3 text-right">{{ $tr('العقار', 'Property') }}</th>
                    <th class="px-4 py-3 text-right">{{ $tr('موعد الاجتماع', 'Scheduled At') }}</th>
                    <th class="px-4 py-3 text-right">{{ $tr('الحالة', 'Status') }}</th>
                    <th class="px-4 py-3 text-right">{{ $tr('إجراءات', 'Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($meetings as $m)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $m->title }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $m->association->property->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs">{{ $m->scheduled_at->format('Y/m/d H:i') }}</td>
                    <td class="px-4 py-3 text-xs">{{ $m->statusLabel() }}</td>
                    <td class="px-4 py-3 text-xs">
                        <a href="{{ route('manager.meetings.show', $m) }}" class="text-blue-700 mx-1">{{ $tr('عرض التفاصيل', 'View Details') }}</a>
                        <a href="{{ route('manager.meetings.edit', $m) }}" class="text-gray-600 mx-1">{{ $tr('تعديل', 'Edit') }}</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-10 text-center text-gray-400">{{ $tr('لا توجد بيانات', 'No data available') }}</td></tr>
                @endforelse
            </tbody>
        </table></div>
        <div class="p-3 border-t border-gray-100">{{ $meetings->links() }}</div>
    </div>
</x-app-layout>
