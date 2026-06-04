{{--
  Property cards partial — horizontal list layout
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
<div @if($gridId) id="{{ $gridId }}" @endif class="space-y-4">
  @forelse($properties as $p)
  @php
    $cardIdx  = $loop->index;
    $isHidden = $initialShow !== null && $cardIdx >= $initialShow;
    $img1     = $p->images->get(0);
    $img2     = $p->images->get(1);
    $img3     = $p->images->get(2);
    $minRent  = $p->units->where('listing_type','rent')->whereNotNull('rent_price')->min('rent_price');
    $minSale  = $p->units->where('listing_type','sale')->whereNotNull('sale_price')->min('sale_price');
    $price    = $minSale ?? $minRent;
    $maxBeds  = $p->units->max('bedrooms');
    $maxBath  = $p->units->max('bathrooms');
    $maxArea  = $p->units->max('area');
  @endphp

  <div class="mk-card bg-white flex{{ $isHidden ? ' featured-hidden' : '' }}"
       style="border:1px solid #e8ecf0;border-radius:12px;overflow:hidden;height:260px;transition:box-shadow .3s,border-color .3s{{ $isHidden ? ';display:none' : '' }}">

    {{-- ── Gallery ── --}}
    <div class="relative flex overflow-hidden flex-shrink-0" style="width:50%">

      {{-- Main image --}}
      <div class="relative overflow-hidden" style="{{ ($img2 || $img3) ? 'flex:3' : 'flex:1' }}">
        @if($img1)
        <img src="{{ $img1->url() }}" loading="lazy" alt="{{ $p->name_ar ?: $p->name }}"
             style="width:100%;height:100%;object-fit:cover;transition:transform .5s">
        @else
        <div style="width:100%;height:100%;background:linear-gradient(135deg,#0f2444,#1e4d8c);display:flex;align-items:center;justify-content:center">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="width:3rem;height:3rem;opacity:.18;color:#fff"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
        </div>
        @endif
      </div>

      {{-- 2 stacked side images --}}
      @if($img2 || $img3)
      <div class="flex flex-col gap-0.5 flex-shrink-0" style="flex:2;margin-left:2px">
        <div class="overflow-hidden" style="flex:1;min-height:0">
          @if($img2)
          <img src="{{ $img2->url() }}" loading="lazy" alt=""
               style="width:100%;height:100%;object-fit:cover;transition:transform .5s">
          @else
          <div style="width:100%;height:100%;background:#dde3ec"></div>
          @endif
        </div>
        <div class="overflow-hidden" style="flex:1;min-height:0">
          @if($img3)
          <img src="{{ $img3->url() }}" loading="lazy" alt=""
               style="width:100%;height:100%;object-fit:cover;transition:transform .5s">
          @else
          <div style="width:100%;height:100%;background:#dde3ec"></div>
          @endif
        </div>
      </div>
      @endif

      {{-- Bottom-left: expand + photo count --}}
      <div class="absolute bottom-2.5 start-2.5 flex items-center gap-1.5 z-10">
        <a href="{{ route('properties.show', $p) }}"
           style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.88);display:flex;align-items:center;justify-content:center;transition:background .2s;text-decoration:none"
           onmouseover="this.style.background='#fff'" onmouseout="this.style.background='rgba(255,255,255,.88)'">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;color:#0f2444"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
        </a>
        @if($p->images->count() > 1)
        <span style="background:rgba(0,0,0,.5);color:#fff;font-size:.62rem;font-weight:700;padding:2px 7px;border-radius:999px">
          {{ $p->images->count() }}
          <span data-ar>صور</span><span data-en class="hidden">photos</span>
        </span>
        @endif
      </div>
    </div>

    {{-- ── Details ── --}}
    <div class="flex flex-col flex-1 min-w-0" style="padding:16px 20px 14px">

      @php
        $typeAr = match($p->type) {
          'apartment_building'=>'عمارة','villa'=>'فيلا','farm'=>'مزرعة','chalet'=>'شاليه',
          'apartment'=>'شقة','office'=>'مكتب','shop'=>'محل','studio'=>'استوديو','land'=>'أرض',default=>$p->type,
        };
        $typeEn = match($p->type) {
          'apartment_building'=>'Building','villa'=>'Villa','farm'=>'Farm','chalet'=>'Chalet',
          'apartment'=>'Apartment','office'=>'Office','shop'=>'Shop','studio'=>'Studio','land'=>'Land',default=>ucfirst($p->type),
        };
        $purposeAr = match($p->purpose??''){'rent'=>'للإيجار','sale'=>'للبيع','both'=>'إيجار/بيع',default=>''};
        $purposeEn = match($p->purpose??''){'rent'=>'For Rent','sale'=>'For Sale','both'=>'Rent/Sale',default=>''};
      @endphp

      {{-- Row 1: type badge + purpose --}}
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
        <span style="font-size:.65rem;font-weight:800;letter-spacing:.07em;text-transform:uppercase;color:#c9a84c">
          <span data-ar>{{ $typeAr }}</span><span data-en class="hidden">{{ $typeEn }}</span>
        </span>
        @if($purposeAr)
        <span style="font-size:.62rem;font-weight:700;background:rgba(15,36,68,.07);color:#0f2444;padding:2px 8px;border-radius:6px">
          <span data-ar>{{ $purposeAr }}</span><span data-en class="hidden">{{ $purposeEn }}</span>
        </span>
        @endif
      </div>

      {{-- Row 2: Name --}}
      <a href="{{ route('properties.show', $p) }}"
         style="font-size:.92rem;font-weight:800;color:#0f2444;text-decoration:none;line-height:1.3;margin-bottom:4px;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden"
         onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='#0f2444'">
        <span data-ar>{{ $p->name_ar ?: $p->name }}</span>
        <span data-en class="hidden">{{ $p->name_en ?: $p->name }}</span>
      </a>

      {{-- Row 3: Price --}}
      <div style="margin-bottom:12px">
        @if($price)
        <span style="font-size:1.2rem;font-weight:900;color:#0f2444">{{ number_format($price) }}</span>
        <span style="font-size:.72rem;font-weight:600;color:#a0856a;margin-inline-start:3px">ر.ع</span>
        @endif
      </div>

      {{-- Row 4: Specs --}}
      @if($maxBeds || $maxBath || $maxArea)
      <div style="display:flex;align-items:center;gap:16px;font-size:.78rem;font-weight:600;color:#64748b;border-top:1px solid #f1f5f9;padding-top:10px;margin-bottom:auto">
        @if($maxBeds)
        <span style="display:flex;align-items:center;gap:4px">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:14px;height:14px;color:#c9a84c"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0-6.75H8.25m3.75 0H15.75M3.75 7.5h16.5"/></svg>
          {{ $maxBeds }}
        </span>
        @endif
        @if($maxBath)
        <span style="display:flex;align-items:center;gap:4px">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:14px;height:14px;color:#c9a84c"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
          {{ $maxBath }}
        </span>
        @endif
        @if($maxArea)
        <span style="display:flex;align-items:center;gap:4px">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:14px;height:14px;color:#c9a84c"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
          {{ number_format($maxArea) }} م²
        </span>
        @endif
      </div>
      @endif

      {{-- Row 5: Action buttons --}}
      <div style="display:flex;gap:8px;margin-top:12px">
        @if($phoneClean)
        <a href="tel:{{ $phoneClean }}"
           style="flex:1;display:flex;align-items:center;justify-content:center;gap:5px;padding:8px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:.73rem;font-weight:700;color:#475569;text-decoration:none;transition:all .2s"
           onmouseover="this.style.borderColor='#0f2444';this.style.color='#0f2444'" onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#475569'">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25z"/></svg>
          <span data-ar>اتصال</span><span data-en class="hidden">Call</span>
        </a>
        @endif
        <a href="{{ route('properties.show', $p) }}"
           style="flex:2;display:flex;align-items:center;justify-content:center;gap:5px;padding:8px;background:#0f2444;border:1.5px solid #0f2444;border-radius:10px;font-size:.73rem;font-weight:700;color:#fff;text-decoration:none;transition:all .2s"
           onmouseover="this.style.background='#1a3a6b';this.style.borderColor='#1a3a6b'" onmouseout="this.style.background='#0f2444';this.style.borderColor='#0f2444'">
          <span data-ar>عرض التفاصيل</span><span data-en class="hidden">View Details</span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
        </a>
        @if($waClean)
        <a href="https://api.whatsapp.com/send?phone={{ $waClean }}" target="_blank"
           style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border:1.5px solid #25d366;border-radius:8px;color:#25d366;text-decoration:none;flex-shrink:0;transition:all .2s"
           onmouseover="this.style.background='#25d366';this.style.color='#fff'" onmouseout="this.style.background='';this.style.color='#25d366'">
          <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
        </a>
        @endif
      </div>
    </div>
  </div>
  @empty
  <div class="text-center py-16" style="color:#9ca3af">
    <p data-ar>لا توجد عقارات في هذه المنطقة</p>
    <p data-en class="hidden">No properties in this area</p>
  </div>
  @endforelse
</div>
<style>
  /* Prevent horizontal overflow and make cards stack on small screens */
  .mk-card{box-sizing:border-box;max-width:100%;overflow-x:hidden}
  .mk-card img{display:block;max-width:100%;height:auto}

  @media(max-width:640px){
    .mk-card{flex-direction:column;height:auto !important;width:100% !important;max-width:100% !important}
    .mk-card .relative.flex{width:100% !important;min-width:0 !important}
    .mk-card .relative.flex img, .mk-card .relative.flex > div{width:100% !important;height:auto !important}
    .mk-card .flex-1{width:100% !important}
    .mk-card .min-w-0{padding:12px !important}
    .mk-card .absolute{left:8px;right:auto}
    .mk-card [style*="width:36px"][href*="whatsapp"]{width:40px;height:40px}
    .type-slider-outer, .type-slider-track, .type-slider{max-width:100vw;overflow-x:hidden}
    /* Allow title to use two lines on mobile */
    .mk-card .flex-1 > a{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
  }
</style>
