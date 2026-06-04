<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr   = fn(string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('الملاك', 'Owners') }} — {{ $property->name }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('الملاك', 'Owners') }} — {{ $property->name }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $property->code }}</p>
        </div>
        <a href="{{ route('manager.properties.show', $property) }}" class="text-sm text-gray-600 hover:text-gray-900">{{ $tr('رجوع', 'Back') }}</a>
    </div>

    @php $totalPercent = $property->owners->sum('pivot.ownership_percentage'); @endphp

    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-5">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-sm font-bold text-gray-800">{{ $tr('الملاك', 'Owners') }}</h3>
            <span class="text-xs px-2 py-0.5 rounded-full {{ abs($totalPercent - 100) < 0.5 ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }}">
                {{ $tr('الإجمالي', 'Total') }}: {{ number_format($totalPercent, 2) }}%
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="text-right py-2">{{ $tr('الاسم', 'Name') }}</th>
                        <th class="text-right py-2">{{ $tr('نسبة الملكية', 'Ownership %') }}</th>
                        <th class="text-right py-2">{{ $tr('المالك الرئيسي', 'Primary Owner') }}</th>
                        <th class="text-right py-2">{{ $tr('منذ', 'Since') }}</th>
                        <th class="text-right py-2">{{ $tr('إجراءات', 'Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($property->owners as $owner)
                    <tr>
                        <form method="POST" action="{{ route('manager.properties.owners.update', [$property, $owner]) }}">
                            @csrf @method('PATCH')
                            <td class="py-2">{{ $owner->user?->name ?? '—' }}<div class="text-xs text-gray-500">{{ $owner->phone }}</div></td>
                            <td class="py-2"><input type="number" step="0.01" name="ownership_percentage" value="{{ $owner->pivot->ownership_percentage }}" class="w-24 border border-gray-200 rounded px-2 py-1 text-sm"></td>
                            <td class="py-2"><input type="checkbox" name="is_primary" value="1" @checked($owner->pivot->is_primary)></td>
                            <td class="py-2"><input type="date" name="since_date" value="{{ $owner->pivot->since_date }}" class="border border-gray-200 rounded px-2 py-1 text-xs"></td>
                            <td class="py-2 text-xs">
                                <button class="text-blue-700 hover:text-blue-900 mx-1">{{ $tr('تحديث', 'Update') }}</button>
                        </form>
                                <form method="POST" action="{{ route('manager.properties.owners.destroy', [$property, $owner]) }}" class="inline" onsubmit="return confirm('{{ $tr('هل أنت متأكد؟', 'Are you sure?') }}')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 mx-1">{{ $tr('حذف', 'Delete') }}</button>
                                </form>
                            </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-6 text-center text-gray-400 text-xs">{{ $tr('لا توجد بيانات', 'No data available') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 p-5">
        <h3 class="text-sm font-bold text-gray-800 mb-3">{{ $tr('إضافة', 'Add') }} — {{ $tr('مالك', 'Owner') }}</h3>
        <form method="POST" action="{{ route('manager.properties.owners.store', $property) }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            @csrf
            <select name="owner_id" required class="border border-gray-200 rounded-lg px-3 py-2 text-sm md:col-span-2">
                <option value="">--</option>
                @foreach($availableOwners as $o)
                <option value="{{ $o->id }}">{{ $o->user?->name ?? '—' }} ({{ $o->phone }})</option>
                @endforeach
            </select>
            <input type="number" step="0.01" name="ownership_percentage" required placeholder="{{ $tr('نسبة الملكية', 'Ownership %') }}" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <label class="flex items-center gap-2 text-sm text-gray-700"><input type="checkbox" name="is_primary" value="1"> {{ $tr('المالك الرئيسي', 'Primary Owner') }}</label>
            <button class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm">{{ $tr('إضافة', 'Add') }}</button>
        </form>
        @error('ownership_percentage')<p class="text-red-600 text-xs mt-2">{{ $message }}</p>@enderror
    </div>
</x-app-layout>
