<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
<title>{{ app()->getLocale() === 'ar' ? $article->title_ar : ($article->title_en ?: $article->title_ar) }} — ثروة</title>
@php
  $_isArLang   = app()->getLocale() === 'ar';
  $_fontFamily = $_isArLang ? "'Cairo'" : "'Sora'";
  $_lbPrevSide = $_isArLang ? 'right' : 'left';
  $_lbNextSide = $_isArLang ? 'left'  : 'right';
@endphp
@vite(['resources/css/app.css','resources/js/app.js'])
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--navy:#0f2444;--navy-mid:#1a3a6b;--gold:#c9a84c;--text:#1a2437;--muted:#64748b;--border:#e8ecf0;--off:#f5f7fa}
*{font-family:{{ $_fontFamily }},sans-serif}
body{background:var(--off);color:var(--text);overflow-x:clip}
.article-body{line-height:2;font-size:.97rem;color:var(--text)}
.article-body p{margin-bottom:1.2em}
.related-card{background:#fff;border:1px solid var(--border);border-radius:14px;overflow:hidden;transition:all .3s}
.related-card:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(15,36,68,.1);border-color:rgba(201,168,76,.4)}
.related-card:hover img{transform:scale(1.06)}
.related-card img{transition:transform .5s ease}

/* Lightbox */
#lightbox{position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:9999;display:none;align-items:center;justify-content:center}
#lightbox.open{display:flex}
#lightbox img{max-width:90vw;max-height:88vh;object-fit:contain;border-radius:8px}
#lb-prev,#lb-next{position:absolute;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.15);border:none;color:#fff;width:44px;height:44px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s}
#lb-prev:hover,#lb-next:hover{background:rgba(255,255,255,.3)}
#lb-prev{ {{ $_lbPrevSide }}:16px }
#lb-next{ {{ $_lbNextSide }}:16px }
#lb-close{position:absolute;top:16px;right:16px;background:rgba(255,255,255,.15);border:none;color:#fff;width:36px;height:36px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s}
#lb-close:hover{background:rgba(255,255,255,.3)}
#lb-counter{position:absolute;bottom:20px;left:50%;transform:translateX(-50%);color:rgba(255,255,255,.7);font-size:.8rem;font-weight:600}

/* Gallery grid */
.gallery-item{position:relative;overflow:hidden;border-radius:12px;cursor:pointer;transition:transform .3s,box-shadow .3s}
.gallery-item:hover{transform:translateY(-3px);box-shadow:0 12px 32px rgba(15,36,68,.18)}
.gallery-item img{width:100%;height:100%;object-fit:cover;transition:transform .5s}
.gallery-item:hover img{transform:scale(1.07)}
</style>
</head>
<body>

@include('_partials.public-nav')

@php
  $allImages = $article->images;
  $primaryImg = $allImages->firstWhere('is_primary', true) ?? $allImages->first();
  $sideImgs   = $primaryImg ? $allImages->where('id', '!=', $primaryImg->id)->take(2)->values() : collect();
  $extraCount = $allImages->count() - 1 - $sideImgs->count();
  $isAr = app()->getLocale() === 'ar';
@endphp

