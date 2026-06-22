<x-app-layout>
@php
    $isAr = app()->getLocale() === 'ar';
    $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    $locale = $isAr ? 'ar' : 'en';
@endphp
<x-slot name="title">{{ $tr('العملاء والطلبات', 'Customers') }}</x-slot>

{{-- Bulk delete form (hidden, submitted by JS) --}}
<form id="bulk-form" method="POST" action="{{ route('manager.customers.bulk-destroy') }}">
    @csrf @method('DELETE')
    <input type="hidden" name="ids" id="bulk-ids">
</form>

<div class="py-4">
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('العملاء والطلبات', 'Customers & Requirements') }}</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $tr('سجل متطلبات العملاء الباحثين عن عقارات', 'Track property seekers and their requirements') }}</p>
        </div>
        <div class="flex gap-2 flex-wrap items-center">
            {{-- Bulk delete button (visible only when rows selected) --}}
            <button id="bulk-delete-btn" type="button" onclick="submitBulkDelete()"
                class="hidden items-center gap-1.5 border border-red-300 text-red-700 bg-red-50 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span id="bulk-delete-label">{{ $tr('حذف المحدد', 'Delete selected') }}</span>
            </button>
            <a href="{{ route('manager.customers.export', request()->only('search', 'status', 'purpose', 'type')) }}"
               class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                {{ $tr('تصدير Excel', 'Export Excel') }}
            </a>

            <a href="{{ route('manager.customers.create') }}"
               class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $tr('إضافة عميل', 'Add Customer') }}
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Search & Filters --}}
    <form method="GET" action="{{ route('manager.customers.index') }}"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('بحث', 'Search') }}</label>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="{{ $tr('اسم، هاتف، بريد، موقع…', 'Name, phone, email, location…') }}"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none">
        </div>
        <div class="w-36">
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('الحالة', 'Status') }}</label>
            <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none">
                <option value="">{{ $tr('الكل', 'All') }}</option>
                @foreach(\App\Models\Customer::$statuses as $val => $labels)
                <option value="{{ $val }}" @selected($status === $val)>{{ $labels[$locale] }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-36">
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('الغرض', 'Purpose') }}</label>
            <select name="purpose" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none">
                <option value="">{{ $tr('الكل', 'All') }}</option>
                @foreach(\App\Models\Customer::$purposes as $val => $labels)
                <option value="{{ $val }}" @selected($purpose === $val)>{{ $labels[$locale] }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-40">
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('نوع العقار', 'Property Type') }}</label>
            <select name="type" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none">
                <option value="">{{ $tr('الكل', 'All') }}</option>
                @foreach(\App\Models\Customer::$propertyTypes as $val => $labels)
                <option value="{{ $val }}" @selected($type === $val)>{{ $labels[$locale] }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                {{ $tr('بحث', 'Search') }}
            </button>
            @if($search || $status || $purpose || $type)
            <a href="{{ route('manager.customers.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition">
                {{ $tr('مسح', 'Clear') }}
            </a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if($customers->isEmpty())
        <div class="py-16 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <p class="text-gray-400 text-sm">{{ $tr('لا يوجد عملاء بعد', 'No customers yet') }}</p>
            <a href="{{ route('manager.customers.create') }}" class="inline-block mt-3 text-blue-600 hover:underline text-sm">{{ $tr('إضافة أول عميل', 'Add first customer') }}</a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 text-xs">
                    <tr>
                        <th class="px-4 py-3 w-8">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 cursor-pointer" title="{{ $tr('تحديد الكل', 'Select all') }}">
                        </th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('العميل', 'Customer') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('الموقع المطلوب', 'Desired Location') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('المتطلبات', 'Requirements') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('الميزانية', 'Budget') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('الحالة', 'Status') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('الإجراءات', 'Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($customers as $customer)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 w-8">
                            <input type="checkbox" class="row-check rounded border-gray-300 cursor-pointer" value="{{ $customer->id }}">
                        </td>
                        {{-- Customer info --}}
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-800">{{ $customer->name }}</div>
                            @if($customer->mobile)
                            <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $customer->mobile }}
                            </div>
                            @endif
                            @if($customer->email)
                            <div class="text-xs text-gray-400 mt-0.5">{{ $customer->email }}</div>
                            @endif
                        </td>
                        {{-- Location --}}
                        <td class="px-4 py-3">
                            @if($customer->location)
                            <div class="flex items-center gap-1 text-gray-700">
                                <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $customer->locationLabel($locale) }}
                            </div>
                            @else
                            <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        {{-- Requirements --}}
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                <span class="inline-block bg-blue-50 text-blue-700 text-xs px-2 py-0.5 rounded-full font-medium">
                                    {{ $customer->typeLabel($locale) }}
                                </span>
                                <span class="inline-block bg-purple-50 text-purple-700 text-xs px-2 py-0.5 rounded-full font-medium">
                                    {{ $customer->purposeLabel($locale) }}
                                </span>
                                @if($customer->bedrooms)
                                <span class="inline-block bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">
                                    {{ $customer->bedrooms }} {{ $tr('غرف', 'bed') }}
                                </span>
                                @endif
                            </div>
                        </td>
                        {{-- Budget --}}
                        <td class="px-4 py-3 text-gray-700 text-xs">
                            @if($customer->min_budget || $customer->max_budget)
                            <div>
                                @if($customer->min_budget){{ number_format($customer->min_budget) }}@endif
                                @if($customer->min_budget && $customer->max_budget) — @endif
                                @if($customer->max_budget){{ number_format($customer->max_budget) }}@endif
                                <span class="text-gray-400"> {{ $tr('ريال', 'OMR') }}</span>
                            </div>
                            @else
                            <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        {{-- Status --}}
                        <td class="px-4 py-3">
                            <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $customer->statusColor() }}">
                                {{ $customer->statusLabel($locale) }}
                            </span>
                        </td>
                        {{-- Actions --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                {{-- WhatsApp --}}
                                @php
                                    $waMsg = $tr(
                                        'مرحباً ' . $customer->name . '، لدينا عروض عقارية قد تناسب متطلباتك.',
                                        'Hello ' . $customer->name . ', we have property offers that may match your requirements.'
                                    );
                                    $waUrl = $customer->whatsappUrl($waMsg);
                                @endphp
                                @if($waUrl)
                                <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                                   title="{{ $tr('إرسال واتساب', 'Send WhatsApp') }}"
                                   class="inline-flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold px-2.5 py-1.5 rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.71.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.999-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                    {{ $tr('واتساب', 'WhatsApp') }}
                                </a>
                                @endif
                                <a href="{{ route('manager.customers.show', $customer) }}"
                                   class="inline-flex items-center gap-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-2.5 py-1.5 rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $tr('عرض', 'Show') }}
                                </a>
                                <a href="{{ route('manager.customers.edit', $customer) }}"
                                   class="inline-flex items-center gap-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-2.5 py-1.5 rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    {{ $tr('تعديل', 'Edit') }}
                                </a>
                                <form method="POST" action="{{ route('manager.customers.destroy', $customer) }}"
                                      onsubmit="return confirm('{{ $tr('حذف هذا العميل؟', 'Delete this customer?') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium px-2.5 py-1.5 rounded-lg transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        {{ $tr('حذف', 'Delete') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @if($customer->notes)
                    <tr class="bg-amber-50/50">
                        <td colspan="7" class="px-4 py-2 text-xs text-gray-600 italic">
                            <span class="font-semibold text-gray-500">{{ $tr('ملاحظات:', 'Notes:') }}</span> {{ $customer->notes }}
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($customers->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $customers->links() }}
        </div>
        @endif
        @endif
    </div>
</div>

<script>
(function () {
    const selectAll = document.getElementById('select-all');
    const bulkBtn   = document.getElementById('bulk-delete-btn');
    const bulkLabel = document.getElementById('bulk-delete-label');
    const bulkIds   = document.getElementById('bulk-ids');
    const labelAr   = '{{ $tr("حذف المحدد", "Delete selected") }}';

    if (!selectAll) return;

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
        const msg = '{{ $tr("هل تريد حذف", "Delete") }} ' + count + ' {{ $tr("عميل؟ لا يمكن التراجع عن هذا.", "customer(s)? This cannot be undone.") }}';
        if (!confirm(msg)) return;
        bulkIds.value = ids;
        document.getElementById('bulk-form').submit();
    };
})();
</script>
</x-app-layout>
