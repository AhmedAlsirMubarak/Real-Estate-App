{{-- Shared public navbar — included on all public pages --}}
@php
use App\Models\WebsiteSection;
$_navSection  = WebsiteSection::where('page','home')->where('key','contact')->with(['activeItems'])->first();
$_navPhone    = $_navSection?->activeItems->firstWhere('icon','phone')?->body_ar ?? '';
$_navEmail    = $_navSection?->activeItems->firstWhere('icon','email')?->body_ar ?? '';
$_navFooter   = WebsiteSection::where('page','global')->where('key','footer')->first();
$_navSocials  = $_navFooter?->extra ?? [];
$_navWaClean  = preg_replace('/\D/','',$_navPhone);
$_waRaw       = $_navSocials['whatsapp'] ?? '';
$_waDigits    = preg_replace('/\D/', '', $_waRaw);
$_navWaHref   = $_waDigits
    ? 'https://api.whatsapp.com/send?phone=' . $_waDigits
    : ($_navWaClean ? 'https://api.whatsapp.com/send?phone=' . $_navWaClean : null);
$_isHome      = request()->routeIs('home');
$_isAr        = app()->getLocale() === 'ar';
$_navLinks    = [
  [route('home'),                  'الرئيسية',   'Home'],
  [route('properties.index'),      'العقارات',   'Properties'],
  [route('news.index'),            'الأخبار',    'News'],
  [route('home').'#about',         'عن الشركة',  'About'],
  [route('home').'#contact',       'تواصل معنا', 'Contact'],
];
$_socialSvg = [
  'whatsapp'  => '<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>',
  'instagram' => '<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/>',
  'twitter'   => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.259 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>',
  'facebook'  => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>',
  'linkedin'  => '<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>',
];
$_socialUrls = [
  'whatsapp'  => $_navWaHref,
  'instagram' => $_navSocials['instagram'] ?? null,
  'twitter'   => $_navSocials['twitter']   ?? null,
  'facebook'  => $_navSocials['facebook']  ?? null,
  'linkedin'  => $_navSocials['linkedin']  ?? null,
];
@endphp

<style>
/* ── PUBLIC HEADER ── */
#pub-header { position: sticky; top: 0; z-index: 100; }

/* ── TOPBAR: always gold ── */
#pub-topbar {
  background: #e7c25d;
  border-bottom: 1px solid rgba(0,0,0,.08);
}
#pub-topbar a { color: #000000; font-size: .75rem; font-weight: 600; text-decoration: none; transition: opacity .15s; }
#pub-topbar a:hover { opacity: .7; }
#pub-topbar .tb-icon { color: #0f2444; }
#pub-topbar .tb-social { color: rgba(15,36,68,.55); transition: color .15s; }
#pub-topbar .tb-social:hover { color: #0f2444; }

/* ── NAVBAR: solid navy by default; home-page JS overrides to transparent at top ── */
#pub-navbar {
  background: #0f2444;
  border-bottom: 1px solid rgba(255,255,255,.07);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  transition: background .35s, box-shadow .35s, border-color .35s;
}
#pub-navbar.is-scrolled {
  background: #0f2444;
  box-shadow: 0 4px 20px rgba(0,0,0,.25);
  border-bottom-color: transparent;
}

