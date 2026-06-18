<x-app-layout>
    <x-slot name="title">{{ $property->name }}</x-slot>

@php
    $isAr = app()->getLocale() === 'ar';
    $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    $isExternal = $isExternal ?? false;
    $routePrefix = $routePrefix ?? 'manager.properties';
@endphp

    <div class="mb-5 flex flex-col md:flex-row md:items-start md:justify-between gap-3">
        <div>
            <a href="{{ route($routePrefix . '.index') }}" class="text-xs text-gray-500 hover:text-gray-700 mb-1 inline-block">
                ← {{ $isExternal ? $tr('العقارات الخارجية', 'External Properties') : $tr('كل العقارات', 'All Properties') }}
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ $property->name }}</h2>
            <div class="flex flex-wrap items-center gap-2 mt-1">
                <span class="font-mono text-xs text-gray-500">{{ $property->code }}</span>
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-indigo-50 text-indigo-700">{{ $property->typeLabel() }}</span>
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs
                    @if($property->purpose === 'rent') bg-blue-50 text-blue-700
                    @elseif($property->purpose === 'sale') bg-green-50 text-green-700
                    @else bg-purple-50 text-purple-700 @endif">
                    {{ $property->purposeLabel() }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1">{{ $property->address }} @if($property->city) — {{ $property->city }} @endif</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route($routePrefix . '.edit', $property) }}" class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-3 py-2 rounded-lg text-sm">
                {{ $tr('تعديل', 'Edit') }}
            </a>
            @if(in_array($property->type, ['apartment_building', 'land']) || $property->units->isEmpty())
            <a href="{{ route('manager.units.create', $property) }}" class="bg-blue-900 hover:bg-blue-800 text-white px-3 py-2 rounded-lg text-sm">
                + {{ $tr('إضافة وحدة', 'Add Unit') }}
            </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ $tr('المالك', 'Owner') }}</p>
            <p class="text-base font-bold text-gray-800 mt-1">
                @if($property->owner)
                    {{ $property->owner->user?->name ?? $tr('مالك', 'Owner') }}
                    <span class="text-xs font-normal text-gray-500 block mt-0.5">
                        {{ $tr('عمولة الشركة', 'Company Commission') }}: {{ $property->owner->commission_rate }}%
                    </span>
                @else
                    <span class="text-blue-700">{{ $tr('الشركة', 'Company') }}</span>
                @endif
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ $tr('الموظف المسؤول', 'Assigned Employee') }}</p>
            <p class="text-base font-bold text-gray-800 mt-1">{{ $property->employee?->name ?? ('— ' . $tr('غير مُسنَد', 'Unassigned') . ' —') }}</p>
            @if($property->employee)
            <form method="POST" action="{{ route('manager.properties.transfer', $property) }}" class="mt-2 flex gap-1">
                @csrf @method('PATCH')
                <select name="employee_id" class="flex-1 border border-gray-200 rounded px-2 py-1 text-xs">
                    <option value="">— {{ $tr('إزالة', 'Remove') }} —</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" @selected($emp->id==$property->employee_id)>{{ $emp->name }}</option>
                    @endforeach
                </select>
                <button class="bg-blue-900 text-white px-2 py-1 rounded text-xs">{{ $tr('تحويل', 'Transfer') }}</button>
            </form>
            @endif
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ $tr('الحالة', 'Status') }}</p>
            <p class="text-base font-bold text-gray-800 mt-1">{{ $property->statusLabel() }}</p>
            @if($property->floors)
            <p class="text-xs text-gray-500 mt-1">{{ $property->floors }} {{ $tr('طوابق', 'floors') }}</p>
            @endif
        </div>
    </div>

    @if($property->description)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-5">
        <p class="text-xs text-gray-500 mb-1">{{ $tr('الوصف', 'Description') }}</p>
        <p class="text-sm text-gray-700">{{ $property->description }}</p>
    </div>
    @endif

    {{-- Units --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-5">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-sm">{{ $tr('الوحدات', 'Units') }} ({{ $property->units->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs">
                    <tr>
                        <th class="px-4 py-2 text-start">{{ $tr('رقم الوحدة', 'Unit No.') }}</th>
                        <th class="px-4 py-2 text-start">{{ $tr('الطابق', 'Floor') }}</th>
                        <th class="px-4 py-2 text-start">{{ $tr('النوع', 'Type') }}</th>
                        <th class="px-4 py-2 text-start">{{ $tr('المساحة', 'Area') }}</th>
                        <th class="px-4 py-2 text-start">{{ $tr('سعر الإيجار', 'Rent Price') }}</th>
                        <th class="px-4 py-2 text-start">{{ $tr('سعر البيع', 'Sale Price') }}</th>
                        <th class="px-4 py-2 text-start">{{ $tr('الحالة', 'Status') }}</th>
                        <th class="px-4 py-2 text-start">{{ $tr('إجراءات', 'Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($property->units as $unit)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5 font-medium">{{ $unit->unit_number ?? '—' }}</td>
                        <td class="px-4 py-2.5">{{ $unit->floor ?? '—' }}</td>
                        <td class="px-4 py-2.5">{{ $unit->typeLabel() }}</td>
                        <td class="px-4 py-2.5">{{ $unit->area ? number_format($unit->area) . ' m²' : '—' }}</td>
                        <td class="px-4 py-2.5">{{ $unit->rent_price ? number_format($unit->rent_price) : '—' }}</td>
                        <td class="px-4 py-2.5">{{ $unit->sale_price ? number_format($unit->sale_price) : '—' }}</td>
                        <td class="px-4 py-2.5">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs
                                @if($unit->status==='available') bg-green-50 text-green-700
                                @elseif($unit->status==='rented') bg-blue-50 text-blue-700
                                @elseif($unit->status==='sold') bg-purple-50 text-purple-700
                                @elseif($unit->status==='reserved') bg-yellow-50 text-yellow-700
                                @else bg-gray-100 text-gray-600 @endif">
                                {{ $unit->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5">
                            <div class="flex gap-1.5 text-xs">
                                <a href="{{ route('manager.units.edit', [$property, $unit]) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $tr('تعديل', 'Edit') }}
                                </a>
                                <span class="text-gray-300">·</span>
                                <form method="POST" action="{{ route('manager.units.destroy', [$property, $unit]) }}"
                                      onsubmit="return confirm('{{ $tr('حذف الوحدة؟', 'Delete this unit?') }}')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800">{{ $tr('حذف', 'Delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-8 text-gray-400 text-sm">{{ $tr('لا توجد وحدات', 'No units found') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent expenses --}}
    @if($property->expenses->count())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 text-sm">{{ $tr('آخر المصروفات', 'Recent Expenses') }}</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($property->expenses as $expense)
            <div class="px-4 py-3 flex items-start justify-between">
                <div>
                    <div class="font-medium text-sm text-gray-800">{{ $expense->title }}</div>
                    <div class="text-xs text-gray-500">{{ $expense->categoryLabel() }} · {{ $expense->expense_date->format('Y-m-d') }}</div>
                </div>
                <div class="text-sm font-bold text-red-600">-{{ number_format($expense->amount) }} {{ $tr('ر.ع', 'OMR') }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Map & Directions ── --}}
    @if($property->latitude && $property->longitude)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-5">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-blue-700"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0z"/></svg>
                {{ $tr('موقع العقار', 'Property Location') }}
                <span class="text-xs font-normal text-gray-400">{{ $property->latitude }}, {{ $property->longitude }}</span>
            </h3>
            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $property->latitude }},{{ $property->longitude }}"
               target="_blank" rel="noopener"
               class="flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-lg text-white bg-blue-900 hover:bg-blue-800 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836-.88 1.38-1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z"/></svg>
                {{ $tr('فتح الاتجاهات', 'Get Directions') }}
            </a>
        </div>
        <div class="p-3">
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
            <div id="mgr-prop-map" style="height:300px;border-radius:10px;border:1px solid #e2e8f0;z-index:0"></div>
            <script>
            (function(){
                var map = L.map('mgr-prop-map', { scrollWheelZoom: false })
                    .setView([{{ $property->latitude }}, {{ $property->longitude }}], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                    maxZoom: 19
                }).addTo(map);
                @php
                    $mgrPopup = '<strong>' . e($property->name) . '</strong>'
                        . ($property->city ? '<br><span style="color:#64748b">' . e($property->city) . '</span>' : '');
                @endphp
                L.marker([{{ $property->latitude }}, {{ $property->longitude }}])
                    .addTo(map)
                    .bindPopup({!! json_encode($mgrPopup) !!})
                    .openPopup();
            })();
            </script>
        </div>
    </div>
    @elseif($property->city || $property->address)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 mt-5 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-sm text-yellow-800">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0z"/></svg>
            {{ $tr('لم يتم تعيين إحداثيات لهذا العقار بعد', 'No map coordinates set for this property yet') }}
        </div>
        <a href="{{ route($routePrefix . '.edit', $property) }}"
           class="text-xs font-bold text-yellow-800 underline hover:text-yellow-900 flex-shrink-0">
            {{ $tr('إضافة إحداثيات', 'Add Coordinates') }} →
        </a>
    </div>
    @endif

    {{-- ── Image Gallery Management ── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-5">
        {{-- Commission Invoice Generator --}}
        @if($property->rent_commission_rate !== null || $property->sale_commission_rate !== null)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-5" x-data="{ open: false }">
            <button type="button" @click="open = !open"
                    class="w-full flex items-center justify-between px-5 py-4 text-left">
                <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    {{ $tr('إصدار فاتورة عمولة أعمال', 'Generate Commission Invoice') }}
                </h3>
                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="open" x-cloak class="px-5 pb-5 border-t border-gray-100 pt-4">
                <form method="POST" action="{{ route('manager.properties.commission-invoice', $property) }}" target="_blank">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Invoice for --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('الفاتورة موجهة إلى', 'Invoice For') }}</label>
                            <select name="invoice_for" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-300">
                                <option value="owner">{{ $tr('المالك', 'Owner') }}</option>
                                <option value="client">{{ $tr('العميل (مستأجر / مشتري)', 'Client (Tenant / Buyer)') }}</option>
                            </select>
                        </div>
                        {{-- Recipient name --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('اسم المستلم', 'Recipient Name') }}</label>
                            <input type="text" name="recipient_name" required
                                   value="{{ $property->owner?->user?->name }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-300">
                        </div>
                        {{-- Monthly rent --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('قيمة الإيجار الشهري (ر.ع)', 'Monthly Rent (OMR)') }}</label>
                            @php $suggestedRent = $rentalContracts->first()?->monthly_rent ?? ''; @endphp
                            <input type="number" step="0.001" min="0" name="monthly_rent" required
                                   value="{{ $suggestedRent }}"
                                   placeholder="0.000"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-300">
                            @if($rentalContracts->isNotEmpty())
                            <p class="text-xs text-gray-400 mt-1">{{ $tr('من العقد النشط', 'From active contract') }}: {{ number_format($suggestedRent, 3) }} ر.ع</p>
                            @endif
                        </div>
                        {{-- Duration --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('مدة العقد (شهور)', 'Contract Duration (months)') }}</label>
                            @php
                                $c = $rentalContracts->first();
                                $suggestedMonths = $c ? $c->start_date->diffInMonths($c->end_date) : '';
                            @endphp
                            <input type="number" min="1" name="duration_months" required
                                   value="{{ $suggestedMonths }}"
                                   placeholder="{{ $tr('مثال: 12', 'e.g. 12') }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-300">
                        </div>
                        {{-- Commission rate --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('نسبة العمولة (%)', 'Commission Rate (%)') }}</label>
                            <input type="number" step="0.01" min="0" max="100" name="commission_rate" required
                                   value="{{ $property->rent_commission_rate ?? $property->sale_commission_rate }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-300">
                        </div>
                        {{-- Invoice date --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('تاريخ الفاتورة', 'Invoice Date') }}</label>
                            <input type="date" name="invoice_date" value="{{ now()->toDateString() }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-300">
                        </div>
                        {{-- Notes --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('ملاحظات (اختياري)', 'Notes (optional)') }}</label>
                            <textarea name="notes" rows="2"
                                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-300 resize-none"></textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            {{ $tr('إصدار الفاتورة (PDF)', 'Generate Invoice (PDF)') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- Commission Invoice History --}}
        @if($commissionInvoices->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-5">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <h3 class="font-bold text-gray-800 text-sm">{{ $tr('فواتير العمولة المُصدرة', 'Issued Commission Invoices') }}</h3>
                <span class="text-xs bg-emerald-100 text-emerald-700 font-semibold px-2 py-0.5 rounded-full">{{ $commissionInvoices->count() }}</span>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($commissionInvoices as $inv)
                <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $inv->invoice_number }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $inv->invoice_date->format('Y/m/d') }} &middot;
                                <span class="font-medium {{ $inv->invoice_for === 'owner' ? 'text-blue-600' : 'text-green-600' }}">
                                    {{ $inv->invoice_for === 'owner' ? $tr('المالك', 'Owner') : $tr('العميل', 'Client') }}
                                </span>
                                &middot; {{ $inv->recipient_name }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0 {{ app()->getLocale() === 'ar' ? 'mr-3' : 'ml-3' }}">
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-800">{{ number_format($inv->commission_amount, 3) }} <span class="text-xs font-normal text-gray-400">ر.ع</span></p>
                            <p class="text-xs text-gray-400">{{ $inv->commission_rate }}% &middot; {{ $inv->duration_months }} {{ $tr('شهر', 'mo') }}</p>
                        </div>
                        <div class="flex items-center gap-1">
                            {{-- View --}}
                            <a href="{{ route('manager.properties.commission-invoice.download', [$property, $inv]) }}"
                               target="_blank"
                               class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition"
                               title="{{ $tr('عرض', 'View') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            {{-- Download --}}
                            <a href="{{ route('manager.properties.commission-invoice.download', [$property, $inv]) . '?download=1' }}"
                               class="p-1.5 rounded-lg text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 transition"
                               title="{{ $tr('تنزيل', 'Download') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                            {{-- Delete --}}
                            <form method="POST" action="{{ route('manager.properties.commission-invoice.destroy', [$property, $inv]) }}"
                                  onsubmit="return confirm('{{ $tr('هل تريد حذف هذه الفاتورة؟', 'Delete this invoice?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
                                        title="{{ $tr('حذف', 'Delete') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-blue-700"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                {{ $tr('صور العقار', 'Property Images') }}
                <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-0.5 rounded-full">{{ $property->images->count() }} / 7</span>
            </h3>
        </div>

        {{-- Upload form --}}
        <div class="px-4 py-4 border-b border-gray-100 bg-gray-50">
            @if($property->images->count() < 7)
            <form id="img-upload-form-{{ $property->id }}"
                  method="POST"
                  action="{{ route('manager.properties.images.store', $property) }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="flex items-center gap-3 flex-wrap">
                    <input type="file" id="img-file-{{ $property->id }}" name="images[]" multiple accept="image/*"
                           class="block flex-1 min-w-0 text-sm text-gray-600 border border-gray-200 rounded-lg px-3 py-2 bg-white
                                  file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium
                                  file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <button type="submit"
                            class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 flex-shrink-0 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                        {{ $tr('رفع الصور', 'Upload') }}
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1">{{ $tr('JPG، PNG، WebP — حد أقصى 10 ميجا للصورة', 'JPG, PNG, WebP — max 10 MB each') }}</p>
            </form>
            @else
            <p class="text-sm text-amber-700 font-medium">{{ $tr('تم الوصول للحد الأقصى (7 صور). احذف صورة لإضافة أخرى.', 'Maximum reached (7 images). Delete one to add another.') }}</p>
            @endif
            @if(session('success'))
            <p class="text-green-700 text-xs mt-2 font-medium">✓ {{ session('success') }}</p>
            @endif
            @php $imgErrors = collect($errors->getMessages())->filter(fn($m,$k) => str_starts_with($k,'images'))->flatten(); @endphp
            @foreach($imgErrors as $msg)
            <p class="text-red-600 text-xs mt-1 font-medium">✗ {{ $msg }}</p>
            @endforeach
        </div>

        {{-- Images grid --}}
        @if($property->images->isEmpty())
        <div class="py-10 text-center text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-10 h-10 mx-auto mb-2 opacity-30"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z"/></svg>
            <p class="text-sm">{{ $tr('لا توجد صور بعد', 'No images yet') }}</p>
        </div>
        @else
        <div class="p-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
            @foreach($property->images->sortByDesc('is_primary') as $img)
            <div class="relative group rounded-xl overflow-hidden border-2 {{ $img->is_primary ? 'border-yellow-400' : 'border-gray-200' }}" style="height:130px">
                <img src="{{ $img->url() }}" class="w-full h-full object-cover" alt="">
                @if($img->is_primary)
                <span class="absolute top-1.5 start-1.5 bg-yellow-400 text-yellow-900 text-[10px] font-black px-1.5 py-0.5 rounded">
                    {{ $tr('رئيسية', 'Primary') }}
                </span>
                @endif
                {{-- Actions overlay --}}
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                    @if(!$img->is_primary)
                    <form method="POST" action="{{ route('manager.properties.images.primary', [$property, $img]) }}">
                        @csrf @method('PATCH')
                        <button type="submit" title="{{ $tr('تعيين رئيسية', 'Set Primary') }}"
                                class="w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center hover:bg-yellow-300 transition">
                            <svg viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-yellow-900"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 0 0 .95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 0 0-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 0 0-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 0 0-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 0 0 .951-.69l1.519-4.674z"/></svg>
                        </button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('manager.properties.images.destroy', [$property, $img]) }}"
                          onsubmit="return confirm('{{ $tr('حذف الصورة؟', 'Delete image?') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" title="{{ $tr('حذف', 'Delete') }}"
                                class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center hover:bg-red-600 transition">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-white"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</x-app-layout>
