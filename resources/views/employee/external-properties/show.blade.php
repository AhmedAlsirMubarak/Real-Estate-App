<x-app-layout>
@php
    $isAr = app()->getLocale() === 'ar';
    $tr   = fn(string $ar, string $en) => $isAr ? $ar : $en;
    $typeLabels = [
        'apartment_building' => $tr('عمارة', 'Apartment Building'),
        'villa'  => $tr('فيلا', 'Villa'),
        'flat'   => $tr('شقة', 'Flat'),
        'farm'   => $tr('مزرعة', 'Farm'),
        'chalet' => $tr('شاليه', 'Chalet'),
        'land'   => $tr('أرض', 'Land'),
    ];
    $purposeLabels = [
        'rent'           => $tr('إيجار', 'Rent'),
        'sale'           => $tr('بيع', 'Sale'),
        'both'           => $tr('إيجار وبيع', 'Rent & Sale'),
        'exclusive_rent' => $tr('إيجار حصري', 'Exclusive Rent'),
        'exclusive_sale' => $tr('بيع حصري', 'Exclusive Sale'),
    ];
@endphp
<x-slot name="title">{{ $property->name }}</x-slot>

<div class="py-4">
    <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
        <div>
            <a href="{{ route('employee.external-properties.index') }}"
               class="text-sm text-gray-500 hover:text-gray-700 inline-flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                {{ $tr('العقارات الخارجية', 'External Properties') }}
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ $property->name }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $property->code }} · {{ $typeLabels[$property->type] ?? $property->type }}</p>
        </div>
        <a href="{{ route('employee.external-properties.edit', $property) }}"
           class="inline-flex items-center gap-1.5 bg-teal-50 hover:bg-teal-100 text-teal-700 px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            {{ $tr('تعديل', 'Edit') }}
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Images --}}
    @if($property->images->count())
    <div class="mb-5 grid grid-cols-4 sm:grid-cols-6 gap-2">
        @foreach($property->images as $img)
        <a href="{{ asset('storage/' . $img->path) }}" target="_blank"
           class="relative rounded-xl overflow-hidden border border-gray-200 aspect-square block">
            <img src="{{ asset('storage/' . $img->path) }}" class="w-full h-full object-cover hover:opacity-90 transition">
            @if($img->is_primary)
            <div class="absolute top-1 start-1 bg-teal-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded">{{ $tr('رئيسية', 'Main') }}</div>
            @endif
        </a>
        @endforeach
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Main details --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">{{ $tr('معلومات العقار', 'Property Info') }}</h3>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('الغرض', 'Purpose') }}</dt>
                        <dd><span class="bg-teal-50 text-teal-700 text-xs px-2 py-0.5 rounded-full font-medium">{{ $purposeLabels[$property->purpose] ?? $property->purpose }}</span></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('الحالة', 'Status') }}</dt>
                        <dd><span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-green-50 text-green-700">{{ $property->status ?? $tr('نشط', 'Active') }}</span></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('العنوان', 'Address') }}</dt>
                        <dd class="text-gray-700">{{ $property->address ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('المدينة', 'City') }}</dt>
                        <dd class="text-gray-700">{{ $property->city ?: '—' }}</dd>
                    </div>
                    @if($property->floors)
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('الطوابق', 'Floors') }}</dt>
                        <dd class="text-gray-700">{{ $property->floors }}</dd>
                    </div>
                    @endif
                    @if($property->total_area)
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('المساحة الإجمالية', 'Total Area') }}</dt>
                        <dd class="text-gray-700">{{ number_format($property->total_area, 2) }} م²</dd>
                    </div>
                    @endif
                    @if($property->bedrooms !== null)
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('غرف النوم', 'Bedrooms') }}</dt>
                        <dd class="text-gray-700">{{ $property->bedrooms }}</dd>
                    </div>
                    @endif
                    @if($property->bathrooms !== null)
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('الحمامات', 'Bathrooms') }}</dt>
                        <dd class="text-gray-700">{{ $property->bathrooms }}</dd>
                    </div>
                    @endif
                    @if($property->electricity_account_number)
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('حساب الكهرباء', 'Electricity Account') }}</dt>
                        <dd class="text-gray-700">{{ $property->electricity_account_number }}</dd>
                    </div>
                    @endif
                    @if($property->water_account_number)
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('حساب الماء', 'Water Account') }}</dt>
                        <dd class="text-gray-700">{{ $property->water_account_number }}</dd>
                    </div>
                    @endif
                </dl>
                @if($property->description)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500 mb-1">{{ $tr('الوصف', 'Description') }}</p>
                    <p class="text-sm text-gray-700">{{ $isAr ? $property->description_ar : $property->description_en }}</p>
                </div>
                @endif
            </div>

            {{-- Commission --}}
            @if($property->rent_commission_rate || $property->sale_commission_rate)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ $tr('عمولة الأعمال', 'Business Commission') }}</h3>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    @if($property->rent_commission_rate)
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('عمولة الإيجار', 'Rent Commission') }}</dt>
                        <dd class="font-medium text-gray-800">{{ $property->rent_commission_rate }}%</dd>
                    </div>
                    @endif
                    @if($property->sale_commission_rate)
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('عمولة البيع', 'Sale Commission') }}</dt>
                        <dd class="font-medium text-gray-800">{{ $property->sale_commission_rate }}%</dd>
                    </div>
                    @endif
                    @if($property->commission_payer)
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('يدفع العمولة', 'Paid By') }}</dt>
                        <dd class="text-gray-700">{{ $property->commission_payer }}</dd>
                    </div>
                    @endif
                </dl>
                @if($property->commission_notes)
                <p class="mt-3 text-xs text-gray-500">{{ $property->commission_notes }}</p>
                @endif
            </div>
            @endif

            {{-- Units --}}
            @if($property->units->count())
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ $tr('الوحدات', 'Units') }} ({{ $property->units->count() }})</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-50 text-gray-500">
                            <tr>
                                <th class="px-3 py-2 text-start">{{ $tr('الكود', 'Code') }}</th>
                                <th class="px-3 py-2 text-start">{{ $tr('النوع', 'Type') }}</th>
                                <th class="px-3 py-2 text-start">{{ $tr('الغرف', 'Beds') }}</th>
                                <th class="px-3 py-2 text-start">{{ $tr('الإيجار', 'Rent') }}</th>
                                <th class="px-3 py-2 text-start">{{ $tr('الحالة', 'Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($property->units as $unit)
                            <tr>
                                <td class="px-3 py-2 font-medium text-gray-700">{{ $unit->code ?? $unit->id }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $unit->type }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $unit->bedrooms ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $unit->rent_price ? number_format($unit->rent_price, 0) : '—' }}</td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-0.5 rounded-full text-xs
                                        {{ $unit->status === 'available' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $unit->status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Active Rental Contracts --}}
            @if($rentalContracts->count())
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ $tr('عقود الإيجار النشطة', 'Active Rental Contracts') }}</h3>
                <ul class="divide-y divide-gray-50 text-sm">
                    @foreach($rentalContracts as $contract)
                    <li class="py-2.5 flex justify-between items-center">
                        <div>
                            <p class="font-medium text-gray-800">{{ $contract->tenant?->user?->name ?? '—' }}</p>
                            <p class="text-xs text-gray-500">{{ $contract->unit?->code }} · {{ $contract->start_date?->format('Y-m-d') }} → {{ $contract->end_date?->format('Y-m-d') }}</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">{{ number_format($contract->rent_amount, 0) }} {{ $tr('ريال', 'OMR') }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            {{-- Owner & Employee --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ $tr('المسؤولون', 'Responsible Parties') }}</h3>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('المالك', 'Owner') }}</dt>
                        <dd class="font-medium text-gray-800">{{ $property->owner?->user?->name ?? $tr('الشركة', 'Company') }}</dd>
                    </div>
                    @if($property->employee)
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('الموظف المسؤول', 'Assigned Employee') }}</dt>
                        <dd class="font-medium text-gray-800">{{ $property->employee->name }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Recent expenses --}}
            @if($property->expenses->count())
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ $tr('المصروفات الأخيرة', 'Recent Expenses') }}</h3>
                <ul class="divide-y divide-gray-50 text-xs">
                    @foreach($property->expenses->take(5) as $exp)
                    <li class="py-2 flex justify-between">
                        <span class="text-gray-600">{{ $exp->description ?? $exp->category }}</span>
                        <span class="font-medium text-gray-800">{{ number_format($exp->amount, 2) }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
</x-app-layout>