<div class="max-w-4xl mx-auto px-4 sm:px-6 py-10">

  {{-- ── Breadcrumb ── --}}
  <nav class="flex items-center gap-2 text-xs mb-6" style="color:var(--muted)">
    <a href="{{ route('home') }}" class="hover:text-navy transition">{{ $isAr ? 'الرئيسية' : 'Home' }}</a>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $isAr ? 'M15.75 19.5 8.25 12l7.5-7.5' : 'M8.25 4.5l7.5 7.5-7.5 7.5' }}"/></svg>
    <a href="{{ route('news.index') }}" class="hover:text-navy transition">{{ $isAr ? 'الأخبار' : 'News' }}</a>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $isAr ? 'M15.75 19.5 8.25 12l7.5-7.5' : 'M8.25 4.5l7.5 7.5-7.5 7.5' }}"/></svg>
    <span class="truncate max-w-xs">{{ Str::limit($isAr ? ($article->title_ar ?: $article->title_en) : ($article->title_en ?: $article->title_ar), 50) }}</span>
  </nav>

  {{-- ── Article header ── --}}
  <div class="mb-6">
    <div class="flex items-center gap-2 mb-3">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 flex-shrink-0" style="color:var(--gold)"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
      <span class="text-sm font-semibold" style="color:var(--muted)">
        {{ $article->published_at?->translatedFormat('j F Y') ?? $article->created_at->translatedFormat('j F Y') }}
      </span>
      @if($allImages->count() > 0)
      <span class="flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full ms-1" style="background:rgba(201,168,76,.12);color:var(--gold)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z"/></svg>
        {{ $allImages->count() }}
      </span>
      @endif
    </div>
    <h1 class="text-2xl sm:text-3xl font-black leading-snug mb-3" style="color:var(--navy)">
      {{ $isAr ? ($article->title_ar ?: $article->title_en) : ($article->title_en ?: $article->title_ar) }}
    </h1>
    @php $exc = $isAr ? ($article->excerpt_ar ?: $article->excerpt_en) : ($article->excerpt_en ?: $article->excerpt_ar); @endphp
    @if($exc)
    <p class="text-base leading-relaxed" style="color:var(--muted)">{{ $exc }}</p>
    @endif
  </div>


  {{-- ── Hero image gallery (top) ── --}}
  @if($allImages->isNotEmpty())
  <div class="rounded-2xl overflow-hidden mb-8 flex gap-1.5" style="height:400px">
    {{-- Main large image --}}
    <div class="relative overflow-hidden group cursor-pointer" style="{{ $sideImgs->isNotEmpty() ? 'flex:3' : 'flex:1' }}"
         onclick="openLightbox(0)">
      <img src="{{ $primaryImg->url() }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="{{ $article->title_ar }}">
      {{-- Hover overlay --}}
      <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 group-hover:opacity-100 transition"></div>
      <div class="absolute bottom-3 end-3 w-9 h-9 rounded-full bg-white/90 flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4" style="color:#0f2444"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
      </div>
    </div>

    {{-- Side images --}}
    @if($sideImgs->isNotEmpty())
    <div class="flex flex-col gap-1.5" style="flex:2">
      @foreach($sideImgs as $i => $sImg)
      <div class="relative overflow-hidden group cursor-pointer" style="flex:1;min-height:0"
           onclick="openLightbox({{ $i + 1 }})">
        <img src="{{ $sImg->url() }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="">
        @if($i === $sideImgs->count() - 1 && $extraCount > 0)
        <div class="absolute inset-0 flex flex-col items-center justify-center gap-1" style="background:rgba(15,36,68,.65)">
          <span class="text-white font-black text-3xl leading-none">+{{ $extraCount }}</span>
          <span class="text-white/80 text-xs font-semibold">{{ $isAr ? 'صورة أخرى' : 'more photos' }}</span>
        </div>
        @else
        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition" style="background:rgba(0,0,0,.2)">
          <div class="w-9 h-9 rounded-full bg-white/90 flex items-center justify-center shadow">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4" style="color:#0f2444"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
          </div>
        </div>
        @endif
      </div>
      @endforeach
    </div>
    @endif
  </div>

  @elseif($article->imageUrl())
  <div class="rounded-2xl overflow-hidden mb-8 cursor-pointer group" style="height:400px" onclick="openLightbox(0)">
    <img src="{{ $article->imageUrl() }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="{{ $article->title_ar }}">
  </div>
  @endif

  {{-- ── Article body ── --}}
  @php $body = $isAr ? ($article->body_ar ?: $article->body_en) : ($article->body_en ?: $article->body_ar); @endphp
  @if($body)
  <div class="bg-white rounded-2xl border p-6 sm:p-8 mb-8" style="border-color:var(--border)">
    <div class="article-body">{!! nl2br(e($body)) !!}</div>
  </div>
  @endif

  {{-- ── Full photo gallery (all images below article) ── --}}
  @if($allImages->count() > 1)
  <div class="mb-10">
    {{-- Gallery grid --}}
    @php
      $imgCount = $allImages->count();
      $gallCols = $imgCount === 1 ? 'grid-cols-1'
                : ($imgCount === 2 ? 'grid-cols-2'
                : ($imgCount === 3 ? 'grid-cols-3'
                : 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-4'));
    @endphp
    <div class="grid {{ $gallCols }} gap-3">
      @foreach($allImages as $idx => $img)
      <div class="gallery-item" style="height:220px" onclick="openLightbox({{ $idx }})">
        <img src="{{ $img->url() }}" alt="" loading="lazy">
      </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- ── Related articles ── --}}
  @if($related->isNotEmpty())
  <div class="mb-10">
    <h2 class="text-lg font-black mb-5" style="color:var(--navy)">
      {{ $isAr ? 'مقالات ذات صلة' : 'Related Articles' }}
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      @foreach($related as $rel)
      <a href="{{ route('news.show', $rel) }}" class="related-card block">
        <div class="overflow-hidden" style="height:150px">
          @if($rel->imageUrl())
          <img src="{{ $rel->imageUrl() }}" class="w-full h-full object-cover" alt="" loading="lazy">
          @else
          <div class="w-full h-full flex items-center justify-center" style="background:linear-gradient(135deg,#0f2444,#1a3a6b)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-10 h-10 opacity-20 text-white"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z"/></svg>
          </div>
          @endif
        </div>
        <div class="p-4">
          <p class="text-xs mb-1.5" style="color:var(--muted)">
            {{ $rel->published_at?->format('d M Y') }}
          </p>
          <p class="text-sm font-bold leading-snug" style="color:var(--navy)">
            {{ Str::limit($isAr ? ($rel->title_ar ?: $rel->title_en) : ($rel->title_en ?: $rel->title_ar), 65) }}
          </p>
        </div>
      </a>
      @endforeach
    </div>
  </div>
  @endif

