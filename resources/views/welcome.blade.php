<!DOCTYPE html>
<html lang="ar" dir="rtl" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثروة | Tharwa — Real Estate Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;700;800;900&family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #0f2444;
            --navy-mid: #1a3a6b;
            --gold: #c9a84c;
            --gold-light: #e8c96e;
            --text-dark: #1a2437;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --bg-section: #f8fafc;
        }
        [lang="ar"] * { font-family: 'Cairo', sans-serif; }
        [lang="en"] * { font-family: 'Sora', sans-serif; }
        html { scroll-behavior: smooth; }
        body { background: #ffffff; color: var(--text-dark); overflow-x: hidden; }

        /* Navbar */
        #navbar { transition: all 0.3s ease; }
        #navbar.scrolled {
            background: rgba(255,255,255,0.97) !important;
            backdrop-filter: blur(12px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
        }
        .nav-link {
            position: relative; color: rgba(255,255,255,0.8);
            font-size: 0.875rem; font-weight: 500; transition: color 0.2s;
        }
        .nav-link:hover { color: #fff; }
        .nav-link::after {
            content: ''; position: absolute; bottom: -4px; right: 0; left: 0;
            width: 0; height: 2px; background: var(--gold);
            transition: width 0.3s ease; margin: 0 auto;
        }
        .nav-link:hover::after { width: 100%; }
        #navbar.scrolled .nav-link { color: #475569; }
        #navbar.scrolled .nav-link:hover { color: var(--navy); }

        /* Hero */
        .hero-bg {
            background:
                linear-gradient(130deg, rgba(9, 24, 44, 0.93) 0%, rgba(18, 53, 95, 0.88) 52%, rgba(31, 82, 136, 0.84) 100%),
                url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1800&q=80');
            background-size: cover;
            background-position: center;
        }

        .hero-visual {
            position: relative;
            width: min(100%, 530px);
            min-height: 470px;
        }
        .hero-main-card {
            position: relative;
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.18);
            box-shadow: 0 35px 70px rgba(5, 13, 25, 0.45);
        }
        .hero-main-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(5,10,18,0.7), rgba(5,10,18,0.08));
        }
        .hero-main-card img {
            width: 100%;
            height: 440px;
            object-fit: cover;
            transition: transform 0.8s ease;
        }
        .hero-main-card:hover img { transform: scale(1.05); }

        .hero-chip {
            position: absolute;
            z-index: 2;
            right: 20px;
            left: 20px;
            bottom: 20px;
            border-radius: 18px;
            padding: 12px 14px;
            background: rgba(15,36,68,0.7);
            border: 1px solid rgba(255,255,255,0.16);
            backdrop-filter: blur(8px);
        }
        .hero-mini-card {
            position: absolute;
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 20px 45px rgba(9, 20, 38, 0.3);
            border: 1px solid rgba(201,168,76,0.3);
            animation: float 6s ease-in-out infinite;
        }
        .hero-mini-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .hero-mini-card-1 {
            width: 165px;
            height: 130px;
            top: -22px;
            left: -30px;
            animation-delay: 0.2s;
        }
        .hero-mini-card-2 {
            width: 178px;
            height: 142px;
            bottom: -26px;
            left: -34px;
            animation-delay: 0.8s;
        }

        .property-media {
            position: relative;
            height: 13.5rem;
            overflow: hidden;
        }
        .property-media::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(9, 22, 42, 0.65), rgba(9, 22, 42, 0.06));
        }
        .property-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s ease;
        }
        .property-card:hover .property-media img { transform: scale(1.08); }
        .property-pill {
            position: absolute;
            z-index: 2;
            top: 12px;
            right: 12px;
            background: rgba(255,255,255,0.92);
            color: var(--navy);
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 800;
            padding: 4px 11px;
        }
        .property-feature {
            position: absolute;
            z-index: 2;
            left: 12px;
            bottom: 12px;
            background: rgba(9, 24, 44, 0.75);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 600;
            border-radius: 999px;
            padding: 4px 10px;
        }

        .about-visual-grid {
            position: relative;
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            grid-template-rows: 170px 170px;
            gap: 12px;
            margin-bottom: 18px;
        }
        .about-main-photo {
            grid-row: span 2;
            border-radius: 22px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 20px 48px rgba(15,36,68,0.22);
        }
        .about-main-photo::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(5,12,22,0.5), rgba(5,12,22,0));
        }
        .about-main-photo img,
        .about-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .about-thumb {
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid rgba(201,168,76,0.28);
            box-shadow: 0 12px 30px rgba(15,36,68,0.16);
        }
        .about-floating-badge {
            position: absolute;
            z-index: 2;
            bottom: 14px;
            right: 14px;
            background: rgba(255,255,255,0.94);
            color: var(--navy);
            border-radius: 14px;
            padding: 10px 12px;
            font-size: 0.75rem;
            font-weight: 800;
            line-height: 1.3;
            box-shadow: 0 10px 24px rgba(9,22,42,0.2);
        }
        .value-card { transition: all 0.3s ease; }
        .value-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 30px rgba(15,36,68,0.11);
            border-color: rgba(201,168,76,0.45) !important;
        }

        /* Buttons */
        .btn-gold { background: var(--gold); color: var(--navy); font-weight: 700; transition: all 0.3s ease; }
        .btn-gold:hover { background: var(--gold-light); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(201,168,76,0.35); }
        .btn-outline-white { border: 2px solid rgba(255,255,255,0.35); color: #fff; font-weight: 600; transition: all 0.3s ease; }
        .btn-outline-white:hover { border-color: var(--gold); color: var(--gold); }
        .btn-navy { background: var(--navy); color: #fff; font-weight: 700; transition: all 0.3s ease; }
        .btn-navy:hover { background: var(--navy-mid); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(15,36,68,0.25); }

        /* Heading line */
        .heading-line { display: inline-block; width: 48px; height: 3px; background: var(--gold); border-radius: 2px; margin-bottom: 1rem; }

        /* Cards */
        .service-card { border: 1px solid var(--border); background: #fff; transition: all 0.35s cubic-bezier(0.175,0.885,0.32,1.275); }
        .service-card:hover { transform: translateY(-7px); box-shadow: 0 20px 45px rgba(15,36,68,0.1); border-color: var(--gold); }
        .service-icon { width: 52px; height: 52px; background: #f0f4f8; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem; transition: background 0.3s; }
        .service-card:hover .service-icon { background: var(--navy); }
        .service-card:hover .service-icon svg { color: var(--gold) !important; }

        .property-card { border: 1px solid var(--border); background: #fff; transition: all 0.3s ease; }
        .property-card:hover { transform: translateY(-5px); box-shadow: 0 16px 40px rgba(15,36,68,0.1); border-color: rgba(201,168,76,0.4); }

        /* Stats */
        .stats-bar { background: var(--navy); }

        /* Contact inputs */
        .input-field { border: 1px solid var(--border); background: #fff; color: var(--text-dark); transition: all 0.3s ease; }
        .input-field:focus { border-color: var(--navy-mid); box-shadow: 0 0 0 3px rgba(26,58,107,0.1); outline: none; }
        .input-field::placeholder { color: #94a3b8; }

        /* Animations */
        .fade-in { opacity: 0; transform: translateY(24px); transition: all 0.65s ease; }
        .fade-in.visible { opacity: 1; transform: translateY(0); }
        .floating { animation: float 5s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-14px); } }

        @media (max-width: 1024px) {
            .hero-visual {
                width: 100%;
                min-height: auto;
                margin-top: 12px;
            }
            .hero-main-card img { height: 330px; }
            .hero-mini-card {
                width: 135px;
                height: 110px;
            }
            .hero-mini-card-1 { top: -14px; left: -8px; }
            .hero-mini-card-2 { bottom: -15px; left: -10px; }
            .about-visual-grid {
                grid-template-columns: 1fr;
                grid-template-rows: 250px 140px 140px;
            }
            .about-main-photo { grid-row: span 1; }
        }

        /* Mobile menu */
        #mobile-menu { transition: all 0.3s ease; }

        /* Language toggle */
        .lang-btn { border: 1.5px solid rgba(255,255,255,0.3); color: rgba(255,255,255,0.85); transition: all 0.2s; padding: 5px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; }
        .lang-btn:hover { border-color: var(--gold); color: var(--gold); }
        #navbar.scrolled .lang-btn { border-color: rgba(15,36,68,0.3); color: var(--navy); }
        #navbar.scrolled .lang-btn:hover { border-color: var(--gold); color: var(--gold); }

        footer { background: var(--navy); }

        /* RTL/LTR nav line fix */
        [dir="ltr"] .nav-link::after { right: 0; left: 0; }
        [dir="rtl"] .nav-link::after { right: 0; left: 0; }

        /* Smooth lang transition */
        [data-ar], [data-en] { transition: opacity 0.2s ease; }
    </style>
</head>
<body>

{{-- ======= NAVBAR ======= --}}
<nav id="navbar" class="fixed top-0 right-0 left-0 z-50 py-3 px-4 sm:px-6 bg-transparent">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        {{-- Logo --}}
        <a href="#home" class="flex items-center gap-2.5 flex-shrink-0 mt-6 mb-6">
            <div class="w-20 h-20 rounded-xl flex items-center justify-center shadow flex-shrink-0" style="background-color: #fff;">
                <img src="{{ asset('img/logo.png') }}" alt="logo" class="h-[72px] w-auto">
            </div>
         
        </a>

        {{-- Desktop Nav links --}}
        <div class="hidden lg:flex items-center gap-6 xl:gap-8">
            <a href="#home"       class="nav-link" data-ar-text="الرئيسية"     data-en-text="Home">الرئيسية</a>
            <a href="#services"   class="nav-link" data-ar-text="خدماتنا"      data-en-text="Services">خدماتنا</a>
            <a href="{{ route('properties.index') }}" class="nav-link" data-ar-text="العقارات"     data-en-text="Properties">العقارات</a>
            <a href="#about"      class="nav-link" data-ar-text="عن الشركة"    data-en-text="About">عن الشركة</a>
            <a href="#contact"    class="nav-link" data-ar-text="تواصل معنا"   data-en-text="Contact">تواصل معنا</a>
        </div>

        {{-- Right side: Lang + Login + Hamburger --}}
        <div class="flex items-center gap-2 sm:gap-3">
            {{-- Language Toggle --}}
            <button onclick="toggleLang()" class="lang-btn hidden sm:block" id="lang-btn">EN</button>

            {{-- Login Button --}}
            <a href="{{ route('login') }}" class="btn-gold px-4 py-2 rounded-xl text-sm flex items-center gap-1.5 shadow-sm flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
                <span data-ar>دخول</span><span data-en class="hidden">Login</span>
            </a>

            {{-- Mobile Hamburger --}}
            <button id="hamburger" class="lg:hidden text-white p-1.5 rounded-lg hover:bg-white/10 transition" onclick="toggleMobileMenu()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path id="hamburger-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobile-menu" class="hidden lg:hidden mt-3 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mx-0">
        <div class="p-4 space-y-1">
            <a href="#home"       onclick="closeMobileMenu()" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 text-gray-700 font-medium text-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 flex-shrink-0" style="color:var(--gold)"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                <span data-ar-text="الرئيسية" data-en-text="Home">الرئيسية</span>
            </a>
            <a href="#services"   onclick="closeMobileMenu()" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 text-gray-700 font-medium text-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 flex-shrink-0" style="color:var(--gold)"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                <span data-ar-text="خدماتنا" data-en-text="Services">خدماتنا</span>
            </a>
            <a href="{{ route('properties.index') }}" onclick="closeMobileMenu()" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 text-gray-700 font-medium text-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 flex-shrink-0" style="color:var(--gold)"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                <span data-ar-text="العقارات" data-en-text="Properties">العقارات</span>
            </a>
            <a href="#about"      onclick="closeMobileMenu()" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 text-gray-700 font-medium text-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 flex-shrink-0" style="color:var(--gold)"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                <span data-ar-text="عن الشركة" data-en-text="About">عن الشركة</span>
            </a>
            <a href="#contact"    onclick="closeMobileMenu()" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 text-gray-700 font-medium text-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 flex-shrink-0" style="color:var(--gold)"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                <span data-ar-text="تواصل معنا" data-en-text="Contact">تواصل معنا</span>
            </a>
            <div class="border-t border-gray-100 pt-3 mt-2 flex items-center gap-3">
                <a href="{{ route('login') }}" class="btn-navy flex-1 py-2.5 rounded-xl text-sm text-center">
                    <span data-ar>تسجيل الدخول</span><span data-en class="hidden">Login</span>
                </a>
                <button onclick="toggleLang()" class="px-4 py-2.5 rounded-xl text-sm font-bold border border-gray-200 hover:border-yellow-400 hover:text-yellow-600 text-gray-600 transition" id="lang-btn-mobile">EN</button>
            </div>
        </div>
    </div>
</nav>

{{-- ======= HERO ======= --}}
<section id="home" class="hero-bg min-h-screen flex items-center relative overflow-hidden pt-16 sm:pt-20">
    <div class="absolute inset-0 opacity-[0.04]"
         style="background-image:url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23ffffff'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\");"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 grid lg:grid-cols-2 gap-10 lg:gap-14 items-center w-full py-12 lg:py-0">
        <div class="fade-in text-white">
            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 mb-6 text-sm font-medium"
                 style="background: rgba(201,168,76,0.15); border: 1px solid rgba(201,168,76,0.3); color: var(--gold-light);">
                <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                <span data-ar>الرائد في إدارة العقارات</span>
                <span data-en class="hidden">Leading Real Estate Management</span>
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black leading-tight mb-5">
                <span data-ar>إدارة عقارية<br><span style="color:var(--gold);">ذكية ومتكاملة</span></span>
                <span data-en class="hidden">Smart &amp; Integrated<br><span style="color:var(--gold);">Property Management</span></span>
            </h1>

            <p class="text-white/70 text-base sm:text-lg leading-relaxed mb-8 max-w-xl">
                <span data-ar>نقدم حلولاً متطورة لإدارة العقارات والمباني السكنية والتجارية. من المستأجر إلى الإدارة، كل شيء في منصة واحدة قوية.</span>
                <span data-en class="hidden">We provide advanced solutions for managing residential and commercial properties. From tenants to administration — everything in one powerful platform.</span>
            </p>

            <div class="flex flex-wrap gap-3 mb-10">
                <a href="{{ route('login') }}" class="btn-gold px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl flex items-center gap-2 text-sm sm:text-base shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/></svg>
                    <span data-ar>ابدأ الآن</span><span data-en class="hidden">Get Started</span>
                </a>
                <a href="#about" class="btn-outline-white px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl flex items-center gap-2 text-sm sm:text-base">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                    <span data-ar>تعرف علينا</span><span data-en class="hidden">Learn More</span>
                </a>
            </div>

            <div class="flex items-center gap-6 sm:gap-10 flex-wrap">
                <div>
                    <p class="text-2xl sm:text-3xl font-black" style="color:var(--gold);">50+</p>
                    <p class="text-white/50 text-xs mt-1"><span data-ar>مبنى تحت الإدارة</span><span data-en class="hidden">Buildings Managed</span></p>
                </div>
                <div class="w-px h-8 sm:h-10 bg-white/15"></div>
                <div>
                    <p class="text-2xl sm:text-3xl font-black" style="color:var(--gold);">500+</p>
                    <p class="text-white/50 text-xs mt-1"><span data-ar>وحدة مدارة</span><span data-en class="hidden">Units Managed</span></p>
                </div>
                <div class="w-px h-8 sm:h-10 bg-white/15"></div>
                <div>
                    <p class="text-2xl sm:text-3xl font-black" style="color:var(--gold);">98%</p>
                    <p class="text-white/50 text-xs mt-1"><span data-ar>نسبة رضا العملاء</span><span data-en class="hidden">Client Satisfaction</span></p>
                </div>
            </div>
        </div>

        {{-- Hero Visual --}}
        <div class="flex justify-center items-center">
            <div class="hero-visual fade-in">
                <div class="hero-main-card">
                    <img src="https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&w=1400&q=80"
                         loading="lazy"
                         alt="Luxury tower skyline">
                    <div class="hero-chip text-white">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/70 mb-1">
                            <span data-ar>فرص استثمارية</span>
                            <span data-en class="hidden">Investment Opportunities</span>
                        </p>
                        <p class="font-bold text-sm sm:text-base">
                            <span data-ar>إدارة احترافية لمحفظة عقارية متنوعة</span>
                            <span data-en class="hidden">Professional management for a diverse property portfolio</span>
                        </p>
                    </div>
                </div>

                <div class="hero-mini-card hero-mini-card-1">
                    <img src="https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=900&q=80"
                         loading="lazy"
                         alt="Modern apartment facade">
                </div>

                <div class="hero-mini-card hero-mini-card-2">
                    <img src="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=900&q=80"
                         loading="lazy"
                         alt="Elegant building lobby">
                </div>
            </div>
        </div>
    </div>

    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 animate-bounce hidden sm:block">
        <a href="#services" class="text-white/40 hover:text-white/80 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
        </a>
    </div>
</section>

{{-- ======= SERVICES ======= --}}
<section id="services" class="py-16 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12 sm:mb-16 fade-in">
            <div class="heading-line mx-auto"></div>
            <p class="text-xs sm:text-sm font-semibold uppercase tracking-widest mb-2" style="color:var(--gold);">
                <span data-ar>ما نقدمه</span><span data-en class="hidden">What We Offer</span>
            </p>
            <h2 class="text-3xl sm:text-4xl font-black" style="color:var(--navy);">
                <span data-ar>خدماتنا المتميزة</span><span data-en class="hidden">Our Premium Services</span>
            </h2>
            <p class="mt-3 max-w-xl mx-auto text-sm sm:text-base" style="color:var(--text-muted);">
                <span data-ar>منظومة متكاملة من الخدمات العقارية الذكية لضمان أفضل تجربة إدارية</span>
                <span data-en class="hidden">A comprehensive suite of smart real estate services for the best management experience</span>
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-7">
            @php
            $svgStroke = 'xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"';
            $services = [
                ['svg'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>',  'ar_title'=>'إدارة المباني',       'en_title'=>'Building Management',      'ar_desc'=>'إدارة شاملة لجميع المباني والوحدات السكنية والتجارية مع تتبع دقيق.', 'en_desc'=>'Comprehensive management of residential and commercial buildings with precise tracking.'],
                ['svg'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0z"/>',      'ar_title'=>'إدارة المستأجرين',    'en_title'=>'Tenant Management',         'ar_desc'=>'نظام متكامل لإدارة عقود الإيجار وبيانات المستأجرين مع إشعارات الدفع.', 'en_desc'=>'Complete system for managing lease contracts and tenant data with payment notifications.'],
                ['svg'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"/>',      'ar_title'=>'الصيانة والإصلاحات',  'en_title'=>'Maintenance & Repairs',     'ar_desc'=>'بوابة إلكترونية لتقديم طلبات الصيانة ومتابعة حالتها بشكل فوري.', 'en_desc'=>'Online portal to submit and track maintenance requests in real time.'],
                ['svg'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5z"/>',  'ar_title'=>'التقارير المالية',     'en_title'=>'Financial Reports',         'ar_desc'=>'تقارير مالية تفصيلية قابلة للطباعة لكل مبنى تشمل الإيرادات والمصروفات.', 'en_desc'=>'Detailed printable financial reports per building covering revenue and expenses.'],
                ['svg'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>',   'ar_title'=>'إدارة الموظفين',      'en_title'=>'Staff Management',          'ar_desc'=>'توزيع المهام بين الموظفين وإسناد المباني وتتبع الأداء بكل سهولة.', 'en_desc'=>'Assign tasks and buildings to employees and track performance effortlessly.'],
                ['svg'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 8.25h3"/>',  'ar_title'=>'بوابة المستأجر',      'en_title'=>'Tenant Portal',             'ar_desc'=>'واجهة خاصة للمستأجر لعرض إشعارات الدفع وتقديم الطلبات من أي مكان.', 'en_desc'=>'A dedicated interface for tenants to view payment notices and submit requests anywhere.'],
            ];
            @endphp

            @foreach($services as $s)
            <div class="service-card rounded-2xl p-6 sm:p-7 fade-in">
                <div class="service-icon">
                    <svg {!! $svgStroke !!} class="w-6 h-6" style="color:var(--navy-mid);">{!! $s['svg'] !!}</svg>
                </div>
                <h3 class="text-base sm:text-lg font-bold mb-2" style="color:var(--navy);">
                    <span data-ar>{{ $s['ar_title'] }}</span>
                    <span data-en class="hidden">{{ $s['en_title'] }}</span>
                </h3>
                <p class="text-sm leading-relaxed" style="color:var(--text-muted);">
                    <span data-ar>{{ $s['ar_desc'] }}</span>
                    <span data-en class="hidden">{{ $s['en_desc'] }}</span>
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ======= STATS ======= --}}
<section class="stats-bar py-14 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-10 text-center text-white">
            @foreach([
                ['50+',  'مبنى تحت إدارتنا',      'Buildings Under Management'],
                ['500+', 'وحدة مؤجرة',             'Units Leased'],
                ['200+', 'مستأجر سعيد',            'Happy Tenants'],
                ['98%',  'نسبة رضا العملاء',       'Client Satisfaction Rate'],
            ] as $stat)
            <div class="fade-in">
                <p class="text-4xl sm:text-5xl font-black mb-2" style="color:var(--gold);">{{ $stat[0] }}</p>
                <p class="text-white/60 text-xs sm:text-sm">
                    <span data-ar>{{ $stat[1] }}</span>
                    <span data-en class="hidden">{{ $stat[2] }}</span>
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ======= PROPERTIES ======= --}}
<section id="properties" class="py-16 sm:py-24" style="background:var(--bg-section);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12 sm:mb-16 fade-in">
            <div class="heading-line mx-auto"></div>
            <p class="text-xs sm:text-sm font-semibold uppercase tracking-widest mb-2" style="color:var(--gold);">
                <span data-ar>محفظتنا</span><span data-en class="hidden">Our Portfolio</span>
            </p>
            <h2 class="text-3xl sm:text-4xl font-black" style="color:var(--navy);">
                <span data-ar>أبرز عقاراتنا</span><span data-en class="hidden">Featured Properties</span>
            </h2>
            <p class="mt-3 max-w-xl mx-auto text-sm sm:text-base" style="color:var(--text-muted);">
                <span data-ar>نمتلك محفظة متنوعة من العقارات الراقية في أفضل المواقع</span>
                <span data-en class="hidden">We hold a diverse portfolio of premium properties in the best locations</span>
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-7">
            @php
            $properties = [
                ['ar_name'=>'برج ثروة 1',   'en_name'=>'Tharwa Tower 1', 'ar_loc'=>'حي العليا، الرياض',   'en_loc'=>'Al-Olaya, Riyadh',   'ar_type'=>'سكني',       'en_type'=>'Residential', 'ar_feature'=>'إطلالة بانورامية', 'en_feature'=>'Panoramic View', 'units'=>40, 'image'=>'https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?auto=format&fit=crop&w=1200&q=80'],
                ['ar_name'=>'عمارة النور',  'en_name'=>'Al-Nour Bldg',   'ar_loc'=>'حي المروج، الرياض',   'en_loc'=>'Al-Muruj, Riyadh',   'ar_type'=>'سكني تجاري', 'en_type'=>'Mixed Use',   'ar_feature'=>'وصول ذكي',        'en_feature'=>'Smart Access',   'units'=>24, 'image'=>'https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1200&q=80'],
                ['ar_name'=>'مجمع الأمير', 'en_name'=>'Al-Amir Complex', 'ar_loc'=>'حي النخيل، جدة',      'en_loc'=>'Al-Nakheel, Jeddah', 'ar_type'=>'تجاري',      'en_type'=>'Commercial',  'ar_feature'=>'مركز أعمال',      'en_feature'=>'Business Hub',    'units'=>16, 'image'=>'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1200&q=80'],
            ];
            @endphp

            @foreach($properties as $p)
            <div class="property-card rounded-2xl overflow-hidden fade-in">
                <div class="property-media">
                    <img src="{{ $p['image'] }}" loading="lazy" alt="{{ $p['en_name'] }}">
                    <span class="property-pill">
                        <span data-ar>{{ $p['ar_type'] }}</span>
                        <span data-en class="hidden">{{ $p['en_type'] }}</span>
                    </span>
                    <span class="property-feature">
                        <span data-ar>{{ $p['ar_feature'] }}</span>
                        <span data-en class="hidden">{{ $p['en_feature'] }}</span>
                    </span>
                </div>
                <div class="p-5 sm:p-6">
                    <h3 class="text-base sm:text-lg font-bold mb-1" style="color:var(--navy);">
                        <span data-ar>{{ $p['ar_name'] }}</span>
                        <span data-en class="hidden">{{ $p['en_name'] }}</span>
                    </h3>
                    <div class="flex items-center gap-2 text-sm mb-4" style="color:var(--text-muted);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 flex-shrink-0" style="color:var(--gold);"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0z"/></svg>
                        <span data-ar>{{ $p['ar_loc'] }}</span>
                        <span data-en class="hidden">{{ $p['en_loc'] }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t" style="border-color:var(--border);">
                        <div class="flex items-center gap-2 text-sm" style="color:var(--text-muted);">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4" style="color:var(--navy-mid);"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25zM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25z"/></svg>
                            {{ $p['units'] }} <span data-ar>وحدة</span><span data-en class="hidden">Units</span>
                        </div>
                        <span class="text-xs font-semibold px-3 py-1 rounded-full" style="background:#e8f5e9; color:#2e7d32;">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-green-600 me-1 align-middle"></span>
                            <span data-ar>نشط</span><span data-en class="hidden">Active</span>
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- View All button --}}
        <div class="text-center mt-10 sm:mt-12">
            <a href="{{ route('properties.index') }}"
               class="inline-flex items-center gap-2.5 px-8 py-3.5 rounded-2xl font-bold text-sm transition-all duration-300"
               style="background:var(--navy); color:#fff;"
               onmouseover="this.style.background='var(--navy-mid)'" onmouseout="this.style.background='var(--navy)'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25zM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25z"/></svg>
                <span data-ar>عرض جميع العقارات</span>
                <span data-en class="hidden">View All Properties</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4" style="color:var(--gold);"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 19.5-7.5-7.5 7.5-7.5"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- ======= ABOUT ======= --}}
<section id="about" class="py-16 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            {{-- Text --}}
            <div class="fade-in">
                <div class="heading-line"></div>
                <p class="text-xs sm:text-sm font-semibold uppercase tracking-widest mb-2" style="color:var(--gold);">
                    <span data-ar>من نحن</span><span data-en class="hidden">About Us</span>
                </p>
                <h2 class="text-3xl sm:text-4xl font-black mb-5" style="color:var(--navy);">
                    <span data-ar>شركة ثروة للعقارات</span>
                    <span data-en class="hidden">Tharwa Real Estate Co.</span>
                </h2>
                <p class="leading-relaxed mb-4 text-base sm:text-lg" style="color:#374151;">
                    <span data-ar>شركة ثروة للعقارات رائدة في قطاع إدارة العقارات بالمملكة العربية السعودية. نقدم خدمات إدارية متكاملة للمباني السكنية والتجارية بأعلى معايير الجودة والاحترافية.</span>
                    <span data-en class="hidden">Tharwa Real Estate is a leader in property management in Saudi Arabia. We provide comprehensive management services for residential and commercial buildings with the highest standards of quality and professionalism.</span>
                </p>
                <p class="leading-relaxed mb-8 text-sm sm:text-base" style="color:var(--text-muted);">
                    <span data-ar>يجمعنا شغف حقيقي بتطوير قطاع العقارات وتسهيل التعاملات بين ملاك العقارات والمستأجرين من خلال منصتنا الرقمية المتطورة.</span>
                    <span data-en class="hidden">We share a genuine passion for advancing the real estate sector and simplifying dealings between property owners and tenants through our advanced digital platform.</span>
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-8">
                    @foreach([
                        ['ar'=>'خبرة تزيد عن 10 سنوات', 'en'=>'Over 10 years of experience'],
                        ['ar'=>'فريق متخصص ومحترف',     'en'=>'Specialized professional team'],
                        ['ar'=>'دعم فني على مدار الساعة','en'=>'24/7 technical support'],
                        ['ar'=>'أسعار تنافسية وشفافة',  'en'=>'Competitive & transparent pricing'],
                    ] as $f)
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0" style="background:rgba(201,168,76,0.15);">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3" style="color:var(--gold);"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        </div>
                        <span class="text-sm" style="color:#374151;">
                            <span data-ar>{{ $f['ar'] }}</span>
                            <span data-en class="hidden">{{ $f['en'] }}</span>
                        </span>
                    </div>
                    @endforeach
                </div>
                <a href="#contact" class="btn-navy px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl inline-flex items-center gap-2 text-sm sm:text-base">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                    <span data-ar>تواصل معنا</span><span data-en class="hidden">Contact Us</span>
                </a>
            </div>

            {{-- Visual + Value Cards --}}
            <div class="fade-in">
                <div class="about-visual-grid">
                    <div class="about-main-photo">
                        <img src="https://images.unsplash.com/photo-1501183638710-841dd1904471?auto=format&fit=crop&w=1200&q=80"
                             loading="lazy"
                             alt="Modern residential community">
                        <div class="about-floating-badge">
                            <span data-ar>معدل إشغال 97%</span>
                            <span data-en class="hidden">97% Occupancy Rate</span>
                        </div>
                    </div>
                    <div class="about-thumb">
                        <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=900&q=80"
                             loading="lazy"
                             alt="Villa entrance">
                    </div>
                    <div class="about-thumb">
                        <img src="https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=900&q=80"
                             loading="lazy"
                             alt="Luxury apartment interior">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                @foreach([
                    ['svg'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0"/>', 'ar_title'=>'جودة عالية',    'en_title'=>'High Quality',    'ar_desc'=>'معايير عالمية في جميع خدماتنا', 'en_desc'=>'World-class standards in all our services'],
                    ['svg'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-3.15 0 .075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 0 1 3.15 0V15M6.9 7.575a1.575 1.575 0 1 0-3.15 0v8.175a6.75 6.75 0 0 0 6.75 6.75h2.018a5.25 5.25 0 0 0 3.712-1.538l1.732-1.732a5.25 5.25 0 0 0 1.538-3.712l.003-2.024a.668.668 0 0 1 .198-.471 1.575 1.575 0 1 0-2.228-2.228 3.818 3.818 0 0 0-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0 1 16.35 15m.002 0h-.002"/>', 'ar_title'=>'شراكة موثوقة',  'en_title'=>'Trusted Partner',  'ar_desc'=>'علاقات طويلة الأمد مع عملائنا',  'en_desc'=>'Long-term relationships with our clients'],
                    ['svg'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z"/>', 'ar_title'=>'أمان وخصوصية',  'en_title'=>'Security & Privacy','ar_desc'=>'حماية كاملة لبيانات العملاء',  'en_desc'=>'Full protection of client data'],
                    ['svg'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/>', 'ar_title'=>'دعم متواصل',    'en_title'=>'Continuous Support','ar_desc'=>'نحن دائماً بجانبك لمساعدتك',   'en_desc'=>'We are always here to help you'],
                ] as $v)
                <div class="value-card rounded-2xl p-5 sm:p-6 border" style="border-color:var(--border); background:var(--bg-section);">
                    <div class="w-10 sm:w-11 h-10 sm:h-11 rounded-xl flex items-center justify-center mb-3 sm:mb-4" style="background:rgba(15,36,68,0.07);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="color:var(--navy-mid);">{!! $v['svg'] !!}</svg>
                    </div>
                    <h4 class="font-bold text-xs sm:text-sm mb-1" style="color:var(--navy);">
                        <span data-ar>{{ $v['ar_title'] }}</span>
                        <span data-en class="hidden">{{ $v['en_title'] }}</span>
                    </h4>
                    <p class="text-xs" style="color:var(--text-muted);">
                        <span data-ar>{{ $v['ar_desc'] }}</span>
                        <span data-en class="hidden">{{ $v['en_desc'] }}</span>
                    </p>
                </div>
                @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ======= CONTACT ======= --}}
<section id="contact" class="py-16 sm:py-24" style="background:var(--bg-section);">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-10 sm:mb-14 fade-in">
            <div class="heading-line mx-auto"></div>
            <p class="text-xs sm:text-sm font-semibold uppercase tracking-widest mb-2" style="color:var(--gold);">
                <span data-ar>تواصل معنا</span><span data-en class="hidden">Contact Us</span>
            </p>
            <h2 class="text-3xl sm:text-4xl font-black" style="color:var(--navy);">
                <span data-ar>نسعد بخدمتك</span><span data-en class="hidden">We're Happy to Help</span>
            </h2>
            <p class="mt-3 max-w-lg mx-auto text-sm sm:text-base" style="color:var(--text-muted);">
                <span data-ar>هل لديك سؤال أو تحتاج إلى مزيد من المعلومات؟ تواصل معنا وسنرد عليك في أقرب وقت</span>
                <span data-en class="hidden">Have a question or need more information? Contact us and we'll reply as soon as possible</span>
            </p>
        </div>

        @if(session('contact_success'))
        <div class="max-w-xl mx-auto mb-8 rounded-2xl p-5 flex items-center gap-4 fade-in" style="background:#e8f5e9; border:1px solid #a5d6a7;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 flex-shrink-0" style="color:#2e7d32;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
            <p class="font-semibold" style="color:#1b5e20;">{{ session('contact_success') }}</p>
        </div>
        @endif

        <div class="grid lg:grid-cols-5 gap-8 lg:gap-10">
            {{-- Info --}}
            <div class="lg:col-span-2 fade-in space-y-4 sm:space-y-5">
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border" style="border-color:var(--border);">
                    <h3 class="font-bold text-sm sm:text-base mb-4 sm:mb-5" style="color:var(--navy);">
                        <span data-ar>معلومات التواصل</span><span data-en class="hidden">Contact Information</span>
                    </h3>
                    <div class="space-y-4">
                        @foreach([
                            ['svg'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0z"/>', 'ar_label'=>'العنوان',      'en_label'=>'Address',      'ar_val'=>'الرياض، المملكة العربية السعودية', 'en_val'=>'Riyadh, Saudi Arabia'],
                            ['svg'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25z"/>', 'ar_label'=>'الهاتف',       'en_label'=>'Phone',        'ar_val'=>'+966 50 000 0000',                 'en_val'=>'+966 50 000 0000'],
                            ['svg'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>', 'ar_label'=>'البريد',        'en_label'=>'Email',        'ar_val'=>'info@tharwa.com',                  'en_val'=>'info@tharwa.com'],
                            ['svg'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>', 'ar_label'=>'أوقات العمل',  'en_label'=>'Working Hours','ar_val'=>'الأحد - الخميس، 8ص - 5م',          'en_val'=>'Sun–Thu, 8AM–5PM'],
                        ] as $c)
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(15,36,68,0.07);">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4" style="color:var(--navy-mid);">{!! $c['svg'] !!}</svg>
                            </div>
                            <div>
                                <p class="text-xs mb-0.5" style="color:var(--text-muted);">
                                    <span data-ar>{{ $c['ar_label'] }}</span><span data-en class="hidden">{{ $c['en_label'] }}</span>
                                </p>
                                <p class="text-sm font-medium" style="color:var(--text-dark);">
                                    <span data-ar>{{ $c['ar_val'] }}</span><span data-en class="hidden">{{ $c['en_val'] }}</span>
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border" style="border-color:var(--border);">
                    <p class="text-sm mb-4" style="color:var(--text-muted);">
                        <span data-ar>تابعنا على وسائل التواصل</span>
                        <span data-en class="hidden">Follow us on social media</span>
                    </p>
                    <div class="flex gap-3">
                        @foreach([
                        ['path'=>'<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.259 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>', 'fill'=>true],
                        ['path'=>'<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>', 'fill'=>true],
                        ['path'=>'<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>', 'fill'=>true],
                        ['path'=>'<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>', 'fill'=>true],
                    ] as $social)
                    <a href="#" class="w-9 h-9 rounded-xl flex items-center justify-center transition-all hover:scale-110"
                       style="background:var(--bg-section); border:1px solid var(--border); color:var(--navy-mid);">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">{!! $social['path'] !!}</svg>
                    </a>
                    @endforeach
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <div class="lg:col-span-3 fade-in">
                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border" style="border-color:var(--border);">
                    <h3 class="font-bold text-base sm:text-lg mb-5 sm:mb-6" style="color:var(--navy);">
                        <span data-ar>أرسل لنا رسالة</span><span data-en class="hidden">Send Us a Message</span>
                    </h3>
                    <form method="POST" action="{{ route('contact.store') }}" class="space-y-4 sm:space-y-5">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">
                                    <span data-ar>الاسم الكامل</span><span data-en class="hidden">Full Name</span>
                                    <span style="color:var(--gold);">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    placeholder-ar="أحمد محمد" placeholder-en="John Smith"
                                    class="input-field w-full rounded-xl px-4 py-3 text-sm @error('name') !border-red-400 @enderror">
                                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">
                                    <span data-ar>رقم الجوال</span><span data-en class="hidden">Phone Number</span>
                                </label>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                    placeholder-ar="05xxxxxxxx" placeholder-en="+966 5x xxx xxxx"
                                    class="input-field w-full rounded-xl px-4 py-3 text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">
                                <span data-ar>البريد الإلكتروني</span><span data-en class="hidden">Email Address</span>
                                <span style="color:var(--gold);">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="example@email.com"
                                class="input-field w-full rounded-xl px-4 py-3 text-sm @error('email') !border-red-400 @enderror">
                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">
                                <span data-ar>الموضوع</span><span data-en class="hidden">Subject</span>
                                <span style="color:var(--gold);">*</span>
                            </label>
                            <select name="subject" class="input-field w-full rounded-xl px-4 py-3 text-sm @error('subject') !border-red-400 @enderror">
                                <option value="" data-ar="-- اختر الموضوع --" data-en="-- Select Subject --">-- اختر الموضوع --</option>
                                @foreach([
                                    ['ar'=>'استفسار عام',       'en'=>'General Inquiry'],
                                    ['ar'=>'طلب عرض سعر',      'en'=>'Price Quote Request'],
                                    ['ar'=>'الإبلاغ عن مشكلة', 'en'=>'Report a Problem'],
                                    ['ar'=>'طلب شراكة',        'en'=>'Partnership Request'],
                                    ['ar'=>'أخرى',             'en'=>'Other'],
                                ] as $opt)
                                <option value="{{ $opt['ar'] }}"
                                    data-ar="{{ $opt['ar'] }}" data-en="{{ $opt['en'] }}"
                                    {{ old('subject') == $opt['ar'] ? 'selected' : '' }}>
                                    {{ $opt['ar'] }}
                                </option>
                                @endforeach
                            </select>
                            @error('subject')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">
                                <span data-ar>الرسالة</span><span data-en class="hidden">Message</span>
                                <span style="color:var(--gold);">*</span>
                            </label>
                            <textarea name="message" rows="4"
                                placeholder-ar="اكتب رسالتك هنا..." placeholder-en="Write your message here..."
                                class="input-field w-full rounded-xl px-4 py-3 text-sm resize-none @error('message') !border-red-400 @enderror">{{ old('message') }}</textarea>
                            @error('message')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="btn-navy w-full py-3.5 rounded-xl flex items-center justify-center gap-2 text-sm sm:text-base shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12zm0 0h7.5"/></svg>
                            <span data-ar>إرسال الرسالة</span><span data-en class="hidden">Send Message</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ======= FOOTER ======= --}}
<footer class="py-10 sm:py-12 border-t" style="border-color:rgba(255,255,255,0.06);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 sm:gap-10 mb-8 sm:mb-10">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-20 h-20 rounded-xl flex items-center justify-center" style="background:#fff;">
                     <img src="{{ asset('img/logo.png') }}" alt="Tharwa Logo" class="w-12">
                    </div>
                 
                </div>
                <p class="text-sm leading-relaxed text-white/45">
                    <span data-ar>منصة متكاملة لإدارة العقارات والمباني بأحدث التقنيات وأعلى معايير الجودة.</span>
                    <span data-en class="hidden">An integrated platform for property and building management with the latest technology and highest quality standards.</span>
                </p>
            </div>
            <div>
                <h4 class="font-bold text-sm mb-4 text-white/60 uppercase tracking-widest">
                    <span data-ar>روابط سريعة</span><span data-en class="hidden">Quick Links</span>
                </h4>
                <ul class="space-y-2">
                    @foreach([
                        ['ar'=>'الرئيسية','en'=>'Home','url'=>'#home'],
                        ['ar'=>'خدماتنا','en'=>'Services','url'=>'#services'],
                        ['ar'=>'العقارات','en'=>'Properties','url'=>route('properties.index')],
                        ['ar'=>'عن الشركة','en'=>'About','url'=>'#about'],
                        ['ar'=>'تواصل معنا','en'=>'Contact','url'=>'#contact'],
                    ] as $link)
                    <li>
                        <a href="{{ $link['url'] }}" class="text-sm text-white/45 hover:text-white transition">
                            <span data-ar>{{ $link['ar'] }}</span>
                            <span data-en class="hidden">{{ $link['en'] }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-sm mb-4 text-white/60 uppercase tracking-widest">
                    <span data-ar>الدخول للنظام</span><span data-en class="hidden">System Access</span>
                </h4>
                <p class="text-sm text-white/45 mb-4">
                    <span data-ar>هل أنت من فريق ثروة؟ سجل دخولك للوصول إلى لوحة التحكم.</span>
                    <span data-en class="hidden">Are you a Tharwa team member? Log in to access your dashboard.</span>
                </p>
                <a href="{{ route('login') }}" class="btn-gold px-5 sm:px-6 py-2.5 rounded-xl inline-flex items-center gap-2 text-sm shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
                    <span data-ar>تسجيل الدخول</span><span data-en class="hidden">Login</span>
                </a>
            </div>
        </div>
        <div class="border-t pt-6 flex flex-col sm:flex-row items-center justify-between gap-3" style="border-color:rgba(255,255,255,0.08);">
            <p class="text-xs text-white/30">
                © {{ date('Y') }} <span data-ar>شركة ثروة للعقارات. جميع الحقوق محفوظة.</span>
                <span data-en class="hidden">Tharwa Real Estate Co. All rights reserved.</span>
            </p>
            <div class="flex items-center gap-3">
                <a href="https://www.instagram.com/mohamed_izeldeen/" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="inline-flex items-center text-xs text-white/20 hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 h-4" fill="white">
                        <path d="M7.75 2h8.5A5.75 5.75 0 0 1 22 7.75v8.5A5.75 5.75 0 0 1 16.25 22h-8.5A5.75 5.75 0 0 1 2 16.25v-8.5A5.75 5.75 0 0 1 7.75 2Zm0 1.5A4.25 4.25 0 0 0 3.5 7.75v8.5a4.25 4.25 0 0 0 4.25 4.25h8.5a4.25 4.25 0 0 0 4.25-4.25v-8.5a4.25 4.25 0 0 0-4.25-4.25h-8.5Zm8.95 1.75a1.05 1.05 0 1 1 0 2.1 1.05 1.05 0 0 1 0-2.1ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Z"/>
                    </svg>
                </a>
                <a href="https://www.instagram.com/mohamed_izeldeen/" target="_blank" rel="noopener noreferrer" aria-label="Developer Instagram" class="inline-flex items-center text-xs text-white/20 hover:text-white transition">
                    <p class="text-xs text-white">
                        <span data-ar>تم التطوير بواسطة محمد عزالدين</span>
                        <span data-en class="hidden">Developed by Mohamed Izeldeen</span>
                    </p>
                </a>
            </div>
        </div>
    </div>
</footer>

<script>
// ====== LANGUAGE SYSTEM ======
const html    = document.getElementById('html-root');
const langBtn = document.getElementById('lang-btn');
const langBtnMobile = document.getElementById('lang-btn-mobile');

let currentLang = localStorage.getItem('tharwa_lang') || 'ar';
applyLang(currentLang, false);

function toggleLang() {
    currentLang = currentLang === 'ar' ? 'en' : 'ar';
    localStorage.setItem('tharwa_lang', currentLang);
    applyLang(currentLang, true);
}

function applyLang(lang, animate) {
    const isAr = lang === 'ar';

    // Direction & language attribute
    html.setAttribute('lang', lang);
    html.setAttribute('dir', isAr ? 'rtl' : 'ltr');

    // Toggle data-ar / data-en elements
    document.querySelectorAll('[data-ar]').forEach(el => el.classList.toggle('hidden', !isAr));
    document.querySelectorAll('[data-en]').forEach(el => el.classList.toggle('hidden', isAr));

    // Nav links text via data attributes
    document.querySelectorAll('[data-ar-text]').forEach(el => {
        el.textContent = isAr ? el.dataset.arText : el.dataset.enText;
    });

    // Placeholders
    document.querySelectorAll('[placeholder-ar]').forEach(el => {
        el.placeholder = isAr ? el.getAttribute('placeholder-ar') : el.getAttribute('placeholder-en');
    });

    // Select options
    document.querySelectorAll('select option[data-ar]').forEach(opt => {
        opt.textContent = isAr ? opt.dataset.ar : opt.dataset.en;
    });

    // Update lang toggle button labels
    if (langBtn) langBtn.textContent = isAr ? 'EN' : 'عر';
    if (langBtnMobile) langBtnMobile.textContent = isAr ? 'EN' : 'عر';

    // Update scrolled nav link colors
    const scrolled = window.scrollY > 60;
    document.querySelectorAll('.nav-link').forEach(l => {
        l.style.color = scrolled ? '#475569' : '';
    });
}

// ====== NAVBAR SCROLL ======
const navbar   = document.getElementById('navbar');
const logoText = document.getElementById('logo-text');

window.addEventListener('scroll', () => {
    const scrolled = window.scrollY > 60;
    navbar.classList.toggle('scrolled', scrolled);
    document.querySelectorAll('.nav-link').forEach(l => {
        l.style.color = scrolled ? '#475569' : '';
    });
    const hamburger = document.getElementById('hamburger');
    if (hamburger) {
        hamburger.querySelector('svg').style.stroke = scrolled ? '#0f2444' : 'white';
    }
});

// ====== MOBILE MENU ======
let menuOpen = false;
function toggleMobileMenu() {
    menuOpen = !menuOpen;
    const menu = document.getElementById('mobile-menu');
    const icon = document.getElementById('hamburger-icon');
    if (menuOpen) {
        menu.classList.remove('hidden');
        icon.setAttribute('d', 'M6 18L18 6M6 6l12 12');
    } else {
        menu.classList.add('hidden');
        icon.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
    }
}
function closeMobileMenu() {
    menuOpen = false;
    document.getElementById('mobile-menu').classList.add('hidden');
    document.getElementById('hamburger-icon').setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
}

// ====== FADE-IN ON SCROLL ======
const observer = new IntersectionObserver(
    entries => entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); }),
    { threshold: 0.1 }
);
document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
</script>
</body>
</html>
