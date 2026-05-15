<x-app-layout>
    <x-slot name="title">{{ __('My Dues') }}</x-slot>

    <h2 class="text-xl font-bold text-gray-800 mb-5">{{ __('My Dues') }}</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ __('Pending') }}</p>
            <p class="text-lg font-bold text-yellow-700 mt-1">{{ number_format($totals['pending'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ __('Paid') }}</p>
            <p class="text-lg font-bold text-green-700 mt-1">{{ number_format($totals['paid'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ __('Overdue') }}</p>
            <p class="text-lg font-bold text-red-700 mt-1">{{ number_format($totals['overdue'], 2) }}</p>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-xl border border-gray-100 p-4 mb-5 flex gap-3">
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('Status') }}</option>
            <option value="pending" @selected(request('status')==='pending')>{{ __('Pending') }}</option>
            <option value="paid" @selected(request('status')==='paid')>{{ __('Paid') }}</option>
            <option value="overdue" @selected(request('status')==='overdue')>{{ __('Overdue') }}</option>
        </select>
        <button class="bg-blue-900 text-white px-4 py-2 rounded-lg text-sm">{{ __('Search') }}</button>
    </form>

    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-600 uppercase">
                <tr>
                    <th class="px-4 py-3 text-right">{{ __('Property') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Period') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Amount') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Due Date') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($dues as $due)
            <tr>
                <td class="px-4 py-3">{{ $due->association->property->name }}</td>
                <td class="px-4 py-3 text-xs">{{ $due->periodLabel() }}</td>
                <td class="px-4 py-3 font-semibold">{{ number_format($due->amount, 2) }}</td>
                <td class="px-4 py-3 text-xs">{{ $due->due_date->format('Y/m/d') }}</td>
                <td class="px-4 py-3">
                    <span class="text-xs px-2 py-0.5 rounded-full
                        @if($due->status==='paid') bg-green-50 text-green-700
                        @elseif($due->status==='overdue') bg-red-50 text-red-700
                        @elseif($due->status==='waived') bg-gray-100 text-gray-600
                        @else bg-yellow-50 text-yellow-700 @endif">{{ $due->statusLabel() }}</span>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="py-10 text-center text-gray-400">{{ __('No data available') }}</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="p-3 border-t border-gray-100">{{ $dues->links() }}</div>
    </div>
</x-app-layout>
