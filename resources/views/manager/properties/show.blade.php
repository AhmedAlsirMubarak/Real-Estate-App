<x-app-layout>
    <x-slot name="title">{{ $property->name }}</x-slot>

    <div class="mb-5 flex flex-col md:flex-row md:items-start md:justify-between gap-3">
        <div>
            <a href="{{ route('manager.properties.index') }}" class="text-xs text-gray-500 hover:text-gray-700 mb-1 inline-block">← كل العقارات</a>
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
            <a href="{{ route('manager.properties.edit', $property) }}" class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-3 py-2 rounded-lg text-sm">تعديل</a>
            @if($property->type === 'apartment_building')
            <a href="{{ route('manager.units.create', $property) }}" class="bg-blue-900 hover:bg-blue-800 text-white px-3 py-2 rounded-lg text-sm">+ إضافة وحدة</a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">المالك</p>
            <p class="text-base font-bold text-gray-800 mt-1">
                @if($property->owner)
                    {{ $property->owner->user->name }}
                    <span class="text-xs font-normal text-gray-500 block mt-0.5">عمولة الشركة: {{ $property->owner->commission_rate }}%</span>
                @else
                    <span class="text-blue-700">الشركة</span>
                @endif
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">الموظف المسؤول</p>
            <p class="text-base font-bold text-gray-800 mt-1">{{ $property->employee?->name ?? '— غير مُسنَد —' }}</p>
            @if($property->employee)
            <form method="POST" action="{{ route('manager.properties.transfer', $property) }}" class="mt-2 flex gap-1">
                @csrf @method('PATCH')
                <select name="employee_id" class="flex-1 border border-gray-200 rounded px-2 py-1 text-xs">
                    <option value="">— إزالة —</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" @selected($emp->id==$property->employee_id)>{{ $emp->name }}</option>
                    @endforeach
                </select>
                <button class="bg-blue-900 text-white px-2 py-1 rounded text-xs">تحويل</button>
            </form>
            @endif
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">الحالة</p>
            <p class="text-base font-bold text-gray-800 mt-1">{{ $property->statusLabel() }}</p>
            @if($property->floors)
            <p class="text-xs text-gray-500 mt-1">{{ $property->floors }} طوابق</p>
            @endif
        </div>
    </div>

    @if($property->description)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-5">
        <p class="text-xs text-gray-500 mb-1">الوصف</p>
        <p class="text-sm text-gray-700">{{ $property->description }}</p>
    </div>
    @endif

    {{-- Units --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-5">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-sm">الوحدات ({{ $property->units->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs">
                    <tr>
                        <th class="px-4 py-2 text-right">رقم الوحدة</th>
                        <th class="px-4 py-2 text-right">الطابق</th>
                        <th class="px-4 py-2 text-right">النوع</th>
                        <th class="px-4 py-2 text-right">المساحة</th>
                        <th class="px-4 py-2 text-right">سعر الإيجار</th>
                        <th class="px-4 py-2 text-right">سعر البيع</th>
                        <th class="px-4 py-2 text-right">الحالة</th>
                        <th class="px-4 py-2 text-right">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($property->units as $unit)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5 font-medium">{{ $unit->unit_number ?? '—' }}</td>
                        <td class="px-4 py-2.5">{{ $unit->floor ?? '—' }}</td>
                        <td class="px-4 py-2.5">{{ $unit->type }}</td>
                        <td class="px-4 py-2.5">{{ $unit->area ? $unit->area . ' م²' : '—' }}</td>
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
                                <a href="{{ route('manager.units.edit', [$property, $unit]) }}" class="text-blue-600 hover:text-blue-800">تعديل</a>
                                <span class="text-gray-300">·</span>
                                <form method="POST" action="{{ route('manager.units.destroy', [$property, $unit]) }}" onsubmit="return confirm('حذف الوحدة؟')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-8 text-gray-400 text-sm">لا توجد وحدات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent expenses --}}
    @if($property->expenses->count())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 text-sm">آخر المصروفات</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($property->expenses as $expense)
            <div class="px-4 py-3 flex items-start justify-between">
                <div>
                    <div class="font-medium text-sm text-gray-800">{{ $expense->title }}</div>
                    <div class="text-xs text-gray-500">{{ $expense->categoryLabel() }} · {{ $expense->expense_date->format('Y-m-d') }}</div>
                </div>
                <div class="text-sm font-bold text-red-600">-{{ number_format($expense->amount) }} ر.س</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</x-app-layout>
