@php $tr = fn($ar,$en) => app()->getLocale()==='ar' ? $ar : $en; @endphp
<x-app-layout>
<div class="min-h-screen bg-gray-50 py-8">
<div class="max-w-4xl mx-auto px-4">

{{-- Page header --}}
<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('manager.associations.index') }}"
       class="p-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-gray-500 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="{{ app()->getLocale()==='ar' ? 'M9 5l7 7-7 7' : 'M15 19l-7-7 7-7' }}"/>
        </svg>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $tr('التقرير الشامل لجمعية الملاك','Comprehensive HOA Report') }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $tr('توليد تقرير PDF احترافي متكامل لجمعية الملاك','Generate a professional comprehensive PDF report for an owners association') }}</p>
    </div>
</div>

@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
    <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('manager.associations.report.generate') }}" target="_blank">
@csrf

<div class="space-y-6">

{{-- 1. Association & Period ──────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 bg-blue-50 border-b border-blue-100">
        <h2 class="font-semibold text-blue-900 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            {{ $tr('الجمعية والفترة الزمنية','Association & Period') }}
        </h2>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Property --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                {{ $tr('العقار','Property') }} <span class="text-red-500">*</span>
            </label>
            <select name="property_id" required
                    class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm px-4 py-2.5">
                <option value="">{{ $tr('— اختر العقار —','— Select Property —') }}</option>
                <option value="all" {{ old('property_id', $selectedId) === 'all' ? 'selected' : '' }}
                        class="font-semibold text-teal-700">
                    ★ {{ $tr('جميع العقارات (تقرير موحد)','All Properties (Combined Report)') }}
                </option>
                <option disabled>──────────────────────────</option>
                @foreach($properties as $prop)
                <option value="{{ $prop->id }}" {{ old('property_id', $selectedId) == $prop->id ? 'selected' : '' }}>
                    {{ $prop->name }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Quick presets --}}
        <div class="md:col-span-2">
            <p class="text-xs text-gray-500 mb-2">{{ $tr('اختر فترة سريعة:','Quick select:') }}</p>
            <div class="flex flex-wrap gap-2" id="presets">
                @php
                $presets = [
                    ['label_ar'=>'آخر 3 أشهر','label_en'=>'Last 3 months','from'=>now()->subMonths(3)->startOfMonth()->format('Y-m-d'),'to'=>now()->endOfMonth()->format('Y-m-d')],
                    ['label_ar'=>'آخر 6 أشهر','label_en'=>'Last 6 months','from'=>now()->subMonths(6)->startOfMonth()->format('Y-m-d'),'to'=>now()->endOfMonth()->format('Y-m-d')],
                    ['label_ar'=>'هذا العام','label_en'=>'This year','from'=>now()->startOfYear()->format('Y-m-d'),'to'=>now()->endOfYear()->format('Y-m-d')],
                    ['label_ar'=>'السنة الماضية','label_en'=>'Last year','from'=>now()->subYear()->startOfYear()->format('Y-m-d'),'to'=>now()->subYear()->endOfYear()->format('Y-m-d')],
                    ['label_ar'=>'آخر 12 شهر','label_en'=>'Last 12 months','from'=>now()->subYear()->startOfMonth()->format('Y-m-d'),'to'=>now()->endOfMonth()->format('Y-m-d')],
                ];
                @endphp
                @foreach($presets as $p)
                <button type="button"
                        data-from="{{ $p['from'] }}" data-to="{{ $p['to'] }}"
                        onclick="setPreset(this)"
                        class="px-3 py-1.5 text-xs rounded-lg border border-blue-200 text-blue-700 bg-blue-50 hover:bg-blue-100 transition">
                    {{ $tr($p['label_ar'], $p['label_en']) }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- From date --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                {{ $tr('من تاريخ','From Date') }} <span class="text-red-500">*</span>
            </label>
            <input type="date" name="from" id="inp-from"
                   value="{{ old('from', now()->subMonths(3)->startOfMonth()->format('Y-m-d')) }}"
                   required
                   class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm px-4 py-2.5">
        </div>

        {{-- To date --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                {{ $tr('إلى تاريخ','To Date') }} <span class="text-red-500">*</span>
            </label>
            <input type="date" name="to" id="inp-to"
                   value="{{ old('to', now()->endOfMonth()->format('Y-m-d')) }}"
                   required
                   class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm px-4 py-2.5">
        </div>

        {{-- Language --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                {{ $tr('لغة التقرير','Report Language') }}
            </label>
            <select name="locale"
                    class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm px-4 py-2.5">
                <option value="ar" {{ old('locale','ar')==='ar' ? 'selected' : '' }}>العربية</option>
                <option value="en" {{ old('locale','ar')==='en' ? 'selected' : '' }}>English</option>
            </select>
        </div>

        {{-- Output mode --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                {{ $tr('طريقة العرض','Output Mode') }}
            </label>
            <select name="mode"
                    class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm px-4 py-2.5">
                <option value="preview">{{ $tr('عرض في المتصفح','Preview in browser') }}</option>
                <option value="download">{{ $tr('تنزيل مباشرة','Download directly') }}</option>
            </select>
        </div>

    </div>
</div>

{{-- 2. Report Sections ──────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 bg-teal-50 border-b border-teal-100 flex items-center justify-between">
        <h2 class="font-semibold text-teal-900 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            {{ $tr('أقسام التقرير','Report Sections') }}
        </h2>
        <div class="flex gap-3 text-xs">
            <button type="button" onclick="toggleAll(true)"
                    class="text-teal-700 hover:underline">{{ $tr('تحديد الكل','Select all') }}</button>
            <span class="text-gray-300">|</span>
            <button type="button" onclick="toggleAll(false)"
                    class="text-gray-500 hover:underline">{{ $tr('إلغاء الكل','Deselect all') }}</button>
        </div>
    </div>
    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @php
        $sections = [
            ['key'=>'executive_summary',  'ar'=>'الملخص التنفيذي',            'en'=>'Executive Summary',            'icon'=>'📊'],
            ['key'=>'association_info',   'ar'=>'معلومات الجمعية',             'en'=>'Association Information',      'icon'=>'🏢'],
            ['key'=>'financial_summary',  'ar'=>'الملخص المالي',              'en'=>'Financial Summary',             'icon'=>'💰'],
            ['key'=>'contributions',      'ar'=>'تقرير الاشتراكات التفصيلي',  'en'=>'Detailed Contributions',       'icon'=>'📋'],
            ['key'=>'owner_statements',   'ar'=>'كشوف حساب الملاك',           'en'=>'Owner Statements',              'icon'=>'👤'],
            ['key'=>'aging',              'ar'=>'تقرير تقادم الأرصدة',        'en'=>'Aging Report',                  'icon'=>'⏱'],
            ['key'=>'unit_status',        'ar'=>'حالة التحصيل لكل وحدة',      'en'=>'Unit Collection Status',       'icon'=>'🏠'],
            ['key'=>'expenses',           'ar'=>'تقرير المصروفات',            'en'=>'Expense Report',                'icon'=>'💳'],
            ['key'=>'maintenance',        'ar'=>'الصيانة وأوامر العمل',       'en'=>'Maintenance Report',            'icon'=>'🔧'],
            ['key'=>'meetings',           'ar'=>'الاجتماعات والحوكمة',        'en'=>'Meetings & Governance',         'icon'=>'📅'],
            ['key'=>'reserve_fund',       'ar'=>'صندوق الاحتياطي',            'en'=>'Reserve Fund',                  'icon'=>'🏦'],
            ['key'=>'commission_invoices','ar'=>'فواتير العمولة التجارية',     'en'=>'Business Commission Invoices',  'icon'=>'💼'],
            ['key'=>'attachments',        'ar'=>'المرفقات والوثائق',           'en'=>'Attachments',                   'icon'=>'📎'],
        ];
        @endphp
        @foreach($sections as $s)
        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50 hover:bg-teal-50 hover:border-teal-200 cursor-pointer transition group">
            <input type="checkbox" name="sections[]" value="{{ $s['key'] }}" checked
                   class="rounded border-gray-300 text-teal-600 focus:ring-teal-500 w-4 h-4">
            <span class="text-base">{{ $s['icon'] }}</span>
            <span class="text-sm text-gray-700 group-hover:text-teal-900">{{ $tr($s['ar'], $s['en']) }}</span>
        </label>
        @endforeach
    </div>
</div>

{{-- Submit ──────────────────────────────────────────────────────────────── --}}
<div class="flex items-center justify-between">
    <a href="{{ route('manager.associations.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 transition">
        ← {{ $tr('العودة للجمعيات','Back to Associations') }}
    </a>
    <button type="submit"
            class="inline-flex items-center gap-2 px-8 py-3 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-xl shadow-lg shadow-blue-200 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        {{ $tr('توليد التقرير PDF','Generate PDF Report') }}
    </button>
</div>

</div>{{-- /space-y-6 --}}
</form>
</div>
</div>

<script>
function setPreset(btn) {
    document.getElementById('inp-from').value = btn.dataset.from;
    document.getElementById('inp-to').value   = btn.dataset.to;
}
function toggleAll(state) {
    document.querySelectorAll('input[name="sections[]"]').forEach(cb => cb.checked = state);
}
</script>
</x-app-layout>
