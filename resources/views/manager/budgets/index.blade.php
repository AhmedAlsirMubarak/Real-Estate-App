<x-app-layout>
    <x-slot name="title">{{ __('Company Budget') }}</x-slot>

    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ __('Company Budget') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ __('Allocated') }}: <strong>{{ number_format($totalAllocated, 2) }}</strong> &nbsp;·&nbsp;
                {{ __('Spent') }}: <strong class="text-red-600">{{ number_format($totalSpent, 2) }}</strong>
            </p>
        </div>
        <a href="{{ route('manager.budgets.create') }}" class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium">{{ __('Add') }}</a>
    </div>

    <form method="GET" class="bg-white rounded-xl border border-gray-100 p-4 mb-5 grid grid-cols-1 md:grid-cols-4 gap-3">
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('Status') }}</option>
            <option value="draft"    @selected(request('status')==='draft')>{{ __('Draft') }}</option>
            <option value="approved" @selected(request('status')==='approved')>{{ __('Approved') }}</option>
            <option value="closed"   @selected(request('status')==='closed')>{{ __('Closed') }}</option>
        </select>
        <select name="category" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('Category') }}</option>
            <option value="hr"          @selected(request('category')==='hr')>HR</option>
            <option value="operations"  @selected(request('category')==='operations')>{{ __('Operations') }}</option>
            <option value="it"          @selected(request('category')==='it')>IT</option>
            <option value="marketing"   @selected(request('category')==='marketing')>{{ __('Marketing') }}</option>
            <option value="maintenance" @selected(request('category')==='maintenance')>{{ __('Maintenance') }}</option>
            <option value="other"       @selected(request('category')==='other')>{{ __('Other') }}</option>
        </select>
        <input type="number" name="year" value="{{ request('year', now()->year) }}" placeholder="{{ __('Year') }}" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
        <button class="bg-blue-900 text-white px-4 py-2 rounded-lg text-sm">{{ __('Search') }}</button>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">{{ __('Title') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Category') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Period') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Allocated') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Spent') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Remaining') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Usage') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
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
                                <a href="{{ route('manager.budgets.edit', $b) }}" class="text-indigo-600 hover:text-indigo-800 text-xs">{{ __('Edit') }}</a>
                                <form method="POST" action="{{ route('manager.budgets.destroy', $b) }}" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 text-xs">{{ __('Delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="py-10 text-center text-gray-400">{{ __('No data available') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">{{ $budgets->links() }}</div>
    </div>
</x-app-layout>
