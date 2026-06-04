{{-- Shared form partial for create/edit --}}
@php $isEdit = isset($news) && $news->exists ?? false; @endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

  {{-- ── Main column: text fields ── --}}
  <div class="lg:col-span-2 space-y-5">

    <div class="bg-white border rounded-2xl p-5 space-y-5" style="border-color:#e2e8f0">
      <h3 class="text-xs font-bold uppercase tracking-wider pb-3 border-b" style="color:#64748b;border-color:#f1f5f9">
        {{ app()->getLocale() === 'ar' ? 'محتوى المقال' : 'Article Content' }}
      </h3>

      {{-- Title AR --}}
      <div>
        <label class="block text-xs font-semibold mb-1.5" style="color:#475569">
          {{ app()->getLocale() === 'ar' ? 'العنوان (عربي)' : 'Title (Arabic)' }} <span class="text-red-500">*</span>
        </label>
        <input type="text" name="title_ar" value="{{ old('title_ar', $news->title_ar ?? '') }}"
               class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none"
               style="border-color:#e2e8f0" required dir="rtl">
        @error('title_ar') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Title EN --}}
      <div>
        <label class="block text-xs font-semibold mb-1.5" style="color:#475569">
          {{ app()->getLocale() === 'ar' ? 'العنوان (إنجليزي)' : 'Title (English)' }}
        </label>
        <input type="text" name="title_en" value="{{ old('title_en', $news->title_en ?? '') }}"
               class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none"
               style="border-color:#e2e8f0" dir="ltr">
      </div>

      {{-- Excerpt AR --}}
      <div>
        <label class="block text-xs font-semibold mb-1.5" style="color:#475569">
          {{ app()->getLocale() === 'ar' ? 'مقدمة قصيرة (عربي)' : 'Short Excerpt (Arabic)' }}
        </label>
        <textarea name="excerpt_ar" rows="2"
                  class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none resize-none"
                  style="border-color:#e2e8f0" dir="rtl">{{ old('excerpt_ar', $news->excerpt_ar ?? '') }}</textarea>
      </div>

      {{-- Excerpt EN --}}
      <div>
        <label class="block text-xs font-semibold mb-1.5" style="color:#475569">
          {{ app()->getLocale() === 'ar' ? 'مقدمة قصيرة (إنجليزي)' : 'Short Excerpt (English)' }}
        </label>
        <textarea name="excerpt_en" rows="2"
                  class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none resize-none"
                  style="border-color:#e2e8f0" dir="ltr">{{ old('excerpt_en', $news->excerpt_en ?? '') }}</textarea>
      </div>

      {{-- Body AR --}}
      <div>
        <label class="block text-xs font-semibold mb-1.5" style="color:#475569">
          {{ app()->getLocale() === 'ar' ? 'المحتوى (عربي)' : 'Body (Arabic)' }}
        </label>
        <textarea name="body_ar" rows="9"
                  class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none resize-y"
                  style="border-color:#e2e8f0" dir="rtl">{{ old('body_ar', $news->body_ar ?? '') }}</textarea>
      </div>

      {{-- Body EN --}}
      <div>
        <label class="block text-xs font-semibold mb-1.5" style="color:#475569">
          {{ app()->getLocale() === 'ar' ? 'المحتوى (إنجليزي)' : 'Body (English)' }}
        </label>
        <textarea name="body_en" rows="9"
                  class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none resize-y"
                  style="border-color:#e2e8f0" dir="ltr">{{ old('body_en', $news->body_en ?? '') }}</textarea>
      </div>
    </div>

  </div>

  {{-- ── Sidebar ── --}}
  <div class="space-y-4">

    {{-- Publish --}}
    <div class="bg-white border rounded-2xl p-5" style="border-color:#e2e8f0">
      <h3 class="text-xs font-bold uppercase tracking-wider mb-4" style="color:#64748b">
        {{ app()->getLocale() === 'ar' ? 'النشر' : 'Publish' }}
      </h3>
      <button type="submit"
              class="w-full py-3 rounded-xl text-sm font-bold text-white transition flex items-center justify-center gap-2"
              style="background:#0f2444"
              onmouseover="this.style.background='#1a3a6b'" onmouseout="this.style.background='#0f2444'">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
        {{ app()->getLocale() === 'ar' ? 'حفظ المقال' : 'Save Article' }}
      </button>
      <a href="{{ route('manager.news.index') }}"
         class="block text-center mt-2 text-xs font-semibold py-2.5 rounded-xl border transition"
         style="border-color:#e2e8f0;color:#64748b"
         onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
      </a>
    </div>

    {{-- Status --}}
    <div class="bg-white border rounded-2xl p-5" style="border-color:#e2e8f0">
      <h3 class="text-xs font-bold uppercase tracking-wider mb-3" style="color:#64748b">
        {{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}
      </h3>
      <label class="flex items-center gap-3 cursor-pointer select-none">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded accent-amber-500"
               {{ old('is_active', ($news->is_active ?? true) ? '1' : '0') == '1' ? 'checked' : '' }}>
        <span class="text-sm font-medium" style="color:#1e293b">
          {{ app()->getLocale() === 'ar' ? 'مقال منشور' : 'Published' }}
        </span>
      </label>
    </div>

    {{-- Published date --}}
    <div class="bg-white border rounded-2xl p-5" style="border-color:#e2e8f0">
      <h3 class="text-xs font-bold uppercase tracking-wider mb-3" style="color:#64748b">
        {{ app()->getLocale() === 'ar' ? 'تاريخ النشر' : 'Publish Date' }}
      </h3>
      <input type="datetime-local" name="published_at"
             value="{{ old('published_at', isset($news->published_at) ? $news->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
             class="w-full rounded-xl border px-3 py-2 text-sm focus:outline-none"
             style="border-color:#e2e8f0">
    </div>

    {{-- Sort order --}}
    <div class="bg-white border rounded-2xl p-5" style="border-color:#e2e8f0">
      <label class="block text-xs font-bold uppercase tracking-wider mb-3" style="color:#64748b">
        {{ app()->getLocale() === 'ar' ? 'ترتيب العرض' : 'Sort Order' }}
      </label>
      <input type="number" name="sort_order" min="0"
             value="{{ old('sort_order', $news->sort_order ?? 0) }}"
             class="w-full rounded-xl border px-3 py-2 text-sm focus:outline-none"
             style="border-color:#e2e8f0">
    </div>

  </div>
</div>

{{-- ══════════ IMAGES SECTION (full width) ══════════ --}}
@php $isAr = app()->getLocale() === 'ar'; @endphp
<div class="bg-white border rounded-2xl overflow-hidden" style="border-color:#e2e8f0">

  {{-- Header --}}
  <div class="px-6 py-4 flex items-center justify-between" style="background:#f8fafc;border-bottom:1px solid #f1f5f9">
    <div class="flex items-center gap-3">
      <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#0f2444">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-white"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
      </div>
      <div>
        <h3 class="text-sm font-bold" style="color:#1e293b">{{ $isAr ? 'صور المقال' : 'Article Images' }}</h3>
        <p class="text-xs" style="color:#94a3b8">{{ $isAr ? 'الصورة الرئيسية تظهر كغلاف في أعلى المقال — صور المعرض تظهر أسفل النص' : 'Cover image appears at the top of the article — gallery images appear below the text' }}</p>
      </div>
    </div>
    @if($isEdit && $news->relationLoaded('images'))
    <span class="text-xs font-bold px-2.5 py-1 rounded-full" style="background:#e2e8f0;color:#475569">
      {{ $news->images->count() }} {{ $isAr ? 'صورة' : 'images' }}
    </span>
    @endif
  </div>

  {{-- Website layout diagram --}}
  <div class="px-6 pt-5 pb-4" style="border-bottom:1px solid #f1f5f9">
    <p class="text-xs font-bold mb-3" style="color:#64748b">{{ $isAr ? 'كيف تظهر الصور على الموقع:' : 'How images appear on the website:' }}</p>
    <div class="flex gap-4 items-start">
      {{-- Diagram --}}
      <div class="flex-shrink-0 rounded-xl overflow-hidden" style="width:200px;background:#f1f5f9;border:1px solid #e2e8f0;padding:10px">
        {{-- Article title bar --}}
        <div class="rounded mb-2" style="height:8px;background:#cbd5e1;width:80%"></div>
        <div class="rounded mb-3" style="height:5px;background:#e2e8f0;width:55%"></div>
        {{-- Cover image area (gold border) --}}
        <div class="rounded-lg mb-1 flex overflow-hidden gap-0.5" style="height:40px;border:2px solid #c9a84c">
          <div class="flex-1" style="background:linear-gradient(135deg,#c9a84c33,#c9a84c22);display:flex;align-items:center;justify-content:center">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:10px;height:10px;color:#c9a84c"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          </div>
          <div style="width:30%;display:flex;flex-direction:column;gap:1px">
            <div style="flex:1;background:#dde3ec;border-radius:2px"></div>
            <div style="flex:1;background:#dde3ec;border-radius:2px"></div>
          </div>
        </div>
        <p style="font-size:7px;color:#c9a84c;font-weight:700;text-align:center;margin-bottom:6px">{{ $isAr ? '← صورة الغلاف' : 'Cover Image ↑' }}</p>
        {{-- Article text lines --}}
        <div class="rounded mb-1" style="height:4px;background:#e2e8f0"></div>
        <div class="rounded mb-1" style="height:4px;background:#e2e8f0;width:90%"></div>
        <div class="rounded mb-3" style="height:4px;background:#e2e8f0;width:75%"></div>
        {{-- Gallery area (navy border) --}}
        <div class="rounded-lg grid grid-cols-4 gap-0.5 mb-1" style="border:2px solid #0f2444;padding:3px">
          @for($d=0;$d<4;$d++)
          <div style="height:14px;background:#dde3ec;border-radius:2px"></div>
          @endfor
        </div>
        <p style="font-size:7px;color:#0f2444;font-weight:700;text-align:center">{{ $isAr ? '← معرض الصور' : 'Gallery ↑' }}</p>
      </div>
      {{-- Legend --}}
      <div class="space-y-3 text-xs">
        <div class="flex items-start gap-2.5">
          <div class="w-4 h-4 rounded flex-shrink-0 mt-0.5" style="background:#c9a84c;border:2px solid #c9a84c"></div>
          <div>
            <p class="font-bold" style="color:#1e293b">{{ $isAr ? 'صورة الغلاف (الرئيسية)' : 'Cover Image (Featured)' }}</p>
            <p style="color:#64748b">{{ $isAr ? 'تظهر في أعلى المقال، كبيرة وبارزة. صورة واحدة فقط.' : 'Appears at the top of the article, large and prominent. Only one.' }}</p>
          </div>
        </div>
        <div class="flex items-start gap-2.5">
          <div class="w-4 h-4 rounded flex-shrink-0 mt-0.5" style="background:#0f2444;border:2px solid #0f2444"></div>
          <div>
            <p class="font-bold" style="color:#1e293b">{{ $isAr ? 'صور المعرض' : 'Gallery Images' }}</p>
            <p style="color:#64748b">{{ $isAr ? 'تظهر في شبكة صور أسفل نص المقال. يمكن إضافة عدد غير محدود.' : 'Appear in an image grid below the article text. Unlimited count.' }}</p>
          </div>
        </div>
        <div class="flex items-start gap-2.5">
          <div class="w-4 h-4 rounded-full flex-shrink-0 mt-0.5 flex items-center justify-center" style="background:#f59e0b">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:8px;height:8px;color:#fff"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          </div>
          <div>
            <p class="font-bold" style="color:#1e293b">{{ $isAr ? 'زر تعيين كغلاف ⭐' : '⭐ Set as Cover button' }}</p>
            <p style="color:#64748b">{{ $isAr ? 'مرّر على أي صورة في المعرض وانقر ⭐ لجعلها الغلاف.' : 'Hover over any gallery image and click ⭐ to make it the cover.' }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="p-6">

    @if($isEdit && $news->relationLoaded('images') && $news->images->isNotEmpty())
    {{-- ── Edit mode: existing images ── --}}
    @php
      $coverImg    = $news->images->firstWhere('is_primary', true) ?? $news->images->first();
      $galleryImgs = $news->images->where('id', '!=', $coverImg->id)->values();
    @endphp

    {{-- ── SECTION 1: Cover Image ── --}}
    <div class="rounded-2xl p-4 mb-5" style="background:#fffbeb;border:2px solid #fcd34d">
      <div class="flex items-center gap-2 mb-3">
        <span class="flex items-center gap-1.5 text-xs font-black px-3 py-1 rounded-full" style="background:#f59e0b;color:#fff">
          <svg viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          {{ $isAr ? 'صورة الغلاف' : 'Cover Image' }}
        </span>
        <span class="text-xs" style="color:#92680a">
          {{ $isAr ? '← تظهر في أعلى المقال على الموقع' : '← Displayed at the top of the article on the website' }}
        </span>
      </div>
      <div class="flex gap-4 items-start">
        <div class="relative rounded-xl overflow-hidden group flex-shrink-0" style="width:200px;height:140px">
          <img src="{{ $coverImg->url() }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="">
          <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition"></div>
        </div>
        <div class="flex-1">
          <p class="text-xs mb-3" style="color:#92680a">
            {{ $isAr ? 'هذه الصورة تظهر كبيرة في أعلى المقال مع بقية الصور جانبها. لتغيير الغلاف، مرّر على أي صورة في المعرض واختر ⭐ "تعيين كغلاف".' : 'This image appears large at the top of the article. To change the cover, hover over any gallery image and click ⭐ "Set as Cover".' }}
          </p>
          <form action="{{ route('manager.news.images.destroy', [$news, $coverImg]) }}" method="POST"
                onsubmit="return confirm('{{ $isAr ? 'حذف صورة الغلاف؟' : 'Delete cover image?' }}')">
            @csrf @method('DELETE')
            <button type="submit" class="flex items-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-lg transition"
                    style="background:#fee2e2;color:#dc2626;border:1px solid #fca5a5"
                    onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
              {{ $isAr ? 'حذف صورة الغلاف' : 'Delete Cover Image' }}
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- ── SECTION 2: Gallery Images ── --}}
    <div class="rounded-2xl p-4 mb-5" style="background:#f0f4ff;border:2px solid #c7d2fe">
      <div class="flex items-center gap-2 mb-3">
        <span class="flex items-center gap-1.5 text-xs font-black px-3 py-1 rounded-full" style="background:#0f2444;color:#fff">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z"/></svg>
          {{ $isAr ? 'معرض الصور' : 'Photo Gallery' }}
          @if($galleryImgs->count()) <span class="font-normal">({{ $galleryImgs->count() }})</span> @endif
        </span>
        <span class="text-xs" style="color:#3730a3">
          {{ $isAr ? '← تظهر في شبكة صور أسفل نص المقال' : '← Displayed in a photo grid below the article text' }}
        </span>
      </div>

      @if($galleryImgs->isNotEmpty())
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-3">
        @foreach($galleryImgs as $idx => $img)
        <div class="relative group rounded-xl overflow-hidden" style="height:90px;border:1px solid #c7d2fe">
          <img src="{{ $img->url() }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" alt="">
          {{-- Index number --}}
          <span class="absolute top-1.5 start-1.5 text-[9px] font-bold text-white px-1.5 py-0.5 rounded" style="background:rgba(15,36,68,.6)">{{ $idx + 1 }}</span>
          {{-- Hover actions --}}
          <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition flex flex-col items-center justify-center gap-1.5 px-2"
               style="background:rgba(0,0,0,.6)">
            <form action="{{ route('manager.news.images.primary', [$news, $img]) }}" method="POST" class="w-full">
              @csrf @method('PATCH')
              <button type="submit" class="w-full flex items-center justify-center gap-1 text-[10px] font-bold py-1 rounded-lg transition"
                      style="background:#f59e0b;color:#fff" title="{{ $isAr ? 'تعيين كغلاف' : 'Set as Cover' }}"
                      onmouseover="this.style.background='#d97706'" onmouseout="this.style.background='#f59e0b'">
                <svg viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                {{ $isAr ? 'تعيين كغلاف' : 'Set Cover' }}
              </button>
            </form>
            <form action="{{ route('manager.news.images.destroy', [$news, $img]) }}" method="POST" class="w-full"
                  onsubmit="return confirm('{{ $isAr ? 'حذف الصورة؟' : 'Delete?' }}')">
              @csrf @method('DELETE')
              <button type="submit" class="w-full flex items-center justify-center gap-1 text-[10px] font-bold py-1 rounded-lg transition"
                      style="background:#ef4444;color:#fff"
                      onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                {{ $isAr ? 'حذف' : 'Delete' }}
              </button>
            </form>
          </div>
        </div>
        @endforeach
      </div>
      @else
      <div class="rounded-xl flex flex-col items-center justify-center gap-2 py-6 mb-3" style="border:2px dashed #c7d2fe;background:#eef2ff">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-8 h-8" style="color:#a5b4fc"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z"/></svg>
        <p class="text-xs font-semibold" style="color:#6366f1">{{ $isAr ? 'لا توجد صور معرض بعد' : 'No gallery images yet' }}</p>
        <p class="text-xs" style="color:#818cf8">{{ $isAr ? 'أضف صوراً أدناه لتظهر في معرض الصور أسفل المقال' : 'Upload images below to show in the gallery under the article' }}</p>
      </div>
      @endif

      {{-- Upload more into gallery --}}
      <form action="{{ route('manager.news.images.store', $news) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="flex items-center gap-3">
          <input type="file" name="images[]" accept="image/*" multiple id="gallery-upload-input"
                 class="flex-1 text-sm text-gray-500
                        file:me-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                        file:text-xs file:font-bold file:text-white cursor-pointer"
                 style="file-selector-button-background:#0f2444"
                 onchange="this.nextElementSibling.style.display='flex';document.getElementById('gallery-upload-count').textContent=this.files.length+' {{ $isAr ? 'ملف' : 'file(s)' }}'">
          <button type="submit" style="display:none;background:#0f2444"
                  class="flex-shrink-0 items-center gap-1.5 text-xs font-bold text-white px-4 py-2 rounded-xl transition"
                  onmouseover="this.style.background='#1a3a6b'" onmouseout="this.style.background='#0f2444'">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
            {{ $isAr ? 'رفع للمعرض' : 'Upload to Gallery' }}
            <span id="gallery-upload-count" class="font-normal opacity-80"></span>
          </button>
        </div>
        <p class="text-xs mt-1.5" style="color:#94a3b8">{{ $isAr ? 'يمكن اختيار عدة صور دفعة واحدة · JPG أو PNG · بحد أقصى 4MB' : 'Multiple files at once · JPG or PNG · max 4MB each' }}</p>
      </form>
    </div>

    @else
    {{-- ── No images yet (create mode or edit with 0 images) ── --}}

    {{-- If old single image exists --}}
    @if($isEdit && !empty($news->image))
    <div class="rounded-2xl p-4 mb-5" style="background:#fffbeb;border:2px solid #fcd34d">
      <p class="text-xs font-bold mb-2" style="color:#92680a">{{ $isAr ? 'الصورة الحالية (قديمة)' : 'Current Image (legacy)' }}</p>
      <div class="flex gap-4 items-center">
        <img src="{{ asset('storage/' . $news->image) }}" class="rounded-xl object-cover flex-shrink-0" style="width:120px;height:80px">
        <p class="text-xs" style="color:#92680a">{{ $isAr ? 'أضف صوراً جديدة أدناه. الصورة الأولى ستصبح الغلاف الجديد.' : 'Upload new images below. The first one will become the new cover.' }}</p>
      </div>
    </div>
    @endif

    {{-- Upload zone with two-column explained layout --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

      {{-- Left: Cover info --}}
      <div class="rounded-2xl p-5" style="background:#fffbeb;border:2px dashed #fcd34d">
        <div class="flex items-center gap-2 mb-3">
          <svg viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4" style="color:#f59e0b"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          <span class="text-xs font-black" style="color:#92680a">{{ $isAr ? 'الصورة الأولى = الغلاف' : 'First Image = Cover' }}</span>
        </div>
        <p class="text-xs leading-relaxed" style="color:#92680a">
          {{ $isAr
            ? 'الصورة الأولى التي تختارها ستكون صورة الغلاف الرئيسية، تظهر في أعلى المقال وفي قائمة الأخبار.'
            : 'The first image you select will be the cover photo, shown at the top of the article and in the news listing.' }}
        </p>
      </div>

      {{-- Right: Gallery info --}}
      <div class="rounded-2xl p-5" style="background:#f0f4ff;border:2px dashed #c7d2fe">
        <div class="flex items-center gap-2 mb-3">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4" style="color:#6366f1"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z"/></svg>
          <span class="text-xs font-black" style="color:#3730a3">{{ $isAr ? 'باقي الصور = معرض' : 'Other Images = Gallery' }}</span>
        </div>
        <p class="text-xs leading-relaxed" style="color:#3730a3">
          {{ $isAr
            ? 'الصور الإضافية تظهر في شبكة صور أسفل نص المقال. يمكن تغيير الغلاف لاحقاً بالضغط على ⭐ أي صورة.'
            : 'Additional images appear in a photo grid below the article text. You can change the cover later by clicking ⭐ on any image.' }}
        </p>
      </div>
    </div>

    {{-- File input --}}
    <div class="mt-5 rounded-2xl p-6 text-center" style="background:#f8fafc;border:2px dashed #e2e8f0">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 mx-auto mb-3" style="color:#94a3b8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
      <p class="text-sm font-semibold mb-4" style="color:#475569">{{ $isAr ? 'اختر صوراً من جهازك (يمكن اختيار عدة صور دفعة واحدة)' : 'Select images from your device (multiple files allowed)' }}</p>
      <input type="file" name="images[]" id="news-images-input" accept="image/*" multiple
             class="block w-full text-sm text-gray-500 mb-3 file:me-3 file:py-2 file:px-5 file:rounded-xl file:border-0 file:text-sm file:font-bold file:text-white cursor-pointer"
             onchange="
               var n=this.files.length,el=document.getElementById('img-preview-list'),badge=document.getElementById('img-count');
               el.innerHTML='';
               if(n>0){
                 badge.textContent=n+' {{ $isAr ? 'صورة محددة — الأولى ستكون الغلاف' : 'selected — first will be cover' }}';
                 badge.style.display='inline-block';
                 for(var i=0;i<n;i++){
                   var r=new FileReader(),wrap=document.createElement('div');
                   wrap.style.cssText='position:relative;width:64px;height:64px;border-radius:10px;overflow:hidden;flex-shrink:0;'+(i===0?'border:2.5px solid #f59e0b':'border:1.5px solid #e2e8f0');
                   if(i===0){var badge2=document.createElement('span');badge2.textContent='★';badge2.style.cssText='position:absolute;top:2px;left:2px;background:rgba(245,158,11,.9);color:#fff;font-size:9px;font-weight:900;padding:1px 4px;border-radius:4px;';wrap.appendChild(badge2);}
                   r.onload=(function(w){return function(e){var img=document.createElement('img');img.src=e.target.result;img.style.cssText='width:100%;height:100%;object-fit:cover';w.appendChild(img);}})(wrap);
                   r.readAsDataURL(this.files[i]);
                   el.appendChild(wrap);
                 }
               }
             ">
      <div id="img-preview-list" class="flex flex-wrap gap-2 justify-center mt-2"></div>
      <span id="img-count" class="mt-3 text-xs font-bold px-3 py-1 rounded-full" style="display:none;background:#dcfce7;color:#15803d"></span>
    </div>
    @endif

  </div>
</div>
