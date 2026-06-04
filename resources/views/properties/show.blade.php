<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ app()->getLocale() === 'ar' ? ($property->name_ar ?: $property->name) : ($property->name_en ?: $property->name) }} — ثروة</title>
@vite(['resources/css/app.css','resources/js/app.js'])
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--navy:#0f2444;--navy-mid:#1a3a6b;--gold:#c9a84c;--gold-light:#e8c96e;--text:#1a2437;--muted:#64748b;--border:#e2e8f0;--off:#f8fafc}
*{font-family:{{ app()->getLocale()==='ar' ? "'Cairo'" : "'Sora'" }},sans-serif}
html{scroll-behavior:smooth}
body{background:#fff;color:var(--text);overflow-x:hidden}

/* Gallery */
.gal-main{position:relative;overflow:hidden;border-radius:14px 14px 0 0;cursor:pointer;background:#0f2444}
.gal-main img{width:100%;height:420px;object-fit:cover;transition:transform .5s}
.gal-main:hover img{transform:scale(1.03)}
.gal-thumb{height:90px;overflow:hidden;cursor:pointer;position:relative;background:#0f2444}
.gal-thumb img{width:100%;height:100%;object-fit:cover;transition:transform .4s,opacity .3s}
.gal-thumb:hover img{transform:scale(1.08);opacity:.85}
.gal-thumb.active{outline:3px solid var(--gold);outline-offset:-3px}

/* Sections */
.section-card{background:#fff;border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:20px}
.section-head{padding:14px 20px;border-bottom:1px solid var(--border);font-weight:800;font-size:.875rem;color:var(--navy)}
.section-body{padding:18px 20px}

/* Spec chips */
.spec-chip{display:flex;flex-direction:column;align-items:center;gap:4px;padding:14px 10px;background:var(--off);border-radius:12px;border:1px solid var(--border);flex:1;min-width:80px}
.spec-chip svg{color:var(--gold)}
.spec-chip .val{font-size:.95rem;font-weight:800;color:var(--navy)}
.spec-chip .lbl{font-size:.65rem;font-weight:600;color:var(--muted);text-align:center}

/* Detail table */
.detail-row{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)}
.detail-row:last-child{border-bottom:none}
.detail-row .dk{font-size:.78rem;color:var(--muted)}
.detail-row .dv{font-size:.78rem;font-weight:700;color:var(--navy)}

/* Sidebar */
.sidebar-card{background:#fff;border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:16px}
.sidebar-head{padding:12px 16px;border-bottom:1px solid var(--border);font-weight:800;font-size:.8rem;color:var(--navy);background:var(--off)}

/* Form inputs */
.f-input{width:100%;border:1px solid var(--border);border-radius:10px;padding:10px 14px;font-size:.82rem;color:var(--text);outline:none;background:#fff;transition:border .2s}
.f-input:focus{border-color:var(--navy)}

/* Similar cards */
.sim-card{background:#fff;border:1px solid var(--border);border-radius:14px;overflow:hidden;transition:all .3s}
.sim-card:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(15,36,68,.10);border-color:rgba(201,168,76,.4)}
.sim-card:hover img{transform:scale(1.06)}
.sim-card img{transition:transform .5s}

/* Lightbox */
#prop-lb{display:none;position:fixed;inset:0;background:rgba(0,0,0,.93);z-index:9999;align-items:center;justify-content:center}
#prop-lb.open{display:flex}
#prop-lb img{max-width:90vw;max-height:88vh;object-fit:contain;border-radius:8px}
#lb-close,#lb-prev,#lb-next{position:absolute;background:rgba(255,255,255,.15);border:none;color:#fff;cursor:pointer;border-radius:50%;display:flex;align-items:center;justify-content:center;transition:background .2s}
#lb-close:hover,#lb-prev:hover,#lb-next:hover{background:rgba(255,255,255,.3)}
#lb-close{top:16px;right:16px;width:36px;height:36px}
#lb-prev{top:50%;left:16px;transform:translateY(-50%);width:44px;height:44px}
#lb-next{top:50%;right:16px;transform:translateY(-50%);width:44px;height:44px}
#lb-counter{position:absolute;bottom:20px;left:50%;transform:translateX(-50%);color:rgba(255,255,255,.6);font-size:.8rem}

@media(max-width:640px){
  .gal-main img{height:220px}
  .spec-chip{min-width:56px;padding:9px 5px;font-size:.8rem}
  .spec-chip .val{font-size:.85rem}
  .section-body{padding:14px}
}
@media(min-width:641px) and (max-width:768px){.gal-main img{height:260px}}
</style>
</head>
<body>

@include('_partials.public-nav')

@php
  $isAr  = app()->getLocale() === 'ar';
  $tr    = fn(string $ar, string $en) => $isAr ? $ar : $en;
  $pName = $isAr ? ($property->name_ar ?: $property->name) : ($property->name_en ?: $property->name);
  $pDesc = $isAr ? ($property->description_ar ?: $property->description) : ($property->description_en ?: $property->description);
  $pCity = $isAr ? ($property->city_ar ?: $property->city) : ($property->city_en ?: $property->city);
  $pAddr = $isAr ? ($property->address_ar ?: $property->address) : ($property->address_en ?: $property->address);
  $allImages = $property->images;
  $heroImage = $allImages->firstWhere('is_primary', true) ?? $allImages->first();
  $displayPrice = $minSalePrice ?? $minRentPrice;
  $currency = 'OMR';
  $waHref = $waNum ? 'https://api.whatsapp.com/send?phone='.$waNum : null;
@endphp

{{-- ── BREADCRUMB ── --}}
<div style="background:#fff;border-bottom:1px solid var(--border)">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center gap-2 text-xs" style="color:var(--muted)">
    <a href="{{ route('home') }}" class="hover:text-navy transition">{{ $tr('الرئيسية','Home') }}</a>
    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isAr ? 'M15.75 19.5 8.25 12l7.5-7.5' : 'M8.25 4.5l7.5 7.5-7.5 7.5' }}"/></svg>
    <a href="{{ route('properties.index') }}" class="hover:text-navy transition">{{ $tr('العقارات','Properties') }}</a>
    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isAr ? 'M15.75 19.5 8.25 12l7.5-7.5' : 'M8.25 4.5l7.5 7.5-7.5 7.5' }}"/></svg>
    <span class="truncate font-semibold" style="color:var(--navy)">{{ Str::limit($pName, 60) }}</span>
  </div>
</div>

{{-- ── TITLE + PRICE BAR ── --}}
<div style="background:#fff;border-bottom:1px solid var(--border)">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-start justify-between gap-4 flex-wrap">
    <div>
      <h1 class="text-xl sm:text-2xl font-black leading-snug" style="color:var(--navy)">{{ $pName }}</h1>
      @if($pCity || $pAddr)
      <p class="text-sm mt-1 flex items-center gap-1.5" style="color:var(--muted)">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--gold)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0z"/></svg>
        {{ implode($isAr ? '، ' : ', ', array_filter([$pCity, $pAddr])) }}
      </p>
      @endif
    </div>
    @if($displayPrice)
    <div class="text-end flex-shrink-0">
      <p class="text-2xl font-black" style="color:var(--navy)">{{ number_format($displayPrice) }} <span class="text-base font-semibold" style="color:var(--gold)">{{ $currency }}</span></p>
      <p class="text-xs" style="color:var(--muted)">{{ $minSalePrice ? $tr('سعر البيع','Sale Price') : $tr('الإيجار/سنة','Rent/Year') }}</p>
    </div>
    @endif
  </div>
