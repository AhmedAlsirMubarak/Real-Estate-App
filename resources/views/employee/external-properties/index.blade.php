<x-app-layout>
@php
    $isAr = app()->getLocale() === 'ar';
    $tr   = fn(string $ar, string $en) => $isAr ? $ar : $en;
@endphp
<x-slot name="title">{{ $tr('العقارات الخارجية', 'External Properties') }}</x-slot>

<div class="py-4">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('العقارات الخارجية', 'External Properties') }}</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $tr('العقارات الخارجية المُدارة لصالح ملاك خارجيين', 'Properties managed on behalf of external owners') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('employee.external-properties.export', request()->only('search', 'type', 'purpose')) }}"
               class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                {{ $tr('تصدير Excel', 'Export Excel') }}
            </a>

            <a href="{{ route('employee.external-properties.create') }}"
               class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $tr('إضافة عقار خارجي', 'Add External Property') }}
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('employee.external-properties.index') }}"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('بحث', 'Search') }}</label>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="{{ $tr('اسم، كود، عنوان…', 'Name, code, address…') }}"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none">
        </div>
        <div class="w-40">
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('النوع', 'Type') }}</label>
            <select name="type" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none">
                <option value="">{{ $tr('الكل', 'All') }}</option>
                <option value="apartment_building" @selected($typeFilter==='apartment_building')>{{ $tr('عمارة', 'Apartment Building') }}</option>
                <option value="villa"   @selected($typeFilter==='villa')>{{ $tr('فيلا', 'Villa') }}</option>
                <option value="flat"    @selected($typeFilter==='flat')>{{ $tr('شقة', 'Flat') }}</option>
                <option value="farm"    @selected($typeFilter==='farm')>{{ $tr('مزرعة', 'Farm') }}</option>
                <option value="chalet"  @selected($typeFilter==='chalet')>{{ $tr('شاليه', 'Chalet') }}</option>
                <option value="land"    @selected($typeFilter==='land')>{{ $tr('أرض', 'Land') }}</option>
            </select>
        </div>
        <div class="w-40">
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('الغرض', 'Purpose') }}</label>
            <select name="purpose" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none">
                <option value="">{{ $tr('الكل', 'All') }}</option>
                <option value="rent"           @selected($purposeFilter==='rent')>{{ $tr('إيجار', 'Rent') }}</option>
                <option value="sale"           @selected($purposeFilter==='sale')>{{ $tr('بيع', 'Sale') }}</option>
                <option value="both"           @selected($purposeFilter==='both')>{{ $tr('إيجار وبيع', 'Rent & Sale') }}</option>
                <option value="exclusive_rent" @selected($purposeFilter==='exclusive_rent')>{{ $tr('ايجار حصري', 'Exclusive Rent') }}</option>
                <option value="exclusive_sale" @selected($purposeFilter==='exclusive_sale')>{{ $tr('بيع حصري', 'Exclusive Sale') }}</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">{{ $tr('بحث', 'Search') }}</button>
            @if($search || $typeFilter || $purposeFilter)
            <a href="{{ route('employee.external-properties.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition">{{ $tr('مسح', 'Clear') }}</a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if($properties->isEmpty())
        <div class="py-16 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <p class="text-gray-400 text-sm">{{ $tr('لا توجد عقارات خارجية بعد', 'No external properties yet') }}</p>
            <a href="{{ route('employee.external-properties.create') }}" class="inline-block mt-3 text-teal-600 hover:underline text-sm">{{ $tr('إضافة أول عقار', 'Add first property') }}</a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 text-xs">
                    <tr>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('العقار', 'Property') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('النوع', 'Type') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('الغرض', 'Purpose') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('المالك', 'Owner') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('الوحدات', 'Units') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('الحالة', 'Status') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('الإجراءات', 'Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($properties as $property)
                    @php
                        $typeLabels = [
                            'apartment_building' => $tr('عمارة', 'Apt. Building'),
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
                            'exclusive_rent' => $tr('إيجار حصري', 'Excl. Rent'),
                            'exclusive_sale' => $tr('بيع حصري', 'Excl. Sale'),
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-800">{{ $property->name }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $property->code }}</div>
                            @if($property->city)
                            <div class="text-xs text-gray-500">{{ $property->city }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $typeLabels[$property->type] ?? $property->type }}</td>
                        <td class="px-4 py-3 text-xs">
                            <span class="bg-teal-50 text-teal-700 px-2 py-0.5 rounded-full font-medium">{{ $purposeLabels[$property->purpose] ?? $property->purpose }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $property->owner?->user?->name ?? $tr('الشركة', 'Company') }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $property->units?->count() ?? 0 }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'active'            => 'bg-green-50 text-green-700',
                                    'rented'            => 'bg-blue-50 text-blue-700',
                                    'sold'              => 'bg-gray-100 text-gray-600',
                                    'under_maintenance' => 'bg-yellow-50 text-yellow-700',
                                    'archived'          => 'bg-gray-100 text-gray-400',
                                ];
                            @endphp
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $statusColors[$property->status ?? 'active'] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $property->status ?? $tr('نشط', 'Active') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('employee.external-properties.show', $property) }}"
                                   class="inline-flex items-center bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-2.5 py-1.5 rounded-lg transition">
                                    {{ $tr('عرض', 'View') }}
                                </a>
                                <a href="{{ route('employee.external-properties.edit', $property) }}"
                                   class="inline-flex items-center bg-teal-50 hover:bg-teal-100 text-teal-700 text-xs font-medium px-2.5 py-1.5 rounded-lg transition">
                                    {{ $tr('تعديل', 'Edit') }}
                                </a>
                                @if($property->created_by === auth()->id())
                                <form method="POST" action="{{ route('employee.external-properties.destroy', $property) }}"
                                      onsubmit="return confirm('{{ $tr('هل أنت متأكد من حذف هذا العقار؟', 'Are you sure you want to delete this property?') }}')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium px-2.5 py-1.5 rounded-lg transition">
                                        {{ $tr('حذف', 'Delete') }}
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($properties->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $properties->links() }}</div>
        @endif
        @endif
    </div>
</div>
</x-app-layout>
