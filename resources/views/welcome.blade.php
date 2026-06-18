<!DOCTYPE html>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
@php
  $metaTitle = app()->getLocale() === 'ar'
      ? 'ثروة للعقارات | بيع وإيجار العقارات في عُمان'
      : 'Tharwa Real Estate | Buy & Rent Properties in Oman';
  $metaDescription = app()->getLocale() === 'ar'
      ? 'ثروة للعقارات — شركة رائدة في إدارة وبيع وإيجار العقارات في سلطنة عُمان. تصفح شققاً وفللاً ومزارع وشاليهات للبيع والإيجار بأفضل الأسعار.'
      : 'Tharwa Real Estate — a leading property management, sales, and rental company in Oman. Browse apartments, villas, farms, and chalets for sale and rent at the best prices.';
@endphp
@php $errors ??= new \Illuminate\Support\ViewErrorBag; @endphp
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<link rel="canonical" href="{{ url('/') }}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ url('/') }}">
<meta property="og:image" content="{{ asset('img/logo.png') }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ asset('img/logo.png') }}">
@vite(['resources/css/app.css','resources/js/app.js'])
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; }
html, body { margin: 0; padding: 0; overflow-x: clip; }

:root {
  --navy: #0f2444; --navy-mid: #1a3a6b; --navy-light: #1e4d8c;
  --gold: #c9a84c; --gold-light: #e8c96e;
  --off: #f5f7fa; --border: #e8ecf0;
  --text: #1a2437; --muted: #64748b;
  --pub-header-h: 104px; /* updated by JS */
}
[lang="ar"] * { font-family: 'Cairo', sans-serif; }
[lang="en"] * { font-family: 'Sora', sans-serif; }
html { scroll-behavior: smooth; background: #fff; }
body { background: #fff; color: var(--text); }

/* Fix header to float over hero on home page (no gap, no negative margin) */
#pub-header { position: fixed !important; width: 100%; }

/* ── HERO ── */
.hero-section {
  width: 100%;
  height: 56.25vw; /* 16:9 — matches standard video aspect ratio exactly */
  max-height: 92vh; /* cap on tall / portrait windows */
  min-height: 480px;
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: var(--navy);
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  overflow: hidden;
}
/* Dark gradient overlay (z-1) */
.hero-overlay {
  position: absolute; inset: 0; z-index: 1;
  background: linear-gradient(to bottom,
    rgba(9,24,44,.40) 0%,
    rgba(9,24,44,.15) 18%,
    rgba(9,24,44,.08) 50%,
    rgba(9,24,44,.12) 78%,
    rgba(9,24,44,.28) 100%
  );
}

/* Video/iframe container (z-0) */
.hero-video-bg { position: absolute; inset: 0; z-index: 0; overflow: hidden; background: var(--navy); }
.hero-video-bg video {
  position: absolute;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  /* anchor from top-center: scale clips the dark bottom edge, keeps bright horizon visible */
  transform: scale(1.15);
  transform-origin: top center;
  display: block;
  pointer-events: none;
}
/* Hide ALL native browser video controls & overlays */
.hero-video-bg video::-webkit-media-controls             { display: none !important; }
.hero-video-bg video::-webkit-media-controls-enclosure   { display: none !important; }
.hero-video-bg video::-webkit-media-controls-panel       { display: none !important; }
.hero-video-bg video::-webkit-media-controls-play-button { display: none !important; }
.hero-video-bg video::-webkit-media-controls-start-playback-button { display: none !important; }
.hero-video-bg video::-webkit-media-controls-overlay-play-button   { display: none !important; }
.hero-video-bg video::-webkit-media-controls-overlay-enclosure     { display: none !important; }
.hero-video-bg iframe {
  position: absolute; top: 50%; left: 50%;
  width: 100vw; height: 56.25vw; min-height: 100%; min-width: 177.78vh;
  transform: translate(-50%, -50%); border: 0; pointer-events: none;
}


/* Allow city tabs to wrap on small screens to avoid horizontal overflow */
@media(max-width:640px){
  .city-tab{white-space:normal;padding-inline:10px}
}



/* ── STATS ── */
.stat-card{text-align:center;padding:32px 20px}
.stat-num{font-size:2.5rem;font-weight:400;color:var(--navy);line-height:1}
.stat-num span{color:var(--gold)}

/* ── PROPERTY CARDS ── */
.prop-card{background:#fff;border:1px solid var(--border);border-radius:16px;overflow:hidden;transition:all .3s;display:flex;flex-direction:column}
.prop-card:hover{transform:translateY(-6px);box-shadow:0 20px 50px rgba(15,36,68,.14);border-color:rgba(201,168,76,.4)}
.prop-img{position:relative;height:220px;overflow:hidden;flex-shrink:0}
.prop-img img{width:100%;height:100%;object-fit:cover;transition:transform .6s}
.prop-card:hover .prop-img img{transform:scale(1.07)}
.prop-badge{position:absolute;top:14px;right:14px;padding:5px 13px;border-radius:999px;font-size:.72rem;font-weight:800;background:var(--gold);color:var(--navy)}
.prop-purpose{position:absolute;top:14px;left:14px;padding:5px 13px;border-radius:999px;font-size:.72rem;font-weight:700;background:rgba(15,36,68,.75);color:#fff;backdrop-filter:blur(4px)}
.prop-price{position:absolute;bottom:14px;right:14px;padding:6px 14px;border-radius:12px;font-size:.8rem;font-weight:900;background:rgba(255,255,255,.95);color:var(--navy);backdrop-filter:blur(8px)}
.prop-meta{display:flex;align-items:center;gap:14px;font-size:.78rem;color:var(--muted)}
.prop-meta-item{display:flex;align-items:center;gap:4px}
.btn-wa{display:flex;align-items:center;gap:6px;background:#25d366;color:#fff;font-size:.78rem;font-weight:700;padding:8px 14px;border-radius:10px;transition:all .2s}
.btn-wa:hover{background:#20b858;transform:translateY(-1px)}
.btn-call{display:flex;align-items:center;gap:6px;background:var(--off);color:var(--navy);font-size:.78rem;font-weight:700;padding:8px 14px;border-radius:10px;border:1px solid var(--border);transition:all .2s}
.btn-call:hover{background:var(--navy);color:#fff}

/* ── SERVICE CARDS ── */
.svc-card{background:#fff;border:1px solid var(--border);border-radius:16px;padding:28px 24px;transition:all .3s}
.svc-card:hover{transform:translateY(-5px);box-shadow:0 16px 40px rgba(15,36,68,.1);border-color:var(--gold)}
.svc-icon{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:18px;background:rgba(15,36,68,.07);transition:background .3s}
.svc-card:hover .svc-icon{background:var(--navy)}
.svc-card:hover .svc-icon svg{color:var(--gold)!important}

/* ── TYPE CARDS ── */
.type-card{position:relative;border-radius:20px;overflow:hidden;text-decoration:none;display:block;width:300px;height:380px;flex-shrink:0;cursor:pointer;box-shadow:0 4px 18px rgba(15,36,68,.10);transition:transform .3s,box-shadow .3s}
.type-card:hover{transform:translateY(-8px);box-shadow:0 20px 40px rgba(15,36,68,.22)}
.type-card-img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .4s}
.type-card:hover .type-card-img{transform:scale(1.07)}
.type-card-fallback{width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(145deg,#0f2444 0%,#1e4d8c 100%)}
.type-card-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(10,22,45,.85) 0%,rgba(10,22,45,.3) 55%,transparent 100%);transition:background .3s}
.type-card:hover .type-card-overlay{background:linear-gradient(to top,rgba(10,22,45,.92) 0%,rgba(10,22,45,.5) 60%,rgba(10,22,45,.1) 100%)}
.type-card-body{position:absolute;bottom:0;left:0;right:0;padding:20px 18px 18px;text-align:center}
.type-card-icon{position:absolute;top:18px;right:18px;width:48px;height:48px;border-radius:14px;background:rgba(255,255,255,.18);backdrop-filter:blur(8px);display:flex;align-items:center;justify-content:center}
.type-card-body{position:absolute;bottom:0;left:0;right:0;padding:28px 22px 24px;text-align:center}
.type-label{color:#fff;font-size:1.15rem;font-weight:800;letter-spacing:.01em;display:block;margin-bottom:8px}
.type-count{display:inline-block;background:var(--gold);color:#0f2444;font-size:.8rem;font-weight:700;padding:4px 14px;border-radius:99px}
.type-explore{color:rgba(255,255,255,.65);font-size:.8rem;font-weight:500}

/* ── TYPE SLIDER ── */
.type-card{
  flex-shrink:0;
  width:min(300px,80vw)!important;
  height:380px!important;
}
.type-dots{display:flex;justify-content:center;gap:7px;margin-top:20px}
.type-dot{width:8px;height:8px;border-radius:50%;background:var(--border);border:none;cursor:pointer;transition:all .25s;padding:0}
.type-dot.active{width:24px;border-radius:99px;background:var(--navy)}

/* ── COMMUNITY CARDS ── */
.community-card{position:relative;border-radius:18px;overflow:hidden;height:220px;cursor:pointer}
.community-card img{width:100%;height:100%;object-fit:cover;transition:transform .5s}
.community-card:hover img{transform:scale(1.08)}
.community-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(9,24,44,.8) 0%,rgba(9,24,44,.1) 60%)}
.community-info{position:absolute;bottom:0;left:0;right:0;padding:18px}

/* ── CTA SECTION ── */
.cta-section{background:var(--navy);position:relative;overflow:hidden}
.cta-section::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5z' fill='%23ffffff' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");opacity:.5}

/* ── TESTIMONIAL ── */
.testi-card{background:#fff;border:1px solid var(--border);border-radius:18px;padding:28px;transition:all .3s}
.testi-card:hover{box-shadow:0 16px 40px rgba(15,36,68,.1);border-color:rgba(201,168,76,.4)}

/* ── SECTION HEADING ── */
.section-tag{display:inline-flex;align-items:center;gap:8px;background:rgba(201,168,76,.12);border:1px solid rgba(201,168,76,.3);color:var(--gold);font-size:.75rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;padding:6px 16px;border-radius:999px;margin-bottom:12px}
.section-tag::before{content:'';width:6px;height:6px;border-radius:50%;background:var(--gold);display:inline-block}

/* ── FOOTER ── */
footer{background:var(--navy)}

/* ── ANIMATIONS ── */
.fade-up{opacity:0;transform:translateY(28px);transition:all .65s ease}
.fade-up.visible{opacity:1;transform:translateY(0)}

.btn-gold{background:var(--gold);color:var(--navy);font-weight:700;transition:all .25s}
.btn-gold:hover{background:var(--gold-light);transform:translateY(-2px);box-shadow:0 8px 20px rgba(201,168,76,.35)}
.btn-navy{background:var(--navy);color:#fff;font-weight:700;transition:all .25s}
.btn-navy:hover{background:var(--navy-mid)}
.input-field{border:1px solid var(--border);background:#fff;color:var(--text);transition:all .2s;outline:none}
.input-field:focus{border-color:var(--navy-mid);box-shadow:0 0 0 3px rgba(26,58,107,.1)}

@media(max-width:768px){
  /* On mobile: auto-height so no empty dark space below text content */
  .hero-section{
    height: auto !important;
    min-height: 390px !important;
    max-height: none !important;
  }
  .stat-num{font-size:2rem}
}

/* ── Property cards ── */
.mk-card{display:flex;flex-direction:column;box-sizing:border-box;border:1px solid #e8ecf0;border-radius:12px;overflow:hidden;background:#fff;transition:box-shadow .3s,border-color .3s}
.mk-gallery{display:flex;width:100%;height:176px;position:relative;overflow:hidden;flex-shrink:0}
.mk-side-imgs{display:none;flex-direction:column;gap:2px;flex:2;flex-shrink:0;margin-left:2px}
.mk-details{display:flex;flex-direction:column;flex:1;min-width:0;padding:14px 16px}
.mk-btns{display:flex;gap:6px;margin-top:auto;padding-top:6px}
@media(min-width:640px){
  .mk-card{flex-direction:row}
  .mk-gallery{width:50%;height:auto}
  .mk-side-imgs{display:flex}
  .mk-call-btn{display:flex !important}
}
@media(max-width:639px){
  .mk-call-btn{display:none !important}
}
</style>
</head>
<body>
@include('_partials.gtm-body')

@php
$s = fn(string $key) => $sections[$key] ?? null;

$iconSvg = [
  'building' =>'<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>',
  'key'     =>'<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z"/>',
  'users'   =>'<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0z"/>',
  'star'    =>'<path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/>',
  'wrench'  =>'<path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75"/>',
  'chart'   =>'<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5z"/>',
  'employee'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>',
  'portal'  =>'<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 8.25h3"/>',
  'check'   =>'<path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>',
  'apartment'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>',
  'villa'   =>'<path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>',
  'office'  =>'<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z"/>',
  'shop'    =>'<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72"/>',
  'studio'  =>'<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819"/>',
  'land'    =>'<path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836-.88 1.38-1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z"/>',
  'location'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0z"/>',
  'phone'   =>'<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25z"/>',
  'email'   =>'<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>',
  'clock'   =>'<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>',
  'default' =>'<path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>',
];
$ico = fn(string $k, string $cls='w-6 h-6') =>
  '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="'.$cls.'">'.($iconSvg[$k] ?? $iconSvg['default']).'</svg>';

$contactItems  = $s('contact')?->activeItems ?? collect();
$phone         = $contactItems->firstWhere('icon','phone')?->body_ar ?? '';
$email         = $contactItems->firstWhere('icon','email')?->body_ar ?? '';
$socials       = $footer?->extra ?? [];
$waPhoneClean  = preg_replace('/\D/', '', $phone);
$waRaw         = $socials['whatsapp'] ?? null;
$waDigits      = $waRaw ? preg_replace('/\D/', '', $waRaw) : null;
$waHref        = $waDigits
    ? 'https://api.whatsapp.com/send?phone=' . $waDigits
    : ($waPhoneClean ? 'https://api.whatsapp.com/send?phone=' . $waPhoneClean : null);

$heroBg = $s('hero')?->imageUrl()
  ?? 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1920&q=80';

$heroExtra    = (array)($s('hero')?->extra ?? []);
$heroBgType   = $heroExtra['hero_bg_type'] ?? 'image';
$heroVideoUrl = trim($heroExtra['hero_video_url'] ?? '');
$heroVideoFile= $heroExtra['hero_video_file'] ?? null;
$heroHasVideo = $heroBgType === 'video' && ($heroVideoUrl || $heroVideoFile);

// Detect YouTube
$ytId = null;
if ($heroVideoUrl && preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $heroVideoUrl, $m)) {
    $ytId = $m[1];
}
// Detect Vimeo
$vimeoId = null;
if (!$ytId && $heroVideoUrl && preg_match('/vimeo\.com\/(\d+)/', $heroVideoUrl, $m)) {
    $vimeoId = $m[1];
}
// Direct video file (uploaded or direct URL)
$directVideoSrc = null;
if ($heroHasVideo && !$ytId && !$vimeoId) {
    $directVideoSrc = $heroVideoFile ? asset('storage/' . $heroVideoFile) : $heroVideoUrl;
}
@endphp

@include('_partials.public-nav')

{{-- ══════════ HERO ══════════ --}}
<section id="home" class="hero-section" @if(!$heroHasVideo) style="background-image:url('{{ $heroBg }}')" @endif>

  {{-- Video background layer (z-0, below overlay) --}}
  @if($heroHasVideo)
  <div class="hero-video-bg">
    @if($ytId)
      <iframe src="https://www.youtube.com/embed/{{ $ytId }}?autoplay=1&mute=1&loop=1&playlist={{ $ytId }}&controls=0&showinfo=0&rel=0&modestbranding=1&playsinline=1&enablejsapi=0"
              allow="autoplay;encrypted-media" allowfullscreen></iframe>
    @elseif($vimeoId)
      <iframe src="https://player.vimeo.com/video/{{ $vimeoId }}?autoplay=1&muted=1&loop=1&background=1&byline=0&title=0"
              allow="autoplay;fullscreen"></iframe>
    @else
      <video autoplay muted loop playsinline preload="auto" disablepictureinpicture disableremoteplayback x-webkit-airplay="deny"
             controlslist="nodownload noremoteplayback nofullscreen"
             style="opacity:1;pointer-events:none" tabindex="-1" id="hero-vid">
        <source src="{{ $directVideoSrc }}" @if(str_ends_with(strtolower($directVideoSrc ?? ''), '.webm')) type="video/webm" @else type="video/mp4" @endif>
      </video>
    @endif
  </div>
  @endif

  {{-- Dark gradient overlay (z-1) --}}
  <div class="hero-overlay"></div>

  {{-- Content (z-10, above overlay) --}}
  <div class="relative z-10 flex-1 flex flex-col justify-center max-w-7xl mx-auto w-full px-4 sm:px-6 pt-20 sm:pt-28 pb-10 sm:pb-12">

    <div class="max-w-2xl fade-up">
      <div class="inline-flex items-center gap-2 mb-3 sm:mb-5 bg-white/10 backdrop-blur border border-white/20 text-white/80 text-xs font-semibold px-4 py-2 rounded-full">
        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
        <span data-ar>{{ $s('hero')?->extra['badge_ar'] ?? 'الرائد في إدارة العقارات' }}</span>
        <span data-en class="hidden">{{ $s('hero')?->extra['badge_en'] ?? 'Leading Real Estate Agency' }}</span>
      </div>

      @php
        $heroTitleAr = $s('hero')?->title_ar ?? 'اعثر على عقار أحلامك';
        $heroTitleEn = $s('hero')?->title_en ?? 'Find Your Dream Property';
        // Split at midpoint so the gold line gets a balanced chunk (not just one word)
        $arWords  = explode(' ', $heroTitleAr);
        $enWords  = explode(' ', $heroTitleEn);
        $arMid    = (int) ceil(count($arWords) / 2);
        $enMid    = (int) ceil(count($enWords) / 2);
        $heroAr1  = implode(' ', array_slice($arWords, 0, $arMid));
        $heroAr2  = implode(' ', array_slice($arWords, $arMid));
        $heroEn1  = implode(' ', array_slice($enWords, 0, $enMid));
        $heroEn2  = implode(' ', array_slice($enWords, $enMid));
      @endphp
      <h1 class="text-2xl sm:text-4xl lg:text-6xl font-black text-white mb-3 sm:mb-5" style="line-height:1.3">
        <span data-ar>{{ $heroAr1 }}<br><span style="color:var(--gold)">{{ $heroAr2 }}</span></span>
        <span data-en class="hidden">{{ $heroEn1 }}<br><span style="color:var(--gold)">{{ $heroEn2 }}</span></span>
      </h1>

      <p class="text-white/65 text-base sm:text-lg leading-relaxed max-w-xl">
        <span data-ar>{{ $s('hero')?->body_ar ?? '' }}</span>
        <span data-en class="hidden">{{ $s('hero')?->body_en ?? '' }}</span>
      </p>
    </div>
  </div>

</section>

{{-- ══════════ STATS ══════════ --}}
<section class="pt-12 sm:pt-16 pb-10 sm:pb-14 bg-white border-b border-gray-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-0 divide-x divide-gray-100 rtl:divide-x-reverse">
      @foreach($s('stats')?->activeItems ?? collect() as $stat)
      <div class="stat-card fade-up">
        <div class="stat-num">{!! preg_replace('/\d+/', '<span>$0</span>', e($stat->value)) !!}</div>
        <p class="text-sm text-gray-500 mt-2 font-medium">
          <span data-ar>{{ $stat->title_ar }}</span>
          <span data-en class="hidden">{{ $stat->title_en }}</span>
        </p>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ══════════ FEATURED PROPERTIES ══════════ --}}
@php
$waNum = $s('contact')?->activeItems->firstWhere('icon','phone')?->body_ar ?? '';
$phoneNum = $s('contact')?->activeItems->firstWhere('icon','phone')?->body_ar ?? '';
$firstCity = $cities->first() ?? null;
@endphp

<section id="properties" class="py-10 sm:py-20 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">

    {{-- Section header (CMS-controlled) --}}
    <div class="text-center mb-2 fade-up">
      @if($s('featured_properties')?->subtitle_ar)
      <div class="section-tag mx-auto mb-2" style="width:fit-content">
        <span data-ar>{{ $s('featured_properties')->subtitle_ar }}</span>
        <span data-en class="hidden">{{ $s('featured_properties')->subtitle_en ?? $s('featured_properties')->subtitle_ar }}</span>
      </div>
      @endif
      <h2 class="text-2xl sm:text-3xl font-black" style="color:var(--navy)">
        <span data-ar>{{ $s('featured_properties')?->title_ar ?? 'اعثر على أفضل العقارات المتاحة' }}</span>
        <span data-en class="hidden">{{ $s('featured_properties')?->title_en ?? 'Find The Best Available Properties' }}</span>
      </h2>
      <p class="mt-2 text-sm" style="color:var(--muted)">
        <span data-ar>{{ $s('featured_properties')?->body_ar ?? '' }}</span>
        <span data-en class="hidden">{{ $s('featured_properties')?->body_en ?? '' }}</span>
      </p>
    </div>

    {{-- Location tabs --}}
    @if($cities->count() > 0)
    <div class="flex items-center justify-center border-b mb-8 overflow-x-auto" style="border-color:var(--border)">
      @foreach($cities as $cityObj)
      @php $citySlugTab = Str::slug($cityObj->city_en ?: $cityObj->city); @endphp
      <button
        onclick="switchCity('{{ $citySlugTab }}')"
        id="city-tab-{{ $citySlugTab }}"
        class="city-tab flex items-center gap-1.5 px-5 py-4 text-xs font-bold uppercase tracking-widest whitespace-nowrap transition-all border-b-2 -mb-px"
        style="border-color:transparent;color:var(--muted)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0z"/></svg>
        <span data-ar>{{ $cityObj->city_ar ?: $cityObj->city }}</span>
        <span data-en class="hidden">{{ $cityObj->city_en ?: $cityObj->city }}</span>
      </button>
      @endforeach
      {{-- All tab --}}
      <button
        onclick="switchCity('all')"
        id="city-tab-all"
        class="city-tab flex items-center gap-1.5 px-5 py-4 text-xs font-bold uppercase tracking-widest whitespace-nowrap transition-all border-b-2 -mb-px"
        style="border-color:var(--gold);color:var(--navy)">
        <span data-ar>الكل</span><span data-en class="hidden">All</span>
      </button>
    </div>
    @endif

    {{-- Properties grid per city --}}
    @foreach($propertiesByCity as $city => $cityProps)
    @php
      $cityObj  = $cities->firstWhere('city', $city);
      $citySlug = Str::slug($cityObj?->city_en ?: $cityObj?->city ?: $city);
    @endphp
    <div id="city-grid-{{ $citySlug }}" class="city-grid hidden">
      @include('_partials.property-cards', [
        'properties'  => $cityProps,
        'waNum'       => $waNum,
        'phoneNum'    => $phoneNum,
        'initialShow' => 4,
        'gridId'      => 'featured-grid-'.$citySlug,
      ])
      @if($cityProps->count() > 4)
      <div class="text-center mt-8" id="feat-more-wrap-{{ $citySlug }}">
        <button onclick="loadMoreFeatured('{{ $citySlug }}')"
                class="inline-flex items-center gap-2 px-7 py-3 rounded-xl font-bold text-sm border-2 transition"
                style="border-color:var(--gold);color:var(--gold);background:transparent"
                onmouseover="this.style.background='var(--gold)';this.style.color='var(--navy)'"
                onmouseout="this.style.background='transparent';this.style.color='var(--gold)'">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
          <span data-ar>عرض المزيد</span><span data-en class="hidden">Load More</span>
          <span class="text-xs opacity-70">({{ $cityProps->count() - 4 }})</span>
        </button>
      </div>
      @endif
    </div>
    @endforeach

    {{-- All properties grid (hidden by default) --}}
    <div id="city-grid-all" class="city-grid">
      @include('_partials.property-cards', [
        'properties'  => $featured,
        'waNum'       => $waNum,
        'phoneNum'    => $phoneNum,
        'initialShow' => 4,
        'gridId'      => 'featured-grid-all',
      ])
      @if($featured->count() > 4)
      <div class="text-center mt-8" id="feat-more-wrap-all">
        <button onclick="loadMoreFeatured('all')"
                class="inline-flex items-center gap-2 px-7 py-3 rounded-xl font-bold text-sm border-2 transition"
                style="border-color:var(--gold);color:var(--gold);background:transparent"
                onmouseover="this.style.background='var(--gold)';this.style.color='var(--navy)'"
                onmouseout="this.style.background='transparent';this.style.color='var(--gold)'">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
          <span data-ar>عرض المزيد</span><span data-en class="hidden">Load More</span>
          <span class="text-xs opacity-70">({{ $featured->count() - 4 }})</span>
        </button>
      </div>
      @endif
    </div>

  </div>
</section>

{{-- ══════════ PROPERTY TYPES ══════════ --}}
@if($s('property_types') && $s('property_types')->activeItems->count())
<section id="types" class="py-10 sm:py-20 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="text-center mb-10 fade-up">
      <div class="section-tag mx-auto" style="width:fit-content">
        <span data-ar>{{ $s('property_types')?->subtitle_ar ?? 'أنواع العقارات' }}</span>
        <span data-en class="hidden">{{ $s('property_types')?->subtitle_en ?? 'Property Types' }}</span>
      </div>
      <h2 class="text-2xl sm:text-3xl font-black mt-2" style="color:var(--navy)">
        <span data-ar>{{ $s('property_types')?->title_ar ?? 'تصفح حسب النوع' }}</span>
        <span data-en class="hidden">{{ $s('property_types')?->title_en ?? 'Browse by Type' }}</span>
      </h2>
    </div>

    {{-- Slider — same structure as video slider: position:relative wrapper → overflow:hidden clip → flex row, arrows outside clip --}}
    <div style="position:relative;max-width:1240px;margin:0 auto">

      <div style="overflow:hidden;border-radius:16px">
      <div id="typeSlider" style="display:flex;gap:16px;direction:ltr;transition:transform .4s cubic-bezier(.25,.46,.45,.94);will-change:transform">
        @foreach($s('property_types')->activeItems as $type)
        @php $typeCount = $typeCounts[$type->value] ?? 0; @endphp
        <a href="{{ route('properties.index', ['type' => $type->value]) }}" class="type-card" style="flex-shrink:0">

          @if($type->imageUrl())
            <img src="{{ $type->imageUrl() }}" alt="{{ $type->title_ar }}" class="type-card-img">
          @else
            <div class="type-card-fallback">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                   stroke="rgba(255,255,255,.2)" style="width:110px;height:110px">
                {!! $iconSvg[$type->icon ?? 'building'] ?? $iconSvg['default'] !!}
              </svg>
            </div>
          @endif

          <div class="type-card-overlay"></div>

          <div class="type-card-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="#fff" style="width:22px;height:22px">
              {!! $iconSvg[$type->icon ?? 'building'] ?? $iconSvg['default'] !!}
            </svg>
          </div>

          <div class="type-card-body">
            <span class="type-label">
              <span data-ar>{{ $type->title_ar }}</span>
              <span data-en class="hidden">{{ $type->title_en }}</span>
            </span>
            @if($typeCount)
              <span class="type-count">
                {{ $typeCount }} {{ app()->getLocale()==='ar' ? 'عقار' : ($typeCount===1?'property':'properties') }}
              </span>
            @else
              <span class="type-explore">{{ app()->getLocale()==='ar' ? 'استكشف' : 'Explore' }}</span>
            @endif
          </div>

        </a>
        @endforeach
      </div>
      </div>{{-- /overflow:hidden clip --}}

      {{-- Arrows outside clip, absolutely positioned on the position:relative wrapper --}}
      <button id="typePrev" aria-label="Previous"
        style="position:absolute;top:50%;transform:translateY(-50%);inset-inline-start:-18px;z-index:10;width:44px;height:44px;border-radius:50%;background:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(0,0,0,.2);transition:all .2s;touch-action:manipulation">
        <svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="2.5" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>
      </button>
      <button id="typeNext" aria-label="Next"
        style="position:absolute;top:50%;transform:translateY(-50%);inset-inline-end:-18px;z-index:10;width:44px;height:44px;border-radius:50%;background:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(0,0,0,.2);transition:all .2s;touch-action:manipulation">
        <svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="2.5" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
      </button>

    </div>

    {{-- Dots --}}
    <div class="type-dots" id="typeDots"></div>


    <script>
    (function(){
      var slider  = document.getElementById('typeSlider');
      var dotsWrap= document.getElementById('typeDots');
      if (!slider) return;

      var cards   = Array.from(slider.querySelectorAll('.type-card'));
      var total   = cards.length;
      var current = 0;
      var timer   = null;

      function cardStep(){ return (cards[0] ? cards[0].offsetWidth : 300) + 16; }

      function goTo(idx){
        current = ((idx % total) + total) % total;
        slider.style.transform = 'translateX(-' + (current * cardStep()) + 'px)';
        updateDots();
      }

      function updateDots(){
        dotsWrap.querySelectorAll('.type-dot').forEach(function(d, i){
          d.classList.toggle('active', i === current);
        });
      }

      // Build dots
      cards.forEach(function(_, i){
        var d = document.createElement('button');
        d.className = 'type-dot' + (i === 0 ? ' active' : '');
        d.setAttribute('aria-label', 'Slide ' + (i+1));
        d.addEventListener('click', function(){ goTo(i); resetTimer(); });
        dotsWrap.appendChild(d);
      });

      // Arrow buttons — respond to both click and touch
      function addTap(el, fn){
        if (!el) return;
        el.addEventListener('click', fn);
        el.addEventListener('touchend', function(e){ e.preventDefault(); fn(); }, {passive:false});
      }
      addTap(document.getElementById('typePrev'), function(){ goTo(current - 1); resetTimer(); });
      addTap(document.getElementById('typeNext'), function(){ goTo(current + 1); resetTimer(); });

      // Touch swipe on the slider row
      var swipeX = 0;
      slider.addEventListener('touchstart', function(e){ swipeX = e.touches[0].clientX; clearInterval(timer); }, {passive:true});
      slider.addEventListener('touchend', function(e){
        var dx = swipeX - e.changedTouches[0].clientX;
        if(Math.abs(dx) > 40) goTo(dx > 0 ? current + 1 : current - 1);
        if(visible) resetTimer();
      }, {passive:true});

      // Auto-play — only while the slider is visible in the viewport
      function startTimer(){ timer = setInterval(function(){ goTo(current + 1); }, 3500); }
      function resetTimer(){ clearInterval(timer); startTimer(); }
      slider.addEventListener('mouseenter', function(){ clearInterval(timer); });
      slider.addEventListener('mouseleave', function(){ if(visible) startTimer(); });

      var visible = false;
      var io = new IntersectionObserver(function(entries){
        visible = entries[0].isIntersecting;
        if(visible) startTimer(); else clearInterval(timer);
      }, { threshold: 0.3 });
      io.observe(slider);

      window.addEventListener('resize', function(){ goTo(current); });
    })();
    </script>
  </div>
</section>
@endif





{{-- ══════════ ABOUT ══════════ --}}
<section id="about" class="py-16 sm:py-20 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
      {{-- Image side --}}
      <div class="relative fade-up order-2 lg:order-1">
        <div class="rounded-2xl overflow-hidden shadow-2xl" style="height:420px">
          @if($s('about')?->imageUrl())
          <img src="{{ $s('about')->imageUrl() }}" class="w-full h-full object-cover" alt="{{ app()->getLocale() === 'ar' ? 'عن ثروة للعقارات' : 'About Tharwa Real Estate' }}">
          @else
          <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1200&q=80" class="w-full h-full object-cover" alt="{{ app()->getLocale() === 'ar' ? 'عن ثروة للعقارات' : 'About Tharwa Real Estate' }}">
          @endif
        </div>
        {{-- floating badge --}}
        @if($s('about')?->extra['badge_ar'] ?? null)
        <div class="absolute -bottom-5 -right-5 sm:-bottom-6 sm:-right-6 bg-white rounded-2xl shadow-xl p-4 border border-gray-100">
          <p class="text-2xl font-black" style="color:var(--navy)">{{ $s('stats')?->activeItems->first()?->value ?? '50+' }}</p>
          <p class="text-xs font-semibold mt-0.5" style="color:var(--muted)">
            <span data-ar>{{ $s('about')->extra['badge_ar'] }}</span>
            <span data-en class="hidden">{{ $s('about')->extra['badge_en'] ?? $s('about')->extra['badge_ar'] }}</span>
          </p>
        </div>
        @endif
        {{-- decorative block --}}
        <div class="absolute -top-5 -left-5 sm:-top-6 sm:-left-6 w-24 h-24 rounded-2xl -z-10" style="background:var(--gold);opacity:.25"></div>
      </div>

      {{-- Text side --}}
      <div class="fade-up order-1 lg:order-2">
        <div class="section-tag">
          <span data-ar>{{ $s('about')?->subtitle_ar ?? 'من نحن' }}</span>
          <span data-en class="hidden">{{ $s('about')?->subtitle_en ?? 'About Us' }}</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-black mt-3 mb-5" style="color:var(--navy)">
          <span data-ar>{{ $s('about')?->title_ar ?? 'شركة ثروة للتطوير العقاري' }}</span>
          <span data-en class="hidden">{{ $s('about')?->title_en ?? 'Tharwa Real Estate' }}</span>
        </h2>
        <p class="text-sm sm:text-base leading-relaxed mb-6" style="color:var(--muted)">
          <span data-ar>{{ $s('about')?->body_ar ?? '' }}</span>
          <span data-en class="hidden">{{ $s('about')?->body_en ?? '' }}</span>
        </p>

        @if($s('about') && $s('about')->activeItems->count())
        <div class="space-y-3 mb-7">
          @foreach($s('about')->activeItems as $f)
          <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(201,168,76,.15)">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5" style="color:var(--gold)"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
            </div>
            <span class="text-sm font-medium" style="color:var(--text)">
              <span data-ar>{{ $f->title_ar }}</span><span data-en class="hidden">{{ $f->title_en }}</span>
            </span>
          </div>
          @endforeach
        </div>
        @endif

        <a href="{{ $s('about')?->button_url ?? '#contact' }}"
          class="btn-navy inline-flex items-center gap-2.5 px-7 py-3.5 rounded-xl text-sm">
          <span data-ar>{{ $s('about')?->button_text_ar ?? 'تواصل معنا' }}</span>
          <span data-en class="hidden">{{ $s('about')?->button_text_en ?? 'Contact Us' }}</span>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
        </a>
      </div>
    </div>
  </div>
</section>

{{-- ══════════ PARTNERS ══════════ --}}
@if($s('partners'))
<section id="partners" class="relative overflow-hidden" style="background:#f8fafc;padding:60px 0">

  {{-- Decorative top border --}}
  <div class="absolute top-0 left-0 right-0 h-px" style="background:linear-gradient(to right,transparent,var(--gold),transparent)"></div>
  {{-- Decorative bottom border --}}
  <div class="absolute bottom-0 left-0 right-0 h-px" style="background:linear-gradient(to right,transparent,var(--gold),transparent)"></div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6">

    {{-- Header --}}
    <div class="text-center mb-10 fade-up">
      <div class="inline-flex items-center gap-3 mb-3">
        <div class="h-px w-12" style="background:var(--gold)"></div>
        <p class="text-xs font-bold uppercase tracking-[.18em]" style="color:var(--gold)">
          <span data-ar>{{ $s('partners')->subtitle_ar ?: 'شركاؤنا وعملاؤنا' }}</span>
          <span data-en class="hidden">{{ $s('partners')->subtitle_en ?: 'Our Partners & Clients' }}</span>
        </p>
        <div class="h-px w-12" style="background:var(--gold)"></div>
      </div>
      <h2 class="text-2xl sm:text-3xl font-black" style="color:var(--navy)">
        <span data-ar>{{ $s('partners')->title_ar ?: 'شركاؤنا' }}</span>
        <span data-en class="hidden">{{ $s('partners')->title_en ?: 'Our Partners' }}</span>
      </h2>
    </div>

    @if($s('partners')->activeItems->count())
    <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6">
      @foreach($s('partners')->activeItems as $partner)
      <a href="{{ $partner->url ?? '#' }}"
         {{ ($partner->url && $partner->url !== '#') ? 'target="_blank" rel="noopener"' : '' }}
         title="{{ $partner->title_ar }}"
         class="partner-logo-card group fade-up">
        @if($partner->image)
        <img src="{{ $partner->imageUrl() }}" alt="{{ $partner->title_ar }}"
             class="h-16 sm:h-20 w-auto object-contain transition-all duration-300"
             style="filter:grayscale(20%) opacity(0.9)"
             onmouseover="this.style.filter='grayscale(0%) opacity(1)'"
             onmouseout="this.style.filter='grayscale(20%) opacity(0.9)'">
        @else
        <span class="text-sm font-bold" style="color:var(--navy)">{{ $partner->title_ar }}</span>
        @endif
      </a>
      @endforeach
    </div>
    @else
    <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6">
      @for($i = 0; $i < 5; $i++)
      <div class="partner-logo-card">
        <div class="h-10 w-28 rounded-lg animate-pulse" style="background:#e2e8f0"></div>
      </div>
      @endfor
    </div>
    <p class="text-center text-xs mt-6" style="color:var(--muted)">
      <span data-ar>أضف شركاءك من لوحة التحكم ← محتوى الموقع ← شركاؤنا</span>
      <span data-en class="hidden">Add partners from Dashboard → Website Content → Partners</span>
    </p>
    @endif

  </div>
</section>

<style>
.partner-logo-card {
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff;
  border: 1px solid #e8ecf0;
  border-radius: 16px;
  padding: 24px 36px;
  min-width: 180px;
  min-height: 110px;
  transition: all 0.3s ease;
  box-shadow: 0 1px 4px rgba(15,36,68,.05);
}
.partner-logo-card:hover {
  border-color: var(--gold);
  box-shadow: 0 6px 24px rgba(201,168,76,.18);
  transform: translateY(-3px);
}

@media(max-width:480px){
  .partner-logo-card{padding:12px 14px;min-width:120px}
}
</style>
@endif

{{-- ══════════ CTA BANNER ══════════ --}}
@if($s('cta'))
<section class="cta-section py-16 sm:py-20 relative">
  <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6">
    <div class="rounded-3xl overflow-hidden" style="background:linear-gradient(135deg,var(--navy-light),var(--navy));">
      <div class="grid lg:grid-cols-2 gap-0 items-center">
        <div class="p-10 sm:p-14">
          <p class="text-xs font-bold uppercase tracking-widest mb-3" style="color:var(--gold)">
            <span data-ar>عقارك يستحق الأفضل</span><span data-en class="hidden">Your Property Deserves the Best</span>
          </p>
          <h2 class="text-2xl sm:text-3xl font-black text-white mb-4 leading-snug">
            <span data-ar>{{ $s('cta')?->title_ar ?? 'هل تريد تأجير أو بيع عقارك؟' }}</span>
            <span data-en class="hidden">{{ $s('cta')?->title_en ?? 'Want to Sell or Rent Your Property?' }}</span>
          </h2>
          <p class="text-white/60 text-sm leading-relaxed mb-8">
            <span data-ar>{{ $s('cta')?->body_ar ?? '' }}</span>
            <span data-en class="hidden">{{ $s('cta')?->body_en ?? '' }}</span>
          </p>
          <a href="{{ $s('cta')?->button_url ?? '#contact' }}"
            class="btn-gold inline-flex items-center gap-2.5 px-7 py-3.5 rounded-xl text-sm shadow-lg">
            <span data-ar>{{ $s('cta')?->button_text_ar ?? 'تواصل معنا الآن' }}</span>
            <span data-en class="hidden">{{ $s('cta')?->button_text_en ?? 'Contact Us Now' }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
          </a>
        </div>
        @php $ctaImg = $s('cta')?->imageUrl() ?? 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80'; @endphp
        <div class="hidden lg:block h-full" style="min-height:280px;background:url('{{ $ctaImg }}') center/cover;opacity:.35;border-radius:0 1.5rem 1.5rem 0"></div>
      </div>
    </div>
  </div>
</section>
@endif

{{-- ══════════ VIDEO GALLERY ══════════ --}}
@php $videoSection = $s('videos'); $videoItems = $videoSection?->activeItems ?? collect(); @endphp
@if($videoSection && $videoSection->is_active && $videoItems->isNotEmpty())
<section id="videos" style="padding:72px 0;background:linear-gradient(160deg,#06122a 0%,#0f2444 60%,#0d1f3c 100%);overflow:hidden">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">

    {{-- Header --}}
    <div class="text-center mb-10 fade-up">
      <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(201,168,76,.12);border:1px solid rgba(201,168,76,.25);color:#c9a84c;font-size:.65rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;padding:5px 16px;border-radius:999px;margin-bottom:14px">
        <svg viewBox="0 0 24 24" fill="currentColor" style="width:12px;height:12px"><path d="M4.5 4.5a3 3 0 0 0-3 3v9a3 3 0 0 0 3 3h8.25a3 3 0 0 0 3-3v-9a3 3 0 0 0-3-3H4.5ZM19.94 18.75l-2.69-2.69V7.94l2.69-2.69c.944-.945 2.56-.276 2.56 1.06v11.38c0 1.336-1.616 2.005-2.56 1.06Z"/></svg>
        <span data-ar>{{ $videoSection->subtitle_ar ?: 'فيديوهاتنا' }}</span>
        <span data-en class="hidden">{{ $videoSection->subtitle_en ?: 'Our Videos' }}</span>
      </div>
      <h2 style="font-size:clamp(1.5rem,3.5vw,2.2rem);font-weight:900;color:#fff;margin-bottom:8px">
        <span data-ar>{{ $videoSection->title_ar }}</span>
        <span data-en class="hidden">{{ $videoSection->title_en }}</span>
      </h2>
      @if($videoSection->body_ar)
      <p style="font-size:.9rem;color:rgba(255,255,255,.55);max-width:520px;margin:0 auto">
        <span data-ar>{{ $videoSection->body_ar }}</span>
        <span data-en class="hidden">{{ $videoSection->body_en }}</span>
      </p>
      @endif
    </div>

    {{-- Slider wrapper --}}
    <div style="position:relative">

      {{-- Track --}}
      <div id="vid-outer" style="overflow:hidden;border-radius:16px">
        <div id="vid-track" style="display:flex;gap:20px;transition:transform .45s cubic-bezier(.4,0,.2,1);direction:ltr;will-change:transform">
          @foreach($videoItems as $vi)
          @php
            $vPath     = $vi->extra['video_path'] ?? null;
            $vThumb    = $vi->imageUrl();
            $vTitleAr  = $vi->title_ar ?: '';
            $vTitleEn  = $vi->title_en ?: '';
            $vDescAr   = $vi->body_ar ?: ($vi->subtitle_ar ?: '');
            $vDescEn   = $vi->body_en ?: ($vi->subtitle_en ?: $vDescAr);
          @endphp
          @if($vPath)
          <div class="vid-card"
               style="flex-shrink:0;border-radius:16px;overflow:hidden;background:#0a1628;cursor:pointer;position:relative"
               onclick="openVidModal('{{ asset('storage/'.$vPath) }}', '{{ addslashes($vTitleAr) }}', '{{ addslashes($vTitleEn) }}')">

            {{-- Thumbnail --}}
            <div style="position:relative;padding-top:56.25%;overflow:hidden;background:#0a1628">
              @if($vThumb)
              <img src="{{ $vThumb }}" alt="{{ $vTitleAr }}" loading="lazy"
                   style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .5s"
                   class="vid-thumb-img">
              @else
              <div style="position:absolute;inset:0;background:linear-gradient(135deg,#0f2444,#1a3a6b);display:flex;align-items:center;justify-content:center">
                <svg viewBox="0 0 24 24" fill="rgba(255,255,255,.06)" style="width:5rem;height:5rem"><path d="M4.5 4.5a3 3 0 0 0-3 3v9a3 3 0 0 0 3 3h8.25a3 3 0 0 0 3-3v-9a3 3 0 0 0-3-3H4.5Z"/></svg>
              </div>
              @endif

              {{-- Gradient overlay --}}
              <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(6,18,42,.85) 0%,transparent 55%)"></div>

              {{-- Play button --}}
              <div class="vid-play-btn" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center">
                <div style="width:64px;height:64px;border-radius:50%;background:rgba(255,255,255,.15);backdrop-filter:blur(8px);border:2px solid rgba(255,255,255,.3);display:flex;align-items:center;justify-content:center;transition:all .25s;box-shadow:0 8px 32px rgba(0,0,0,.4)">
                  <svg viewBox="0 0 24 24" fill="#fff" style="width:26px;height:26px;margin-inline-start:4px"><path d="M8 5v14l11-7z"/></svg>
                </div>
              </div>

              {{-- Duration badge --}}
              @if(!empty($vi->value))
              <div style="position:absolute;bottom:10px;inset-inline-end:12px;background:rgba(0,0,0,.65);color:#fff;font-size:.62rem;font-weight:700;padding:2px 8px;border-radius:5px">{{ $vi->value }}</div>
              @endif
            </div>

            {{-- Title row --}}
            <div style="padding:14px 16px 16px;background:#0d1d35">
              <h3 style="font-size:.9rem;font-weight:800;color:#fff;line-height:1.3;margin-bottom:4px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical">
                <span data-ar>{{ $vTitleAr }}</span>
                <span data-en class="hidden">{{ $vTitleEn }}</span>
              </h3>
              @if($vDescAr || $vDescEn)
              <p style="font-size:.75rem;color:rgba(255,255,255,.5);overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;margin-top:4px">
                <span data-ar>{{ $vDescAr }}</span>
                <span data-en class="hidden">{{ $vDescEn ?: $vDescAr }}</span>
              </p>
              @endif
            </div>
          </div>
          @endif
          @endforeach
        </div>
      </div>

      {{-- Navigation arrows --}}
      @if($videoItems->count() > 1)
      <button id="vid-prev" onclick="vidNav(-1)"
        style="position:absolute;top:35%;transform:translateY(-50%);inset-inline-start:-18px;z-index:10;width:44px;height:44px;border-radius:50%;background:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(0,0,0,.35);transition:all .2s"
        onmouseover="this.style.background='#c9a84c'" onmouseout="this.style.background='#fff'">
        <svg viewBox="0 0 24 24" fill="none" stroke="#0f2444" stroke-width="2.5" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18 6-6-6-6"/></svg>
      </button>
      <button id="vid-next" onclick="vidNav(1)"
        style="position:absolute;top:35%;transform:translateY(-50%);inset-inline-end:-18px;z-index:10;width:44px;height:44px;border-radius:50%;background:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(0,0,0,.35);transition:all .2s"
        onmouseover="this.style.background='#c9a84c'" onmouseout="this.style.background='#fff'">
        <svg viewBox="0 0 24 24" fill="none" stroke="#0f2444" stroke-width="2.5" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18-6-6 6-6"/></svg>
      </button>
      @endif

      {{-- Dot indicators --}}
      @if($videoItems->count() > 1)
      <div id="vid-dots" style="display:flex;justify-content:center;gap:6px;margin-top:24px">
        @foreach($videoItems->filter(fn($v)=>!empty($v->extra['video_path']))->values() as $i => $vi2)
        <button onclick="vidGoTo({{ $i }})" class="vid-dot"
          style="width:{{ $i===0 ? '24px' : '8px' }};height:8px;border-radius:999px;border:none;cursor:pointer;transition:all .3s;background:{{ $i===0 ? '#c9a84c' : 'rgba(255,255,255,.25)' }}"></button>
        @endforeach
      </div>
      @endif

    </div>
  </div>
</section>

{{-- Video Modal --}}
<div id="vid-modal" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,.92);align-items:center;justify-content:center;padding:20px" onclick="if(event.target===this)closeVidModal()">
  <div style="position:relative;width:100%;max-width:900px">
    <button onclick="closeVidModal()"
      style="position:absolute;top:-44px;inset-inline-end:0;width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;cursor:pointer;font-size:1.1rem;display:flex;align-items:center;justify-content:center">✕</button>
    <div id="vid-modal-title" style="color:#fff;font-size:.95rem;font-weight:700;margin-bottom:10px;text-align:start"></div>
    <video id="vid-modal-player" controls playsinline
      style="width:100%;border-radius:14px;background:#000;max-height:78vh;display:block;outline:none">
    </video>
  </div>
</div>

<style>
#vid-track .vid-card { width: 100%; }
#vid-track .vid-card:hover .vid-thumb-img { transform: scale(1.04); }
#vid-track .vid-card:hover .vid-play-btn > div { background: rgba(201,168,76,.25); border-color: #c9a84c; transform: scale(1.08); }
@media(min-width:640px) {
  #vid-track .vid-card { width: calc(50% - 10px) }
}
@media(min-width:1024px) {
  #vid-track .vid-card { width: calc(33.333% - 14px) }
}
</style>

<script>
(function(){
  var track   = document.getElementById('vid-track');
  var dots    = document.querySelectorAll('.vid-dot');
  var current = 0;
  var cards   = track ? track.querySelectorAll('.vid-card') : [];
  var total   = cards.length;

  function visibleCount(){
    return window.innerWidth >= 1024 ? 3 : window.innerWidth >= 640 ? 2 : 1;
  }
  function maxIndex(){
    return Math.max(0, total - visibleCount());
  }
  function updateLayout(){
    if(!track) return;
    if(total <= visibleCount()){
      track.style.justifyContent = 'center';
      track.style.transform = '';
      current = 0;
    } else {
      track.style.justifyContent = 'flex-start';
    }
  }
  function goTo(n){
    updateLayout();
    if(total <= visibleCount()) return;
    current = Math.max(0, Math.min(n, maxIndex()));
    if(!cards.length) return;
    var gap   = 20;
    var cardW = cards[0].offsetWidth;
    track.style.transform = 'translateX(-' + (current * (cardW + gap)) + 'px)';
    dots.forEach(function(d,i){
      d.style.width      = i === current ? '24px' : '8px';
      d.style.background = i === current ? '#c9a84c' : 'rgba(255,255,255,.25)';
    });
  }
  window.vidNav = function(dir){ goTo(current + dir); };
  window.vidGoTo = function(n){ goTo(n); };
  window.addEventListener('resize', function(){ goTo(current); }, { passive:true });
  updateLayout();

  /* Modal */
  var modal   = document.getElementById('vid-modal');
  var player  = document.getElementById('vid-modal-player');
  var mTitle  = document.getElementById('vid-modal-title');
  var _isAr   = document.documentElement.lang === 'ar';
  window.openVidModal = function(src, titleAr, titleEn){
    if(!modal || !player) return;
    player.src = src;
    mTitle.textContent = _isAr ? titleAr : (titleEn || titleAr);
    modal.style.display = 'flex';
    player.play().catch(function(){});
    document.body.style.overflow = 'hidden';
  };
  window.closeVidModal = function(){
    if(!modal || !player) return;
    player.pause();
    player.src = '';
    modal.style.display = 'none';
    document.body.style.overflow = '';
  };
  document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeVidModal(); });
})();
</script>
@endif

{{-- ══════════ LATEST NEWS ══════════ --}}
@unless($latestNews->isEmpty())
<section id="news" class="py-16 sm:py-20 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="text-center mb-10 fade-up">
      <div class="section-tag mx-auto" style="width:fit-content">
        <span data-ar>أخبار ثروة</span><span data-en class="hidden">Tharwa News</span>
      </div>
      <h2 class="text-2xl sm:text-3xl font-black mt-2" style="color:var(--navy)">
        <span data-ar>آخر الأخبار</span><span data-en class="hidden">Latest News</span>
      </h2>
      <p class="mt-2 text-sm" style="color:var(--muted)">
        <span data-ar>ابقَ على اطلاع بآخر مستجداتنا وأخبار السوق العقاري</span>
        <span data-en class="hidden">Stay updated with our latest news and real estate market updates</span>
      </p>
    </div>
    @php
      $newsCount = $latestNews->count();
      $newsCols  = $newsCount === 1 ? 'grid-cols-1 max-w-lg mx-auto'
                 : ($newsCount === 2 ? 'grid-cols-1 sm:grid-cols-2'
                 : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3');
      $newsImgH  = $newsCount <= 2 ? '280px' : '240px';
    @endphp
    <div class="grid {{ $newsCols }} gap-6">
      @foreach($latestNews as $article)
      <a href="{{ route('news.show', $article) }}" class="group block bg-white border rounded-2xl overflow-hidden fade-up"
         style="border-color:var(--border);transition:all .3s"
         onmouseover="this.style.transform='translateY(-5px)';this.style.boxShadow='0 20px 50px rgba(15,36,68,.12)';this.style.borderColor='rgba(201,168,76,.4)'"
         onmouseout="this.style.transform='';this.style.boxShadow='';this.style.borderColor='var(--border)'">
        <div class="overflow-hidden relative" style="height:{{ $newsImgH }}">
          @if($article->imageUrl())
          <img src="{{ $article->imageUrl() }}" loading="lazy" alt="{{ $article->title_ar }}"
               style="width:100%;height:100%;object-fit:cover;transition:transform .5s"
               class="group-hover:scale-105">
          @else
          <div style="width:100%;height:100%;background:linear-gradient(135deg,#0f2444,#1a3a6b);display:flex;align-items:center;justify-content:center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="width:3.5rem;height:3.5rem;opacity:.2;color:#fff"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z"/></svg>
          </div>
          @endif
          <div class="absolute bottom-3 start-3 flex items-center gap-1.5"
               style="background:rgba(15,36,68,.75);color:#fff;font-size:.7rem;font-weight:700;padding:5px 12px;border-radius:999px;backdrop-filter:blur(4px)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:12px;height:12px"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
            {{ $article->published_at?->format('d M Y') ?? $article->created_at->format('d M Y') }}
          </div>
        </div>
        <div class="p-5">
          <h3 style="font-weight:700;font-size:1rem;color:var(--navy);margin-bottom:6px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.5">
            <span data-ar>{{ $article->title_ar }}</span>
            <span data-en class="hidden">{{ $article->title_en ?: $article->title_ar }}</span>
          </h3>
          @if($article->excerpt_ar)
          <p style="font-size:.82rem;color:var(--muted);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.6;margin-bottom:10px">
            <span data-ar>{{ $article->excerpt_ar }}</span>
            <span data-en class="hidden">{{ $article->excerpt_en ?: $article->excerpt_ar }}</span>
          </p>
          @endif
          <div class="flex items-center gap-1 mt-3" style="font-size:.78rem;font-weight:700;color:var(--gold)">
            <span data-ar>اقرأ المزيد</span><span data-en class="hidden">Read more</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
          </div>
        </div>
      </a>
      @endforeach
    </div>
    <div class="text-center mt-10">
      <a href="{{ route('news.index') }}"
         class="inline-flex items-center gap-2 text-sm font-bold border-2 px-7 py-3 rounded-xl transition"
         style="border-color:var(--gold);color:var(--gold)"
         onmouseover="this.style.background='var(--gold)';this.style.color='var(--navy)'"
         onmouseout="this.style.background='';this.style.color='var(--gold)'">
        <span data-ar>عرض جميع الأخبار</span><span data-en class="hidden">View All News</span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
      </a>
    </div>
  </div>
</section>
@endunless

{{-- ══════════ TESTIMONIALS ══════════ --}}
@if($s('testimonials') && $s('testimonials')->activeItems->count())
<section id="testimonials" class="py-16 sm:py-20" style="background:var(--off)">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="text-center mb-10 fade-up">
      <div class="section-tag mx-auto" style="width:fit-content">
        <span data-ar>{{ $s('testimonials')?->subtitle_ar ?? 'آراء العملاء' }}</span>
        <span data-en class="hidden">{{ $s('testimonials')?->subtitle_en ?? 'Testimonials' }}</span>
      </div>
      <h2 class="text-2xl sm:text-3xl font-black mt-2" style="color:var(--navy)">
        <span data-ar>{{ $s('testimonials')?->title_ar ?? 'ماذا يقول عملاؤنا' }}</span>
        <span data-en class="hidden">{{ $s('testimonials')?->title_en ?? 'What Our Clients Say' }}</span>
      </h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      @foreach($s('testimonials')->activeItems as $t)
      <div class="testi-card fade-up">
        <div class="flex gap-1 mb-4">
          @for($i=0;$i<5;$i++)
          <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-yellow-400"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z"/></svg>
          @endfor
        </div>
        <p class="text-sm leading-relaxed mb-5" style="color:var(--muted)">
          <span data-ar>"{{ $t->body_ar }}"</span>
          <span data-en class="hidden">"{{ $t->body_en ?? $t->body_ar }}"</span>
        </p>
        <div class="flex items-center gap-3 pt-4 border-t" style="border-color:var(--border)">
          @if($t->image)
          <img src="{{ $t->imageUrl() }}" class="w-10 h-10 rounded-full object-cover border-2" style="border-color:var(--gold)" alt="{{ $t->title_ar }}">
          @else
          <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white text-sm" style="background:linear-gradient(135deg,var(--navy),var(--navy-mid))">{{ mb_substr($t->title_ar ?? '؟',0,1) }}</div>
          @endif
          <div>
            <p class="font-bold text-sm" style="color:var(--navy)">
              <span data-ar>{{ $t->title_ar }}</span><span data-en class="hidden">{{ $t->title_en ?? $t->title_ar }}</span>
            </p>
            <p class="text-xs" style="color:var(--muted)">
              <span data-ar>{{ $t->subtitle_ar }}</span><span data-en class="hidden">{{ $t->subtitle_en ?? $t->subtitle_ar }}</span>
            </p>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- ══════════ CONTACT ══════════ --}}
<section id="contact" class="py-16 sm:py-20" style="background:var(--off)">
  <div class="max-w-6xl mx-auto px-4 sm:px-6">
    <div class="text-center mb-10 fade-up">
      <div class="section-tag mx-auto" style="width:fit-content">
        <span data-ar>{{ $s('contact')?->subtitle_ar ?? 'تواصل معنا' }}</span>
        <span data-en class="hidden">{{ $s('contact')?->subtitle_en ?? 'Contact Us' }}</span>
      </div>
      <h2 class="text-2xl sm:text-3xl font-black mt-2" style="color:var(--navy)">
        <span data-ar>{{ $s('contact')?->title_ar ?? 'نسعد بخدمتك' }}</span>
        <span data-en class="hidden">{{ $s('contact')?->title_en ?? "We're Happy to Help" }}</span>
      </h2>
      <p class="mt-2 text-sm max-w-lg mx-auto" style="color:var(--muted)">
        <span data-ar>{{ $s('contact')?->body_ar ?? '' }}</span>
        <span data-en class="hidden">{{ $s('contact')?->body_en ?? '' }}</span>
      </p>
    </div>

    @if(session('contact_success'))
    <div class="max-w-xl mx-auto mb-8 rounded-2xl p-4 flex items-center gap-3 bg-green-50 border border-green-200">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-green-600 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
      <p class="font-semibold text-green-800 text-sm">{{ session('contact_success') }}</p>
    </div>
    @endif

    <div class="grid lg:grid-cols-5 gap-6 lg:gap-8">
      {{-- Info cards --}}
      <div class="lg:col-span-2 space-y-4 fade-up">
        @foreach($s('contact')?->activeItems ?? collect() as $info)
        <div class="bg-white rounded-2xl p-5 border flex items-start gap-4" style="border-color:var(--border)">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(15,36,68,.07)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="color:var(--navy)">{!! $iconSvg[$info->icon ?? 'location'] ?? $iconSvg['location'] !!}</svg>
          </div>
          <div>
            <p class="text-xs font-semibold mb-0.5" style="color:var(--muted)">
              <span data-ar>{{ $info->title_ar }}</span><span data-en class="hidden">{{ $info->title_en ?? $info->title_ar }}</span>
            </p>
            <p class="text-sm font-semibold" style="color:var(--text)">
              @php $isPhoneItem = in_array($info->icon ?? '', ['phone', 'mobile', 'tel']); @endphp
              <span data-ar @if($isPhoneItem) dir="ltr" style="unicode-bidi:embed;" @endif>{{ $info->body_ar }}</span>
              <span data-en class="hidden" @if($isPhoneItem) dir="ltr" style="unicode-bidi:embed;" @endif>{{ $info->body_en ?? $info->body_ar }}</span>
            </p>
          </div>
        </div>
        @endforeach
      </div>

      {{-- Form --}}
      <div class="lg:col-span-3 fade-up">
        <div class="bg-white rounded-2xl p-6 sm:p-8 border shadow-sm" style="border-color:var(--border)">
          <h3 class="font-bold text-base mb-5" style="color:var(--navy)">
            <span data-ar>أرسل لنا رسالة</span><span data-en class="hidden">Send Us a Message</span>
          </h3>
          <form method="POST" action="{{ route('contact.store') }}" class="space-y-4">
            @csrf
            {{-- Honeypot: left empty by real users; bots fill it and get silently rejected --}}
            <input type="text" name="website" value="" autocomplete="off" tabindex="-1" aria-hidden="true" style="position:fixed;left:-9999px;top:-9999px;width:1px;height:1px;opacity:0;pointer-events:none;">
            <div class="grid sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:var(--text)">
                  <span data-ar>الاسم الكامل</span><span data-en class="hidden">Full Name</span> <span style="color:var(--gold)">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                  placeholder-ar="أحمد محمد" placeholder-en="John Smith" placeholder="أحمد محمد"
                  class="input-field w-full rounded-xl px-4 py-3 text-sm @error('name') border-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:var(--text)">
                  <span data-ar>رقم الجوال</span><span data-en class="hidden">Phone</span>
                </label>
                <input type="tel" name="phone" value="{{ old('phone') }}"
                  placeholder-ar="05xxxxxxxx" placeholder-en="+968 9x..." placeholder="05xxxxxxxx"
                  class="input-field w-full rounded-xl px-4 py-3 text-sm">
              </div>
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1.5" style="color:var(--text)">
                <span data-ar>البريد الإلكتروني</span><span data-en class="hidden">Email</span> <span style="color:var(--gold)">*</span>
              </label>
              <input type="email" name="email" value="{{ old('email') }}" placeholder="example@email.com"
                class="input-field w-full rounded-xl px-4 py-3 text-sm @error('email') border-red-400 @enderror">
              @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1.5" style="color:var(--text)">
                <span data-ar>الموضوع</span><span data-en class="hidden">Subject</span> <span style="color:var(--gold)">*</span>
              </label>
              <select name="subject" class="input-field w-full rounded-xl px-4 py-3 text-sm @error('subject') border-red-400 @enderror">
                <option value="" data-ar="-- اختر --" data-en="-- Select --">-- اختر --</option>
                @foreach([['ar'=>'استفسار عام','en'=>'General Inquiry'],['ar'=>'طلب عرض سعر','en'=>'Quote Request'],['ar'=>'الإبلاغ عن مشكلة','en'=>'Report Issue'],['ar'=>'طلب شراكة','en'=>'Partnership'],['ar'=>'أخرى','en'=>'Other']] as $opt)
                <option value="{{ $opt['ar'] }}" data-ar="{{ $opt['ar'] }}" data-en="{{ $opt['en'] }}" {{ old('subject') == $opt['ar'] ? 'selected' : '' }}>{{ $opt['ar'] }}</option>
                @endforeach
              </select>
              @error('subject')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1.5" style="color:var(--text)">
                <span data-ar>الرسالة</span><span data-en class="hidden">Message</span> <span style="color:var(--gold)">*</span>
              </label>
              <textarea name="message" rows="4" placeholder-ar="اكتب رسالتك..." placeholder-en="Write your message..." placeholder="اكتب رسالتك..."
                class="input-field w-full rounded-xl px-4 py-3 text-sm resize-none @error('message') border-red-400 @enderror">{{ old('message') }}</textarea>
              @error('message')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn-navy w-full py-3.5 rounded-xl flex items-center justify-center gap-2 text-sm font-bold">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12zm0 0h7.5"/></svg>
              <span data-ar>إرسال الرسالة</span><span data-en class="hidden">Send Message</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ══════════ FOOTER ══════════ --}}
<footer class="py-12 sm:py-16" style="background:var(--navy)">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">

    {{-- Main grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 pb-10 border-b" style="border-color:rgba(255,255,255,.08)">

      {{-- Col 1+2: Logo + description + social icons --}}
      <div class="lg:col-span-2">
        <img src="{{ asset('img/logo.png') }}" alt="Tharwa" class="h-14 mb-4 brightness-0 invert opacity-90">
        <p class="text-sm leading-relaxed mb-6" style="color:rgba(255,255,255,.45);max-width:22rem">
          <span data-ar>{{ $footer?->body_ar ?? 'منصة متكاملة لإدارة العقارات السكنية والتجارية بأعلى معايير الجودة والاحترافية.' }}</span>
          <span data-en class="hidden">{{ $footer?->body_en ?? 'An integrated platform for residential and commercial property management.' }}</span>
        </p>

        {{-- Social icons — only shown when a URL is set --}}
        <div class="flex flex-wrap gap-2.5">
          @php
          $socialLinks = [
            'whatsapp' => [
              'href'  => $waHref,
              'label' => 'WhatsApp',
              'color' => '#25d366',
              'svg'   => '<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>',
            ],
            'instagram' => [
              'href'  => $socials['instagram'] ?? null,
              'label' => 'Instagram',
              'color' => '#e1306c',
              'svg'   => '<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/>',
            ],
            'twitter' => [
              'href'  => $socials['twitter'] ?? null,
              'label' => 'X / Twitter',
              'color' => '#ffffff',
              'svg'   => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.259 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>',
            ],
            'facebook' => [
              'href'  => $socials['facebook'] ?? null,
              'label' => 'Facebook',
              'color' => '#1877f2',
              'svg'   => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>',
            ],
            'linkedin' => [
              'href'  => $socials['linkedin'] ?? null,
              'label' => 'LinkedIn',
              'color' => '#0a66c2',
              'svg'   => '<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>',
            ],
            'tiktok' => [
              'href'  => $socials['tiktok'] ?? null,
              'label' => 'TikTok',
              'color' => '#010101',
              'svg'   => '<path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.18 8.18 0 0 0 4.78 1.52V6.75a4.85 4.85 0 0 1-1.01-.06z"/>',
            ],
          ];
          @endphp

          @foreach($socialLinks as $net => $info)
          @if($info['href'])
          <a href="{{ $info['href'] }}" target="_blank" rel="noopener" title="{{ $info['label'] }}"
             class="w-9 h-9 rounded-xl flex items-center justify-center transition-all"
             style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.55)"
             onmouseover="this.style.background='{{ $info['color'] }}';this.style.color='#fff';this.style.borderColor='{{ $info['color'] }}'"
             onmouseout="this.style.background='rgba(255,255,255,.07)';this.style.color='rgba(255,255,255,.55)';this.style.borderColor='rgba(255,255,255,.1)'">
            <svg viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">{!! $info['svg'] !!}</svg>
          </a>
          @endif
          @endforeach
        </div>
      </div>

      {{-- Col 3: Quick links --}}
      <div>
        <h4 class="text-xs font-bold uppercase tracking-widest mb-5" style="color:rgba(255,255,255,.35)">
          <span data-ar>روابط سريعة</span><span data-en class="hidden">Quick Links</span>
        </h4>
        <ul class="space-y-3">
          @foreach([
            ['#home',                    'الرئيسية',    'Home'],
            ['#services',                'الخدمات',     'Services'],
            [route('properties.index'),  'العقارات',    'Properties'],
            ['#types',                   'أنواع العقارات','Property Types'],
            ['#about',                   'عن الشركة',   'About Us'],
            ['#contact',                 'تواصل معنا',  'Contact'],
          ] as [$u, $ar, $en])
          <li>
            <a href="{{ $u }}" class="text-sm transition flex items-center gap-2 group"
               style="color:rgba(255,255,255,.4)"
               onmouseover="this.style.color='rgba(255,255,255,.9)'"
               onmouseout="this.style.color='rgba(255,255,255,.4)'">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                   class="w-3 h-3 flex-shrink-0" style="color:var(--gold)">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
              </svg>
              <span data-ar>{{ $ar }}</span><span data-en class="hidden">{{ $en }}</span>
            </a>
          </li>
          @endforeach
        </ul>
      </div>

      {{-- Col 4: Contact info + WhatsApp --}}
      <div>
        <h4 class="text-xs font-bold uppercase tracking-widest mb-5" style="color:rgba(255,255,255,.35)">
          <span data-ar>تواصل معنا</span><span data-en class="hidden">Contact Us</span>
        </h4>

        <div class="space-y-3">
          {{-- Phone --}}
          @if($phone)
          <a href="tel:{{ preg_replace('/\D/','',$phone) }}"
             class="flex items-center gap-3 text-sm transition"
             style="color:rgba(255,255,255,.45)"
             onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.45)'">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                  style="background:rgba(255,255,255,.08)">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25z"/>
              </svg>
            </span>
            <span dir="ltr" style="unicode-bidi:embed;">{{ $phone }}</span>
          </a>
          @endif

          {{-- Email --}}
          @if($email)
          <a href="mailto:{{ $email }}"
             class="flex items-center gap-3 text-sm transition"
             style="color:rgba(255,255,255,.45)"
             onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.45)'">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                  style="background:rgba(255,255,255,.08)">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
              </svg>
            </span>
            {{ $email }}
          </a>
          @endif

          {{-- WhatsApp — below email --}}
          @if($waHref)
          <a href="{{ $waHref }}" target="_blank" rel="noopener"
             class="flex items-center gap-3 text-sm transition"
             style="color:rgba(255,255,255,.45)"
             onmouseover="this.style.color='#25d366'" onmouseout="this.style.color='rgba(255,255,255,.45)'">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                  style="background:rgba(37,211,102,.15)">
              <svg viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4" style="color:#25d366">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
              </svg>
            </span>
            <span data-ar>تواصل عبر واتساب</span>
            <span data-en class="hidden">Chat on WhatsApp</span>
          </a>
          @endif
        </div>

      </div>
    </div>

    {{-- Bottom bar --}}
    <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
      <p class="text-xs" style="color:rgba(255,255,255,.22)">
        © {{ date('Y') }}
        <span data-ar>{{ $footer?->title_ar ?? 'شركة ثروة للتطوير العقاري' }} — جميع الحقوق محفوظة.</span>
        <span data-en class="hidden">{{ $footer?->title_en ?? 'Tharwa Real Estate' }} — All rights reserved.</span>
      </p>
      <p class="text-xs" style="color:rgba(255,255,255,.18)">
        <span data-ar>منصة إدارة عقارية متكاملة</span>
        <span data-en class="hidden">Integrated Real Estate Management Platform</span>
      </p>
    </div>

  </div>
</footer>

<script>
// ── Disable browser media-session overlay on the hero video ──
(function(){
  var v = document.getElementById('hero-vid');
  if(v && 'mediaSession' in navigator){
    navigator.mediaSession.metadata = null;
    ['play','pause','stop','seekbackward','seekforward','previoustrack','nexttrack'].forEach(function(a){
      try { navigator.mediaSession.setActionHandler(a, null); } catch(e){}
    });
  }
})();

// ── Language ──────────────────────────────
let lang = localStorage.getItem('tharwa_lang') || 'ar';
applyLang(lang);
// Sync server session on every home page load so other pages render in the same language
fetch('/locale/' + lang, { method: 'GET', credentials: 'same-origin' });

function toggleLang(){
  lang = lang === 'ar' ? 'en' : 'ar';
  localStorage.setItem('tharwa_lang', lang);
  applyLang(lang);
  fetch('/locale/' + lang, { method: 'GET', credentials: 'same-origin' });
}
function applyLang(l){
  const isAr = l === 'ar';
  const root = document.getElementById('html-root') || document.documentElement;
  root.setAttribute('lang', l);
  root.setAttribute('dir', isAr ? 'rtl' : 'ltr');
  document.querySelectorAll('[data-ar]:not(option)').forEach(e => e.classList.toggle('hidden', !isAr));
  document.querySelectorAll('[data-en]:not(option)').forEach(e => e.classList.toggle('hidden', isAr));
  document.querySelectorAll('[data-ar-text]').forEach(e => e.textContent = isAr ? e.dataset.arText : e.dataset.enText);
  document.querySelectorAll('[placeholder-ar]').forEach(e => e.placeholder = isAr ? e.getAttribute('placeholder-ar') : e.getAttribute('placeholder-en'));
  document.querySelectorAll('select option[data-ar]').forEach(o => o.textContent = isAr ? o.dataset.ar : o.dataset.en);
  const lbl = isAr ? 'EN' : 'عر';
  ['lang-btn','lang-btn-mob'].forEach(id => {
    const el = document.getElementById(id);
    if(el){ const sp = el.querySelector('#lang-label'); if(sp) sp.textContent = lbl; else el.textContent = lbl; }
  });
}

// ── Navbar shadow on scroll — handled by public-nav partial ──

// ── Search tabs ───────────────────────────
function setTab(val){
  document.getElementById('purpose-input').value = val;
  ['rent','sale','both'].forEach(t => {
    document.getElementById('tab-'+t).classList.toggle('active', t === val);
  });
}

// ── Featured load more ────────────────────
function loadMoreFeatured(citySlug) {
  var grid = document.getElementById('featured-grid-' + citySlug);
  if (!grid) return;
  grid.querySelectorAll('.featured-hidden').forEach(function(el) {
    el.style.display = '';
    el.classList.remove('featured-hidden');
    setTimeout(function() { el.classList.add('visible'); }, 50);
  });
  var wrap = document.getElementById('feat-more-wrap-' + citySlug);
  if (wrap) wrap.style.display = 'none';
}

// ── City tabs ─────────────────────────────
function switchCity(city) {
  // hide all grids
  document.querySelectorAll('.city-grid').forEach(g => g.classList.add('hidden'));
  // show selected
  const slug = city === 'all' ? 'all' : city.replace(/\s+/g,'-').replace(/[^\w\-]/g,'').toLowerCase();
  const grid = document.getElementById('city-grid-' + slug);
  if (grid) grid.classList.remove('hidden');
  // update tab styles
  document.querySelectorAll('.city-tab').forEach(t => {
    t.style.borderColor = 'transparent';
    t.style.color = 'var(--muted)';
  });
  const activeTab = city === 'all'
    ? document.getElementById('city-tab-all')
    : document.getElementById('city-tab-' + slug);
  if (activeTab) {
    activeTab.style.borderColor = 'var(--gold)';
    activeTab.style.color = 'var(--navy)';
  }
}

// ── mk-card hover ──────────────────────────
document.querySelectorAll('.mk-card').forEach(c => {
  c.addEventListener('mouseenter', () => {
    c.style.boxShadow = '0 8px 30px rgba(15,36,68,.12)';
    c.style.transform = 'translateY(-4px)';
    const img = c.querySelector('img');
    if (img) img.style.transform = 'scale(1.06)';
  });
  c.addEventListener('mouseleave', () => {
    c.style.boxShadow = '';
    c.style.transform = '';
    const img = c.querySelector('img');
    if (img) img.style.transform = '';
  });
});

// ── Fade-up on scroll ─────────────────────
const obs = new IntersectionObserver(
  entries => entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); }),
  { threshold: 0.1 }
);
document.querySelectorAll('.fade-up').forEach(el => obs.observe(el));
</script>


</body>
</html>
