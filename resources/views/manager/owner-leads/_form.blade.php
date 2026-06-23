@php
    $isAr = app()->getLocale() === 'ar';
    $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    $locale = $isAr ? 'ar' : 'en';
    $o = $ownerLead ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Name --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الاسم', 'Full Name') }} <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $o?->name) }}" required
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none @error('name') border-red-400 @enderror">
        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Mobile --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('رقم الهاتف / واتساب', 'Mobile / WhatsApp') }}</label>
        <input type="text" name="mobile" value="{{ old('mobile', $o?->mobile) }}"
               placeholder="{{ $tr('مثال: 0512345678', 'e.g. 0512345678') }}"
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none @error('mobile') border-red-400 @enderror">
        @error('mobile')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Email --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('البريد الإلكتروني', 'Email') }}</label>
        <input type="email" name="email" value="{{ old('email', $o?->email) }}"
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none @error('email') border-red-400 @enderror">
        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Location --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المنطقة / الموقع', 'Area / Location') }}</label>
        <select name="location" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none">
            <option value="">{{ $tr('-- اختر المنطقة --', '-- Select Area --') }}</option>
            @foreach(\App\Models\OwnerLead::$locations as $ar => $en)
            <option value="{{ $ar }}" @selected(old('location', $o?->location) === $ar)>
                {{ $isAr ? $ar : $en }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- Property Type --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('نوع العقار', 'Property Type') }}</label>
        <select name="property_type" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
            @foreach(\App\Models\OwnerLead::$propertyTypes as $val => $labels)
            <option value="{{ $val }}" @selected(old('property_type', $o?->property_type ?? 'any') === $val)>{{ $labels[$locale] }}</option>
            @endforeach
        </select>
    </div>

    {{-- Purpose --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الغرض', 'Purpose') }}</label>
        <select name="purpose" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
            @foreach(\App\Models\OwnerLead::$purposes as $val => $labels)
            <option value="{{ $val }}" @selected(old('purpose', $o?->purpose ?? 'both') === $val)>{{ $labels[$locale] }}</option>
            @endforeach
        </select>
    </div>

    {{-- Min Budget --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحد الأدنى للميزانية (ريال)', 'Min Budget (OMR)') }}</label>
        <input type="number" step="1" min="0" name="min_budget" value="{{ old('min_budget', $o?->min_budget) }}"
               placeholder="{{ $tr('اختياري', 'Optional') }}"
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none">
    </div>

    {{-- Max Budget --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحد الأقصى للميزانية (ريال)', 'Max Budget (OMR)') }}</label>
        <input type="number" step="1" min="0" name="max_budget" value="{{ old('max_budget', $o?->max_budget) }}"
               placeholder="{{ $tr('اختياري', 'Optional') }}"
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none">
    </div>

    {{-- Bedrooms --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('عدد غرف النوم', 'Bedrooms') }}</label>
        <input type="number" min="0" max="20" name="bedrooms" value="{{ old('bedrooms', $o?->bedrooms) }}"
               placeholder="{{ $tr('اختياري', 'Optional') }}"
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none">
    </div>

    {{-- Status --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحالة', 'Status') }}</label>
        <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
            @foreach(\App\Models\OwnerLead::$statuses as $val => $labels)
            <option value="{{ $val }}" @selected(old('status', $o?->status ?? 'new') === $val)>{{ $labels[$locale] }}</option>
            @endforeach
        </select>
    </div>

    {{-- Source --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المصدر', 'Source') }}</label>
        <select name="source" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
            <option value="">{{ $tr('-- اختر المصدر --', '-- Select Source --') }}</option>
            @foreach(\App\Models\OwnerLead::$sources as $val => $labels)
            <option value="{{ $val }}" @selected(old('source', $o?->source) === $val)>{{ $labels[$locale] }}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- Notes --}}
<div class="mt-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('ملاحظات إضافية', 'Additional Notes') }}</label>
    <textarea name="notes" rows="3"
              placeholder="{{ $tr('أي تفاصيل إضافية…', 'Any extra details…') }}"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none resize-none">{{ old('notes', $o?->notes) }}</textarea>
</div>
