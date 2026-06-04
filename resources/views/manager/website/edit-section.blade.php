<x-app-layout>
@php $title = 'تعديل القسم — ' . ($meta['label_ar'] ?? $key); @endphp

<div class="max-w-5xl mx-auto py-8 px-4">

    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
        <a href="{{ route('manager.website.index') }}" class="hover:text-navy">محتوى الموقع</a>
        <span>/</span>
        <a href="{{ route('manager.website.page', $page) }}" class="hover:text-navy">{{ $pageInfo['label_ar'] }}</a>
        <span>/</span>
        <span class="text-gray-700 font-medium">{{ $meta['label_ar'] ?? $key }}</span>
    </nav>

    @if(session('success'))
    <div class="mb-5 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Section Content Form --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-5">محتوى القسم</h2>

        <form method="POST" action="{{ route('manager.website.section.update', [$page, $key]) }}" enctype="multipart/form-data">
            @csrf
            @php $extra = (array) ($section->extra ?? []); @endphp

            <div class="grid md:grid-cols-2 gap-5">

                {{-- Title --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">العنوان (عربي)</label>
                    <input type="text" name="title_ar" value="{{ old('title_ar', $section->title_ar) }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Title (English)</label>
                    <input type="text" name="title_en" value="{{ old('title_en', $section->title_en) }}" dir="ltr"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>

                {{-- Subtitle --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">العنوان الفرعي (عربي)</label>
                    <input type="text" name="subtitle_ar" value="{{ old('subtitle_ar', $section->subtitle_ar) }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Subtitle (English)</label>
                    <input type="text" name="subtitle_en" value="{{ old('subtitle_en', $section->subtitle_en) }}" dir="ltr"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>

                {{-- Body --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">النص الرئيسي (عربي)</label>
                    <textarea name="body_ar" rows="3"
                              class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition resize-none">{{ old('body_ar', $section->body_ar) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Body Text (English)</label>
                    <textarea name="body_en" rows="3" dir="ltr"
                              class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition resize-none">{{ old('body_en', $section->body_en) }}</textarea>
                </div>

                {{-- Property Showcase picker --}}
                @if(($meta['has_showcase'] ?? false) && $properties->count())
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        العقار المعروض في الواجهة
                        <span class="font-normal text-gray-400 ms-1">(اتركه فارغاً لعرض أحدث عقار تلقائياً)</span>
                    </label>
                    <select name="showcase_property_id"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition bg-white">
                        <option value="">— أحدث عقار نشط (تلقائي) —</option>
                        @foreach($properties as $prop)
                        <option value="{{ $prop->id }}"
                            {{ old('showcase_property_id', $extra['showcase_property_id'] ?? '') == $prop->id ? 'selected' : '' }}>
                            {{ $prop->name }}
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">
                        اختر عقاراً محدداً لتثبيته في هذا القسم، أو اتركه فارغاً ليظهر أحدث عقار مضاف.
                    </p>
                </div>
                @endif

                {{-- Button --}}
                @if(!in_array($key, ['stats', 'testimonials', 'partners', 'contact']))
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">نص الزر (عربي)</label>
                    <input type="text" name="button_text_ar" value="{{ old('button_text_ar', $section->button_text_ar) }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Button Text (English)</label>
                    <input type="text" name="button_text_en" value="{{ old('button_text_en', $section->button_text_en) }}" dir="ltr"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">رابط الزر</label>
                    <input type="text" name="button_url" value="{{ old('button_url', $section->button_url) }}" dir="ltr"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                @endif

                {{-- Hero extra fields --}}
                @if($key === 'hero')
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">نص الشارة التعريفية (عربي)</label>
                    <input type="text" name="badge_ar" value="{{ old('badge_ar', $extra['badge_ar'] ?? '') }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Badge Text (English)</label>
                    <input type="text" name="badge_en" value="{{ old('badge_en', $extra['badge_en'] ?? '') }}" dir="ltr"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">نص الزر الثاني (عربي)</label>
                    <input type="text" name="btn2_text_ar" value="{{ old('btn2_text_ar', $extra['btn2_text_ar'] ?? '') }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">2nd Button (English)</label>
                    <input type="text" name="btn2_text_en" value="{{ old('btn2_text_en', $extra['btn2_text_en'] ?? '') }}" dir="ltr"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">رابط الزر الثاني</label>
                    <input type="text" name="btn2_url" value="{{ old('btn2_url', $extra['btn2_url'] ?? '') }}" dir="ltr"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                @endif

                {{-- About extra --}}
                @if($key === 'about')
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">نص الشارة العائمة (مثال: معدل إشغال 97%)</label>
                    <input type="text" name="badge_ar" value="{{ old('badge_ar', $extra['badge_ar'] ?? '') }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                </div>
                @endif

                {{-- Footer social links --}}
                @if($key === 'footer')
                @foreach(['whatsapp' => ['ar'=>'واتساب — رقم الهاتف فقط','ph'=>'+968XXXXXXXX'], 'twitter' => ['ar'=>'تويتر/X','ph'=>'https://x.com/...'], 'instagram' => ['ar'=>'انستغرام','ph'=>'https://instagram.com/...'], 'facebook' => ['ar'=>'فيسبوك','ph'=>'https://facebook.com/...'], 'linkedin' => ['ar'=>'لينكدإن','ph'=>'https://linkedin.com/...']] as $social => $meta)
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $meta['ar'] }}</label>
                    @php $storedVal = old($social, $extra[$social] ?? ''); @endphp
                    @if($social === 'whatsapp')
                    {{-- Strip any full URL down to raw digits for display --}}
                    @php $displayVal = preg_replace('/\D/', '', $storedVal); @endphp
                    <div class="flex rounded-xl border border-gray-200 overflow-hidden focus-within:ring-2 focus-within:ring-navy/20 focus-within:border-navy transition">
                        <span class="px-3 flex items-center bg-gray-50 text-gray-400 text-xs border-r border-gray-200 select-none whitespace-nowrap" dir="ltr">+</span>
                        <input type="text" name="{{ $social }}" value="{{ $displayVal }}" dir="ltr"
                               placeholder="{{ $meta['ph'] }}"
                               class="flex-1 px-3 py-2.5 text-sm outline-none bg-white">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">أدخل الرقم فقط مثل: <span dir="ltr" class="font-mono">+96824000000</span></p>
                    @else
                    <input type="text" name="{{ $social }}" value="{{ $storedVal }}" dir="ltr"
                           placeholder="{{ $meta['ph'] }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-navy/20 focus:border-navy outline-none transition">
                    @endif
                </div>
                @endforeach
                @endif

                {{-- Image --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">الصورة الرئيسية</label>
                    @if($section->image)
                    <div class="mb-3">
                        <img src="{{ $section->imageUrl() }}" alt="current" class="h-32 rounded-xl object-cover border border-gray-200">
                        <p class="text-xs text-gray-400 mt-1">الصورة الحالية — ارفع صورة جديدة لاستبدالها</p>
                    </div>
                    @endif
                    <input type="file" name="image" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-navy/8 file:text-navy file:font-medium hover:file:bg-navy hover:file:text-white file:transition">
                </div>

            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-navy text-white text-sm font-bold hover:bg-navy-mid transition">
                    حفظ التغييرات
                </button>
                <a href="{{ route('manager.website.page', $page) }}"
                   class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm hover:bg-gray-50 transition">
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    {{-- Items Manager --}}
    @if($meta['has_items'] ?? false)
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-gray-900">العناصر ({{ $items->count() }})</h2>
            <a href="{{ route('manager.website.items.create', [$page, $key]) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gold text-navy text-sm font-bold hover:bg-gold-light transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                إضافة عنصر
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
                <p class="font-semibold text-sm text-gray-900">{{ $item->title_ar ?? $item->value ?? '—' }}</p>
                @if($item->title_en)
                <p class="text-xs text-gray-400" dir="ltr">{{ $item->title_en }}</p>
                @endif
                @if($item->body_ar)
                <p class="text-xs text-gray-500 mt-0.5 truncate">{{ Str::limit($item->body_ar, 55) }}</p>
                @endif
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="text-xs {{ $item->is_active ? 'text-green-600 bg-green-50' : 'text-gray-400 bg-gray-100' }} px-2 py-0.5 rounded-full">
                    {{ $item->is_active ? 'نشط' : 'مخفي' }}
                </span>
                <a href="{{ route('manager.website.items.edit', [$page, $key, $item]) }}"
                   class="w-8 h-8 rounded-lg bg-navy/8 hover:bg-navy hover:text-white text-navy flex items-center justify-center transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                </a>
                <form method="POST" action="{{ route('manager.website.items.destroy', [$page, $key, $item]) }}"
                      onsubmit="return confirm('حذف هذا العنصر؟')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-600 hover:text-white text-red-600 flex items-center justify-center transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-gray-400 text-sm">لا توجد عناصر — أضف العنصر الأول</div>
        @endforelse
    </div>
    @endif

</div>
</x-app-layout>
