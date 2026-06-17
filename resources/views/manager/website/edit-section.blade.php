<x-app-layout>
@php
    $isAr      = app()->getLocale() === 'ar';
    $tr        = fn(string $ar, string $en) => $isAr ? $ar : $en;
    $metaLabel = $isAr ? ($meta['label_ar'] ?? $key) : ($meta['label_en'] ?? $meta['label_ar'] ?? $key);
    $pageLabel = $isAr ? $pageInfo['label_ar'] : ($pageInfo['label_en'] ?? $pageInfo['label_ar']);
    $title     = $tr('تعديل القسم — ', 'Edit Section — ') . $metaLabel;
@endphp

<div class="max-w-5xl mx-auto py-8 px-4">

    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
        <a href="{{ route('manager.website.index') }}" class="hover:text-navy">{{ $tr('محتوى الموقع', 'Website Content') }}</a>
        <span>/</span>
        <a href="{{ route('manager.website.page', $page) }}" class="hover:text-navy">{{ $pageLabel }}</a>
        <span>/</span>
        <span class="text-gray-700 font-medium">{{ $metaLabel }}</span>
    </nav>

    @if(session('success'))
    <div class="mb-5 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Section Content Form --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-5">{{ $tr('محتوى القسم', 'Section Content') }}</h2>

        <form method="POST" action="{{ route('manager.website.section.update', [$page, $key]) }}" enctype="multipart/form-data"
              id="websiteSectionForm"
              @if($key === 'hero') x-data="{ heroBgType: '{{ old('hero_bg_type', $extra['hero_bg_type'] ?? 'image') }}' }" @endif>
            @csrf
            @php $extra = (array) ($section->extra ?? []); @endphp

            <div class="grid md:grid-cols-2 gap-5">

                {{-- Title --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $tr('العنوان (عربي)', 'Title (Arabic)') }}</label>
                    <input type="text" name="title_ar" value="{{ old('title_ar', $section->title_ar) }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $tr('العنوان (إنجليزي)', 'Title (English)') }}</label>
                    <input type="text" name="title_en" value="{{ old('title_en', $section->title_en) }}" dir="ltr"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>

                {{-- Subtitle --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $tr('العنوان الفرعي (عربي)', 'Subtitle (Arabic)') }}</label>
                    <input type="text" name="subtitle_ar" value="{{ old('subtitle_ar', $section->subtitle_ar) }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $tr('العنوان الفرعي (إنجليزي)', 'Subtitle (English)') }}</label>
                    <input type="text" name="subtitle_en" value="{{ old('subtitle_en', $section->subtitle_en) }}" dir="ltr"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>

                {{-- Body --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $tr('النص الرئيسي (عربي)', 'Body Text (Arabic)') }}</label>
                    <textarea name="body_ar" rows="3"
                              class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition resize-none">{{ old('body_ar', $section->body_ar) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $tr('النص الرئيسي (إنجليزي)', 'Body Text (English)') }}</label>
                    <textarea name="body_en" rows="3" dir="ltr"
                              class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition resize-none">{{ old('body_en', $section->body_en) }}</textarea>
                </div>

                {{-- Property Showcase picker --}}
                @if(($meta['has_showcase'] ?? false) && $properties->count())
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        {{ $tr('العقار المعروض في الواجهة', 'Featured Property Display') }}
                        <span class="font-normal text-gray-400 ms-1">({{ $tr('اتركه فارغاً لعرض أحدث عقار تلقائياً', 'Leave empty to auto-show latest property') }})</span>
                    </label>
                    <select name="showcase_property_id"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition bg-white">
                        <option value="">{{ $tr('— أحدث عقار نشط (تلقائي) —', '— Latest active property (auto) —') }}</option>
                        @foreach($properties as $prop)
                        <option value="{{ $prop->id }}"
                            {{ old('showcase_property_id', $extra['showcase_property_id'] ?? '') == $prop->id ? 'selected' : '' }}>
                            {{ $prop->name }}
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $tr('اختر عقاراً محدداً لتثبيته في هذا القسم، أو اتركه فارغاً ليظهر أحدث عقار مضاف.', 'Choose a specific property to pin in this section, or leave empty to show the latest added property.') }}
                    </p>
                </div>
                @endif

                {{-- Button --}}
                @if(!in_array($key, ['stats', 'testimonials', 'partners', 'contact']))
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $tr('نص الزر (عربي)', 'Button Text (Arabic)') }}</label>
                    <input type="text" name="button_text_ar" value="{{ old('button_text_ar', $section->button_text_ar) }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $tr('نص الزر (إنجليزي)', 'Button Text (English)') }}</label>
                    <input type="text" name="button_text_en" value="{{ old('button_text_en', $section->button_text_en) }}" dir="ltr"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $tr('رابط الزر', 'Button URL') }}</label>
                    <input type="text" name="button_url" value="{{ old('button_url', $section->button_url) }}" dir="ltr"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                @endif

                {{-- Hero extra fields --}}
                @if($key === 'hero')
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-2">{{ $tr('نوع خلفية البانر الرئيسي', 'Hero Banner Background Type') }}</label>
                    <div class="flex gap-6 mb-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="hero_bg_type" value="image" x-model="heroBgType" class="accent-navy">
                            <span class="text-sm text-gray-700">{{ $tr('صورة', 'Image') }}</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="hero_bg_type" value="video" x-model="heroBgType" class="accent-navy">
                            <span class="text-sm text-gray-700">{{ $tr('فيديو', 'Video') }}</span>
                        </label>
                    </div>
                </div>

                <div class="md:col-span-2 space-y-4" x-show="heroBgType === 'video'" x-cloak>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $tr('رابط الفيديو', 'Video URL') }}</label>
                        <input type="text" name="hero_video_url"
                               value="{{ old('hero_video_url', $extra['hero_video_url'] ?? '') }}"
                               dir="ltr" placeholder="https://www.youtube.com/watch?v=..."
                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                        <p class="text-xs text-gray-400 mt-1">{{ $tr('يدعم روابط YouTube وVimeo والروابط المباشرة (MP4 / WebM)', 'Supports YouTube, Vimeo, and direct links (MP4 / WebM)') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span class="text-xs text-gray-400 font-medium">{{ $tr('أو رفع ملف فيديو', 'or upload a video file') }}</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $tr('رفع فيديو (MP4، WebM)', 'Upload Video (MP4, WebM)') }}</label>
                        @if(!empty($extra['hero_video_file']))
                        <div class="mb-3">
                            <video src="{{ asset('storage/' . $extra['hero_video_file']) }}"
                                   class="h-28 rounded-xl border border-gray-200 bg-black" controls muted></video>
                            <p class="text-xs text-gray-400 mt-1">{{ $tr('الفيديو الحالي — ارفع فيديو جديداً لاستبداله', 'Current video — upload a new one to replace it') }}</p>
                            <label class="inline-flex items-center gap-2 mt-2 cursor-pointer">
                                <input type="checkbox" name="remove_hero_video_file" value="1"
                                       class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-200">
                                <span class="text-xs text-red-600 font-medium">{{ $tr('إزالة الفيديو المرفوع لاستخدام رابط فيديو فقط', 'Remove uploaded video to use a video URL only') }}</span>
                            </label>
                        </div>
                        @endif
                        <input type="file" name="hero_video_file" accept="video/mp4,video/webm,video/ogg,video/quicktime"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-navy/8 file:text-navy file:font-medium hover:file:bg-navy hover:file:text-white file:transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $tr('نص الشارة التعريفية (عربي)', 'Badge Text (Arabic)') }}</label>
                    <input type="text" name="badge_ar" value="{{ old('badge_ar', $extra['badge_ar'] ?? '') }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $tr('نص الشارة التعريفية (إنجليزي)', 'Badge Text (English)') }}</label>
                    <input type="text" name="badge_en" value="{{ old('badge_en', $extra['badge_en'] ?? '') }}" dir="ltr"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $tr('نص الزر الثاني (عربي)', '2nd Button Text (Arabic)') }}</label>
                    <input type="text" name="btn2_text_ar" value="{{ old('btn2_text_ar', $extra['btn2_text_ar'] ?? '') }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $tr('نص الزر الثاني (إنجليزي)', '2nd Button Text (English)') }}</label>
                    <input type="text" name="btn2_text_en" value="{{ old('btn2_text_en', $extra['btn2_text_en'] ?? '') }}" dir="ltr"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $tr('رابط الزر الثاني', '2nd Button URL') }}</label>
                    <input type="text" name="btn2_url" value="{{ old('btn2_url', $extra['btn2_url'] ?? '') }}" dir="ltr"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                @endif

                {{-- About extra --}}
                @if($key === 'about')
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $tr('نص الشارة العائمة (مثال: معدل إشغال 97%)', 'Floating Badge Text (e.g. 97% Occupancy Rate)') }}</label>
                    <input type="text" name="badge_ar" value="{{ old('badge_ar', $extra['badge_ar'] ?? '') }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                @endif

                {{-- Footer social links --}}
                @if($key === 'footer')
                @php
                $socialFields = [
                    'whatsapp'  => ['ar' => 'واتساب — رقم الهاتف فقط',   'en' => 'WhatsApp — phone number only', 'ph' => '+968XXXXXXXX'],
                    'twitter'   => ['ar' => 'تويتر / X',                  'en' => 'Twitter / X',                  'ph' => 'https://x.com/...'],
                    'instagram' => ['ar' => 'انستغرام',                   'en' => 'Instagram',                    'ph' => 'https://instagram.com/...'],
                    'facebook'  => ['ar' => 'فيسبوك',                     'en' => 'Facebook',                     'ph' => 'https://facebook.com/...'],
                    'linkedin'  => ['ar' => 'لينكدإن',                    'en' => 'LinkedIn',                     'ph' => 'https://linkedin.com/...'],
                    'tiktok'    => ['ar' => 'تيك توك',                    'en' => 'TikTok',                       'ph' => 'https://tiktok.com/@...'],
                ];
                @endphp
                @foreach($socialFields as $social => $sf)
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $isAr ? $sf['ar'] : $sf['en'] }}</label>
                    @php $storedVal = old($social, $extra[$social] ?? ''); @endphp
                    @if($social === 'whatsapp')
                    @php $displayVal = preg_replace('/\D/', '', $storedVal); @endphp
                    <div class="flex rounded-xl border border-gray-200 overflow-hidden focus-within:ring-2 focus-within:ring-navy/20 focus-within:border-navy transition">
                        <span class="px-3 flex items-center bg-gray-50 text-gray-400 text-xs border-r border-gray-200 select-none whitespace-nowrap" dir="ltr">+</span>
                        <input type="text" name="{{ $social }}" value="{{ $displayVal }}" dir="ltr"
                               placeholder="{{ $sf['ph'] }}"
                               class="flex-1 px-3 py-2.5 text-sm outline-none bg-white">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ $tr('أدخل الرقم فقط مثل:', 'Enter digits only, e.g.:') }} <span dir="ltr" class="font-mono">+96824000000</span></p>
                    @else
                    <input type="text" name="{{ $social }}" value="{{ $storedVal }}" dir="ltr"
                           placeholder="{{ $sf['ph'] }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                    @endif
                </div>
                @endforeach
                @endif

                {{-- Image --}}
                <div class="md:col-span-2" @if($key === 'hero') x-show="heroBgType === 'image'" x-cloak @endif>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $tr('الصورة الرئيسية', 'Main Image') }}</label>
                    @if($section->image)
                    <div class="mb-3">
                        <img src="{{ $section->imageUrl() }}" alt="current" class="h-32 rounded-xl object-cover border border-gray-200">
                        <p class="text-xs text-gray-400 mt-1">{{ $tr('الصورة الحالية — ارفع صورة جديدة لاستبدالها', 'Current image — upload a new one to replace it') }}</p>
                    </div>
                    @endif
                    <input type="file" name="image" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-navy/8 file:text-navy file:font-medium hover:file:bg-navy hover:file:text-white file:transition">
                </div>

            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-navy text-white text-sm font-bold hover:bg-navy-mid transition">
                    {{ $tr('حفظ التغييرات', 'Save Changes') }}
                </button>
                <a href="{{ route('manager.website.page', $page) }}"
                   class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm hover:bg-gray-50 transition">
                    {{ $tr('إلغاء', 'Cancel') }}
                </a>
            </div>
        </form>
    </div>

    {{-- Items Manager --}}
    @if($meta['has_items'] ?? false)
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-gray-900">{{ $tr('العناصر', 'Items') }} ({{ $items->count() }})</h2>
            <a href="{{ route('manager.website.items.create', [$page, $key]) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gold text-navy text-sm font-bold hover:bg-gold-light transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                {{ $tr('إضافة عنصر', 'Add Item') }}
            </a>
        </div>

        @forelse($items as $item)
        <div class="flex items-center gap-4 py-3 border-b border-gray-100 last:border-0">
            @if($item->image)
            <img src="{{ $item->imageUrl() }}" alt="" class="w-12 h-12 rounded-xl object-cover flex-shrink-0 border border-gray-100">
            @elseif($item->icon)
            <div class="w-12 h-12 rounded-xl bg-navy/8 flex items-center justify-center flex-shrink-0 text-navy font-bold text-sm">
                {{ strtoupper(substr($item->icon, 0, 2)) }}
            </div>
            @else
            <div class="w-12 h-12 rounded-xl bg-gray-100 flex-shrink-0"></div>
            @endif

            <div class="flex-1 min-w-0">
                @php $itemTitle = $isAr ? ($item->title_ar ?? $item->value ?? '—') : ($item->title_en ?: $item->title_ar ?? $item->value ?? '—'); @endphp
                <p class="font-semibold text-sm text-gray-900">{{ $itemTitle }}</p>
                @if($item->title_en && $item->title_ar)
                <p class="text-xs text-gray-400" dir="{{ $isAr ? 'ltr' : 'rtl' }}">{{ $isAr ? $item->title_en : $item->title_ar }}</p>
                @endif
                @php $itemBody = $isAr ? $item->body_ar : ($item->body_en ?: $item->body_ar); @endphp
                @if($itemBody)
                <p class="text-xs text-gray-500 mt-0.5 truncate">{{ Str::limit($itemBody, 55) }}</p>
                @endif
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="text-xs {{ $item->is_active ? 'text-green-600 bg-green-50' : 'text-gray-400 bg-gray-100' }} px-2 py-0.5 rounded-full">
                    {{ $item->is_active ? $tr('نشط', 'Active') : $tr('مخفي', 'Hidden') }}
                </span>
                <a href="{{ route('manager.website.items.edit', [$page, $key, $item]) }}"
                   class="w-8 h-8 rounded-lg bg-navy/8 hover:bg-navy hover:text-white text-navy flex items-center justify-center transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                </a>
                <form method="POST" action="{{ route('manager.website.items.destroy', [$page, $key, $item]) }}"
                      onsubmit="return confirm('{{ $tr('حذف هذا العنصر؟', 'Delete this item?') }}')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-600 hover:text-white text-red-600 flex items-center justify-center transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-gray-400 text-sm">{{ $tr('لا توجد عناصر — أضف العنصر الأول', 'No items yet — add the first one') }}</div>
        @endforelse
    </div>
    @endif

