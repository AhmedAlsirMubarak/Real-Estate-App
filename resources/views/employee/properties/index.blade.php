<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn(string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
        $purposeLabels = [
            'rent' => $tr('إيجار', 'Rent'),
            'sale' => $tr('بيع', 'Sale'),
            'both' => $tr('إيجار وبيع', 'Rent & Sale'),
        ];
        $statusColors = [
            'available'   => 'bg-green-100 text-green-700',
            'rented'      => 'bg-blue-100 text-blue-700',
            'sold'        => 'bg-gray-100 text-gray-600',
            'maintenance' => 'bg-orange-100 text-orange-700',
        ];
    @endphp
    <x-slot name="title">{{ $tr('عقاراتي', 'My Properties') }}</x-slot>

    <div class="py-4">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('عقاراتي', 'My Properties') }}</h2>
            <a href="{{ route('employee.properties.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $tr('إضافة عقار', 'Add Property') }}
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('العقار', 'Property') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('القسم', 'Section') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('النوع', 'Type') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الغرض', 'Purpose') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('المالك', 'Owner') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الوحدات', 'Units') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('دوري', 'Role') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($properties as $property)
                        @php
                            $isManaged  = (int) $property->employee_id === (int) auth()->id();
                            $isReferral = (int) $property->referral_employee_id === (int) auth()->id();
                            $totalUnits    = $property->units->count();
                            $rentedUnits   = $property->units->where('status', 'rented')->count();
                            $availableUnits = $property->units->where('status', 'available')->count();
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $property->name }}</p>
                                @if($property->code)
                                    <p class="text-xs text-gray-400 font-mono">{{ $property->code }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $sectionColors = [
                                        'management' => 'bg-blue-100 text-blue-700',
                                        'hoa'        => 'bg-purple-100 text-purple-700',
                                        'external'   => 'bg-orange-100 text-orange-700',
                                    ];
                                @endphp
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $sectionColors[$property->section] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $property->sectionLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $property->typeLabel() }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium text-gray-600">{{ $purposeLabels[$property->purpose] ?? $property->purpose }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 text-sm">{{ $property->owner?->user?->name ?? $tr('الشركة', 'Company') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full text-xs font-medium">{{ $totalUnits }} {{ $tr('إجمالي', 'total') }}</span>
                                    @if($rentedUnits)
                                        <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-medium">{{ $rentedUnits }} {{ $tr('مؤجر', 'rented') }}</span>
                                    @endif
                                    @if($availableUnits)
                                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-medium">{{ $availableUnits }} {{ $tr('متاح', 'avail.') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1 flex-wrap">
                                    @if($isManaged)
                                        <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-medium">{{ $tr('مسؤول', 'Manager') }}</span>
                                    @endif
                                    @if($isReferral)
                                        <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full text-xs font-medium">{{ $tr('محيل', 'Referral') }}</span>
                                        @if($property->referral_commission_rate)
                                            <span class="bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-full text-xs">{{ $property->referral_commission_rate }}%</span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                                {{ $tr('لا توجد عقارات مرتبطة بك', 'No properties linked to you yet') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($properties->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">{{ $properties->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
