<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $displayName = function (?string $name, string $fallback = 'User') use ($isAr) {
            if ($name === null || $name === '') {
                return $fallback;
            }

            if ($isAr || ! preg_match('/\p{Arabic}/u', $name)) {
                return $name;
            }

            return $fallback;
        };
    @endphp
    <x-slot name="title">{{ $isAr ? 'العقارات' : 'Properties' }}</x-slot>

    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $isAr ? 'العقارات' : 'Properties' }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $isAr ? 'إدارة جميع العقارات — عمارات، فيلات، مزارع، شاليهات' : 'Manage all properties — buildings, villas, farms, chalets' }}</p>
        </div>
        <a href="{{ route('manager.properties.create') }}"
           class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ $isAr ? 'إضافة عقار' : 'Add Property' }}
        </a>
    </div>

    <form method="GET" class="bg-white rounded-xl p-4 mb-5 shadow-sm border border-gray-100 grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="text" name="search" value="{{ $search }}" placeholder="{{ $isAr ? 'بحث بالاسم أو الكود أو العنوان...' : 'Search by name, code, or address...' }}"
               class="md:col-span-2 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <select name="type" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ $isAr ? 'كل الأنواع' : 'All types' }}</option>
            <option value="apartment_building" @selected(request('type')==='apartment_building')>{{ $isAr ? 'عمارة' : 'Building' }}</option>
            <option value="villa" @selected(request('type')==='villa')>{{ $isAr ? 'فيلا' : 'Villa' }}</option>
            <option value="farm" @selected(request('type')==='farm')>{{ $isAr ? 'مزرعة' : 'Farm' }}</option>
            <option value="chalet" @selected(request('type')==='chalet')>{{ $isAr ? 'شاليه' : 'Chalet' }}</option>
        </select>
        <select name="purpose" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ $isAr ? 'إيجار أو بيع' : 'Rent or sale' }}</option>
            <option value="rent" @selected(request('purpose')==='rent')>{{ $isAr ? 'إيجار' : 'Rent' }}</option>
            <option value="sale" @selected(request('purpose')==='sale')>{{ $isAr ? 'بيع' : 'Sale' }}</option>
            <option value="both" @selected(request('purpose')==='both')>{{ $isAr ? 'إيجار وبيع' : 'Rent & Sale' }}</option>
        </select>
        <div class="md:col-span-4 flex gap-2">
            <button class="bg-blue-900 text-white px-4 py-2 rounded-lg text-sm">{{ $isAr ? 'تصفية' : 'Filter' }}</button>
            <a href="{{ route('manager.properties.index') }}" class="text-gray-500 text-sm px-3 py-2">{{ $isAr ? 'إعادة تعيين' : 'Reset' }}</a>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">{{ $isAr ? 'الكود' : 'Code' }}</th>
                        <th class="px-4 py-3 text-right">{{ $isAr ? 'الاسم' : 'Name' }}</th>
                        <th class="px-4 py-3 text-right">{{ $isAr ? 'النوع' : 'Type' }}</th>
                        <th class="px-4 py-3 text-right">{{ $isAr ? 'الغرض' : 'Purpose' }}</th>
                        <th class="px-4 py-3 text-right">{{ $isAr ? 'المالك' : 'Owner' }}</th>
                        <th class="px-4 py-3 text-right">{{ $isAr ? 'المسؤول' : 'Assigned' }}</th>
                        <th class="px-4 py-3 text-right">{{ $isAr ? 'الوحدات' : 'Units' }}</th>
                        <th class="px-4 py-3 text-right">{{ $isAr ? 'إجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($properties as $property)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $property->code }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $property->name }}</div>
                            <div class="text-xs text-gray-500">{{ $property->address }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-indigo-50 text-indigo-700">{{ $property->typeLabel() }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs
                                @if($property->purpose === 'rent') bg-blue-50 text-blue-700
                                @elseif($property->purpose === 'sale') bg-green-50 text-green-700
                                @else bg-purple-50 text-purple-700 @endif">
                                {{ $property->purposeLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            @if($property->owner)
                                {{ $displayName($property->owner->user->name ?? null, $isAr ? 'مالك' : 'Owner') }}
                                <div class="text-xs text-gray-400">{{ $isAr ? 'عمولة' : 'Commission' }} {{ $property->owner->commission_rate }}%</div>
                            @else
                                <span class="text-xs text-gray-500">{{ $isAr ? 'الشركة' : 'Company' }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $displayName($property->employee?->name, $isAr ? '—' : 'Unassigned') }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $property->units->count() }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-1.5">
                                <a href="{{ route('manager.properties.show', $property) }}" class="text-blue-600 hover:text-blue-800 text-xs">{{ $isAr ? 'عرض' : 'View' }}</a>
                                <span class="text-gray-300">·</span>
                                <a href="{{ route('manager.properties.edit', $property) }}" class="text-gray-600 hover:text-gray-800 text-xs">{{ $isAr ? 'تعديل' : 'Edit' }}</a>
                                <span class="text-gray-300">·</span>
                                <form method="POST" action="{{ route('manager.properties.destroy', $property) }}" onsubmit="return confirm('{{ $isAr ? 'حذف العقار؟' : 'Delete this property?' }}')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 text-xs">{{ $isAr ? 'حذف' : 'Delete' }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-10 text-gray-400 text-sm">{{ $isAr ? 'لا توجد عقارات' : 'No properties found' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">{{ $properties->links() }}</div>
    </div>
</x-app-layout>
