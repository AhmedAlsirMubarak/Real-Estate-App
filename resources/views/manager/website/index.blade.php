<x-app-layout>
@php
    $isAr = app()->getLocale() === 'ar';
    $tr   = fn(string $ar, string $en) => $isAr ? $ar : $en;
@endphp

<div class="max-w-5xl mx-auto py-8 px-4">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $tr('محتوى الموقع', 'Website Content') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $tr('إدارة جميع محتويات وصور الموقع الإلكتروني', 'Manage all website content and images') }}</p>
        </div>
        <a href="{{ route('home') }}" target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-navy text-white text-sm font-medium hover:bg-navy-mid transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
            </svg>
            {{ $tr('معاينة الموقع', 'Preview Website') }}
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-green-800 text-sm flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-green-600 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="grid sm:grid-cols-2 gap-5">
        @foreach($pages as $pageKey => $page)
        <a href="{{ route('manager.website.page', $pageKey) }}"
           class="group bg-white rounded-2xl border border-gray-200 p-6 hover:border-navy hover:shadow-md transition-all">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-navy/8 group-hover:bg-navy transition-colors flex-shrink-0">
                    @if($page['icon'] === 'home')
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-navy group-hover:text-white transition-colors">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                    </svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-navy group-hover:text-white transition-colors">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/>
                    </svg>
                    @endif
                </div>
                <div class="flex-1">
                    <h2 class="text-base font-bold text-gray-900 group-hover:text-navy transition-colors">{{ $tr($page['label_ar'], $page['label_en']) }}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $tr($page['label_en'], $page['label_ar']) }}</p>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                            {{ $sections[$pageKey] ?? 0 }} {{ $tr('أقسام', 'Sections') }}
                        </span>
                        <span class="text-xs text-navy font-medium group-hover:underline">{{ $tr('تعديل المحتوى ←', '→ Edit Content') }}</span>
                    </div>
                </div>
            </div>
        </a>
        @endforeach

        {{-- Latest News card --}}
        @php $newsCount = \App\Models\NewsArticle::count(); @endphp
        <a href="{{ route('manager.news.index') }}"
           class="group bg-white rounded-2xl border border-gray-200 p-6 hover:border-navy hover:shadow-md transition-all">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-navy/8 group-hover:bg-navy transition-colors flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-navy group-hover:text-white transition-colors">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-base font-bold text-gray-900 group-hover:text-navy transition-colors">{{ $tr('آخر الأخبار', 'Latest News') }}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $tr('Latest News', 'آخر الأخبار') }}</p>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                            {{ $newsCount }} {{ $tr('مقال', 'Articles') }}
                        </span>
                        <span class="text-xs text-navy font-medium group-hover:underline">{{ $tr('إدارة الأخبار ←', '→ Manage News') }}</span>
                    </div>
                </div>
            </div>
        </a>
    </div>

</div>
</x-app-layout>
