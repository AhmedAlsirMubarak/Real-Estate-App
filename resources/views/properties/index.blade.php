<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() === 'ar' ? 'العقارات المتاحة' : 'Available Properties' }} — ثروة</title>
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

        .prop-card { background: #fff; border: 1px solid var(--border);
            transition: transform .3s, box-shadow .3s, border-color .3s; }
        .prop-card:hover { transform: translateY(-5px); box-shadow: 0 16px 40px rgba(15,36,68,.1);
            border-color: rgba(201,168,76,.4); }
        .prop-card-img {
            background: var(--navy);
            height: 210px;
            position: relative;
            overflow: hidden;
        }
        .prop-card-img::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(9,22,42,.68), rgba(9,22,42,.08));
            pointer-events: none;
        }
        .prop-card-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .75s ease;
        }
        .prop-card:hover .prop-card-photo { transform: scale(1.08); }
        .img-chip {
            position: absolute;
            z-index: 2;
            bottom: 10px;
            right: 10px;
            background: rgba(255,255,255,.9);
            color: var(--navy);
            border-radius: 999px;
            font-size: .68rem;
            font-weight: 800;
            padding: 4px 10px;
        }

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
    </style>
</head>
<body>

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $propertiesPaginator */
    $propertiesPaginator = $properties;
    $isAr = app()->getLocale() === 'ar';
    $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    $currency = $isAr ? 'ريال' : 'SAR';
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
            <a href="{{ route('home') }}" class="text-white/70 hover:text-white text-sm transition flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                {{ $tr('الرئيسية', 'Home') }}
            </a>
            <a href="{{ route('login') }}" class="btn-gold px-4 py-2 rounded-xl text-sm flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
                {{ $tr('دخول', 'Login') }}
            </a>
        </div>
    </div>