</div>

{{-- Upload progress overlay --}}
<div id="uploadProgressOverlay" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center px-4">
    <div class="bg-white rounded-2xl p-6 w-full max-w-sm shadow-2xl">
        <p class="text-sm font-bold text-gray-800 mb-1" id="uploadProgressLabel">{{ $tr('جاري رفع الملف...', 'Uploading file...') }}</p>
        <p class="text-xs text-gray-400 mb-3" id="uploadProgressDetail">&nbsp;</p>
        <div class="w-full h-3 rounded-full bg-gray-100 overflow-hidden">
            <div id="uploadProgressBar" class="h-full bg-navy rounded-full transition-all" style="width:0%"></div>
        </div>
        <p class="text-end text-xs font-semibold text-navy mt-2" id="uploadProgressPercent">0%</p>
    </div>
</div>

@push('scripts')
<script>
(function(){
    var form = document.getElementById('websiteSectionForm');
    if (!form) return;

    var overlay = document.getElementById('uploadProgressOverlay');
    var bar     = document.getElementById('uploadProgressBar');
    var percent = document.getElementById('uploadProgressPercent');
    var detail  = document.getElementById('uploadProgressDetail');
    var label   = document.getElementById('uploadProgressLabel');

    var uploadingLabel = @json($tr('جاري رفع الملف...', 'Uploading file...'));
    var savingLabel    = @json($tr('جاري الحفظ على الخادم...', 'Saving on the server...'));
    var failLabel      = @json($tr('حدث خطأ أثناء الرفع، حاول مرة أخرى.', 'Upload failed, please try again.'));
    var connErrLabel   = @json($tr('حدث خطأ في الاتصال، حاول مرة أخرى.', 'Connection error, please try again.'));

    function formatMB(bytes){
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    form.addEventListener('submit', function(e){
        var fileInputs = form.querySelectorAll('input[type=file]');
        var hasFile = Array.prototype.some.call(fileInputs, function(input){
            return input.files && input.files.length > 0;
        });
        if (!hasFile) return; // plain text save — no need for a progress UI

        e.preventDefault();

        var submitBtn = form.querySelector('button[type=submit]');
        if (submitBtn) submitBtn.disabled = true;

        label.textContent = uploadingLabel;
        bar.style.width = '0%';
        percent.textContent = '0%';
        detail.textContent = '';
        overlay.classList.remove('hidden');

        var xhr = new XMLHttpRequest();
        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.upload.addEventListener('progress', function(ev){
            if (!ev.lengthComputable) return;
            var pct = Math.round((ev.loaded / ev.total) * 100);
            bar.style.width = pct + '%';
            percent.textContent = pct + '%';
            detail.textContent = formatMB(ev.loaded) + ' / ' + formatMB(ev.total);
            if (pct >= 100) {
                label.textContent = savingLabel;
            }
        });

        xhr.addEventListener('load', function(){
            if (xhr.status >= 200 && xhr.status < 400) {
                window.location.href = xhr.responseURL || form.action;
            } else {
                overlay.classList.add('hidden');
                if (submitBtn) submitBtn.disabled = false;
                alert(failLabel);
            }
        });

        xhr.addEventListener('error', function(){
            overlay.classList.add('hidden');
            if (submitBtn) submitBtn.disabled = false;
            alert(connErrLabel);
        });

        xhr.send(new FormData(form));
    });
})();
</script>
@endpush

</x-app-layout>
