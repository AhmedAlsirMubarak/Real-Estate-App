<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $property->name }} — ثروة</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;700;800;900&family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #0f2444; --navy-mid: #1a3a6b; --gold: #c9a84c; --gold-light: #e8c96e;
            --text-dark: #1a2437; --text-muted: #64748b; --border: #e2e8f0; --bg-section: #f8fafc;
        }
        * { font-family: {{ app()->getLocale() === 'ar' ? "'Cairo'" : "'Sora'" }}, sans-serif; }
        html { scroll-behavior: smooth; }
        body { background: var(--bg-section); color: var(--text-dark); overflow-x: hidden; }
        .navbar { background: var(--navy); }
        .btn-gold  { background: var(--gold); color: var(--navy); font-weight: 700; }
        .btn-gold:hover { background: var(--gold-light); }
        .btn-navy  { background: var(--navy); color: #fff; font-weight: 700; }
        .btn-navy:hover  { background: var(--navy-mid); }
        .btn-outline-navy { border: 1.5px solid var(--navy); color: var(--navy); }
        .btn-outline-navy:hover { background: var(--navy); color: #fff; }

        .stat-card { background: #fff; border: 1px solid var(--border); }
        .unit-row { transition: background .15s; }
        .unit-row:hover { background: #f0f4ff; }

        .badge-available    { background: #dcfce7; color: #166534; }
        .badge-rented       { background: #dbeafe; color: #1e40af; }
        .badge-sold         { background: #fef9c3; color: #92400e; }
        .badge-reserved     { background: #ede9fe; color: #5b21b6; }
        .badge-maintenance  { background: #fee2e2; color: #991b1b; }

        .badge-rent  { background: #dbeafe; color: #1e40af; }
        .badge-sale  { background: #fef9c3; color: #92400e; }
        .badge-both  { background: #ede9fe; color: #5b21b6; }

        .hero-bg {
            position: relative;
            background-size: cover;
            background-position: center;
            overflow: hidden;
        }
        .hero-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 86% 12%, rgba(201,168,76,.3), transparent 42%);
        }
        .hero-card {
            background: rgba(6, 18, 35, .34);
            border: 1px solid rgba(255,255,255,.2);
            backdrop-filter: blur(7px);
        }
        .price-box {
            background: rgba(255,255,255,.1);
            backdrop-filter: blur(9px);
            border: 1px solid rgba(255,255,255,.22);
        }
        .hero-gallery {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: 8px;
            margin-bottom: 14px;
        }
        .hero-gallery img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,.32);
        }
        .hero-gallery .hero-main-image {
            height: 100%;
            min-height: 120px;
        }
        .hero-side {
            display: grid;
            grid-template-rows: 1fr auto;
            gap: 8px;
        }
        .hero-mini-meta {
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,.22);
            padding: 8px;
            font-size: .68rem;
            color: #fff;
            background: rgba(8, 20, 36, .58);
            text-align: center;
            font-weight: 700;
        }
        .detail-cover {
            height: 145px;
            position: relative;
            overflow: hidden;
        }
        .detail-cover::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(8,20,36,.7), rgba(8,20,36,.12));
        }
        .detail-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .detail-chip {
            position: absolute;
            z-index: 2;
            right: 10px;
            bottom: 10px;
            background: rgba(255,255,255,.9);
            color: var(--navy);
            border-radius: 999px;
            font-size: .68rem;
            font-weight: 800;
            padding: 4px 10px;
        }

        @media (max-width: 640px) {
            .hero-gallery img { height: 95px; }
            .hero-gallery .hero-main-image { min-height: 95px; }
        }
    </style>
</head>
<body>

@php
    $isAr = app()->getLocale() === 'ar';
    $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    $currency = $isAr ? 'ريال' : 'OMR';
@endphp

{{-- NAVBAR --}}
<nav class="navbar sticky top-0 z-50 shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between h-14 sm:h-16">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center">
                <img src="{{ asset('img/logo.png') }}" alt="ثروة" class="h-8 w-auto">
            </div>
            <span class="text-white font-black text-lg hidden sm:block">ثروة</span>
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}"
               class="text-white/70 hover:text-white text-sm transition">
                {{ app()->getLocale() === 'ar' ? 'EN' : 'AR' }}
            </a>
            <a href="{{ route('properties.index') }}" class="text-white/70 hover:text-white text-sm transition flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/></svg>
                {{ $tr('العقارات', 'Properties') }}
            </a>
            <a href="{{ route('login') }}" class="btn-gold px-4 py-2 rounded-xl text-sm flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
                {{ $tr('دخول', 'Login') }}
            </a>
        </div>
    </div>
</nav>

@php
    $allImages  = $property->images;
    $heroImage  = $allImages->firstWhere('is_primary', true) ?? $allImages->first();
    $heroUrl    = $heroImage?->url();
    $galleryImages = $allImages->take(3);
    $thumbOne   = $galleryImages->get(1) ?? $galleryImages->get(0);
    $thumbTwo   = $galleryImages->get(2) ?? $galleryImages->get(0);
    $noImage    = $allImages->isEmpty();
@endphp

{{-- HERO HEADER --}}
<div class="hero-bg py-10 sm:py-14" style="background-image: linear-gradient(125deg, rgba(9,24,44,.9) 0%, rgba(20,58,102,.78) 56%, rgba(33,92,137,.76) 100%){{ $heroUrl ? ', url(\'' . $heroUrl . '\')' : '' }};">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-white/50 text-sm mb-6">
            <a href="{{ route('home') }}" class="hover:text-white transition">{{ $tr('الرئيسية', 'Home') }}</a>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 19.5-7.5-7.5 7.5-7.5"/></svg>
            <a href="{{ route('properties.index') }}" class="hover:text-white transition">{{ $tr('العقارات', 'Properties') }}</a>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 19.5-7.5-7.5 7.5-7.5"/></svg>
            <span class="text-white/80 truncate max-w-48">{{ $property->name }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            {{-- Main info --}}
            <div class="lg:col-span-2 hero-card rounded-2xl p-5 sm:p-7">
                <div class="flex flex-wrap gap-2 mb-3">
                    <span class="text-xs font-bold px-3 py-1.5 rounded-full
                        {{ $property->purpose === 'rent' ? 'badge-rent' : ($property->purpose === 'sale' ? 'badge-sale' : 'badge-both') }}">
                        {{ $property->purposeLabel() }}
                    </span>
                    <span class="text-xs font-bold px-3 py-1.5 rounded-full" style="background:rgba(201,168,76,.9); color:var(--navy);">
                        {{ $property->typeLabel() }}
                    </span>
                    <span class="text-xs font-mono text-white/40 px-2 py-1.5">#{{ $property->code }}</span>
                </div>

                <h1 class="text-3xl sm:text-4xl font-black text-white mb-3 leading-tight">{{ $property->name }}</h1>

                @if($property->city || $property->address)
                <div class="flex items-center gap-2 text-white/70 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0" style="color:var(--gold);"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0z"/></svg>
                    <span>{{ implode($isAr ? '، ' : ', ', array_filter([$property->city, $property->address])) }}</span>
                </div>
                @endif

                @if($property->description)
                <p class="text-white/60 text-sm leading-relaxed max-w-2xl">{{ $property->description }}</p>
                @endif
            </div>

            {{-- Price Box --}}
            <div class="price-box rounded-2xl p-5">
                @if(!$noImage)
                <div class="hero-gallery">
                    @if($thumbOne)
                    <img src="{{ $thumbOne->url() }}" loading="lazy" alt="{{ $property->name }}" class="hero-main-image">
                    @else
                    <div class="hero-main-image rounded-xl" style="background:rgba(255,255,255,.1);"></div>
                    @endif
                    <div class="hero-side">
                        @if($thumbTwo)
                        <img src="{{ $thumbTwo->url() }}" loading="lazy" alt="{{ $property->name }}">
                        @else
                        <div class="rounded-xl" style="background:rgba(255,255,255,.1);height:100%;"></div>
                        @endif
                        <div class="hero-mini-meta">
                            {{ $property->typeLabel() }}
                            <div class="text-white/70 mt-0.5">#{{ $property->code }}</div>
                        </div>
                    </div>
                </div>
                @endif

                @if($minRentPrice)
                <div class="mb-4">
                    <p class="text-white/50 text-xs mb-1">{{ $tr('الإيجار يبدأ من', 'Rent starts from') }}</p>
                    <p class="text-2xl font-black text-white">{{ number_format($minRentPrice) }} <span class="text-sm font-normal text-white/60">{{ $tr('ريال/سنة', 'OMR/year') }}</span></p>
                    @if($maxRentPrice && $maxRentPrice !== $minRentPrice)
                    <p class="text-white/50 text-xs mt-0.5">{{ $tr('حتى', 'Up to') }} {{ number_format($maxRentPrice) }} {{ $tr('ريال/سنة', 'OMR/year') }}</p>
                    @endif
                </div>
                @endif
                @if($minSalePrice)
                <div class="{{ $minRentPrice ? 'border-t border-white/20 pt-4' : '' }}">
                    <p class="text-white/50 text-xs mb-1">{{ $tr('البيع يبدأ من', 'Sale starts from') }}</p>
                    <p class="text-2xl font-black text-white">{{ number_format($minSalePrice) }} <span class="text-sm font-normal text-white/60">{{ $currency }}</span></p>
                    @if($maxSalePrice && $maxSalePrice !== $minSalePrice)
                    <p class="text-white/50 text-xs mt-0.5">{{ $tr('حتى', 'Up to') }} {{ number_format($maxSalePrice) }} {{ $currency }}</p>
                    @endif
                </div>
                @endif
                @if(!$minRentPrice && !$minSalePrice)
                <p class="text-white/60 text-sm">{{ $tr('لا توجد وحدات متاحة حالياً', 'No units are currently available') }}</p>
                @endif

                <a href="#units" class="btn-gold w-full mt-4 py-3 rounded-xl text-sm text-center flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25zM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25z"/></svg>
                    {{ $tr('عرض الوحدات', 'View Units') }}
                </a>
                <a href="{{ route('home') }}#contact" class="btn-outline-navy w-full mt-2 py-2.5 rounded-xl text-sm text-center flex items-center justify-center gap-2 bg-white/10 text-white border-white/30 hover:bg-white hover:text-navy">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25z"/></svg>
                    {{ $tr('تواصل معنا', 'Contact Us') }}
                </a>
            </div>
        </div>
    </div>
</div>

{{-- STATS BAR --}}
<div class="bg-white border-b" style="border-color:var(--border);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-2 sm:grid-cols-4 divide-x {{ $isAr ? 'divide-x-reverse' : '' }}" style="border-color:var(--border);">
            @php $stats = [
                ['label' => $tr('إجمالي الوحدات', 'Total Units'),   'value' => $property->units_count,           'unit' => $tr('وحدة', 'unit')],
                ['label' => $tr('الوحدات المتاحة', 'Available Units'),   'value' => $property->available_units_count,  'unit' => $tr('وحدة', 'unit')],
                ['label' => $tr('المساحة الكلية', 'Total Area'),     'value' => $property->total_area ? number_format($property->total_area) : '—', 'unit' => $property->total_area ? 'm²' : ''],
                ['label' => $tr('عدد الطوابق', 'Floors'),        'value' => $property->floors ?? '—',         'unit' => $property->floors ? $tr('طابق', 'floor') : ''],
            ]; @endphp
            @foreach($stats as $i => $stat)
            <div class="py-4 px-4 sm:px-6 text-center {{ $i > 0 ? 'border-r' : '' }}" style="{{ $i > 0 ? 'border-color:var(--border)' : '' }}">
                <p class="text-2xl sm:text-3xl font-black mb-0.5" style="color:var(--navy);">{{ $stat['value'] }}</p>
                <p class="text-xs" style="color:var(--text-muted);">{{ $stat['label'] }}{{ $stat['unit'] ? ' (' . $stat['unit'] . ')' : '' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- MAIN CONTENT --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- UNITS TABLE --}}
        <div class="lg:col-span-2" id="units">
            <div class="bg-white rounded-2xl border overflow-hidden shadow-sm" style="border-color:var(--border);">
                <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border);">
                    <h2 class="text-base font-bold" style="color:var(--navy);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline me-1.5" style="color:var(--gold);"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25zM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25z"/></svg>
                        {{ $tr('الوحدات', 'Units') }} ({{ $units->count() }})
                    </h2>
                    @if($property->available_units_count > 0)
                    <span class="badge-available text-xs font-bold px-3 py-1.5 rounded-full">{{ $property->available_units_count }} {{ $tr('متاح', 'available') }}</span>
                    @endif
                </div>

                @if($units->isEmpty())
                <div class="py-12 text-center" style="color:var(--text-muted);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-12 h-12 mx-auto mb-3 opacity-30"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M12 3.75h.008v.008H12V3.75z"/></svg>
                    <p class="text-sm">{{ $tr('لا توجد وحدات مضافة لهذا العقار', 'No units have been added to this property') }}</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs uppercase tracking-wider border-b" style="color:var(--text-muted); border-color:var(--border); background:var(--bg-section);">
                                <th class="px-4 py-3 text-start font-semibold">{{ $tr('الوحدة', 'Unit') }}</th>
                                <th class="px-4 py-3 text-start font-semibold hidden sm:table-cell">{{ $tr('النوع', 'Type') }}</th>
                                <th class="px-4 py-3 text-start font-semibold hidden md:table-cell">{{ $tr('الطابق', 'Floor') }}</th>
                                <th class="px-4 py-3 text-start font-semibold hidden sm:table-cell">{{ $tr('المساحة', 'Area') }}</th>
                                <th class="px-4 py-3 text-start font-semibold hidden lg:table-cell">{{ $tr('الغرف', 'Rooms') }}</th>
                                <th class="px-4 py-3 text-start font-semibold">{{ $tr('السعر', 'Price') }}</th>
                                <th class="px-4 py-3 text-start font-semibold">{{ $tr('الحالة', 'Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color:var(--border);">
                            @foreach($units as $unit)
                            <tr class="unit-row">
                                <td class="px-4 py-3">
                                    <span class="font-bold" style="color:var(--navy);">{{ $unit->unit_number }}</span>
                                </td>
                                <td class="px-4 py-3 hidden sm:table-cell">
                                    <span class="text-xs text-gray-600">{{ $unit->typeLabel() }}</span>
                                </td>
                                <td class="px-4 py-3 hidden md:table-cell">
                                    <span class="text-xs text-gray-500">{{ $unit->floor ?? '—' }}</span>
                                </td>
                                <td class="px-4 py-3 hidden sm:table-cell">
                                    <span class="text-xs text-gray-600">{{ $unit->area ? number_format($unit->area) . ' m²' : '—' }}</span>
                                </td>
                                <td class="px-4 py-3 hidden lg:table-cell">
                                    @if($unit->bedrooms || $unit->bathrooms)
                                    <span class="text-xs text-gray-500">
                                        {{ $unit->bedrooms ?? 0 }} {{ $tr('غ', 'BR') }} + {{ $unit->bathrooms ?? 0 }} {{ $tr('ح', 'BA') }}
                                    </span>
                                    @else
                                    <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-xs">
                                        @if($unit->rent_price)
                                        <div style="color:var(--navy);">{{ number_format($unit->rent_price) }} <span class="text-gray-400">{{ $tr('إيجار', 'Rent') }}</span></div>
                                        @endif
                                        @if($unit->sale_price)
                                        <div style="color:var(--navy);">{{ number_format($unit->sale_price) }} <span class="text-gray-400">{{ $tr('بيع', 'Sale') }}</span></div>
                                        @endif
                                        @if(!$unit->rent_price && !$unit->sale_price)
                                        <span class="text-gray-400">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @php $statusClasses = [
                                        'available'   => 'badge-available',
                                        'rented'      => 'badge-rented',
                                        'sold'        => 'badge-sold',
                                        'reserved'    => 'badge-reserved',
                                        'maintenance' => 'badge-maintenance',
                                    ][$unit->status] ?? 'bg-gray-100 text-gray-600'; @endphp
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $statusClasses }}">
                                        {{ $unit->statusLabel() }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            {{-- Unit status summary --}}
            @if($units->isNotEmpty())
            <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach(['available' => ['label' => $tr('متاحة', 'Available'), 'class' => 'badge-available'], 'rented' => ['label' => $tr('مؤجرة', 'Rented'), 'class' => 'badge-rented'], 'sold' => ['label' => $tr('مباعة', 'Sold'), 'class' => 'badge-sold'], 'reserved' => ['label' => $tr('محجوزة', 'Reserved'), 'class' => 'badge-reserved']] as $status => $meta)
                @php $count = $units->where('status', $status)->count(); @endphp
                @if($count > 0)
                <div class="bg-white rounded-xl border px-4 py-3 text-center shadow-sm" style="border-color:var(--border);">
                    <p class="text-xl font-black mb-0.5" style="color:var(--navy);">{{ $count }}</p>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $meta['class'] }}">{{ $meta['label'] }}</span>
                </div>
                @endif
                @endforeach
            </div>
            @endif
        </div>

        {{-- SIDEBAR: Property Details + Contact --}}
        <div class="space-y-5">

            {{-- Property details card --}}
            <div class="bg-white rounded-2xl border shadow-sm overflow-hidden" style="border-color:var(--border);">
                <div class="detail-cover">
                    @if($heroUrl)
                    <img src="{{ $heroUrl }}" loading="lazy" alt="{{ $property->name }}">
                    @else
                    <div class="w-full h-full" style="background: linear-gradient(135deg, #0f2444 0%, #1a3a6b 100%);"></div>
                    @endif
                    <span class="detail-chip">{{ $property->purposeLabel() }}</span>
                </div>
                <div class="px-5 py-4 border-b" style="border-color:var(--border); background:var(--bg-section);">
                    <h3 class="text-sm font-bold" style="color:var(--navy);">{{ $tr('تفاصيل العقار', 'Property Details') }}</h3>
                </div>
                <div class="divide-y" style="border-color:var(--border);">
                    @php $details = array_filter([
                        $tr('الكود', 'Code')          => $property->code,
                        $tr('النوع', 'Type')          => $property->typeLabel(),
                        $tr('الغرض', 'Purpose')         => $property->purposeLabel(),
                        $tr('المدينة', 'City')        => $property->city,
                        $tr('العنوان', 'Address')        => $property->address,
                        $tr('المساحة الكلية', 'Total Area') => $property->total_area ? number_format($property->total_area) . ' m²' : null,
                        $tr('عدد الطوابق', 'Floors')   => $property->floors,
                        $tr('غرف النوم', 'Bedrooms')     => $property->bedrooms,
                        $tr('الحمامات', 'Bathrooms')      => $property->bathrooms,
                    ]); @endphp
                    @foreach($details as $label => $value)
                    <div class="px-5 py-3 flex items-center justify-between gap-2">
                        <span class="text-xs" style="color:var(--text-muted);">{{ $label }}</span>
                        <span class="text-xs font-semibold text-end" style="color:var(--text-dark);">{{ $value }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Contact CTA --}}
            <div class="rounded-2xl overflow-hidden shadow-sm" style="background:var(--navy);">
                <div class="px-5 py-6 text-center">
                    <div class="w-12 h-12 rounded-xl mx-auto mb-3 flex items-center justify-center" style="background:rgba(201,168,76,.2);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6" style="color:var(--gold);"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25z"/></svg>
                    </div>
                    <h3 class="text-white font-bold text-sm mb-1">{{ $tr('هل تريد الاستفسار؟', 'Need more information?') }}</h3>
                    <p class="text-white/60 text-xs mb-4">{{ $tr('تواصل معنا الآن وسيردّ عليك فريقنا', 'Contact us now and our team will get back to you') }}</p>
                    <a href="{{ route('home') }}#contact" class="btn-gold w-full py-3 rounded-xl text-sm flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        {{ $tr('أرسل استفساراً', 'Send Inquiry') }}
                    </a>
                </div>
            </div>

            {{-- Back button --}}
            <a href="{{ route('properties.index') }}" class="flex items-center gap-2 text-sm py-3 px-4 rounded-xl bg-white border shadow-sm hover:shadow transition" style="border-color:var(--border); color:var(--text-muted);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/></svg>
                {{ $tr('العودة إلى جميع العقارات', 'Back to all properties') }}
            </a>
        </div>
    </div>
</div>

{{-- Footer --}}
<footer class="mt-10 py-8 border-t text-center text-xs" style="border-color:var(--border); color:var(--text-muted);">
    © {{ date('Y') }} {{ $tr('شركة ثروة للعقارات — جميع الحقوق محفوظة', 'Tharwa Real Estate — All rights reserved') }}
</footer>

</body>
</html>
