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
        @php
        $areas = [
            ['ar' => 'بوشر',                  'en' => 'Bowsher'],
            ['ar' => 'مرتفعات بوشر',           'en' => 'Bowsher Heights'],
            ['ar' => 'الانصب',                 'en' => 'Al Ansab'],
            ['ar' => 'العذيبة الشمالية',        'en' => 'Al Azaiba North'],
            ['ar' => 'العذيبة الجنوبية',        'en' => 'Al Azaiba South'],
            ['ar' => 'الخوض السادسة',           'en' => 'Al Khoud 6'],
            ['ar' => 'الخوض السابعة',           'en' => 'Al Khoud 7'],
            ['ar' => 'الخوض الرابعة',           'en' => 'Al Khoud 4'],
            ['ar' => 'الخوض الكوثر',            'en' => 'Al Khoud Al Kawthar'],
            ['ar' => 'الموالح الجنوبية',        'en' => 'Al Mawaleh South'],
            ['ar' => 'الموالح الشمالية',        'en' => 'Al Mawaleh North'],
            ['ar' => 'الموج',                   'en' => 'Al Mouj'],
            ['ar' => 'مسقط هيلز',              'en' => 'Muscat Hills'],
            ['ar' => 'القرم',                   'en' => 'Al Qurum'],
            ['ar' => 'الخوير',                  'en' => 'Al Khuwair'],
            ['ar' => 'مدينة الاعلام',           'en' => 'Media City'],
            ['ar' => 'مدينة السلطان قابوس',    'en' => 'Madinat Sultan Qaboos'],
            ['ar' => 'مدينة السلطان هيثم',     'en' => 'Madinat Sultan Haitham'],
            ['ar' => 'الغبرة الشمالية',         'en' => 'Al Ghubra North'],
            ['ar' => 'الغبرة الجنوبية',         'en' => 'Al Ghubra South'],
            ['ar' => 'السيب',                   'en' => 'Al Seeb'],
            ['ar' => 'المعبيلة',                'en' => 'Al Mabelah'],
        ];
        @endphp
        <select name="location" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none">
            <option value="">{{ $tr('-- اختر المنطقة --', '-- Select Area --') }}</option>
            @foreach($areas as $area)
            <option value="{{ $area['ar'] }}" @selected(old('location', $c?->location) === $area['ar'])>
                {{ $isAr ? $area['ar'] : $area['en'] }}
            </option>
            @endforeach
        </select>
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

    {{-- Source --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('مصدر العميل', 'Customer Source') }}</label>
        <select name="source" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
            <option value="">{{ $tr('-- اختر المصدر --', '-- Select Source --') }}</option>
            @foreach(\App\Models\Customer::$sources as $val => $labels)
            <option value="{{ $val }}" @selected(old('source', $c?->source) === $val)>{{ $labels[$locale] }}</option>
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
