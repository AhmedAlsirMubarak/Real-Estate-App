@php
$icons = [
    'building' => 'عمارة/مبنى',
    'key'      => 'مفتاح',
    'users'    => 'مستخدمون',
    'star'     => 'نجمة',
    'wrench'   => 'مفك/صيانة',
    'chart'    => 'مخطط/تقارير',
    'employee' => 'موظف',
    'portal'   => 'بوابة',
    'check'    => 'صح/تأكيد',
    'apartment'=> 'شقق',
    'villa'    => 'فيلا',
    'office'   => 'مكتب',
    'shop'     => 'محل',
    'studio'   => 'استوديو',
    'land'     => 'أرض',
    'location' => 'عنوان',
    'phone'    => 'هاتف',
    'email'    => 'بريد',
    'clock'    => 'ساعة',
];
@endphp

<div class="grid md:grid-cols-2 gap-5">

    {{-- Title --}}
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">العنوان (عربي)</label>
        <input type="text" name="title_ar" value="{{ old('title_ar', $item?->title_ar) }}"
               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Title (English)</label>
        <input type="text" name="title_en" value="{{ old('title_en', $item?->title_en) }}" dir="ltr"
               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
    </div>

    {{-- Subtitle --}}
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">العنوان الفرعي / المنصب (عربي)</label>
        <input type="text" name="subtitle_ar" value="{{ old('subtitle_ar', $item?->subtitle_ar) }}"
               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Subtitle / Role (English)</label>
        <input type="text" name="subtitle_en" value="{{ old('subtitle_en', $item?->subtitle_en) }}" dir="ltr"
               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
    </div>

    {{-- Body --}}
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">النص / الوصف (عربي)</label>
        <textarea name="body_ar" rows="3"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition resize-none">{{ old('body_ar', $item?->body_ar) }}</textarea>
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Description (English)</label>
        <textarea name="body_en" rows="3" dir="ltr"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition resize-none">{{ old('body_en', $item?->body_en) }}</textarea>
    </div>

    {{-- Value (stats) --}}
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">القيمة الرقمية (للإحصائيات، مثال: 50+)</label>
        <input type="text" name="value" value="{{ old('value', $item?->value) }}" dir="ltr"
               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition"
               placeholder="50+, 98%, …">
    </div>

    {{-- URL --}}
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">الرابط (اختياري)</label>
        <input type="text" name="url" value="{{ old('url', $item?->url) }}" dir="ltr"
               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition"
               placeholder="https://… أو /properties?type=villa">
    </div>

    {{-- Icon --}}
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">الأيقونة</label>
        <select name="icon"
                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition bg-white">
            <option value="">— بدون أيقونة —</option>
            @foreach($icons as $iconKey => $iconLabel)
            <option value="{{ $iconKey }}" {{ old('icon', $item?->icon) === $iconKey ? 'selected' : '' }}>{{ $iconLabel }} ({{ $iconKey }})</option>
            @endforeach
        </select>
    </div>

    {{-- Sort order --}}
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">الترتيب</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $item?->sort_order ?? 0) }}" min="0"
               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
    </div>

    {{-- Image / Thumbnail --}}
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
            {{ ($meta['item_type'] ?? '') === 'video' ? 'الصورة المصغرة (اختياري — تُعرض قبل تشغيل الفيديو)' : 'الصورة' }}
        </label>
        @if($item?->image)
        <div class="mb-3">
            <img src="{{ $item->imageUrl() }}" alt="" class="h-28 rounded-xl object-cover border border-gray-200">
            <p class="text-xs text-gray-400 mt-1">الصورة الحالية</p>
        </div>
        @endif
        <input type="file" name="image" accept="image/*"
               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-navy/8 file:text-navy file:font-medium hover:file:bg-navy hover:file:text-white file:transition">
    </div>

    {{-- Video file upload (only for video items) --}}
    @if(($meta['item_type'] ?? '') === 'video')
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">ملف الفيديو <span class="text-red-500">*</span></label>
        @if(!empty($item?->extra['video_path']))
        <div class="mb-3 flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-8 h-8 text-navy flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
            <div>
                <p class="text-xs font-semibold text-gray-700">الفيديو الحالي</p>
                <a href="{{ asset('storage/'.$item->extra['video_path']) }}" target="_blank" class="text-xs text-navy hover:underline">عرض الفيديو</a>
            </div>
        </div>
        @endif
        <input type="file" name="video_file" accept="video/mp4,video/webm,video/mov,video/quicktime"
               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-gold/10 file:text-navy file:font-medium hover:file:bg-gold/20 file:transition">
        <p class="text-xs text-gray-400 mt-1.5">
            @if(app()->getLocale() === 'en')
                Supported formats: MP4, WebM, MOV — Max size: 500 MB
            @else
                الصيغ المدعومة: MP4, WebM, MOV — الحد الأقصى: 500 ميجابايت
            @endif
        </p>
    </div>
    @endif

    {{-- Active toggle --}}
    <div class="md:col-span-2 flex items-center gap-3">
        <input type="checkbox" name="is_active" id="is_active" value="1"
               {{ old('is_active', $item?->is_active ?? true) ? 'checked' : '' }}
               class="w-4 h-4 rounded border-gray-300 text-navy focus:ring-navy/20">
        <label for="is_active" class="text-sm text-gray-700 font-medium">ظاهر على الموقع</label>
    </div>
</div>
