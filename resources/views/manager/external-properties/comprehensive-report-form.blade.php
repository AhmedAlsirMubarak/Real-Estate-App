@php $tr = fn($ar,$en) => app()->getLocale()==='ar' ? $ar : $en; @endphp
<x-app-layout>
<div class="min-h-screen bg-gray-50 py-8">
<div class="max-w-4xl mx-auto px-4">

{{-- Header --}}
<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('manager.external-properties.index') }}"
       class="p-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-gray-500 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="{{ app()->getLocale()==='ar' ? 'M9 5l7 7-7 7' : 'M15 19l-7-7 7-7' }}"/>
        </svg>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $tr('التقرير الشامل للعقارات الخارجية','Comprehensive External Properties Report') }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $tr('توليد تقرير PDF احترافي شامل للعقارات الخارجية وفواتير العمولة','Generate a comprehensive PDF report for external properties and commission invoices') }}</p>
    </div>
</div>

@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
    <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('manager.external-properties.report.generate') }}" target="_blank">
@csrf

<div class="space-y-6">

{{-- 1. Filters ──────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 bg-emerald-50 border-b border-emerald-100">
        <h2 class="font-semibold text-emerald-900 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            {{ $tr('نطاق التقرير','Report Scope') }}
        </h2>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Property --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $tr('العقار الخارجي','External Property') }}</label>
            <select name="property_id"
                    class="w-full rounded-xl border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-sm px-4 py-2.5">
                <option value="">★ {{ $tr('جميع العقارات الخارجية (تقرير موحد)','All External Properties (Combined Report)') }}</option>
                <option disabled>──────────────────────</option>
                @foreach($properties as $p)
                <option value="{{ $p->id }}" {{ old('property_id', $selectedPropertyId) == $p->id ? 'selected' : '' }}>
                    {{ $p->name_ar ?? $p->name_en }} — {{ $p->code }}
                    @if($p->type) ({{ $p->typeLabel() }}) @endif
                </option>
                @endforeach
            </select>
        </div>

        {{-- Owner --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $tr('المالك','Owner') }}</label>
            <select name="owner_id"
                    class="w-full rounded-xl border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-sm px-4 py-2.5">
                <option value="">{{ $tr('— جميع الملاك —','— All Owners —') }}</option>
                @foreach($owners as $owner)
                <option value="{{ $owner->id }}" {{ old('owner_id') == $owner->id ? 'selected' : '' }}>
                    {{ $owner->user?->name ?? '—' }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Employee --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $tr('الموظف المسؤول','Assigned Employee') }}</label>
            <select name="employee_id"
                    class="w-full rounded-xl border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-sm px-4 py-2.5">
                <option value="">{{ $tr('— جميع الموظفين —','— All Employees —') }}</option>
                @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                    {{ $emp->name }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Language --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $tr('لغة التقرير','Report Language') }}</label>
            <select name="locale"
                    class="w-full rounded-xl border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-sm px-4 py-2.5">
                <option value="ar" {{ old('locale','ar')==='ar'?'selected':'' }}>العربية</option>
                <option value="en" {{ old('locale','ar')==='en'?'selected':'' }}>English</option>
            </select>
        </div>

        {{-- Output mode --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $tr('طريقة العرض','Output Mode') }}</label>
            <select name="mode"
                    class="w-full rounded-xl border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-sm px-4 py-2.5">
                <option value="preview">{{ $tr('عرض في المتصفح','Preview in browser') }}</option>
                <option value="download">{{ $tr('تنزيل مباشرة','Download directly') }}</option>
            </select>
        </div>

        {{-- Quick date presets --}}
        <div class="md:col-span-2">
            <p class="text-xs text-gray-500 mb-2">{{ $tr('اختر فترة سريعة:','Quick period:') }}</p>
            <div class="flex flex-wrap gap-2">
                @php $presets = [
                    ['ar'=>'آخر 3 أشهر','en'=>'Last 3 months','f'=>now()->subMonths(3)->startOfMonth()->format('Y-m-d'),'t'=>now()->endOfMonth()->format('Y-m-d')],
                    ['ar'=>'آخر 6 أشهر','en'=>'Last 6 months','f'=>now()->subMonths(6)->startOfMonth()->format('Y-m-d'),'t'=>now()->endOfMonth()->format('Y-m-d')],
                    ['ar'=>'هذا العام','en'=>'This year','f'=>now()->startOfYear()->format('Y-m-d'),'t'=>now()->endOfYear()->format('Y-m-d')],
                    ['ar'=>'السنة الماضية','en'=>'Last year','f'=>now()->subYear()->startOfYear()->format('Y-m-d'),'t'=>now()->subYear()->endOfYear()->format('Y-m-d')],
                    ['ar'=>'آخر 12 شهر','en'=>'Last 12 months','f'=>now()->subYear()->startOfMonth()->format('Y-m-d'),'t'=>now()->endOfMonth()->format('Y-m-d')],
                ]; @endphp
                @foreach($presets as $p)
                <button type="button" data-from="{{ $p['f'] }}" data-to="{{ $p['t'] }}"
                        onclick="setPreset(this)"
                        class="px-3 py-1.5 text-xs rounded-lg border border-emerald-200 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition">
                    {{ $tr($p['ar'],$p['en']) }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- From --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $tr('من تاريخ','From Date') }} <span class="text-red-500">*</span></label>
            <input type="date" name="from" id="inp-from"
                   value="{{ old('from', now()->subMonths(3)->startOfMonth()->format('Y-m-d')) }}" required
                   class="w-full rounded-xl border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-sm px-4 py-2.5">
        </div>

        {{-- To --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $tr('إلى تاريخ','To Date') }} <span class="text-red-500">*</span></label>
            <input type="date" name="to" id="inp-to"
                   value="{{ old('to', now()->endOfMonth()->format('Y-m-d')) }}" required
                   class="w-full rounded-xl border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-sm px-4 py-2.5">
        </div>

    </div>
