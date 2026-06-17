<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr   = fn($ar, $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('عمولات العقارات الخارجية', 'External Properties — Business Commission') }}</x-slot>

    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ $tr('عمولة الأعمال — العقارات الخارجية', 'Business Commission — External Properties') }}
            </h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $tr('جميع فواتير العمولة الصادرة للعقارات الخارجية', 'All commission invoices issued for external properties') }}</p>
        </div>
        <a href="{{ route('manager.external-properties.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 inline-flex items-center gap-1">
            ← {{ $tr('العقارات الخارجية', 'External Properties') }}
        </a>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-500">{{ $tr('إجمالي الفواتير', 'Total Invoices') }}</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $invoices->total() }}</p>
        </div>
        <div class="bg-teal-50 rounded-xl border border-teal-100 shadow-sm p-4">
            <p class="text-xs text-teal-600">{{ $tr('إجمالي العمولات', 'Total Commissions') }}</p>
            <p class="text-2xl font-bold text-teal-700 mt-1">{{ number_format($totalCommissions, 3) }} <span class="text-sm font-normal">{{ $tr('ر.ع', 'OMR') }}</span></p>
        </div>
        <div class="bg-blue-50 rounded-xl border border-blue-100 shadow-sm p-4">
            <p class="text-xs text-blue-600">{{ $tr('فواتير المالك', 'Owner Invoices') }}</p>
            <p class="text-2xl font-bold text-blue-700 mt-1">{{ $ownerCount }}</p>
        </div>
        <div class="bg-green-50 rounded-xl border border-green-100 shadow-sm p-4">
            <p class="text-xs text-green-600">{{ $tr('فواتير العميل', 'Client Invoices') }}</p>
            <p class="text-2xl font-bold text-green-700 mt-1">{{ $clientCount }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-xl p-4 mb-5 shadow-sm border border-gray-100 grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="text" name="search" value="{{ $search }}"
               placeholder="{{ $tr('بحث برقم الفاتورة أو المستلم أو العقار...', 'Search by invoice #, recipient, or property...') }}"
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-400 outline-none md:col-span-2">
        <select name="invoice_for" class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-400">
            <option value="">{{ $tr('المالك والعميل', 'Owner & Client') }}</option>
            <option value="owner"  @selected($invoiceFor==='owner')>{{ $tr('المالك فقط', 'Owner only') }}</option>
            <option value="client" @selected($invoiceFor==='client')>{{ $tr('العميل فقط', 'Client only') }}</option>
        </select>
        <div class="flex gap-2">
            <input type="date" name="from" value="{{ $from }}"
                   class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-400">
            <input type="date" name="to" value="{{ $to }}"
                   class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-400">
        </div>
        <div class="md:col-span-4 flex gap-2">
            <button class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm transition">{{ $tr('تصفية', 'Filter') }}</button>
            <a href="{{ route('manager.external-properties.commissions') }}" class="text-gray-500 text-sm px-3 py-2 hover:text-gray-700">{{ $tr('إعادة تعيين', 'Reset') }}</a>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">{{ $tr('رقم الفاتورة', 'Invoice #') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('العقار', 'Property') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('التاريخ', 'Date') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('موجهة إلى', 'For') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('المستلم', 'Recipient') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('المدة', 'Duration') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('النسبة', 'Rate') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('مبلغ العمولة', 'Commission') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('إجراءات', 'Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $inv)
                    <tr class="hover:bg-teal-50/30 transition">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $inv->invoice_number }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('manager.external-properties.show', $inv->property) }}"
                               class="font-medium text-gray-800 hover:text-teal-600 text-xs">
                                {{ $inv->property->name }}
                            </a>
                            <div class="text-xs text-gray-400">{{ $inv->property->code }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $inv->invoice_date->format('Y/m/d') }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $inv->invoice_for === 'owner' ? 'bg-blue-50 text-blue-700' : 'bg-green-50 text-green-700' }}">
                                {{ $inv->invoice_for === 'owner' ? $tr('المالك', 'Owner') : $tr('العميل', 'Client') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-700">{{ $inv->recipient_name }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ number_format($inv->duration_months, 0) }} {{ $tr('شهر', 'mo') }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $inv->commission_rate }}%</td>
                        <td class="px-4 py-3">
                            <span class="font-bold text-teal-700">{{ number_format($inv->commission_amount, 3) }}</span>
                            <span class="text-xs text-gray-400"> {{ $tr('ر.ع', 'OMR') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                @if($inv->file_path)
                                <a href="{{ route('manager.properties.commission-invoice.download', [$inv->property, $inv]) }}"
                                   target="_blank"
                                   class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition"
                                   title="{{ $tr('عرض', 'View') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('manager.properties.commission-invoice.download', [$inv->property, $inv]) . '?download=1' }}"
                                   class="p-1.5 rounded-lg text-gray-400 hover:text-teal-600 hover:bg-teal-50 transition"
                                   title="{{ $tr('تنزيل', 'Download') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                                @endif
                                <form method="POST"
                                      action="{{ route('manager.properties.commission-invoice.destroy', [$inv->property, $inv]) }}"
                                      onsubmit="return confirm('{{ $tr('هل تريد حذف هذه الفاتورة؟', 'Delete this invoice?') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
                                            title="{{ $tr('حذف', 'Delete') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-16 text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-sm">{{ $tr('لا توجد فواتير عمولة بعد', 'No commission invoices yet') }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $tr('يمكن إصدار الفواتير من صفحة العقار', 'Invoices can be generated from the property page') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($invoices->isNotEmpty())
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td colspan="7" class="px-4 py-3 text-xs font-semibold text-gray-600 text-right">{{ $tr('مجموع العمولات (كل النتائج)', 'Total commissions (all results)') }}</td>
                        <td class="px-4 py-3 font-bold text-teal-700">{{ number_format($totalCommissions, 3) }} <span class="text-xs font-normal text-gray-400">{{ $tr('ر.ع', 'OMR') }}</span></td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">{{ $invoices->links() }}</div>
    </div>
</x-app-layout>
