<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr   = fn(string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('ميزانية الشركة', 'Company Budget') }}</x-slot>

    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('ميزانية الشركة', 'Company Budget') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $tr('المخصص', 'Allocated') }}: <strong>{{ number_format($totalAllocated, 2) }}</strong> &nbsp;·&nbsp;
                {{ $tr('المصروف', 'Spent') }}: <strong class="text-red-600">{{ number_format($totalSpent, 2) }}</strong>
            </p>
        </div>
        <a href="{{ route('manager.budgets.create') }}" class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
            {{ $tr('إضافة', 'Add') }}
        </a>
    </div>

    <form method="GET" class="bg-white rounded-xl border border-gray-100 p-4 mb-5 grid grid-cols-1 md:grid-cols-4 gap-3">
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ $tr('الحالة', 'Status') }}</option>
            <option value="draft"    @selected(request('status')==='draft')>{{ $tr('مسودة', 'Draft') }}</option>
            <option value="approved" @selected(request('status')==='approved')>{{ $tr('معتمد', 'Approved') }}</option>
            <option value="closed"   @selected(request('status')==='closed')>{{ $tr('مغلق', 'Closed') }}</option>
        </select>
        <select name="category" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ $tr('الفئة', 'Category') }}</option>
            <option value="hr"          @selected(request('category')==='hr')>{{ $tr('الموارد البشرية', 'HR') }}</option>
            <option value="operations"  @selected(request('category')==='operations')>{{ $tr('العمليات', 'Operations') }}</option>
            <option value="it"          @selected(request('category')==='it')>{{ $tr('تقنية المعلومات', 'IT') }}</option>
            <option value="marketing"   @selected(request('category')==='marketing')>{{ $tr('التسويق', 'Marketing') }}</option>
            <option value="maintenance" @selected(request('category')==='maintenance')>{{ $tr('الصيانة', 'Maintenance') }}</option>
            <option value="other"       @selected(request('category')==='other')>{{ $tr('أخرى', 'Other') }}</option>
        </select>
        <input type="number" name="year" value="{{ request('year', now()->year) }}"
               placeholder="{{ $tr('السنة', 'Year') }}"
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
        <button class="bg-blue-900 text-white px-4 py-2 rounded-lg text-sm">{{ $tr('بحث', 'Search') }}</button>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">{{ $tr('العنوان', 'Title') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('الفئة', 'Category') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('الفترة', 'Period') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('المخصص', 'Allocated') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('المصروف', 'Spent') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('المتبقي', 'Remaining') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('الاستخدام', 'Usage') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('الحالة', 'Status') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('إجراءات', 'Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($budgets as $b)
                    @php $pct = $b->usagePercent(); $remaining = $b->remaining(); @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $b->title }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $b->categoryLabel() }}</td>
                        <td class="px-4 py-3 text-xs">{{ $b->periodLabel() }}</td>
                        <td class="px-4 py-3">{{ number_format($b->allocated_amount, 2) }}</td>
                        <td class="px-4 py-3 text-red-600">{{ number_format($b->spent_amount, 2) }}</td>
                        <td class="px-4 py-3 {{ $remaining < 0 ? 'text-red-700 font-bold' : 'text-green-700' }}">{{ number_format($remaining, 2) }}</td>
                        <td class="px-4 py-3 min-w-[100px]">
                            <div class="flex items-center gap-1.5">
                                <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $pct >= 100 ? 'bg-red-500' : ($pct >= 80 ? 'bg-yellow-400' : 'bg-green-500') }}" style="width:{{ $pct }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 shrink-0">{{ $pct }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                @if($b->status==='approved') bg-green-50 text-green-700
                                @elseif($b->status==='draft') bg-gray-100 text-gray-600
                                @else bg-blue-50 text-blue-700 @endif">
                                {{ $b->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2 flex-wrap">
                                <a href="{{ route('manager.budgets.edit', $b) }}" class="text-indigo-600 hover:text-indigo-800 text-xs">{{ $tr('تعديل', 'Edit') }}</a>
                                <form method="POST" action="{{ route('manager.budgets.destroy', $b) }}"
                                      onsubmit="return confirm('{{ $tr('هل أنت متأكد؟', 'Are you sure?') }}')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 text-xs">{{ $tr('حذف', 'Delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="py-10 text-center text-gray-400">{{ $tr('لا توجد بيانات', 'No data available') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">{{ $budgets->links() }}</div>
    </div>
</x-app-layout>
