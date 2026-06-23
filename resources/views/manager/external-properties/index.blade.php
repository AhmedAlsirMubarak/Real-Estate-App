<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr   = fn($ar, $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('العقارات الخارجية', 'External Properties') }}</x-slot>

    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $tr('العقارات الخارجية', 'External Properties') }}
            </h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $tr('عقارات مُدارة خارج نطاق البناء والجمعية', 'Properties managed outside buildings & HOA') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('manager.external-properties.export', request()->only('search', 'type', 'purpose')) }}"
               class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                {{ $tr('تصدير Excel', 'Export Excel') }}
            </a>
            <a href="{{ route('manager.external-properties.import.form') }}"
               class="inline-flex items-center gap-1.5 border border-gray-200 text-gray-600 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                {{ $tr('استيراد من Excel', 'Import from Excel') }}
            </a>
            <a href="{{ route('manager.external-properties.create') }}"
               class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $tr('إضافة عقار خارجي', 'Add External Property') }}
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-xl p-4 mb-5 shadow-sm border border-gray-100 grid grid-cols-1 md:grid-cols-3 gap-3">
        <input type="text" name="search" value="{{ $search }}"
               placeholder="{{ $tr('بحث بالاسم أو الكود أو العنوان...', 'Search by name, code, or address...') }}"
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-400 outline-none col-span-1 md:col-span-1">
        <select name="type" class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-400">
            <option value="">{{ $tr('كل الأنواع', 'All types') }}</option>
            <option value="apartment_building" @selected($typeFilter==='apartment_building')>{{ $tr('عمارة', 'Building') }}</option>
            <option value="flat"    @selected($typeFilter==='flat')>{{ $tr('شقة', 'Flat') }}</option>
            <option value="villa"   @selected($typeFilter==='villa')>{{ $tr('فيلا', 'Villa') }}</option>
            <option value="farm"    @selected($typeFilter==='farm')>{{ $tr('مزرعة', 'Farm') }}</option>
            <option value="chalet"  @selected($typeFilter==='chalet')>{{ $tr('شاليه', 'Chalet') }}</option>
            <option value="land"    @selected($typeFilter==='land')>{{ $tr('أرض', 'Land') }}</option>
            <option value="office" @selected($typeFilter==='office')>{{ $tr('مكتب', 'Office') }}</option>
            <option value="shop"   @selected($typeFilter==='shop')>{{ $tr('محل', 'Shop') }}</option>
        </select>
        <select name="purpose" class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-400">
            <option value="">{{ $tr('كل الأغراض', 'All purposes') }}</option>
            <option value="rent"           @selected($purposeFilter==='rent')>{{ $tr('إيجار', 'Rent') }}</option>
            <option value="sale"           @selected($purposeFilter==='sale')>{{ $tr('بيع', 'Sale') }}</option>
            <option value="both"           @selected($purposeFilter==='both')>{{ $tr('إيجار وبيع', 'Rent & Sale') }}</option>
            <option value="exclusive_rent" @selected($purposeFilter==='exclusive_rent')>{{ $tr('ايجار حصري', 'Exclusive Rent') }}</option>
            <option value="exclusive_sale" @selected($purposeFilter==='exclusive_sale')>{{ $tr('بيع حصري', 'Exclusive Sale') }}</option>
        </select>
        <div class="md:col-span-3 flex gap-2">
            <button class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm transition">{{ $tr('تصفية', 'Filter') }}</button>
            <a href="{{ route('manager.external-properties.index') }}" class="text-gray-500 text-sm px-3 py-2 hover:text-gray-700">{{ $tr('إعادة تعيين', 'Reset') }}</a>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">{{ $tr('الكود', 'Code') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('الاسم', 'Name') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('النوع', 'Type') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('الغرض', 'Purpose') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('المالك', 'Owner') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('المسؤول', 'Assigned') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('أنشئ بواسطة', 'Created By') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('الحالة', 'Status') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('إجراءات', 'Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($properties as $property)
                    <tr class="hover:bg-teal-50/30 transition">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $property->code }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $property->name }}</div>
                            <div class="text-xs text-gray-400">{{ $property->address }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-teal-50 text-teal-700">{{ $property->typeLabel() }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs
                                @if(in_array($property->purpose, ['exclusive_rent','exclusive_sale'])) bg-amber-50 text-amber-700
                                @elseif($property->purpose === 'rent') bg-blue-50 text-blue-700
                                @elseif($property->purpose === 'sale') bg-green-50 text-green-700
                                @else bg-purple-50 text-purple-700 @endif">
                                {{ $property->purposeLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-700 text-xs">
                            @if($property->owner)
                                {{ $property->owner->user->name ?? '—' }}
                                <div class="text-gray-400">{{ $tr('عمولة', 'Comm.') }} {{ $property->owner->commission_rate }}%</div>
                            @else
                                <span class="text-gray-400">{{ $tr('الشركة', 'Company') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $property->employee?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $property->createdBy?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @php $sl = $property->statusLabel(); @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $sl }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2 items-center">
                                <a href="{{ route('manager.external-properties.show', $property) }}"
                                   class="text-teal-600 hover:text-teal-800 text-xs font-medium">{{ $tr('عرض', 'View') }}</a>
                                <span class="text-gray-300">·</span>
                                <a href="{{ route('manager.external-properties.edit', $property) }}"
                                   class="text-gray-600 hover:text-gray-800 text-xs">{{ $tr('تعديل', 'Edit') }}</a>
                                <span class="text-gray-300">·</span>
                                <form method="POST" action="{{ route('manager.external-properties.destroy', $property) }}"
                                      onsubmit="return confirm('{{ $tr('حذف العقار؟', 'Delete this property?') }}')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 text-xs">{{ $tr('حذف', 'Delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-16 text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm">{{ $tr('لا توجد عقارات خارجية', 'No external properties found') }}</p>
                            <a href="{{ route('manager.external-properties.create') }}" class="mt-2 inline-block text-teal-600 text-xs hover:underline">{{ $tr('+ إضافة أول عقار خارجي', '+ Add the first external property') }}</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">{{ $properties->links() }}</div>
    </div>
</x-app-layout>
