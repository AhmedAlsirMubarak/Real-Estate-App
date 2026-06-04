<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ app()->getLocale() === 'ar' ? 'آخر الأخبار — ثروة' : 'Latest News — Tharwa' }}</title>
@vite(['resources/css/app.css','resources/js/app.js'])
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--navy:#0f2444;--navy-mid:#1a3a6b;--gold:#c9a84c;--gold-light:#e8c96e;--text:#1a2437;--muted:#64748b;--border:#e8ecf0;--off:#f5f7fa}
*{font-family:{{ app()->getLocale() === 'ar' ? "'Cairo'" : "'Sora'" }},sans-serif}
body{background:var(--off);color:var(--text);overflow-x:hidden}
.news-card{background:#fff;border:1px solid var(--border);border-radius:16px;overflow:hidden;transition:all .3s}
.news-card:hover{transform:translateY(-5px);box-shadow:0 20px 50px rgba(15,36,68,.12);border-color:rgba(201,168,76,.4)}
.news-card:hover img{transform:scale(1.06)}
.news-card img{transition:transform .5s ease}
</style>
</head>
<body>

@include('_partials.public-nav')

{{-- Page hero --}}
<div style="background:linear-gradient(135deg,rgba(9,24,44,.92),rgba(24,62,108,.84)),url('https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=1800&q=80');background-size:cover;background-position:center">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-14 sm:py-18 text-center">
    <h1 class="text-3xl sm:text-4xl font-black text-white mb-2">
      {{ app()->getLocale() === 'ar' ? 'آخر الأخبار' : 'Latest News' }}
    </h1>
    <p class="text-white/60 text-sm">
      {{ app()->getLocale() === 'ar' ? 'ابقَ على اطلاع بآخر أخبار ومستجدات ثروة العقارية' : 'Stay updated with the latest news from Tharwa Real Estate' }}
    </p>
  </div>
</div>

{{-- Articles grid --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
  @if($articles->isEmpty())
  <div class="text-center py-20" style="color:var(--muted)">
    <p class="text-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'لا توجد أخبار بعد' : 'No news yet' }}</p>
  </div>
  @else
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($articles as $article)
    <a href="{{ route('news.show', $article) }}" class="news-card block">
      <div class="overflow-hidden" style="height:220px">
        @if($article->imageUrl())
        <img src="{{ $article->imageUrl() }}" class="w-full h-full object-cover" alt="{{ $article->title_ar }}" loading="lazy">
        @else
        <div class="w-full h-full flex items-center justify-center" style="background:linear-gradient(135deg,#0f2444,#1a3a6b)">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-12 h-12 opacity-20 text-white"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z"/></svg>
        </div>
        @endif
      </div>
      <div class="p-5">
        <div class="flex items-center gap-2 mb-3">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 flex-shrink-0" style="color:var(--gold)"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
          <span class="text-xs font-semibold" style="color:var(--muted)">
            {{ $article->published_at?->translatedFormat('j F Y') ?? $article->created_at->translatedFormat('j F Y') }}
          </span>
        </div>
        @php $isAr = app()->getLocale() === 'ar'; @endphp
        <h2 class="font-bold text-base leading-snug mb-2" style="color:var(--navy)">
          {{ $isAr ? ($article->title_ar ?: $article->title_en) : ($article->title_en ?: $article->title_ar) }}
        </h2>
        @php $exc = $isAr ? ($article->excerpt_ar ?: $article->excerpt_en) : ($article->excerpt_en ?: $article->excerpt_ar); @endphp
        @if($exc)
        <p class="text-sm leading-relaxed" style="color:var(--muted)">{{ Str::limit($exc, 120) }}</p>
        @endif
        <div class="flex items-center gap-1 mt-3 text-xs font-bold" style="color:var(--gold)">
          {{ app()->getLocale() === 'ar' ? 'اقرأ المزيد' : 'Read more' }}
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ app()->getLocale() === 'ar' ? 'M15.75 19.5 8.25 12l7.5-7.5' : 'M8.25 4.5l7.5 7.5-7.5 7.5' }}"/></svg>
        </div>
      </div>
    </a>
    @endforeach
  </div>
  @if($articles->hasPages())
  <div class="mt-10 flex justify-center">
    {{ $articles->links() }}
  </div>
  @endif
  @endif
</div>

<footer class="py-8 text-center text-xs border-t mt-12" style="border-color:var(--border);color:var(--muted)">
  © {{ date('Y') }} {{ app()->getLocale() === 'ar' ? 'شركة ثروة للعقارات — جميع الحقوق محفوظة' : 'Tharwa Real Estate — All rights reserved' }}
</footer>

</body>
</html>
