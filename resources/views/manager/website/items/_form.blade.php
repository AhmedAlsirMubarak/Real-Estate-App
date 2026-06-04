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

    {{-- Image --}}
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">الصورة</label>
        @if($item?->image)
        <div class="mb-3">
            <img src="{{ $item->imageUrl() }}" alt="" class="h-28 rounded-xl object-cover border border-gray-200">
            <p class="text-xs text-gray-400 mt-1">الصورة الحالية</p>
        </div>
        @endif
        <input type="file" name="image" accept="image/*"
               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-navy/8 file:text-navy file:font-medium hover:file:bg-navy hover:file:text-white file:transition">
    </div>

    {{-- Active toggle --}}
    <div class="md:col-span-2 flex items-center gap-3">
        <input type="checkbox" name="is_active" id="is_active" value="1"
               {{ old('is_active', $item?->is_active ?? true) ? 'checked' : '' }}
               class="w-4 h-4 rounded border-gray-300 text-navy focus:ring-navy/20">
        <label for="is_active" class="text-sm text-gray-700 font-medium">ظاهر على الموقع</label>
    </div>
</div>
