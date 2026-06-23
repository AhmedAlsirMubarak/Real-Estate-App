{{--
  Property cards partial — 3-column grid layout
  Variables: $properties (Collection), $waNum (string), $phoneNum (string)
             $initialShow (int|null) — hide cards beyond this index; null = show all
             $gridId (string|null)   — id attribute for the wrapper
--}}
@php
  $initialShow = $initialShow ?? null;
  $gridId      = $gridId ?? null;
  $waClean     = preg_replace('/\D/', '', $waNum ?? '');
  $phoneClean  = preg_replace('/\D/', '', $phoneNum ?? '');
@endphp

<div @if($gridId) id="{{ $gridId }}" @endif class="mk-grid">
  @forelse($properties as $p)
  @php
    $cardIdx  = $loop->index;
    $isHidden = $initialShow !== null && $cardIdx >= $initialShow;
    $img1     = $p->images->first();
    $minRent  = $p->units->where('listing_type','rent')->whereNotNull('rent_price')->min('rent_price');
    $minSale  = $p->units->where('listing_type','sale')->whereNotNull('sale_price')->min('sale_price');
    $price    = $minSale ?? $minRent;
    $maxBeds  = $p->units->max('bedrooms');
    $maxBath  = $p->units->max('bathrooms');
    $maxArea  = $p->units->max('area');
    $typeAr   = match($p->type ?? '') {
      'apartment_building'=>'عمارة','villa'=>'فيلا','farm'=>'مزرعة','chalet'=>'شاليه',
      'apartment'=>'شقة','office'=>'مكتب','shop'=>'محل','studio'=>'استوديو','land'=>'أرض',default=>$p->type ?? '',
    };
    $typeEn   = match($p->type ?? '') {
      'apartment_building'=>'Building','villa'=>'Villa','farm'=>'Farm','chalet'=>'Chalet',
      'apartment'=>'Apartment','office'=>'Office','shop'=>'Shop','studio'=>'Studio','land'=>'Land',default=>ucfirst($p->type ?? ''),
    };
    $purposeAr = match($p->purpose ?? ''){'rent'=>'للإيجار','sale'=>'للبيع','both'=>'إيجار/بيع',default=>''};
    $purposeEn = match($p->purpose ?? ''){'rent'=>'For Rent','sale'=>'For Sale','both'=>'Rent/Sale',default=>''};
    $cityAr    = trim($p->city_ar ?? $p->city ?? '');
    $cityEn    = trim($p->city_en ?? $p->city ?? '');
  @endphp

  <div class="mk-card{{ $isHidden ? ' featured-hidden' : '' }}"{{ $isHidden ? ' style="display:none"' : '' }}>

    {{-- ── Image ── --}}
    <a href="{{ route('properties.show', $p) }}" class="mk-img-wrap" tabindex="-1">
      @if($img1)
        <img src="{{ $img1->url() }}" loading="lazy" alt="{{ $p->name_ar ?: $p->name }}" class="mk-img">
      @else
        <div class="mk-img-placeholder">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
        </div>
      @endif

      {{-- Badges overlay --}}
      <div class="mk-badges">
        <span class="mk-badge-type">
          <span data-ar>{{ $typeAr }}</span><span data-en class="hidden">{{ $typeEn }}</span>
        </span>
        @if($purposeAr)
        <span class="mk-badge-purpose">
          <span data-ar>{{ $purposeAr }}</span><span data-en class="hidden">{{ $purposeEn }}</span>
        </span>
        @endif
      </div>

      {{-- Photo count --}}
      @if($p->images->count() > 1)
      <span class="mk-photo-count">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:11px;height:11px"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0z"/></svg>
        {{ $p->images->count() }}
      </span>
      @endif
    </a>

    {{-- ── Details ── --}}
    <div class="mk-details">

      {{-- Name --}}
      <a href="{{ route('properties.show', $p) }}" class="mk-name">
        <span data-ar>{{ $p->name_ar ?: $p->name }}</span>
        <span data-en class="hidden">{{ $p->name_en ?: $p->name }}</span>
      </a>

      {{-- Location --}}
      @if($cityAr || $cityEn)
      <div class="mk-location">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:12px;height:12px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0z"/></svg>
        <span><span data-ar>{{ $cityAr }}</span><span data-en class="hidden">{{ $cityEn }}</span></span>
      </div>
      @endif

      {{-- Specs --}}
      @if($maxBeds || $maxBath || $maxArea)
      <div class="mk-specs">
        @if($maxBeds)
        <span class="mk-spec">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0-6.75H8.25m3.75 0H15.75M3.75 7.5h16.5"/></svg>
          {{ $maxBeds }} <span data-ar>غرف</span><span data-en class="hidden">bd</span>
        </span>
        @endif
        @if($maxBath)
        <span class="mk-spec">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
          {{ $maxBath }} <span data-ar>حمام</span><span data-en class="hidden">ba</span>
        </span>
        @endif
        @if($maxArea)
        <span class="mk-spec">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
          {{ number_format($maxArea) }} م²
        </span>
        @endif
      </div>
      @endif

      {{-- Divider --}}
      <div class="mk-divider"></div>

      {{-- Price + actions --}}
      <div class="mk-footer">
        <div class="mk-price">
          @if($price)
          <span class="mk-price-val">{{ number_format($price) }}</span>
          <span class="mk-price-cur">ر.ع</span>
          @else
          <span class="mk-price-na"><span data-ar>السعر عند الطلب</span><span data-en class="hidden">Price on request</span></span>
          @endif
        </div>
        <div class="mk-actions">
          @if($phoneClean)
          <a href="tel:{{ $phoneClean }}" class="mk-btn-outline" title="Call">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25z"/></svg>
          </a>
          @endif
          @if($waClean)
          <a href="https://api.whatsapp.com/send?phone={{ $waClean }}" target="_blank" class="mk-btn-wa" title="WhatsApp">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
          </a>
          @endif
          <a href="{{ route('properties.show', $p) }}" class="mk-btn-primary">
            <span data-ar>التفاصيل</span><span data-en class="hidden">Details</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:11px;height:11px"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
          </a>
        </div>
      </div>

    </div>
  </div>

  @empty
  <div class="mk-empty">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
    <p data-ar>لا توجد عقارات في هذه المنطقة</p>
    <p data-en class="hidden">No properties available</p>
  </div>
  @endforelse