</div>

{{-- ── MAIN CONTENT ── --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

{{-- ════ LEFT / MAIN COLUMN ════ --}}
<div class="lg:col-span-2">

  {{-- IMAGE GALLERY --}}
  <div class="section-card" style="border-radius:14px;margin-bottom:20px">
    <div class="gal-main" onclick="lbOpen(0)">
      @if($heroImage)
      <img id="main-img" src="{{ $heroImage->url() }}" alt="{{ $pName }}">
      @else
      <div style="height:420px;background:linear-gradient(135deg,#0f2444,#1a3a6b);display:flex;align-items:center;justify-content:center">
        <svg class="w-16 h-16 opacity-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M18 12h.008v.008H18V12zm-6 4.5h.008v.008H12V16.5z"/></svg>
      </div>
      @endif
      @if($allImages->count() > 0)
      <div style="position:absolute;bottom:12px;end:12px;background:rgba(0,0,0,.6);color:#fff;font-size:.72rem;font-weight:700;padding:5px 12px;border-radius:999px;backdrop-filter:blur(4px)">
        <span id="img-counter">1</span> / {{ $allImages->count() }}
        <svg class="w-3 h-3 inline ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909"/></svg>
      </div>
      @endif
    </div>
    @if($allImages->count() > 1)
    @php $thumbCols = min($allImages->count(), 8); @endphp
    <div class="grid gap-1 p-1" style="grid-template-columns:repeat({{ $thumbCols }}, 1fr)">
      @foreach($allImages as $img)
      @php $tIdx = $loop->index; @endphp
      <div class="gal-thumb {{ $tIdx === 0 ? 'active' : '' }}" id="thumb-{{ $tIdx }}" onclick="switchImg({{ $tIdx }}, '{{ $img->url() }}')">
        <img src="{{ $img->url() }}" loading="lazy" alt="">
      </div>
      @endforeach
    </div>
    @endif
  </div>

  {{-- OVERVIEW --}}
  <div class="section-card">
    <div class="section-head">{{ $tr('نظرة عامة','Overview') }}</div>
    <div class="section-body">
      <div class="flex flex-wrap gap-3">
        @php
          $specs = array_filter([
            ['icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21"/>',
              'val'=>$property->typeLabel(), 'lbl'=>$tr('نوع العقار','Property Type')],
            ['icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0-6.75H8.25m3.75 0H15.75M3.75 7.5h16.5"/>',
              'val'=>$property->units->max('bedrooms') ?? $property->bedrooms ?? '—', 'lbl'=>$tr('غرف النوم','Bedrooms')],
            ['icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
              'val'=>$property->units->max('bathrooms') ?? $property->bathrooms ?? '—', 'lbl'=>$tr('الحمامات','Bathrooms')],
            ['icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/>',
              'val'=>($property->total_area ? number_format($property->total_area) : ($property->units->max('area') ? number_format($property->units->max('area')) : '—')), 'lbl'=>'m²'],
          ]);
        @endphp
        @foreach($specs as $spec)
        <div class="spec-chip">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">{!! $spec['icon'] !!}</svg>
          <span class="val">{{ $spec['val'] }}</span>
          <span class="lbl">{{ $spec['lbl'] }}</span>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- DESCRIPTION --}}
  @if($pDesc)
  <div class="section-card">
    <div class="section-head">{{ $tr('الوصف','Description') }}</div>
    <div class="section-body">
      <div class="text-sm leading-relaxed" style="color:var(--text);white-space:pre-line">{{ $pDesc }}</div>
    </div>
  </div>
  @endif

  {{-- PROPERTY DETAILS LIST --}}
  <div class="section-card">
    <div class="section-head">{{ $tr('تفاصيل العقار','Property Details') }}</div>
    <div class="section-body">
      @php
        $detailsList = array_filter([
          $tr('كود العقار','Property Code')       => $property->code,
          $tr('نوع العقار','Property Type')       => $property->typeLabel(),
          $tr('الغرض','Purpose')                  => $property->purposeLabel(),
          $tr('المدينة','City')                   => $pCity,
          $tr('العنوان','Address')                => $pAddr,
          $tr('المساحة الكلية','Total Area')      => $property->total_area ? number_format($property->total_area).' m²' : null,
          $tr('عدد الطوابق','Floors')             => $property->floors,
          $tr('غرف النوم','Bedrooms')             => $property->bedrooms,
          $tr('الحمامات','Bathrooms')             => $property->bathrooms,
          $tr('عدد الوحدات','Total Units')        => $property->units_count ?: null,
          $tr('الوحدات المتاحة','Available Units')=> $property->available_units_count ?: null,
        ]);
      @endphp
      @foreach($detailsList as $k => $v)
      <div class="detail-row">
        <span class="dk">{{ $k }}</span>
        <span class="dv">{{ $v }}</span>
      </div>
      @endforeach
    </div>
  </div>

  {{-- UNITS TABLE --}}
  @if($units->isNotEmpty())
  <div class="section-card" id="units">
    <div class="section-head flex items-center justify-between">
      <span>{{ $tr('الوحدات المتاحة','Available Units') }} ({{ $availableUnits->count() }})</span>
      @if($availableUnits->count() > 0)
      <span class="text-xs font-bold px-2.5 py-1 rounded-full" style="background:#dcfce7;color:#166534">{{ $availableUnits->count() }} {{ $tr('متاح','available') }}</span>
      @endif
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr style="background:var(--off);border-bottom:1px solid var(--border)">
            <th class="px-4 py-3 text-start text-xs font-semibold" style="color:var(--muted)">{{ $tr('الوحدة','Unit') }}</th>
            <th class="px-4 py-3 text-start text-xs font-semibold hidden sm:table-cell" style="color:var(--muted)">{{ $tr('المساحة','Area') }}</th>
            <th class="px-4 py-3 text-start text-xs font-semibold hidden md:table-cell" style="color:var(--muted)">{{ $tr('الغرف','Rooms') }}</th>
            <th class="px-4 py-3 text-start text-xs font-semibold" style="color:var(--muted)">{{ $tr('السعر','Price') }}</th>
            <th class="px-4 py-3 text-start text-xs font-semibold" style="color:var(--muted)">{{ $tr('الحالة','Status') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($units as $unit)
          <tr style="border-bottom:1px solid var(--border)" onmouseover="this.style.background='#f0f4ff'" onmouseout="this.style.background=''">
            <td class="px-4 py-3 font-bold text-sm" style="color:var(--navy)">{{ $unit->unit_number ?: ('F'.$unit->floor) }}</td>
            <td class="px-4 py-3 text-xs hidden sm:table-cell" style="color:var(--muted)">{{ $unit->area ? number_format($unit->area).' m²' : '—' }}</td>
            <td class="px-4 py-3 text-xs hidden md:table-cell" style="color:var(--muted)">
              @if($unit->bedrooms || $unit->bathrooms) {{ $unit->bedrooms ?? 0 }} {{ $tr('غ','BR') }} · {{ $unit->bathrooms ?? 0 }} {{ $tr('ح','BA') }}
              @else —
              @endif
            </td>
            <td class="px-4 py-3 text-xs font-bold" style="color:var(--navy)">
              @if($unit->rent_price) {{ number_format($unit->rent_price) }} <span style="color:var(--muted);font-weight:400">{{ $tr('إيجار','Rent') }}</span><br> @endif
              @if($unit->sale_price) {{ number_format($unit->sale_price) }} <span style="color:var(--muted);font-weight:400">{{ $tr('بيع','Sale') }}</span> @endif
              @if(!$unit->rent_price && !$unit->sale_price) — @endif
            </td>
            <td class="px-4 py-3">
              @php $sc = ['available'=>'#dcfce7:#166534','rented'=>'#dbeafe:#1e40af','sold'=>'#fef9c3:#92400e','reserved'=>'#ede9fe:#5b21b6','maintenance'=>'#fee2e2:#991b1b'][$unit->status] ?? '#f1f5f9:#64748b';
              [$sb,$sc] = explode(':', $sc); @endphp
              <span class="text-xs font-bold px-2.5 py-1 rounded-full" style="background:{{ $sb }};color:{{ $sc }}">{{ $unit->statusLabel() }}</span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

  {{-- ENQUIRE FORM --}}
  <div class="section-card">
    <div class="section-head">{{ $tr('استفسر عن هذا العقار','Enquire About This Property') }}</div>
    <div class="section-body">
      @if(session('contact_success'))
      <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="text-sm font-semibold text-green-800">{{ session('contact_success') }}</span>
      </div>
      @endif
      <form method="POST" action="{{ route('contact.store') }}" class="space-y-3">
        @csrf
        <div class="grid sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold mb-1.5" style="color:var(--muted)">{{ $tr('الاسم','Name') }} <span style="color:var(--gold)">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" class="f-input" required>
            @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-xs font-semibold mb-1.5" style="color:var(--muted)">{{ $tr('الهاتف','Phone') }}</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="f-input">
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold mb-1.5" style="color:var(--muted)">{{ $tr('البريد الإلكتروني','Email') }} <span style="color:var(--gold)">*</span></label>
          <input type="email" name="email" value="{{ old('email') }}" class="f-input" required>
          @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <input type="hidden" name="subject" value="{{ $tr('استفسار عن عقار','Property Inquiry') }}: {{ $pName }}">
        <div>
          <label class="block text-xs font-semibold mb-1.5" style="color:var(--muted)">{{ $tr('الرسالة','Message') }} <span style="color:var(--gold)">*</span></label>
          <textarea name="message" rows="4" class="f-input resize-none" required>{{ old('message', $tr('السلام عليكم، أودّ الاستفسار عن هذا العقار: ','Hello, I am interested in this property: ') . $pName) }}</textarea>
          @error('message')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="w-full py-3 rounded-xl text-sm font-bold text-white flex items-center justify-center gap-2" style="background:var(--navy)" onmouseover="this.style.background='#1a3a6b'" onmouseout="this.style.background='var(--navy)'">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
          {{ $tr('إرسال الاستفسار','Send Enquiry') }}
        </button>
      </form>
    </div>
  </div>

</div>{{-- end left column --}}

{{-- ════ RIGHT SIDEBAR ════ --}}
<div class="lg:col-span-1">

  {{-- QUICK CONTACT CARD --}}
  <div class="sidebar-card">
    <div class="sidebar-head">{{ $tr('تواصل معنا','Contact Us') }}</div>
    <div class="p-4 space-y-3">
      @if($displayPrice)
      <div class="text-center py-3 rounded-xl" style="background:var(--off);border:1px solid var(--border)">
        <p class="text-2xl font-black" style="color:var(--navy)">{{ number_format($displayPrice) }}</p>
        <p class="text-xs font-semibold" style="color:var(--gold)">{{ $currency }}</p>
      </div>
      @endif

      <div class="flex gap-2">
        @if($contactPhone)
        <a href="tel:{{ preg_replace('/\D/','',$contactPhone) }}"
           class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold text-white transition"
           style="background:var(--navy)" onmouseover="this.style.background='#1a3a6b'" onmouseout="this.style.background='var(--navy)'">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
          {{ $tr('اتصال','Call') }}
        </a>
        @endif
        @if($waHref)
        <a href="{{ $waHref }}" target="_blank"
           class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold text-white transition"
           style="background:#25d366" onmouseover="this.style.background='#1da851'" onmouseout="this.style.background='#25d366'">
          <svg viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
          WhatsApp
        </a>
        @endif
      </div>

      @if($contactPhone)
      <p class="text-center text-xs" style="color:var(--muted)">
        <svg class="w-3.5 h-3.5 inline me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--gold)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
        <span dir="ltr" style="unicode-bidi:embed;">{{ $contactPhone }}</span>
      </p>
      @endif
    </div>
  </div>

  {{-- MAP --}}
  <div class="sidebar-card">
    <div class="sidebar-head flex items-center justify-between">
      <span>{{ $tr('الموقع والاتجاهات','Location & Directions') }}</span>
      @php
        $mapsLink = $property->latitude
          ? 'https://www.google.com/maps/dir/?api=1&destination='.$property->latitude.','.$property->longitude
          : 'https://www.google.com/maps/search/?api=1&query='.urlencode(implode(', ', array_filter([$pAddr, $pCity, 'Oman'])));
      @endphp
      <a href="{{ $mapsLink }}" target="_blank" rel="noopener"
         class="flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-lg text-white transition"
         style="background:var(--navy)" onmouseover="this.style.background='#1a3a6b'" onmouseout="this.style.background='var(--navy)'">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z"/></svg>
        {{ $tr('اتجاهات','Directions') }}
      </a>
    </div>
    @if($property->latitude && $property->longitude)
    <div class="p-2">
      <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
      <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
      <div id="prop-map" style="height:220px;border-radius:10px;border:1px solid var(--border);z-index:0"></div>
      <script>
      (function(){
        var map = L.map('prop-map', { zoomControl: true, scrollWheelZoom: false, dragging: false })
          .setView([{{ $property->latitude }}, {{ $property->longitude }}], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>', maxZoom: 19
        }).addTo(map);
        var icon = L.divIcon({
          html: '<div style="width:32px;height:32px;background:#0f2444;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #c9a84c;box-shadow:0 4px 12px rgba(15,36,68,.4)"><div style="width:8px;height:8px;background:#c9a84c;border-radius:50%;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) rotate(45deg)"></div></div>',
          iconSize:[32,32], iconAnchor:[16,32], className:''
        });
        @php
          $popupHtml = '<strong style="color:#0f2444">' . e($pName) . '</strong>'
            . ($pCity ? '<br><span style="color:#64748b;font-size:.75rem">' . e($pCity) . '</span>' : '');
        @endphp
        L.marker([{{ $property->latitude }}, {{ $property->longitude }}], {icon:icon})
          .addTo(map)
          .bindPopup({!! json_encode($popupHtml) !!})
          .openPopup();
        map.on('click', function(){ window.open('{{ $mapsLink }}', '_blank'); });
      })();
      </script>
      <p class="text-xs text-center mt-1.5" style="color:var(--muted)">{{ $tr('انقر على الخريطة لفتح الاتجاهات','Click map to open directions') }}</p>
    </div>
    @else
    <div class="p-4">
      <div style="height:180px;border-radius:10px;overflow:hidden;border:1px solid var(--border)">
        <iframe src="https://maps.google.com/maps?q={{ urlencode(implode(', ', array_filter([$pAddr, $pCity, 'Oman']))) }}&output=embed&z=14"
                width="100%" height="100%" style="border:0" allowfullscreen loading="lazy"></iframe>
      </div>
    </div>
    @endif
  </div>

  {{-- PRICE BOX --}}
  <div class="sidebar-card">
    <div class="sidebar-head">{{ $tr('الأسعار','Pricing') }}</div>
    <div class="p-4 space-y-3">
      @if($minRentPrice)
      <div class="p-3 rounded-xl" style="background:#dbeafe;border:1px solid #bfdbfe">
        <p class="text-xs font-semibold mb-0.5" style="color:#1e40af">{{ $tr('الإيجار','Rent') }}</p>
        <p class="text-lg font-black" style="color:#1e3a8a">{{ number_format($minRentPrice) }} <span class="text-sm font-semibold">{{ $currency }}</span></p>
        @if($maxRentPrice && $maxRentPrice !== $minRentPrice)
        <p class="text-xs" style="color:#3b82f6">{{ $tr('حتى','Up to') }} {{ number_format($maxRentPrice) }}</p>
        @endif
      </div>
      @endif
      @if($minSalePrice)
      <div class="p-3 rounded-xl" style="background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.3)">
        <p class="text-xs font-semibold mb-0.5" style="color:#92680a">{{ $tr('البيع','Sale') }}</p>
        <p class="text-lg font-black" style="color:var(--navy)">{{ number_format($minSalePrice) }} <span class="text-sm font-semibold">{{ $currency }}</span></p>
        @if($maxSalePrice && $maxSalePrice !== $minSalePrice)
        <p class="text-xs" style="color:var(--gold)">{{ $tr('حتى','Up to') }} {{ number_format($maxSalePrice) }}</p>
        @endif
      </div>
      @endif
      @if(!$minRentPrice && !$minSalePrice)
      <p class="text-sm text-center py-3" style="color:var(--muted)">{{ $tr('لا توجد أسعار متاحة','No pricing available') }}</p>
      @endif
    </div>
  </div>

  {{-- BACK BUTTON --}}
  <a href="{{ route('properties.index') }}"
     class="flex items-center gap-2 text-sm py-3 px-4 rounded-xl bg-white border transition"
     style="border-color:var(--border);color:var(--muted)"
     onmouseover="this.style.borderColor='var(--navy)';this.style.color='var(--navy)'"
     onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
    {{ $tr('العودة إلى جميع العقارات','Back to All Properties') }}
  </a>

</div>{{-- end sidebar --}}
</div>{{-- end grid --}}
</div>{{-- end max-w --}}


{{-- ── SIMILAR LISTINGS ── --}}
@if($similar->isNotEmpty())
<div class="max-w-7xl mx-auto px-4 sm:px-6 pb-12">
  <div class="flex items-center gap-4 mb-6">
    <div class="h-px flex-1" style="background:var(--border)"></div>
    <h2 class="text-base font-black" style="color:var(--navy)">{{ $tr('عقارات مشابهة','Similar Listings') }}</h2>
    <div class="h-px flex-1" style="background:var(--border)"></div>
  </div>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
    @foreach($similar as $sim)
    @php
      $simImg = $sim->images->first();
      $simPrice = $sim->units->whereNotNull('sale_price')->min('sale_price') ?? $sim->units->whereNotNull('rent_price')->min('rent_price');
      $simName = $isAr ? ($sim->name_ar ?: $sim->name) : ($sim->name_en ?: $sim->name);
    @endphp
    <a href="{{ route('properties.show', $sim) }}" class="sim-card block">
      <div style="height:180px;overflow:hidden;position:relative">
        @if($simImg)
        <img src="{{ $simImg->url() }}" class="w-full h-full object-cover" alt="{{ $simName }}" loading="lazy">
        @else
        <div class="w-full h-full" style="background:linear-gradient(135deg,#0f2444,#1a3a6b)"></div>
        @endif
        @if($simPrice)
        <div style="position:absolute;bottom:10px;end:10px;background:rgba(255,255,255,.95);color:var(--navy);font-size:.75rem;font-weight:900;padding:4px 12px;border-radius:10px">
          {{ number_format($simPrice) }} {{ $currency }}
        </div>
        @endif
      </div>
      <div class="p-4">
        <p class="text-xs font-bold mb-1" style="color:var(--gold);text-transform:uppercase">{{ $sim->typeLabel() }}</p>
        <p class="text-sm font-bold leading-snug" style="color:var(--navy);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ $simName }}</p>
        @if($sim->city)
        <p class="text-xs mt-1.5 flex items-center gap-1" style="color:var(--muted)">
          <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
          {{ $isAr ? ($sim->city_ar ?: $sim->city) : ($sim->city_en ?: $sim->city) }}
        </p>
        @endif
      </div>
    </a>
    @endforeach
  </div>
</div>
@endif

{{-- LIGHTBOX --}}
<div id="prop-lb" onclick="if(event.target===this)lbClose()">
  <button id="lb-close" onclick="lbClose()">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
  </button>
  <button id="lb-prev" onclick="lbPrev()">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $isAr ? 'M8.25 4.5l7.5 7.5-7.5 7.5' : 'M15.75 19.5L8.25 12l7.5-7.5' }}"/></svg>
  </button>
  <img id="lb-img" src="" alt="">
  <button id="lb-next" onclick="lbNext()">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $isAr ? 'M15.75 19.5L8.25 12l7.5-7.5' : 'M8.25 4.5l7.5 7.5-7.5 7.5' }}"/></svg>
  </button>
  <span id="lb-counter"></span>
