<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr   = fn(string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('جمعية الملاك', 'Owners Association') }}</x-slot>

    {{-- Bulk delete form (hidden, submitted by JS) --}}
    <form id="bulk-form" method="POST" action="{{ route('manager.associations.bulk-destroy') }}">
        @csrf @method('DELETE')
        <input type="hidden" name="ids" id="bulk-ids">
    </form>

    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('جمعية الملاك', 'Owners Association') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $tr('الجمعيات', 'Associations') }}</p>
        </div>
        <div class="flex gap-2 flex-wrap items-center">
            {{-- Bulk delete button (visible only when rows selected) --}}
            <button id="bulk-delete-btn" type="button" onclick="submitBulkDelete()"
                class="hidden items-center gap-1.5 border border-red-300 text-red-700 bg-red-50 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span id="bulk-delete-label">{{ $tr('حذف المحدد', 'Delete selected') }}</span>
            </button>
            <a href="{{ route('manager.associations.export') }}"
               class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                {{ $tr('تصدير Excel', 'Export Excel') }}
            </a>
            <a href="{{ route('manager.associations.import.form') }}"
               class="inline-flex items-center gap-1.5 border border-gray-200 text-gray-600 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                {{ $tr('استيراد من Excel', 'Import from Excel') }}
            </a>
            <a href="{{ route('manager.associations.report.create') }}"
               class="inline-flex items-center gap-1.5 bg-teal-700 hover:bg-teal-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                </svg>
                {{ $tr('توليد تقرير شامل', 'Generate Comprehensive Report') }}
            </a>
            <a href="{{ route('manager.associations.create') }}"
               class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $tr('إضافة', 'Add') }}
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 w-8">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 cursor-pointer" title="{{ $tr('تحديد الكل', 'Select all') }}">
                        </th>
                        <th class="px-4 py-3 text-right">{{ $tr('الاسم', 'Name') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('العقار', 'Property') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('الرسوم الشهرية لكل وحدة', 'Monthly Fee per Unit') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('رقم الوحدة', 'Unit No.') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('الحالة', 'Status') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('أنشئ بواسطة', 'Created By') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('إجراءات', 'Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($associations as $assoc)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 w-8">
                            <input type="checkbox" class="row-check rounded border-gray-300 cursor-pointer" value="{{ $assoc->id }}">
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $assoc->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $assoc->property->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ number_format($assoc->monthly_fee_per_unit, 2) }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            @php $unitNumbers = array_filter((array)($assoc->unit_number ?? [])); @endphp
                            @if(!empty($unitNumbers))
                                <div class="flex flex-wrap gap-1">
                                @foreach($unitNumbers as $uNum)
                                    <span class="inline-flex items-center gap-1 bg-blue-50 border border-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                        {{ $uNum }}
                                    </span>
                                @endforeach
                                </div>
                                @if(count($unitNumbers) > 1)
                                    <span class="text-xs text-gray-400 mt-0.5 block">{{ count($unitNumbers) }} {{ $tr('وحدات', 'units') }}</span>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs
                                {{ $assoc->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $assoc->status === 'active' ? $tr('نشط', 'Active') : $tr('غير نشط', 'Inactive') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $assoc->createdBy?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs">
                            <a href="{{ route('manager.associations.show', $assoc) }}" class="text-blue-700 hover:text-blue-900 mx-1">{{ $tr('عرض التفاصيل', 'View Details') }}</a>
                            <a href="{{ route('manager.associations.report.create', ['association_id' => $assoc->id]) }}" class="text-teal-700 hover:text-teal-900 mx-1">{{ $tr('تقرير', 'Report') }}</a>
                            <a href="{{ route('manager.associations.edit', $assoc) }}" class="text-gray-600 hover:text-gray-900 mx-1">{{ $tr('تعديل', 'Edit') }}</a>
                            <form method="POST" action="{{ route('manager.associations.destroy', $assoc) }}" class="inline"
                                  onsubmit="return confirm('{{ $tr('حذف هذه الجمعية؟', 'Delete this association?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 mx-1">{{ $tr('حذف', 'Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="py-10 text-center text-gray-400">{{ $tr('لا توجد بيانات', 'No data available') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">{{ $associations->links() }}</div>
    </div>

    @if(session('success'))
    <div class="fixed bottom-4 left-4 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm z-50">
        {{ session('success') }}
    </div>
    @endif

<script>
(function () {
    const selectAll = document.getElementById('select-all');
    const bulkBtn   = document.getElementById('bulk-delete-btn');
    const bulkLabel = document.getElementById('bulk-delete-label');
    const bulkIds   = document.getElementById('bulk-ids');
    const labelAr   = '{{ $tr("حذف المحدد", "Delete selected") }}';

    function getChecked() {
        return [...document.querySelectorAll('.row-check:checked')];
    }

    function updateToolbar() {
        const checked = getChecked();
        if (checked.length > 0) {
            bulkBtn.classList.remove('hidden');
            bulkBtn.classList.add('inline-flex');
            bulkLabel.textContent = labelAr + ' (' + checked.length + ')';
        } else {
            bulkBtn.classList.add('hidden');
            bulkBtn.classList.remove('inline-flex');
        }
        const all = document.querySelectorAll('.row-check');
        selectAll.indeterminate = checked.length > 0 && checked.length < all.length;
        selectAll.checked = all.length > 0 && checked.length === all.length;
    }

    selectAll.addEventListener('change', function () {
        document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
        updateToolbar();
    });

    document.querySelectorAll('.row-check').forEach(cb => {
        cb.addEventListener('change', updateToolbar);
    });

    window.submitBulkDelete = function () {
        const ids = getChecked().map(cb => cb.value).join(',');
        if (!ids) return;
        const count = getChecked().length;
        const msg = '{{ $tr("هل تريد حذف", "Delete") }} ' + count + ' {{ $tr("جمعية؟ سيتم حذف جميع الاستحقاقات والاجتماعات المرتبطة بها. لا يمكن التراجع عن هذا.", "association(s)? All related dues and meetings will also be deleted. This cannot be undone.") }}';
        if (!confirm(msg)) return;
        bulkIds.value = ids;
        document.getElementById('bulk-form').submit();
    };
})();
</script>
</x-app-layout>
