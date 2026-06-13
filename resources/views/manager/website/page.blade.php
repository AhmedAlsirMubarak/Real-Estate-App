<x-app-layout>
@php
    $isAr  = app()->getLocale() === 'ar';
    $tr    = fn(string $ar, string $en) => $isAr ? $ar : $en;
    $label = fn(array $meta) => $isAr ? ($meta['label_ar'] ?? '') : ($meta['label_en'] ?? $meta['label_ar'] ?? '');
    $pageLabel = $isAr ? $pageInfo['label_ar'] : ($pageInfo['label_en'] ?? $pageInfo['label_ar']);
    $title = $pageLabel;
@endphp

<div class="max-w-5xl mx-auto py-8 px-4">

    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
        <a href="{{ route('manager.website.index') }}" class="hover:text-navy transition">{{ $tr('محتوى الموقع', 'Website Content') }}</a>
        <span>/</span>
        <span class="text-gray-700 font-medium">{{ $pageLabel }}</span>
    </nav>

    <h1 class="text-2xl font-bold text-gray-900 mb-8">{{ $pageLabel }}</h1>

    @if(session('success'))
    <div class="mb-6 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    <div class="space-y-4">
        @forelse($sections as $section)
        @php $meta = $sectionMeta[$section->key] ?? []; @endphp
        <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:border-gray-300 transition">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-navy/8 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-navy">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 text-sm">{{ $label($meta) ?: $section->key }}</h3>
                        <div class="flex items-center gap-3 mt-1">
                            @php $sectionTitle = $isAr ? $section->title_ar : ($section->title_en ?: $section->title_ar); @endphp
                            @if($sectionTitle)
                            <span class="text-xs text-gray-400">{{ Str::limit($sectionTitle, 40) }}</span>
                            @endif
                            @if($meta['has_items'] ?? false)
                            <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">{{ $section->items_count }} {{ $tr('عنصر', 'Items') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <a href="{{ route('manager.website.section.edit', [$page, $section->key]) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-navy text-white text-xs font-medium hover:bg-navy-mid transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                    </svg>
                    {{ $tr('تعديل', 'Edit') }}
                </a>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-gray-400">{{ $tr('لا توجد أقسام لهذه الصفحة', 'No sections found for this page') }}</div>
        @endforelse
    </div>

</div>
</x-app-layout>
