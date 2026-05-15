<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.س' : 'SAR';
        $displayName = function (?string $name, string $fallback = 'User') use ($isAr) {
            if ($name === null || $name === '') return $fallback;
            if ($isAr || ! preg_match('/\p{Arabic}/u', $name)) return $name;
            return $fallback;
        };
    @endphp
    <x-slot name="title">{{ $tr('تقرير', 'Report') }} {{ $property->name }}</x-slot>

    <div class="mb-5 flex flex-col md:flex-row md:items-start md:justify-between gap-3">
        <div>
            <a href="{{ route('manager.reports.index') }}" class="text-xs text-gray-500 hover:text-gray-700 mb-1 inline-block">← {{ $tr('كل التقارير', 'All Reports') }}</a>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('تقرير', 'Report') }} {{ $property->name }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $property->code }} · {{ $property->typeLabel() }} · {{ $property->purposeLabel() }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('manager.reports.property', ['property' => $property, 'year' => $year, 'month' => $month, 'export' => 'preview']) }}" target="_blank"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm">{{ $tr('معاينة PDF', 'PDF Preview') }}</a>
            <a href="{{ route('manager.reports.property', ['property' => $property, 'year' => $year, 'month' => $month, 'export' => 'pdf']) }}"
               class="bg-blue-900 hover:bg-blue-800 text-white px-3 py-2 rounded-lg text-sm">{{ $tr('تحميل PDF', 'Download PDF') }}</a>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-5 grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
            <label class="block text-xs text-gray-500 mb-1">{{ $tr('السنة', 'Year') }}</label>
            <input type="number" name="year" value="{{ $year }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">{{ $tr('الشهر (اختياري)', 'Month (optional)') }}</label>
            <select name="month" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                <option value="">{{ $tr('كل الأشهر', 'All months') }}</option>
                @for($m=1; $m<=12; $m++)
                <option value="{{ $m }}" @selected($month==$m)>{{ $m }}</option>
                @endfor
            </select>
        </div>
        <div class="flex items-end">
            <button class="bg-blue-900 text-white px-4 py-2 rounded-lg text-sm w-full">{{ $tr('تصفية', 'Filter') }}</button>
        </div>
    </form>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ $tr('الوحدات', 'Units') }}</p>
            <p class="text-xl font-bold text-gray-800 mt-1">{{ $stats['total_units'] }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $stats['rented_units'] }} {{ $tr('مؤجرة', 'rented') }} · {{ $stats['sold_units'] }} {{ $tr('مباعة', 'sold') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ $tr('الإيرادات', 'Revenue') }}</p>
            <p class="text-xl font-bold text-green-700 mt-1">{{ number_format($stats['total_revenue']) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $currency }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ $tr('المصروفات', 'Expenses') }}</p>
            <p class="text-xl font-bold text-red-600 mt-1">{{ number_format($stats['total_expenses']) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $currency }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ $tr('صافي الربح', 'Net Income') }}</p>
            <p class="text-xl font-bold {{ $stats['net_income'] >= 0 ? 'text-blue-700' : 'text-red-700' }} mt-1">{{ number_format($stats['net_income']) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $currency }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 font-bold text-gray-800 text-sm">{{ $tr('المدفوعات', 'Payments') }} ({{ $payments->count() }})</div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs">
                        <tr>
                            <th class="px-3 py-2 text-right">{{ $tr('المستأجر', 'Tenant') }}</th>
                            <th class="px-3 py-2 text-right">{{ $tr('الشهر', 'Month') }}</th>
                            <th class="px-3 py-2 text-right">{{ $tr('المبلغ', 'Amount') }}</th>
                            <th class="px-3 py-2 text-right">{{ $tr('الحالة', 'Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($payments as $p)
                        <tr>
                            <td class="px-3 py-2">{{ $displayName($p->tenant->user->name ?? null, $tr('مستأجر', 'Tenant')) }}</td>
                            <td class="px-3 py-2 text-xs text-gray-500">{{ $p->month }}/{{ $p->year }}</td>
                            <td class="px-3 py-2 font-medium">{{ number_format($p->amount) }}</td>
                            <td class="px-3 py-2"><span class="text-xs">{{ $p->statusLabel() }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-6 text-gray-400 text-sm">{{ $tr('لا توجد مدفوعات', 'No payments') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 font-bold text-gray-800 text-sm">{{ $tr('المصروفات', 'Expenses') }} ({{ $expenses->count() }})</div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs">
                        <tr>
                            <th class="px-3 py-2 text-right">{{ $tr('البيان', 'Title') }}</th>
                            <th class="px-3 py-2 text-right">{{ $tr('الفئة', 'Category') }}</th>
                            <th class="px-3 py-2 text-right">{{ $tr('التاريخ', 'Date') }}</th>
                            <th class="px-3 py-2 text-right">{{ $tr('المبلغ', 'Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($expenses as $e)
                        <tr>
                            <td class="px-3 py-2">{{ $e->title }}</td>
                            <td class="px-3 py-2 text-xs text-gray-500">{{ $e->categoryLabel() }}</td>
                            <td class="px-3 py-2 text-xs">{{ $e->expense_date->format('Y-m-d') }}</td>
                            <td class="px-3 py-2 font-medium text-red-600">-{{ number_format($e->amount) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-6 text-gray-400 text-sm">{{ $tr('لا توجد مصروفات', 'No expenses') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-4">
        <div class="px-4 py-3 border-b border-gray-100 font-bold text-gray-800 text-sm">{{ $tr('طلبات الصيانة', 'Maintenance Requests') }} ({{ $maintenanceRequests->count() }})</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs">
                    <tr>
                        <th class="px-3 py-2 text-right">{{ $tr('العنوان', 'Title') }}</th>
                        <th class="px-3 py-2 text-right">{{ $tr('المستأجر', 'Tenant') }}</th>
                        <th class="px-3 py-2 text-right">{{ $tr('الوحدة', 'Unit') }}</th>
                        <th class="px-3 py-2 text-right">{{ $tr('الأولوية', 'Priority') }}</th>
                        <th class="px-3 py-2 text-right">{{ $tr('الحالة', 'Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($maintenanceRequests as $mr)
                    <tr>
                        <td class="px-3 py-2">{{ $mr->title }}</td>
                        <td class="px-3 py-2">{{ $displayName($mr->tenant->user->name ?? null, $tr('مستأجر', 'Tenant')) }}</td>
                        <td class="px-3 py-2 text-xs">{{ $mr->unit->unit_number ?? '—' }}</td>
                        <td class="px-3 py-2 text-xs">{{ $mr->priorityLabel() }}</td>
                        <td class="px-3 py-2 text-xs">{{ $mr->statusLabel() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-6 text-gray-400 text-sm">{{ $tr('لا توجد طلبات', 'No requests') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
