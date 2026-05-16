@php
    $isAr = app()->getLocale() === 'ar';
    $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    $locale = $isAr ? 'ar' : 'en';
    $c = $customer ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Name --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الاسم', 'Full Name') }} <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $c?->name) }}" required
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none @error('name') border-red-400 @enderror">
        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Mobile --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('رقم الهاتف / واتساب', 'Mobile / WhatsApp') }}</label>
        <input type="text" name="mobile" value="{{ old('mobile', $c?->mobile) }}"
               placeholder="{{ $tr('مثال: 0512345678', 'e.g. 0512345678') }}"
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none @error('mobile') border-red-400 @enderror">
        @error('mobile')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Email --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('البريد الإلكتروني', 'Email') }}</label>
        <input type="email" name="email" value="{{ old('email', $c?->email) }}"
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none @error('email') border-red-400 @enderror">
        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Location --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المنطقة / الموقع المطلوب', 'Desired Area / Location') }}</label>
        <input type="text" name="location" value="{{ old('location', $c?->location) }}"
               placeholder="{{ $tr('مثال: العذيبة، مسقط', 'e.g. Al-Seeb, Muscat') }}"
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none">
    </div>

    {{-- Property Type --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('نوع العقار المطلوب', 'Property Type Needed') }}</label>
        <select name="property_type" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
            @foreach(\App\Models\Customer::$propertyTypes as $val => $labels)
            <option value="{{ $val }}" @selected(old('property_type', $c?->property_type ?? 'any') === $val)>{{ $labels[$locale] }}</option>
            @endforeach
        </select>
    </div>

    {{-- Purpose --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الغرض', 'Purpose') }}</label>
        <select name="purpose" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
            @foreach(\App\Models\Customer::$purposes as $val => $labels)
            <option value="{{ $val }}" @selected(old('purpose', $c?->purpose ?? 'both') === $val)>{{ $labels[$locale] }}</option>
            @endforeach
        </select>
    </div>

    {{-- Min Budget --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحد الأدنى للميزانية (ريال)', 'Min Budget (OMR)') }}</label>
        <input type="number" step="1" min="0" name="min_budget" value="{{ old('min_budget', $c?->min_budget) }}"
               placeholder="{{ $tr('اختياري', 'Optional') }}"
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none">
    </div>

    {{-- Max Budget --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحد الأقصى للميزانية (ريال)', 'Max Budget (OMR)') }}</label>
        <input type="number" step="1" min="0" name="max_budget" value="{{ old('max_budget', $c?->max_budget) }}"
               placeholder="{{ $tr('اختياري', 'Optional') }}"
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none">
    </div>

    {{-- Bedrooms --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('عدد غرف النوم المطلوب', 'Required Bedrooms') }}</label>
        <input type="number" min="0" max="20" name="bedrooms" value="{{ old('bedrooms', $c?->bedrooms) }}"
               placeholder="{{ $tr('اختياري', 'Optional') }}"
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none">
    </div>

    {{-- Status --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('حالة العميل', 'Customer Status') }}</label>
        <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
            @foreach(\App\Models\Customer::$statuses as $val => $labels)
            <option value="{{ $val }}" @selected(old('status', $c?->status ?? 'new') === $val)>{{ $labels[$locale] }}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- Notes --}}
<div class="mt-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('ملاحظات إضافية', 'Additional Notes') }}</label>
    <textarea name="notes" rows="3"
              placeholder="{{ $tr('أي تفاصيل إضافية عن متطلبات العميل…', 'Any extra details about the customer\'s requirements…') }}"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none resize-none">{{ old('notes', $c?->notes) }}</textarea>
</div>
