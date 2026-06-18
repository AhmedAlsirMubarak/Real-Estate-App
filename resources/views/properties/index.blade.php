<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    @include('_partials.gtm-head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    @php
      $metaTitle = app()->getLocale() === 'ar'
          ? 'العقارات المتاحة للبيع والإيجار في عُمان | ثروة للعقارات'
          : 'Properties for Sale & Rent in Oman | Tharwa Real Estate';
      $metaDescription = app()->getLocale() === 'ar'
          ? 'تصفح أحدث العقارات المتاحة للبيع والإيجار في عُمان — شقق، فلل، مزارع وشاليهات. فلترة بالسعر والمدينة وعدد الغرف للعثور على عقارك المثالي.'
          : 'Browse the latest properties for sale and rent in Oman — apartments, villas, farms, and chalets. Filter by price, city, and bedrooms to find your ideal property.';
    @endphp
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('img/logo.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;700;800;900&family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #0f2444; --navy-mid: #1a3a6b; --gold: #c9a84c; --gold-light: #e8c96e;
            --text-dark: #1a2437; --text-muted: #64748b; --border: #e2e8f0; --bg-section: #f8fafc;
        }
        * { font-family: {{ app()->getLocale() === 'ar' ? "'Cairo'" : "'Sora'" }}, sans-serif; }
        html { scroll-behavior: smooth; }
        body { background: var(--bg-section); color: var(--text-dark); overflow-x: clip; }

        .navbar { background: var(--navy); }
        .page-hero {
            position: relative;
            background:
                linear-gradient(125deg, rgba(9,24,44,.92) 0%, rgba(24,62,108,.84) 56%, rgba(41,102,150,.82) 100%),
                url('https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&w=1800&q=80');
            background-size: cover;
            background-position: center;
            overflow: hidden;
        }
        .page-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 85% 12%, rgba(201,168,76,.28), transparent 42%);
            pointer-events: none;
        }
        .stat-pill {
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.22);
            color: #fff;
            backdrop-filter: blur(5px);
            border-radius: 999px;
            padding: 7px 12px;
            font-size: .72rem;
            font-weight: 700;
        }
        .hero-filter-btn {
            border: 1px solid rgba(255,255,255,.35);
            color: #fff;
            background: rgba(255,255,255,.08);
        }
        .hero-filter-btn:hover {
            border-color: var(--gold);
            color: var(--gold-light);
        }

        .filter-card { background: #fff; border: 1px solid var(--border); }
        .filter-input { border: 1px solid var(--border); background: #fff; color: var(--text-dark);
            transition: border-color .2s, box-shadow .2s; }
        .filter-input:focus { outline: none; border-color: var(--navy-mid);
            box-shadow: 0 0 0 3px rgba(26,58,107,.1); }

        .prop-card {
            background: #fff;
            border: 1px solid var(--border);
            transition: box-shadow .3s, border-color .3s;
        }
        .prop-card:hover {
            box-shadow: 0 8px 32px rgba(15,36,68,.12);
            border-color: rgba(201,168,76,.4);
        }
        .prop-card img { transition: transform .5s ease; }
        .prop-card:hover img { transform: scale(1.05); }

        .badge-rent   { background: #dbeafe; color: #1e40af; }
        .badge-sale   { background: #fef9c3; color: #92400e; }
        .badge-both   { background: #ede9fe; color: #5b21b6; }
        .badge-active { background: #dcfce7; color: #166534; }

        .btn-gold  { background: var(--gold); color: var(--navy); font-weight: 700; }
        .btn-gold:hover { background: var(--gold-light); }
        .btn-navy  { background: var(--navy); color: #fff; font-weight: 700; }
        .btn-navy:hover  { background: var(--navy-mid); }
        .btn-outline { border: 1.5px solid var(--border); color: var(--text-muted); }
        .btn-outline:hover { border-color: var(--navy-mid); color: var(--navy); }

        .pagination-link { border: 1px solid var(--border); background: #fff; color: var(--text-muted);
            transition: all .2s; }
        .pagination-link:hover, .pagination-link.active { background: var(--navy); color: #fff; border-color: var(--navy); }

        #filter-sidebar { transition: transform .3s ease; }

        /* Property card responsive layout */
        .prop-card-gallery { width: 100%; height: 11rem; flex-shrink: 0; }
        .prop-card-side-imgs { display: none; }
        @media (min-width: 640px) {
            .prop-card { height: 195px; }
            .prop-card-gallery { width: 52%; height: 100%; }
            .prop-card-side-imgs { display: flex; }
        }
    </style>
</head>
<body>
@include('_partials.gtm-body')

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $propertiesPaginator */
    $propertiesPaginator = $properties;
    $isAr = app()->getLocale() === 'ar';
    $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    $currency = $isAr ? 'ريال' : 'OMR';
@endphp

@include('_partials.public-nav')

{{-- PAGE HEADER --}}
<div class="page-hero border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-9 sm:py-12 relative z-10">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-4xl font-black text-white">{{ $tr('العقارات المتاحة', 'Available Properties') }}</h1>
                <p class="text-sm mt-2 text-white/70">
                    {{ $propertiesPaginator->total() }} {{ $tr('عقار متاح في محفظتنا', 'properties available in our portfolio') }}
                    @if(request()->hasAny(['purpose','type','city','bedrooms','min_price','max_price','min_area']))
                        <span class="text-yellow-200 font-semibold">({{ $tr('نتائج مفلترة', 'Filtered results') }})</span>
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="stat-pill">{{ $cities->count() }} {{ $tr('مدينة', 'Cities') }}</span>
                <span class="stat-pill">{{ $propertiesPaginator->total() }} {{ $tr('عقار', 'Properties') }}</span>
                <button onclick="toggleFilters()" class="sm:hidden hero-filter-btn px-4 py-2 rounded-xl text-sm flex items-center gap-2 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/></svg>
                    {{ $tr('تصفية', 'Filter') }}
                </button>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <div class="flex gap-7 items-start">

        {{-- ===== FILTER SIDEBAR ===== --}}
        <aside id="filter-sidebar"
               class="w-72 flex-shrink-0 hidden sm:block sticky top-20">
            <form method="GET" action="{{ route('properties.index') }}" id="filter-form">
                <div class="filter-card rounded-2xl p-5 shadow-sm mb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-base" style="color:var(--navy);">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 inline me-1" style="color:var(--gold);"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/></svg>
                            {{ $tr('خيارات البحث', 'Search Options') }}
                        </h3>
                        @if(request()->hasAny(['purpose','type','city','bedrooms','min_price','max_price','min_area','sort']))
                            <a href="{{ route('properties.index') }}" class="text-xs text-red-500 hover:text-red-700">{{ $tr('مسح الكل', 'Clear all') }}</a>
                        @endif
                    </div>

                    {{-- Purpose --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">{{ $tr('الغرض', 'Purpose') }}</label>
                        <div class="grid grid-cols-3 gap-1.5">
                            @foreach(['' => $tr('الكل', 'All'), 'rent' => $tr('إيجار', 'Rent'), 'sale' => $tr('بيع', 'Sale')] as $val => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="purpose" value="{{ $val }}" class="sr-only peer"
                                    {{ request('purpose', '') === $val ? 'checked' : '' }}>
                                <span class="block text-center text-xs py-2 rounded-lg border peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 peer-checked:font-bold border-gray-200 text-gray-500 transition">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Type --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">{{ $tr('نوع العقار', 'Property Type') }}</label>
                        <select name="type" class="filter-input w-full rounded-xl px-3 py-2.5 text-sm">
                            <option value="">{{ $tr('جميع الأنواع', 'All types') }}</option>
                            @foreach(['apartment_building' => $tr('عمارة سكنية', 'Apartment Building'), 'flat' => $tr('شقة', 'Flat'), 'villa' => $tr('فيلا', 'Villa'), 'farm' => $tr('مزرعة', 'Farm'), 'chalet' => $tr('شاليه', 'Chalet'), 'land' => $tr('أرض', 'Land')] as $val => $label)
                            <option value="{{ $val }}" {{ request('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- City --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">{{ $tr('المدينة', 'City') }}</label>
                        <select name="city" class="filter-input w-full rounded-xl px-3 py-2.5 text-sm">
                            <option value="">{{ $tr('جميع المدن', 'All cities') }}</option>
                            @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Bedrooms --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">{{ $tr('الغرف (الحد الأدنى)', 'Bedrooms (minimum)') }}</label>
                        <div class="grid grid-cols-5 gap-1">
                            @foreach(['' => $tr('أي', 'Any'), '1' => '1', '2' => '2', '3' => '3', '4' => '4+'] as $val => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="bedrooms" value="{{ $val }}" class="sr-only peer"
                                    {{ request('bedrooms', '') === $val ? 'checked' : '' }}>
                                <span class="block text-center text-xs py-2 rounded-lg border peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 peer-checked:font-bold border-gray-200 text-gray-500 transition">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Price Range --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">{{ $tr('نطاق السعر (ريال)', 'Price Range (OMR)') }}</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}"
                                placeholder="{{ $tr('من', 'Min') }}" min="0"
                                class="filter-input w-full rounded-xl px-3 py-2 text-sm">
                            <input type="number" name="max_price" value="{{ request('max_price') }}"
                                placeholder="{{ $tr('إلى', 'Max') }}" min="0"
                                class="filter-input w-full rounded-xl px-3 py-2 text-sm">
                        </div>
                    </div>

                    {{-- Min Area --}}
                    <div class="mb-5">
                        <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">{{ $tr('المساحة الدنيا (م²)', 'Minimum Area (m²)') }}</label>
                        <input type="number" name="min_area" value="{{ request('min_area') }}"
                            placeholder="{{ $tr('مثال: 100', 'Example: 100') }}" min="0"
                            class="filter-input w-full rounded-xl px-3 py-2.5 text-sm">
                    </div>

                    {{-- Sort --}}
                    <div class="mb-5">
                        <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">{{ $tr('الترتيب', 'Sort') }}</label>
                        <select name="sort" class="filter-input w-full rounded-xl px-3 py-2.5 text-sm">
                            <option value="newest"    {{ request('sort','newest') === 'newest'    ? 'selected' : '' }}>{{ $tr('الأحدث', 'Newest') }}</option>
                            <option value="oldest"    {{ request('sort') === 'oldest'    ? 'selected' : '' }}>{{ $tr('الأقدم', 'Oldest') }}</option>
                            <option value="area_desc" {{ request('sort') === 'area_desc' ? 'selected' : '' }}>{{ $tr('المساحة: الأكبر', 'Area: Largest') }}</option>
                            <option value="area_asc"  {{ request('sort') === 'area_asc'  ? 'selected' : '' }}>{{ $tr('المساحة: الأصغر', 'Area: Smallest') }}</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-navy w-full py-3 rounded-xl text-sm flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 15.803 7.5 7.5 0 0 0 15.803 15.803z"/></svg>
                        {{ $tr('بحث', 'Search') }}
                    </button>
                </div>
            </form>

            {{-- Quick Stats --}}
            <div class="filter-card rounded-2xl p-4 shadow-sm text-center">
                <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:var(--text-muted);">{{ $tr('إحصائيات سريعة', 'Quick Stats') }}</p>
                <p class="text-3xl font-black mb-1" style="color:var(--navy);">{{ $propertiesPaginator->total() }}</p>
                <p class="text-xs" style="color:var(--text-muted);">{{ $tr('عقار متاح', 'available properties') }}</p>
            </div>
        </aside>

        {{-- Mobile filter overlay --}}
        <div id="mobile-filter-overlay"
             class="fixed inset-0 bg-black/50 z-40 hidden sm:hidden"
             onclick="toggleFilters()"></div>
        <div id="mobile-filter-panel"
             class="fixed top-0 {{ $isAr ? 'right-0' : 'left-0' }} h-full w-80 max-w-full bg-white z-50 overflow-y-auto shadow-2xl p-5 hidden sm:hidden">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-base" style="color:var(--navy);">{{ $tr('خيارات البحث', 'Search Options') }}</h3>
                <button onclick="toggleFilters()" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
            {{-- Same form, cloned for mobile --}}
            <form method="GET" action="{{ route('properties.index') }}">
                {{-- Purpose --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">{{ $tr('الغرض', 'Purpose') }}</label>
                    <div class="grid grid-cols-3 gap-1.5">
                        @foreach(['' => $tr('الكل', 'All'), 'rent' => $tr('إيجار', 'Rent'), 'sale' => $tr('بيع', 'Sale')] as $val => $label)
                        <label class="cursor-pointer">
                            <input type="radio" name="purpose" value="{{ $val }}" class="sr-only peer"
                                {{ request('purpose', '') === $val ? 'checked' : '' }}>
                            <span class="block text-center text-xs py-2 rounded-lg border peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 peer-checked:font-bold border-gray-200 text-gray-500 transition">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">{{ $tr('نوع العقار', 'Property Type') }}</label>
                    <select name="type" class="filter-input w-full rounded-xl px-3 py-2.5 text-sm">
                        <option value="">{{ $tr('جميع الأنواع', 'All types') }}</option>
                        @foreach(['apartment_building' => $tr('عمارة سكنية', 'Apartment Building'), 'flat' => $tr('شقة', 'Flat'), 'villa' => $tr('فيلا', 'Villa'), 'farm' => $tr('مزرعة', 'Farm'), 'chalet' => $tr('شاليه', 'Chalet'), 'land' => $tr('أرض', 'Land')] as $val => $label)
                        <option value="{{ $val }}" {{ request('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">{{ $tr('المدينة', 'City') }}</label>
                    <select name="city" class="filter-input w-full rounded-xl px-3 py-2.5 text-sm">
                        <option value="">{{ $tr('جميع المدن', 'All cities') }}</option>
                        @foreach($cities as $city)
                        <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">{{ $tr('الغرف (الحد الأدنى)', 'Bedrooms (minimum)') }}</label>
                    <div class="grid grid-cols-5 gap-1">
                        @foreach(['' => $tr('أي', 'Any'), '1' => '1', '2' => '2', '3' => '3', '4' => '4+'] as $val => $label)
                        <label class="cursor-pointer">
                            <input type="radio" name="bedrooms" value="{{ $val }}" class="sr-only peer"
                                {{ request('bedrooms', '') === $val ? 'checked' : '' }}>
                            <span class="block text-center text-xs py-2 rounded-lg border peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 peer-checked:font-bold border-gray-200 text-gray-500 transition">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">{{ $tr('نطاق السعر (ريال)', 'Price Range (OMR)') }}</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="{{ $tr('من', 'Min') }}" min="0" class="filter-input w-full rounded-xl px-3 py-2 text-sm">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="{{ $tr('إلى', 'Max') }}" min="0" class="filter-input w-full rounded-xl px-3 py-2 text-sm">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">{{ $tr('المساحة الدنيا (م²)', 'Minimum Area (m²)') }}</label>
                    <input type="number" name="min_area" value="{{ request('min_area') }}" placeholder="{{ $tr('مثال: 100', 'Example: 100') }}" min="0" class="filter-input w-full rounded-xl px-3 py-2.5 text-sm">
                </div>
                <div class="mb-5">
                    <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">{{ $tr('الترتيب', 'Sort') }}</label>
                    <select name="sort" class="filter-input w-full rounded-xl px-3 py-2.5 text-sm">
                        <option value="newest" {{ request('sort','newest') === 'newest' ? 'selected' : '' }}>{{ $tr('الأحدث', 'Newest') }}</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>{{ $tr('الأقدم', 'Oldest') }}</option>
                        <option value="area_desc" {{ request('sort') === 'area_desc' ? 'selected' : '' }}>{{ $tr('المساحة: الأكبر', 'Area: Largest') }}</option>
                        <option value="area_asc" {{ request('sort') === 'area_asc' ? 'selected' : '' }}>{{ $tr('المساحة: الأصغر', 'Area: Smallest') }}</option>
                    </select>
                </div>
                <button type="submit" class="btn-navy w-full py-3 rounded-xl text-sm flex items-center justify-center gap-2 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 15.803 7.5 7.5 0 0 0 15.803 15.803z"/></svg>
                    {{ $tr('بحث', 'Search') }}
                </button>
                @if(request()->hasAny(['purpose','type','city','bedrooms','min_price','max_price','min_area','sort']))
                    <a href="{{ route('properties.index') }}" class="block text-center text-xs text-red-500 hover:text-red-700 py-2">{{ $tr('مسح الفلاتر', 'Clear filters') }}</a>
                @endif
            </form>
        </div>

        {{-- ===== PROPERTIES GRID ===== --}}
        <div class="flex-1 min-w-0">

            {{-- Active filters chips --}}
            @if(request()->hasAny(['purpose','type','city','bedrooms','min_price','max_price','min_area']))
            <div class="flex flex-wrap gap-2 mb-5">
                @if(request('purpose'))
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $tr('الغرض', 'Purpose') }}: {{ ['rent'=>$tr('إيجار','Rent'),'sale'=>$tr('بيع','Sale'),'both'=>$tr('إيجار وبيع','Rent & Sale')][request('purpose')] ?? request('purpose') }}
                        <a href="{{ request()->fullUrlWithQuery(['purpose'=>null]) }}" class="hover:text-blue-900">✕</a>
                    </span>
                @endif
                @if(request('type'))
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        {{ $tr('النوع', 'Type') }}: {{ ['apartment_building'=>$tr('عمارة','Building'),'flat'=>$tr('شقة','Flat'),'villa'=>$tr('فيلا','Villa'),'farm'=>$tr('مزرعة','Farm'),'chalet'=>$tr('شاليه','Chalet'),'land'=>$tr('أرض','Land')][request('type')] ?? request('type') }}
                        <a href="{{ request()->fullUrlWithQuery(['type'=>null]) }}" class="hover:text-purple-900">✕</a>
                    </span>
                @endif
                @if(request('city'))
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ $tr('المدينة', 'City') }}: {{ request('city') }}
                        <a href="{{ request()->fullUrlWithQuery(['city'=>null]) }}" class="hover:text-green-900">✕</a>
                    </span>
                @endif
                @if(request('bedrooms'))
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        {{ $tr('غرف', 'Bedrooms') }}: {{ request('bedrooms') }}+
                        <a href="{{ request()->fullUrlWithQuery(['bedrooms'=>null]) }}" class="hover:text-orange-900">✕</a>
                    </span>
                @endif
                @if(request('min_price') || request('max_price'))
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        {{ $tr('السعر', 'Price') }}: {{ request('min_price') ? number_format(request('min_price')) : '0' }} — {{ request('max_price') ? number_format(request('max_price')) : $tr('غير محدد', 'Not set') }} {{ $currency }}
                        <a href="{{ request()->fullUrlWithQuery(['min_price'=>null,'max_price'=>null]) }}" class="hover:text-yellow-900">✕</a>
                    </span>
                @endif
                @if(request('min_area'))
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ $tr('مساحة', 'Area') }}: {{ request('min_area') }}m²+
                        <a href="{{ request()->fullUrlWithQuery(['min_area'=>null]) }}" class="hover:text-gray-900">✕</a>
                    </span>
                @endif
            </div>
            @endif

            @php $waPhone = preg_replace('/\D/', '', $contactPhone ?? ''); @endphp
            @if($propertiesPaginator->isEmpty())
            {{-- Empty State --}}
            <div class="bg-white rounded-2xl border p-12 text-center" style="border-color:var(--border);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-16 h-16 mx-auto mb-4" style="color:var(--border);"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 15.803 7.5 7.5 0 0 0 15.803 15.803z"/></svg>
                <h3 class="text-lg font-bold mb-2" style="color:var(--navy);">{{ $tr('لا توجد نتائج', 'No results found') }}</h3>
                <p class="text-sm mb-5" style="color:var(--text-muted);">{{ $tr('حاول تغيير معايير البحث أو مسح الفلاتر', 'Try changing your filters or clearing search criteria') }}</p>
                <a href="{{ route('properties.index') }}" class="btn-navy px-6 py-2.5 rounded-xl text-sm inline-block">{{ $tr('مسح الفلاتر', 'Clear filters') }}</a>
            </div>
            @else
            {{-- Horizontal property list --}}
            <div class="space-y-4">
                @foreach($propertiesPaginator as $property)
                @php
                    $cardImages = $property->images;
                    $img1 = $cardImages->get(0);
                    $img2 = $cardImages->get(1);
                    $img3 = $cardImages->get(2);
                    $minRent = $property->units->whereNotNull('rent_price')->min('rent_price');
                    $minSale = $property->units->whereNotNull('sale_price')->min('sale_price');
                    $price   = $minSale ?? $minRent;
                    $maxBeds = $property->units->max('bedrooms');
                    $maxBath = $property->units->max('bathrooms');
                    $maxArea = $property->units->max('area') ?? $property->total_area;
                @endphp
                <div class="prop-card bg-white rounded-2xl border overflow-hidden flex flex-col sm:flex-row" style="border-color:var(--border);">


                    {{-- ── Gallery ── --}}
                    <div class="prop-card-gallery relative flex overflow-hidden">
                        {{-- Main large image --}}
                        <div class="relative overflow-hidden" style="{{ ($img2 || $img3) ? 'flex:3' : 'flex:1' }}">
                            @if($img1)
                            <img src="{{ $img1->url() }}" loading="lazy" alt="{{ $property->name }}"
                                 style="width:100%;height:100%;object-fit:cover;transition:transform .5s">
                            @else
                            <div style="width:100%;height:100%;background:linear-gradient(135deg,#0f2444,#1a3a6b);display:flex;align-items:center;justify-content:center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="width:3rem;height:3rem;opacity:.18;color:#fff"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                            </div>
                            @endif
                        </div>

                        {{-- Side: 2 stacked images --}}
                        @if($img2 || $img3)
                        <div class="prop-card-side-imgs flex-col gap-0.5 flex-shrink-0" style="flex:2;margin-{{ $isAr ? 'right' : 'left' }}:2px">
                            <div class="overflow-hidden" style="flex:1;min-height:0">
                                @if($img2)
                                <img src="{{ $img2->url() }}" loading="lazy" alt="{{ $property->name . ' - 2' }}"
                                     style="width:100%;height:100%;object-fit:cover">
                                @else
                                <div style="width:100%;height:100%;background:#dde3ec"></div>
                                @endif
                            </div>
                            <div class="overflow-hidden" style="flex:1;min-height:0">
                                @if($img3)
                                <img src="{{ $img3->url() }}" loading="lazy" alt="{{ $property->name . ' - 3' }}"
                                     style="width:100%;height:100%;object-fit:cover">
                                @else
                                <div style="width:100%;height:100%;background:#dde3ec"></div>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Bottom-left icons --}}
                        <div class="absolute bottom-2.5 {{ $isAr ? 'right-2.5' : 'left-2.5' }} flex items-center gap-1.5 z-10">
                            <a href="{{ route('properties.show', $property) }}"
                               onclick="event.stopPropagation()"
                               style="width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.88);display:flex;align-items:center;justify-content:center;transition:background .2s"
                               onmouseover="this.style.background='#fff'" onmouseout="this.style.background='rgba(255,255,255,.88)'">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;color:#0f2444"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
                            </a>
                            @if($cardImages->count() > 1)
                            <span style="background:rgba(0,0,0,.5);color:#fff;font-size:.65rem;font-weight:700;padding:3px 8px;border-radius:999px">
                                {{ $cardImages->count() }} {{ $tr('صور', 'photos') }}
                            </span>
                            @endif
                        </div>

                        {{-- Purpose badge top-left --}}
                        <div class="absolute top-2.5 {{ $isAr ? 'right-2.5' : 'left-2.5' }} z-10">
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full
                                {{ $property->purpose === 'rent' ? 'badge-rent' : ($property->purpose === 'sale' ? 'badge-sale' : 'badge-both') }}">
                                {{ $property->purposeLabel() }}
                            </span>
                        </div>
                    </div>

                    {{-- ── Details ── --}}
                    <div class="flex-1 flex flex-col px-4 py-3 min-w-0">
                        {{-- Title --}}
                        <h3 class="font-bold text-base leading-snug mb-1 truncate" style="color:var(--navy)">
                            {{ $isAr ? ($property->name_ar ?: $property->name) : ($property->name_en ?: $property->name) }}
                        </h3>

                        {{-- Price --}}
                        @if($price)
                        <p class="font-black mb-1.5" style="font-size:1.05rem;color:var(--text-dark)">
                            {{ number_format($price) }}
                            <span style="font-size:.78rem;font-weight:600;color:var(--text-muted)">{{ $currency }}</span>
                        </p>
                        @endif

                        {{-- Specs row --}}
                        <div class="flex items-center gap-4 mb-2" style="font-size:.82rem;color:var(--text-muted)">
                            @if($maxBeds)
                            <span class="flex items-center gap-1">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:15px;height:15px;color:var(--gold)"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0-6.75H8.25m3.75 0H15.75M3.75 7.5h16.5"/></svg>
                                {{ $maxBeds }}
                            </span>
                            @endif
                            @if($maxBath)
                            <span class="flex items-center gap-1">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:15px;height:15px;color:var(--gold)"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                {{ $maxBath }}
                            </span>
                            @endif
                            @if($maxArea)
                            <span class="flex items-center gap-1">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:15px;height:15px;color:var(--gold)"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
                                {{ number_format($maxArea) }} م²
                            </span>
                            @endif
                        </div>

                        {{-- Type label --}}
                        <p style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text-muted)">
                            {{ $property->typeLabel() }}
                        </p>

                        {{-- Action buttons — pushed to bottom --}}
                        <div class="flex items-center flex-wrap gap-2 mt-auto sm:justify-end">
                            <a href="{{ route('properties.show', $property) }}"
                               style="display:flex;align-items:center;gap:5px;padding:7px 13px;background:var(--navy);border:1.5px solid var(--navy);border-radius:8px;font-size:.75rem;font-weight:700;color:#fff;text-decoration:none;transition:all .2s"
                               onmouseover="this.style.background='#1a3a6b';this.style.borderColor='#1a3a6b'"
                               onmouseout="this.style.background='var(--navy)';this.style.borderColor='var(--navy)'">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg>
                                {{ $tr('تفاصيل', 'Details') }}
                            </a>
                            @if($contactPhone)
                            <a href="tel:{{ $waPhone }}"
                               style="display:flex;align-items:center;gap:5px;padding:7px 13px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.75rem;font-weight:700;color:var(--text-dark);text-decoration:none;transition:all .2s"
                               onmouseover="this.style.borderColor='var(--gold)';this.style.color='var(--navy)'"
                               onmouseout="this.style.borderColor='#e2e8f0';this.style.color='var(--text-dark)'">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25z"/></svg>
                                {{ $tr('اتصال', 'Call') }}
                            </a>
                            @endif
                            @if($waPhone)
                            <a href="https://api.whatsapp.com/send?phone={{ $waPhone }}" target="_blank"
                               style="display:flex;align-items:center;gap:5px;padding:7px 13px;border:1.5px solid #25d366;border-radius:8px;font-size:.75rem;font-weight:700;color:#25d366;text-decoration:none;transition:all .2s"
                               onmouseover="this.style.background='#25d366';this.style.color='#fff'"
                               onmouseout="this.style.background='';this.style.color='#25d366'">
                                <svg viewBox="0 0 24 24" fill="currentColor" style="width:13px;height:13px"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                                WhatsApp
                            </a>
                            @endif
                        </div>
                    </div>

                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($propertiesPaginator->hasPages())
            <div class="mt-8">
                <div class="flex justify-center">
                    {{ $propertiesPaginator->onEachSide(1)->links() }}
                </div>
            </div>
            @endif
            @endif
        </div>
    </div>
</div>

{{-- Footer --}}
<footer class="mt-16 py-8 border-t text-center text-xs" style="border-color:var(--border); color:var(--text-muted);">
    © {{ date('Y') }} {{ $tr('شركة ثروة للتطوير العقاري — جميع الحقوق محفوظة', 'Tharwa Real Estate — All rights reserved') }}
</footer>

<script>
function toggleFilters() {
    const overlay = document.getElementById('mobile-filter-overlay');
    const panel   = document.getElementById('mobile-filter-panel');
    const hidden  = panel.classList.contains('hidden');
    overlay.classList.toggle('hidden', !hidden);
    panel.classList.toggle('hidden', !hidden);
}
// Auto-submit on radio change
document.querySelectorAll('#filter-form input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', () => document.getElementById('filter-form').submit());
});
document.querySelectorAll('#filter-form select[name="sort"]').forEach(sel => {
    sel.addEventListener('change', () => document.getElementById('filter-form').submit());
});
</script>
</body>
</html>