</div>

{{-- FOOTER --}}
<footer class="mt-6 py-8 border-t text-center text-xs" style="border-color:var(--border);color:var(--muted)">
  © {{ date('Y') }} {{ $isAr ? 'شركة ثروة للعقارات — جميع الحقوق محفوظة' : 'Tharwa Real Estate — All rights reserved' }}
</footer>

<script>
// ── Image gallery switcher ──
var _imgs = [
  @foreach($allImages as $img)"{{ $img->url() }}"{{ !$loop->last ? ',' : '' }}@endforeach
];
var _ci = 0;

function switchImg(idx, url) {
  _ci = idx;
  document.getElementById('main-img').src = url;
  document.getElementById('img-counter').textContent = idx + 1;
  document.querySelectorAll('.gal-thumb').forEach((t,i) => t.classList.toggle('active', i===idx));
}

// ── Lightbox ──
function lbOpen(i) {
  _ci = i;
  document.getElementById('lb-img').src = _imgs[_ci];
  document.getElementById('lb-counter').textContent = (_ci+1) + ' / ' + _imgs.length;
  document.getElementById('prop-lb').classList.add('open');
  document.body.style.overflow = 'hidden';
  document.getElementById('lb-prev').style.display = _imgs.length > 1 ? 'flex' : 'none';
  document.getElementById('lb-next').style.display = _imgs.length > 1 ? 'flex' : 'none';
}
function lbClose() {
  document.getElementById('prop-lb').classList.remove('open');
  document.body.style.overflow = '';
}
function lbPrev() { _ci = (_ci - 1 + _imgs.length) % _imgs.length; lbOpen(_ci); }
function lbNext() { _ci = (_ci + 1) % _imgs.length; lbOpen(_ci); }

document.addEventListener('keydown', function(e) {
  if (!document.getElementById('prop-lb').classList.contains('open')) return;
  if (e.key === 'Escape') lbClose();
  if (e.key === 'ArrowLeft')  {{ $isAr ? 'lbNext()' : 'lbPrev()' }};
  if (e.key === 'ArrowRight') {{ $isAr ? 'lbPrev()' : 'lbNext()' }};
});
</script>

</body>
</html>