/* Nav links — always white */
.pub-nav-link {
  position: relative; display: inline-block;
  color: rgba(255,255,255,.85); font-size: .875rem; font-weight: 600;
  padding: .45rem .9rem; border-radius: .4rem;
  transition: color .18s, background .18s; text-decoration: none; white-space: nowrap;
}
.pub-nav-link:hover { color: #fff; background: rgba(255,255,255,.1); }
.pub-nav-link.active { color: #fff; }
.pub-nav-link::after {
  content: ''; position: absolute;
  bottom: -1px; left: .9rem; right: .9rem;
  height: 2px; background: #c9a84c; border-radius: 2px;
  transform: scaleX(0); transform-origin: center;
  transition: transform .22s ease;
}
.pub-nav-link:hover::after,
.pub-nav-link.active::after { transform: scaleX(1); }

/* Lang / hamburger in dark navbar */
#lang-btn {
  color: rgba(255,255,255,.8) !important;
  border-color: rgba(255,255,255,.25) !important;
  background: rgba(255,255,255,.08) !important;
}
#lang-btn:hover { color: #fff !important; background: rgba(255,255,255,.15) !important; }
#pub-hamburger { border-color: rgba(255,255,255,.25) !important; background: rgba(255,255,255,.08) !important; }
#pub-hamburger svg { color: #fff !important; }

/* Mobile menu */
#pub-mobile-menu {
  max-height: 0; overflow: hidden;
  transition: max-height .32s cubic-bezier(.4,0,.2,1);
  background: #0f2444; border-top: 1px solid rgba(255,255,255,.08);
}
#pub-mobile-menu.open { max-height: 520px; }
.pub-mobile-link {
  display: block; padding: .75rem 1rem; border-radius: .5rem;
  font-size: .9rem; font-weight: 600; color: rgba(255,255,255,.8);
  transition: background .15s, color .15s; text-decoration: none;
}
.pub-mobile-link:hover { background: rgba(255,255,255,.08); color: #fff; }
</style>

{{-- ── STICKY HEADER ── --}}
<header id="pub-header" class="{{ $_isHome ?? false ? 'hero-mode' : '' }}">

  {{-- Top info bar (desktop only) --}}
  <div id="pub-topbar" class="hidden sm:block">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-1.5 flex items-center justify-between gap-4">
      {{-- Contact info --}}
      <div class="flex items-center gap-5">
        @if($_navPhone)
        <a href="tel:{{ $_navPhone }}" class="flex items-center gap-1.5">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="tb-icon w-3.5 h-3.5 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25z"/></svg>
          <span dir="ltr" style="unicode-bidi:embed">{{ $_navPhone }}</span>
        </a>
        @endif
        @if($_navEmail)
        <a href="mailto:{{ $_navEmail }}" class="flex items-center gap-1.5">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="tb-icon w-3.5 h-3.5 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
          {{ $_navEmail }}
        </a>
        @endif
      </div>
      {{-- Socials --}}
      <div class="flex items-center" style="gap:14px">
        @foreach($_socialSvg as $_net => $_svg)
        @if($_socialUrls[$_net])
        <a href="{{ $_socialUrls[$_net] }}" target="_blank" rel="noopener" class="tb-social">
          <svg viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5">{!! $_svg !!}</svg>
        </a>
        @endif
        @endforeach
      </div>
    </div>
  </div>

  {{-- ── MAIN NAVBAR ── --}}
  <nav id="pub-navbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="flex items-center justify-between h-16 gap-4">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
          <img src="{{ asset('img/logo.png') }}" alt="Tharwa" class="h-11 w-auto">
        </a>

        {{-- Desktop nav links --}}
        <div class="hidden lg:flex items-center gap-0.5 flex-1 justify-center">
          @foreach($_navLinks as [$_url, $_ar, $_en])
          @php
            $_active = (request()->url() === $_url)
              || (request()->routeIs('news.*')       && $_en === 'News')
              || (request()->routeIs('properties.*') && $_en === 'Properties');
          @endphp
          <a href="{{ $_url }}" class="pub-nav-link {{ $_active ? 'active' : '' }}">
            <span data-ar @if(!$_isAr) class="hidden" @endif>{{ $_ar }}</span>
            <span data-en @if($_isAr) class="hidden" @endif>{{ $_en }}</span>
          </a>
          @endforeach
        </div>

        {{-- Right actions --}}
        <div class="flex items-center gap-2 flex-shrink-0">

          {{-- Language toggle — always visible in navbar (mobile + desktop) --}}
          @if($_isHome)
          <button onclick="toggleLang()" id="lang-btn"
            class="flex items-center gap-1.5 text-xs font-bold px-3 py-2 rounded-lg transition"
            style="color:rgba(255,255,255,.85);border:1.5px solid rgba(255,255,255,.25);background:rgba(255,255,255,.08)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253M3 12c0-.778.099-1.533.284-2.253m0 4.506A8.959 8.959 0 0 0 12 10.5"/></svg>
            <span id="lang-label">EN</span>
          </button>
          @else
          <a href="{{ route('locale.switch', $_isAr ? 'en' : 'ar') }}"
            class="flex items-center gap-1.5 text-xs font-bold px-3 py-2 rounded-lg transition"
            style="color:rgba(255,255,255,.85);border:1.5px solid rgba(255,255,255,.25);background:rgba(255,255,255,.08);text-decoration:none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253M3 12c0-.778.099-1.533.284-2.253m0 4.506A8.959 8.959 0 0 0 12 10.5"/></svg>
            {{ $_isAr ? 'EN' : 'عر' }}
          </a>
          @endif

          {{-- Login button --}}
          <a href="{{ route('login') }}" id="pub-login-btn"
            class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200 hover:opacity-90"
            style="background:#c9a84c; color:#0f2444; box-shadow:0 2px 8px rgba(201,168,76,.35);">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
            <span class="hidden sm:inline" data-ar @if(!$_isAr) style="display:none" @endif>دخول</span>
            <span class="hidden sm:inline" data-en @if($_isAr) style="display:none" @endif>Login</span>
          </a>

          {{-- Hamburger --}}
          <button id="pub-hamburger" onclick="pubToggleMobile()"
            class="lg:hidden flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition">
            <svg id="pub-ham-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path id="pub-ham-path" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>

        </div>
      </div>
    </div>

    {{-- Mobile menu (CSS max-height slide) --}}
    <div id="pub-mobile-menu">
      <div class="max-w-7xl mx-auto px-4 py-3 space-y-0.5">
        @foreach($_navLinks as [$_url, $_ar, $_en])
        <a href="{{ $_url }}" onclick="pubCloseMobile()" class="pub-mobile-link">
          <span data-ar @if(!$_isAr) class="hidden" @endif>{{ $_ar }}</span>
          <span data-en @if($_isAr) class="hidden" @endif>{{ $_en }}</span>
        </a>
        @endforeach
        <div class="pt-3 mt-2 border-t border-white/10">
          <a href="{{ route('login') }}" class="block py-2.5 rounded-xl text-sm text-center font-bold text-white"
             style="background: linear-gradient(135deg,#0f2444,#1a3a6b)">
            <span data-ar @if(!$_isAr) class="hidden" @endif>تسجيل الدخول</span>
            <span data-en @if($_isAr) class="hidden" @endif>Login</span>
          </a>
        </div>
      </div>
    </div>
  </nav>

</header>

<script>
(function () {
  var _navbar = document.getElementById('pub-navbar');

  /* Navbar solid shadow on scroll */
  function onScroll() {
    _navbar && _navbar.classList.toggle('is-scrolled', window.scrollY > 40);
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* Expose header height as CSS var so hero can size itself */
  function updateHeaderH() {
    var h = document.getElementById('pub-header');
    if (h) document.documentElement.style.setProperty('--pub-header-h', h.offsetHeight + 'px');
  }
  updateHeaderH();
  window.addEventListener('resize', updateHeaderH, { passive: true });

  /* Mobile menu */
  var _mOpen = false;
  window.pubToggleMobile = function () {
    _mOpen = !_mOpen;
    document.getElementById('pub-mobile-menu').classList.toggle('open', _mOpen);
    document.getElementById('pub-ham-path').setAttribute('d',
      _mOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16');
  };
  window.pubCloseMobile = function () {
    _mOpen = false;
    document.getElementById('pub-mobile-menu').classList.remove('open');
    document.getElementById('pub-ham-path').setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
  };
})();
</script>

{{-- WhatsApp float button --}}
@if($_navWaHref)
<a href="{{ $_navWaHref }}" target="_blank" rel="noopener" aria-label="WhatsApp" class="wa-float"
  style="position:fixed;bottom:28px;z-index:9999;width:56px;height:56px;background:#25d366;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 18px rgba(37,211,102,.45);transition:transform .2s,box-shadow .2s;text-decoration:none"
  onmouseover="this.style.transform='scale(1.1)';this.style.boxShadow='0 6px 26px rgba(37,211,102,.6)'"
  onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 4px 18px rgba(37,211,102,.45)'">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="28" height="28" fill="#fff">
    <path d="M16.003 2.667C8.639 2.667 2.667 8.638 2.667 16c0 2.35.638 4.553 1.748 6.45L2.667 29.333l7.09-1.727A13.266 13.266 0 0016.003 29.333C23.362 29.333 29.333 23.362 29.333 16S23.362 2.667 16.003 2.667zm0 2.4c5.922 0 10.93 5.007 10.93 10.933 0 5.927-5.008 10.933-10.93 10.933a10.9 10.9 0 01-5.558-1.52l-.398-.236-4.207 1.024 1.063-4.088-.262-.415A10.896 10.896 0 015.07 16c0-5.926 5.008-10.933 10.933-10.933zm-3.01 5.6c-.25 0-.657.094-.999.47-.343.375-1.313 1.282-1.313 3.126 0 1.843 1.344 3.624 1.531 3.874.188.25 2.595 4.12 6.376 5.611 3.78 1.49 3.78 1 4.467.938.688-.062 2.22-.907 2.532-1.782.313-.875.313-1.625.22-1.782-.094-.156-.344-.25-.72-.438-.375-.187-2.218-1.094-2.562-1.219-.344-.125-.594-.187-.844.188-.25.374-.968 1.218-1.187 1.468-.22.25-.438.281-.814.094-.375-.188-1.584-.584-3.016-1.86-1.115-.993-1.868-2.219-2.087-2.594-.22-.375-.024-.578.163-.765.169-.169.375-.438.563-.657.188-.218.25-.374.375-.624.125-.25.062-.469-.031-.657-.094-.187-.844-2.031-1.157-2.78-.3-.718-.61-.623-.844-.636-.218-.01-.469-.012-.718-.012z"/>
  </svg>
</a>
<style>
  .wa-float { left: 28px; }
  [dir="rtl"] .wa-float { left: auto; right: 28px; }
  @media(max-width:420px) {
    .wa-float { left: 12px; right: auto; width: 50px !important; height: 50px !important; }
    [dir="rtl"] .wa-float { right: 12px; left: auto; }
  }
</style>
@endif
