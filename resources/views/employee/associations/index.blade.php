<x-app-layout>
@php
    $isAr = app()->getLocale() === 'ar';
    $tr   = fn(string $ar, string $en) => $isAr ? $ar : $en;
@endphp
<x-slot name="title">{{ $tr('جمعيات الملاك', 'Owners Associations') }}</x-slot>

<div class="py-4">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('جمعيات الملاك', 'Owners Associations') }}</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $tr('إدارة جمعيات الملاك المرتبطة بالعقارات', 'Manage owners associations linked to properties') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('employee.associations.export') }}"
               class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                {{ $tr('تصدير Excel', 'Export Excel') }}
            </a>
            <a href="{{ route('employee.associations.import.form') }}"
               class="inline-flex items-center gap-1.5 border border-gray-200 text-gray-600 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                {{ $tr('استيراد من Excel', 'Import from Excel') }}
            </a>
            <a href="{{ route('employee.associations.create') }}"
               class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $tr('إضافة جمعية', 'Add Association') }}
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if($associations->isEmpty())
        <div class="py-16 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <p class="text-gray-400 text-sm">{{ $tr('لا توجد جمعيات بعد', 'No associations yet') }}</p>
            <a href="{{ route('employee.associations.create') }}" class="inline-block mt-3 text-blue-600 hover:underline text-sm">{{ $tr('إضافة أول جمعية', 'Add first association') }}</a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 text-xs">
                    <tr>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('الاسم', 'Name') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('العقار', 'Property') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('عدد الملاك', 'Owners') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('الرسوم الشهرية / وحدة', 'Monthly Fee / Unit') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('الحالة', 'Status') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('الإجراءات', 'Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($associations as $assoc)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $assoc->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $assoc->property->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $assoc->property?->owners?->count() ?? 0 }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ number_format($assoc->monthly_fee_per_unit, 2) }} {{ $tr('ريال', 'OMR') }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $assoc->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $assoc->status === 'active' ? $tr('نشط', 'Active') : $tr('غير نشط', 'Inactive') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('employee.associations.show', $assoc) }}"
                                   class="inline-flex items-center bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-2.5 py-1.5 rounded-lg transition">
                                    {{ $tr('عرض', 'View') }}
                                </a>
                                <a href="{{ route('employee.associations.edit', $assoc) }}"
                                   class="inline-flex items-center bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-medium px-2.5 py-1.5 rounded-lg transition">
                                    {{ $tr('تعديل', 'Edit') }}
                                </a>
                                @if($assoc->created_by === auth()->id())
                                <form method="POST" action="{{ route('employee.associations.destroy', $assoc) }}"
                                      onsubmit="return confirm('{{ $tr('هل أنت متأكد من حذف هذه الجمعية؟', 'Are you sure you want to delete this association?') }}')"
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
        @if($associations->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $associations->links() }}</div>
        @endif
        @endif
    </div>
</div>
</x-app-layout>
