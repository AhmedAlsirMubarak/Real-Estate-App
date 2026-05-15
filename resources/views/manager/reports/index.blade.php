<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $displayName = function (?string $name, string $fallback = 'User') use ($isAr) {
            if ($name === null || $name === '') return $fallback;
            if ($isAr || ! preg_match('/\p{Arabic}/u', $name)) return $name;
            return $fallback;
        };
    @endphp
    <x-slot name="title">{{ $tr('التقارير', 'Reports') }}</x-slot>

    <div class="mb-5">
        <h2 class="text-xl font-bold text-gray-800">{{ $tr('تقارير العقارات', 'Property Reports') }}</h2>
        <p class="text-sm text-gray-500 mt-0.5">{{ $tr('اختر عقاراً لعرض تقرير مفصّل عن إيراداته، مصروفاته، صيانته', 'Choose a property to view a detailed report for income, expenses, and maintenance') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($properties as $property)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-bold text-gray-800 text-sm">{{ $property->name }}</h3>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-indigo-50 text-indigo-700">{{ $property->typeLabel() }}</span>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap mt-1">
                        <span class="text-xs text-gray-500">{{ $property->code }}</span>
                        <span class="text-gray-300 text-xs">·</span>
                        <span class="text-xs text-gray-500">{{ $property->purposeLabel() }}</span>
                        <span class="text-gray-300 text-xs">·</span>
                        <span class="text-xs text-gray-500">{{ $property->owner ? $displayName($property->owner->user->name ?? null, $tr('المالك', 'Owner')) : $tr('الشركة', 'Company') }}</span>
                    </div>
                    <a href="{{ route('manager.reports.property', $property) }}"
                       class="inline-block mt-3 bg-blue-900 hover:bg-blue-800 text-white px-3 py-1.5 rounded-lg text-xs font-medium">
                        {{ $tr('عرض التقرير', 'View Report') }}
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12 text-gray-400">{{ $tr('لا توجد عقارات', 'No properties found') }}</div>
        @endforelse
    </div>
</x-app-layout>
