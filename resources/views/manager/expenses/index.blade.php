<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
        $displayName = function (?string $name, string $fallback = 'User') use ($isAr) {
            if ($name === null || $name === '') return $fallback;
            if ($isAr || ! preg_match('/\p{Arabic}/u', $name)) return $name;
            return $fallback;
        };
        $categoryLabels = [
            'utilities' => $tr('مرافق', 'Utilities'),
            'maintenance' => $tr('صيانة', 'Maintenance'),
            'salaries' => $tr('رواتب', 'Salaries'),
            'marketing' => $tr('تسويق', 'Marketing'),
            'repairs' => $tr('إصلاحات', 'Repairs'),
            'other' => $tr('أخرى', 'Other'),
        ];
    @endphp
    <x-slot name="title">{{ $tr('المصروفات', 'Expenses') }}</x-slot>

    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('المصروفات', 'Expenses') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $tr('مصروفات الشركة والعقارات — سنة', 'Company and property expenses — Year') }} {{ $year }}</p>
        </div>
        <a href="{{ route('manager.expenses.create') }}"
           class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ $tr('تسجيل مصروف', 'Add Expense') }}
        </a>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $tr('مصروفات الشركة', 'Company Expenses') }}</p>
                <p class="text-lg font-bold text-blue-800">{{ number_format($totals['company']) }} {{ $currency }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $tr('مصروفات العقارات', 'Property Expenses') }}</p>
                <p class="text-lg font-bold text-orange-700">{{ number_format($totals['property']) }} {{ $currency }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $tr('الإجمالي', 'Total') }}</p>
                <p class="text-lg font-bold text-red-700">{{ number_format($totals['total']) }} {{ $currency }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5 grid grid-cols-1 md:grid-cols-5 gap-3">
        <input type="number" name="year" value="{{ $year }}" placeholder="{{ $tr('السنة', 'Year') }}"
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
        <select name="scope" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ $tr('كل النطاقات', 'All scopes') }}</option>
            <option value="company" @selected($scope==='company')>{{ $tr('شركة', 'Company') }}</option>
            <option value="property" @selected($scope==='property')>{{ $tr('عقار', 'Property') }}</option>
        </select>
        <select name="category" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ $tr('كل الفئات', 'All categories') }}</option>
            <option value="utilities" @selected($category==='utilities')>{{ $tr('مرافق', 'Utilities') }}</option>
            <option value="maintenance" @selected($category==='maintenance')>{{ $tr('صيانة', 'Maintenance') }}</option>
            <option value="salaries" @selected($category==='salaries')>{{ $tr('رواتب', 'Salaries') }}</option>
            <option value="marketing" @selected($category==='marketing')>{{ $tr('تسويق', 'Marketing') }}</option>
            <option value="repairs" @selected($category==='repairs')>{{ $tr('إصلاحات', 'Repairs') }}</option>
            <option value="other" @selected($category==='other')>{{ $tr('أخرى', 'Other') }}</option>
        </select>
        <select name="property_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ $tr('كل العقارات', 'All properties') }}</option>
            @foreach($properties as $p)
            <option value="{{ $p->id }}" @selected($propertyId==$p->id)>{{ $p->name }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button class="bg-blue-900 text-white px-4 py-2 rounded-lg text-sm flex-1">{{ $tr('تصفية', 'Filter') }}</button>
            <a href="{{ route('manager.expenses.index') }}" class="text-gray-500 text-sm px-3 py-2">{{ $tr('إعادة', 'Reset') }}</a>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">{{ $tr('البيان', 'Title') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('الفئة', 'Category') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('النطاق', 'Scope') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('العقار', 'Property') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('التاريخ', 'Date') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('المبلغ', 'Amount') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('دُفع بواسطة', 'Paid by') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('الفاتورة', 'Invoice') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('إجراءات', 'Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $expense->title }}</div>
                            @if($expense->description)
                            <div class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ $expense->description }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-700">{{ $categoryLabels[$expense->category] ?? $expense->categoryLabel() }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs
                                @if($expense->scope === 'company') bg-blue-50 text-blue-700
                                @else bg-orange-50 text-orange-700 @endif">
                                {{ $expense->scope === 'company' ? $tr('شركة', 'Company') : $tr('عقار', 'Property') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">
                            {{ $expense->scope === 'property' && $expense->expensable ? $expense->expensable->name : $tr('—', 'N/A') }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $expense->expense_date->format('Y/m/d') }}</td>
                        <td class="px-4 py-3 font-semibold text-red-700">{{ number_format($expense->amount) }} {{ $currency }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $displayName($expense->paidByUser->name ?? null, $tr('—', 'N/A')) }}</td>
                        <td class="px-4 py-3">
                            @if($expense->receipt_path)
                            <a href="{{ Storage::disk('public')->url($expense->receipt_path) }}" target="_blank"
                               class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                </svg>
                                {{ $tr('عرض', 'View') }}
                            </a>
                            @else
                            <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('manager.expenses.destroy', $expense) }}"
                                  onsubmit="return confirm('{{ $tr('حذف هذا المصروف؟', 'Delete this expense?') }}')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 text-xs">{{ $tr('حذف', 'Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="py-10 text-center text-gray-400">{{ $tr('لا توجد مصروفات', 'No expenses found') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">{{ $expenses->links() }}</div>
    </div>

    @if(session('success'))
    <div class="fixed bottom-4 left-4 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm z-50">
        {{ session('success') }}
    </div>
    @endif
</x-app-layout>