</div>

<style>
/* ── Grid ── */
.mk-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}

/* ── Card ── */
.mk-card{background:#fff;border-radius:14px;border:1px solid #e8ecf0;overflow:hidden;display:flex;flex-direction:column;transition:box-shadow .25s,transform .25s}
.mk-card:hover{box-shadow:0 8px 32px rgba(15,36,68,.12);transform:translateY(-2px)}

/* ── Image ── */
.mk-img-wrap{display:block;position:relative;aspect-ratio:3/3;overflow:hidden;flex-shrink:0;background:#f1f5f9}
.mk-img{width:100%;height:100%;object-fit:cover;object-position:center center;transition:transform .5s}
.mk-card:hover .mk-img{transform:scale(1.04)}
.mk-img-placeholder{width:100%;height:100%;background:linear-gradient(135deg,#0f2444,#1e4d8c);display:flex;align-items:center;justify-content:center}
.mk-img-placeholder svg{width:3rem;height:3rem;opacity:.2;color:#fff}

/* ── Overlays ── */
.mk-badges{position:absolute;top:10px;inset-inline-start:10px;display:flex;gap:5px;flex-wrap:wrap}
.mk-badge-type{font-size:.58rem;font-weight:800;letter-spacing:.07em;text-transform:uppercase;background:rgba(15,36,68,.72);backdrop-filter:blur(4px);color:#f0c060;padding:3px 8px;border-radius:5px}
.mk-badge-purpose{font-size:.58rem;font-weight:700;background:rgba(255,255,255,.85);backdrop-filter:blur(4px);color:#0f2444;padding:3px 8px;border-radius:5px}
.mk-photo-count{position:absolute;bottom:9px;inset-inline-end:9px;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);color:#fff;font-size:.6rem;font-weight:700;padding:3px 7px;border-radius:6px;display:flex;align-items:center;gap:3px}

/* ── Details ── */
.mk-details{padding:14px 16px 14px;display:flex;flex-direction:column;flex:1}
.mk-name{font-size:.92rem;font-weight:800;color:#0f2444;text-decoration:none;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:5px;transition:color .2s}
.mk-name:hover{color:#c9a84c}
.mk-location{display:flex;align-items:center;gap:4px;font-size:.7rem;color:#94a3b8;font-weight:500;margin-bottom:10px}
.mk-location svg{color:#c9a84c}

/* ── Specs ── */
.mk-specs{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:10px}
.mk-spec{display:inline-flex;align-items:center;gap:4px;font-size:.72rem;font-weight:600;color:#475569}
.mk-spec svg{width:13px;height:13px;color:#c9a84c}

/* ── Divider ── */
.mk-divider{border:none;border-top:1px solid #f1f5f9;margin:0 0 10px}

/* ── Footer: price + actions ── */
.mk-footer{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:auto}
.mk-price{display:flex;align-items:baseline;gap:3px}
.mk-price-val{font-size:1.1rem;font-weight:900;color:#0f2444;letter-spacing:-.02em}
.mk-price-cur{font-size:.65rem;font-weight:700;color:#a0856a}
.mk-price-na{font-size:.72rem;color:#94a3b8;font-style:italic}

/* ── Action buttons ── */
.mk-actions{display:flex;gap:5px;align-items:center}
.mk-btn-outline{width:32px;height:32px;display:flex;align-items:center;justify-content:center;border:1.5px solid #e2e8f0;border-radius:8px;color:#475569;text-decoration:none;transition:all .2s;flex-shrink:0}
.mk-btn-outline svg{width:14px;height:14px}
.mk-btn-outline:hover{border-color:#0f2444;color:#0f2444;background:#f8fafc}
.mk-btn-wa{width:32px;height:32px;display:flex;align-items:center;justify-content:center;background:#25d366;border-radius:8px;color:#fff;text-decoration:none;transition:opacity .2s;flex-shrink:0}
.mk-btn-wa svg{width:14px;height:14px}
.mk-btn-wa:hover{opacity:.85}
.mk-btn-primary{display:inline-flex;align-items:center;gap:4px;padding:7px 12px;background:#0f2444;border-radius:8px;font-size:.7rem;font-weight:700;color:#fff;text-decoration:none;transition:background .2s;white-space:nowrap}
.mk-btn-primary:hover{background:#162f5c}

/* ── Empty ── */
.mk-empty{grid-column:1/-1;text-align:center;padding:4rem 1rem;color:#94a3b8}
.mk-empty svg{width:3rem;height:3rem;margin:0 auto 1rem;opacity:.4}

/* ── Responsive ── */
@media(max-width:1024px){.mk-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:600px){.mk-grid{grid-template-columns:1fr;gap:14px}}
</style>
