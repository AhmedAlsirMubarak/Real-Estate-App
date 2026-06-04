@php
    $isAr = app()->getLocale() === 'ar';
    $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    $report = $report ?? null;
    $sectionValue = 'management';
    $periodValue = old('period_months', $report->period_months ?? \App\Models\ScheduledReport::DEFAULT_PERIODS_MANAGEMENT[0]);
@endphp
<input type="hidden" name="section" value="management">

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('اسم التقرير', 'Report Name') }} <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $report->name ?? '') }}" required
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('القسم', 'Section') }}</label>
        <p class="text-sm text-gray-500 bg-gray-50 rounded-lg px-3 py-2 border border-gray-200">
            {{ $tr('إدارة المباني', 'Building Management') }}
            <span class="text-xs text-gray-400 ms-2">{{ $tr('— تقارير جمعية الملاك متاحة من التقرير الشامل', '— HOA reports available via Comprehensive Report') }}</span>
        </p>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحالة', 'Status') }}</label>
        <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            @php $statusValue = old('status', $report->status ?? 'active'); @endphp
            <option value="active" @selected($statusValue === 'active')>{{ $tr('نشط', 'Active') }}</option>
            <option value="paused" @selected($statusValue === 'paused')>{{ $tr('متوقف', 'Paused') }}</option>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('عقار محدد (اختياري)', 'Specific Property (optional)') }}</label>
        <select name="property_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ $tr('— كل عقارات القسم —', '— All properties in section —') }}</option>
            @foreach($properties as $p)
                <option value="{{ $p->id }}" @selected(old('property_id', $report->property_id ?? null) == $p->id)>
                    {{ $p->name }} ({{ $p->code }})
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الفترة (بالأشهر)', 'Period (months)') }} <span class="text-red-500">*</span></label>
        <div class="flex items-center gap-2">
            <select id="periodPreset" class="border border-gray-200 rounded-lg px-3 py-2 text-sm" onchange="onPresetChange(this.value)">
                <option value="">{{ $tr('قيمة مخصصة', 'Custom') }}</option>
                @foreach(\App\Models\ScheduledReport::DEFAULT_PERIODS_MANAGEMENT as $p)
                    <option value="{{ $p }}" @selected((int)$periodValue===$p)>{{ $p }} {{ $tr('شهر', 'mo.') }}</option>
                @endforeach
            </select>
            <input type="number" name="period_months" min="1" max="60"
                   value="{{ $periodValue }}" required
                   class="w-24 border border-gray-200 rounded-lg px-3 py-2 text-sm">
        </div>
        @error('period_months') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('تاريخ التشغيل القادم', 'Next Run Date') }}</label>
        <input type="date" name="next_run_at" value="{{ old('next_run_at', optional($report->next_run_at ?? null)->format('Y-m-d')) }}"
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
        <p class="text-xs text-gray-500 mt-1">{{ $tr('إذا تركته فارغاً، يبدأ بعد الفترة المحددة', 'If empty, starts after the period elapses') }}</p>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('ملاحظات', 'Notes') }}</label>
        <textarea name="notes" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('notes', $report->notes ?? '') }}</textarea>
    </div>
</div>

<script>
function onPresetChange(val) {
    if (val) document.querySelector('input[name=period_months]').value = val;
}
</script>