</div>

{{-- 2. Sections ─────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 bg-emerald-50 border-b border-emerald-100 flex items-center justify-between">
        <h2 class="font-semibold text-emerald-900 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            {{ $tr('أقسام التقرير','Report Sections') }}
        </h2>
        <div class="flex gap-3 text-xs">
            <button type="button" onclick="toggleAll(true)" class="text-emerald-700 hover:underline">{{ $tr('تحديد الكل','Select all') }}</button>
            <span class="text-gray-300">|</span>
            <button type="button" onclick="toggleAll(false)" class="text-gray-500 hover:underline">{{ $tr('إلغاء الكل','Deselect all') }}</button>
        </div>
    </div>
    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @php $sectionList = [
            ['key'=>'executive_summary',   'ar'=>'الملخص التنفيذي',              'en'=>'Executive Summary',         'icon'=>'📊'],
            ['key'=>'property_info',        'ar'=>'معلومات العقارات',              'en'=>'Property Information',      'icon'=>'🏢'],
            ['key'=>'financial_performance','ar'=>'الأداء المالي',                'en'=>'Financial Performance',     'icon'=>'💰'],
            ['key'=>'occupancy',            'ar'=>'تحليل الإشغال',               'en'=>'Occupancy Analysis',        'icon'=>'🏠'],
            ['key'=>'unit_details',         'ar'=>'تفاصيل الوحدات',              'en'=>'Unit Details',               'icon'=>'🔑'],
            ['key'=>'contracts',            'ar'=>'عقود الإيجار',                'en'=>'Lease Contracts',            'icon'=>'📄'],
            ['key'=>'tenants',              'ar'=>'تقرير المستأجرين',             'en'=>'Tenant Report',             'icon'=>'👤'],
            ['key'=>'rental_income',        'ar'=>'الإيرادات الإيجارية',          'en'=>'Rental Income',             'icon'=>'💵'],
            ['key'=>'outstanding',          'ar'=>'التحصيل والأرصدة المتأخرة',    'en'=>'Outstanding Balances',      'icon'=>'⏱'],
            ['key'=>'expenses',             'ar'=>'تقرير المصروفات',              'en'=>'Expense Management',        'icon'=>'💳'],
            ['key'=>'profitability',        'ar'=>'تحليل الربحية',               'en'=>'Profitability Analysis',    'icon'=>'📈'],
            ['key'=>'maintenance',          'ar'=>'الصيانة',                     'en'=>'Maintenance Report',        'icon'=>'🔧'],
            ['key'=>'vacancy',              'ar'=>'تقرير الوحدات الشاغرة',        'en'=>'Vacancy Report',            'icon'=>'🏗'],
            ['key'=>'alerts',               'ar'=>'التنبيهات والأنشطة القادمة',   'en'=>'Upcoming Alerts',           'icon'=>'🔔'],
            ['key'=>'commission_invoices',  'ar'=>'فواتير العمولة التجارية',      'en'=>'Commission Invoices',        'icon'=>'💼'],
            ['key'=>'attachments',          'ar'=>'المرفقات والوثائق',             'en'=>'Attachments',               'icon'=>'📎'],
        ]; @endphp
        @foreach($sectionList as $s)
        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50 hover:bg-emerald-50 hover:border-emerald-200 cursor-pointer transition group">
            <input type="checkbox" name="sections[]" value="{{ $s['key'] }}" checked
                   class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
            <span class="text-base">{{ $s['icon'] }}</span>
            <span class="text-sm text-gray-700 group-hover:text-emerald-900">{{ $tr($s['ar'],$s['en']) }}</span>
        </label>
        @endforeach
    </div>
</div>

{{-- Submit --}}
<div class="flex items-center justify-between">
    <a href="{{ route('manager.external-properties.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
        ← {{ $tr('العودة للعقارات الخارجية','Back to External Properties') }}
    </a>
    <button type="submit"
            class="inline-flex items-center gap-2 px-8 py-3 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold rounded-xl shadow-lg shadow-emerald-200 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        {{ $tr('توليد التقرير PDF','Generate PDF Report') }}
    </button>
</div>

</div>
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