</nav>

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
                            @foreach(['apartment_building' => $tr('عمارة سكنية', 'Apartment Building'), 'villa' => $tr('فيلا', 'Villa'), 'farm' => $tr('مزرعة', 'Farm'), 'chalet' => $tr('شاليه', 'Chalet')] as $val => $label)
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
                        <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">{{ $tr('نطاق السعر (ريال)', 'Price Range (SAR)') }}</label>
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
                        @foreach(['apartment_building' => $tr('عمارة سكنية', 'Apartment Building'), 'villa' => $tr('فيلا', 'Villa'), 'farm' => $tr('مزرعة', 'Farm'), 'chalet' => $tr('شاليه', 'Chalet')] as $val => $label)
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
                    <label class="block text-xs font-semibold mb-2" style="color:var(--text-dark);">{{ $tr('نطاق السعر (ريال)', 'Price Range (SAR)') }}</label>
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
                        {{ $tr('النوع', 'Type') }}: {{ ['apartment_building'=>$tr('عمارة','Building'),'villa'=>$tr('فيلا','Villa'),'farm'=>$tr('مزرعة','Farm'),'chalet'=>$tr('شاليه','Chalet')][request('type')] ?? request('type') }}
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

            @if($propertiesPaginator->isEmpty())
            {{-- Empty State --}}
            <div class="bg-white rounded-2xl border p-12 text-center" style="border-color:var(--border);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-16 h-16 mx-auto mb-4" style="color:var(--border);"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 15.803 7.5 7.5 0 0 0 15.803 15.803z"/></svg>
                <h3 class="text-lg font-bold mb-2" style="color:var(--navy);">{{ $tr('لا توجد نتائج', 'No results found') }}</h3>
                <p class="text-sm mb-5" style="color:var(--text-muted);">{{ $tr('حاول تغيير معايير البحث أو مسح الفلاتر', 'Try changing your filters or clearing search criteria') }}</p>
                <a href="{{ route('properties.index') }}" class="btn-navy px-6 py-2.5 rounded-xl text-sm inline-block">{{ $tr('مسح الفلاتر', 'Clear filters') }}</a>
            </div>
            @else
            {{-- Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($propertiesPaginator as $property)
                <a href="{{ route('properties.show', $property) }}" class="prop-card rounded-2xl overflow-hidden block group">
                    {{-- Property image --}}
                    <div class="prop-card-img">
                        @php
                            $typeImageMap = [
                                'apartment_building' => [
                                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1200&q=80',
                                    'https://images.unsplash.com/photo-1448630360428-65456885c650?auto=format&fit=crop&w=1200&q=80',
                                    'https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&w=1200&q=80',
                                ],
                                'villa' => [
                                    'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=1200&q=80',
                                    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=80',
                                    'https://images.unsplash.com/photo-1605146769289-440113cc3d00?auto=format&fit=crop&w=1200&q=80',
                                ],
                                'farm' => [
                                    'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1200&q=80',
                                    'https://images.unsplash.com/photo-1501004318641-b39e6451bec6?auto=format&fit=crop&w=1200&q=80',
                                    'https://images.unsplash.com/photo-1574943320219-553eb213f72d?auto=format&fit=crop&w=1200&q=80',
                                ],
                                'chalet' => [
                                    'https://images.unsplash.com/photo-1505693314120-0d443867891c?auto=format&fit=crop&w=1200&q=80',
                                    'https://images.unsplash.com/photo-1472220625704-91e1462799b2?auto=format&fit=crop&w=1200&q=80',
                                    'https://images.unsplash.com/photo-1544984243-ec57ea16fe25?auto=format&fit=crop&w=1200&q=80',
                                ],
                            ];

                            $pool = $typeImageMap[$property->type] ?? $typeImageMap['apartment_building'];
                            $heroImage = $pool[$property->id % count($pool)];
                        @endphp
                        <img src="{{ $heroImage }}" loading="lazy" alt="{{ $property->name }}" class="prop-card-photo">
                        {{-- Badges overlay --}}
                        <div class="absolute top-3 start-3 flex gap-1.5 flex-wrap">
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full
                                {{ $property->purpose === 'rent' ? 'badge-rent' : ($property->purpose === 'sale' ? 'badge-sale' : 'badge-both') }}">
                                {{ $property->purposeLabel() }}
                            </span>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full" style="background:rgba(201,168,76,.9); color:var(--navy);">
                                {{ $property->typeLabel() }}
                            </span>
                        </div>
                        @if($property->available_units_count > 0)
                        <div class="absolute top-3 end-3">
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full badge-active">
                                {{ $property->available_units_count }} {{ $tr('متاح', 'available') }}
                            </span>
                        </div>
                        @endif
                        @if($property->total_area)
                        <span class="img-chip">{{ number_format($property->total_area) }} m²</span>
                        @endif
                    </div>

                    {{-- Card body --}}
                    <div class="p-4 sm:p-5">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h3 class="font-bold text-base leading-snug group-hover:text-blue-700 transition" style="color:var(--navy);">{{ $property->name }}</h3>
                            <span class="text-xs font-mono text-gray-400 flex-shrink-0">#{{ $property->code }}</span>
                        </div>

                        @if($property->city || $property->address)
                        <div class="flex items-center gap-1.5 text-sm mb-3" style="color:var(--text-muted);">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 flex-shrink-0" style="color:var(--gold);"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0z"/></svg>
                            <span class="truncate">{{ $property->city ? $property->city . (($property->address) ? ($isAr ? '، ' : ', ') . $property->address : '') : $property->address }}</span>
                        </div>
                        @endif

                        {{-- Stats row --}}
                        <div class="flex items-center gap-4 text-xs mb-4 flex-wrap" style="color:var(--text-muted);">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25zM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25z"/></svg>
                                {{ $property->units_count }} {{ $tr('وحدة', 'units') }}
                            </span>
                            @if($property->total_area)
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
                                {{ number_format($property->total_area) }} m²
                            </span>
                            @endif
                            @if($property->floors)
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5"/></svg>
                                {{ $property->floors }} {{ $tr('طوابق', 'floors') }}
                            </span>
                            @endif
                        </div>

                        {{-- Price hint from available units --}}
                        @php
                            $availUnits = $property->units->where('status', 'available');
                            $minRent = $availUnits->whereNotNull('rent_price')->min('rent_price');
                            $minSale = $availUnits->whereNotNull('sale_price')->min('sale_price');
                        @endphp
                        @if($minRent || $minSale)
                        <div class="border-t pt-3" style="border-color:var(--border);">
                            @if($minRent)
                            <p class="text-xs" style="color:var(--text-muted);">
                                {{ $tr('إيجار يبدأ من', 'Rent starts from') }}
                                <span class="font-bold text-sm" style="color:var(--navy);">{{ number_format($minRent) }}</span>
                                <span class="text-xs">{{ $tr('ريال/سنة', 'SAR/year') }}</span>
                            </p>
                            @endif
                            @if($minSale)
                            <p class="text-xs" style="color:var(--text-muted);">
                                {{ $tr('بيع يبدأ من', 'Sale starts from') }}
                                <span class="font-bold text-sm" style="color:var(--navy);">{{ number_format($minSale) }}</span>
                                <span class="text-xs">{{ $currency }}</span>
                            </p>
                            @endif
                        </div>
                        @endif
                    </div>
                </a>
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
    © {{ date('Y') }} {{ $tr('شركة ثروة للعقارات — جميع الحقوق محفوظة', 'Tharwa Real Estate — All rights reserved') }}
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