</div>

<footer class="py-8 text-center text-xs border-t" style="border-color:var(--border);color:var(--muted)">
  © {{ date('Y') }} {{ $isAr ? 'شركة ثروة للتطوير العقاري — جميع الحقوق محفوظة' : 'Tharwa Real Estate — All rights reserved' }}
</footer>

{{-- ── Lightbox ── --}}
<div id="lightbox" onclick="if(event.target===this)closeLightbox()">
  <button id="lb-close" onclick="closeLightbox()">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
  </button>
  <button id="lb-prev" onclick="lbPrev()">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $isAr ? 'M8.25 4.5l7.5 7.5-7.5 7.5' : 'M15.75 19.5 8.25 12l7.5-7.5' }}"/></svg>
  </button>
  <img id="lb-img" src="" alt="">
  <button id="lb-next" onclick="lbNext()">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $isAr ? 'M15.75 19.5 8.25 12l7.5-7.5' : 'M8.25 4.5l7.5 7.5-7.5 7.5' }}"/></svg>
  </button>
  <span id="lb-counter"></span>
</div>

<script>
var _lbImages = [
  @foreach($allImages as $img)
  "{{ $img->url() }}"{{ !$loop->last ? ',' : '' }}
  @endforeach
  @if($allImages->isEmpty() && $article->imageUrl())
  "{{ $article->imageUrl() }}"
  @endif
];
var _lbIdx = 0;

function openLightbox(idx) {
  _lbIdx = idx;
  document.getElementById('lb-img').src = _lbImages[_lbIdx];
  document.getElementById('lb-counter').textContent = (_lbIdx + 1) + ' / ' + _lbImages.length;
  document.getElementById('lightbox').classList.add('open');
  document.getElementById('lb-prev').style.display = _lbImages.length > 1 ? 'flex' : 'none';
  document.getElementById('lb-next').style.display = _lbImages.length > 1 ? 'flex' : 'none';
  document.body.style.overflow = 'hidden';
}
function closeLightbox() {
  document.getElementById('lightbox').classList.remove('open');
  document.body.style.overflow = '';
}
function lbPrev() { _lbIdx = (_lbIdx - 1 + _lbImages.length) % _lbImages.length; openLightbox(_lbIdx); }
function lbNext() { _lbIdx = (_lbIdx + 1) % _lbImages.length; openLightbox(_lbIdx); }
document.addEventListener('keydown', function(e) {
  if (!document.getElementById('lightbox').classList.contains('open')) return;
  if (e.key === 'Escape') closeLightbox();
  if (e.key === 'ArrowLeft') {{ $isAr ? 'lbNext()' : 'lbPrev()' }};
  if (e.key === 'ArrowRight') {{ $isAr ? 'lbPrev()' : 'lbNext()' }};
});
</script>

</body>
</html>
