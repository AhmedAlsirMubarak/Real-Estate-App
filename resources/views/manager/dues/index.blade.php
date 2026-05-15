<x-app-layout>
    <x-slot name="title">{{ __('Dues') }}</x-slot>

    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <h2 class="text-xl font-bold text-gray-800">{{ __('Dues') }}</h2>
    </div>

    <form method="GET" class="bg-white rounded-xl border border-gray-100 p-4 mb-5 grid grid-cols-1 md:grid-cols-4 gap-3">
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('Status') }}</option>
            <option value="pending" @selected(request('status')==='pending')>{{ __('Pending') }}</option>
            <option value="paid" @selected(request('status')==='paid')>{{ __('Paid') }}</option>
            <option value="overdue" @selected(request('status')==='overdue')>{{ __('Overdue') }}</option>
        </select>
        <select name="month" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('Month') }}</option>
            @for($m=1;$m<=12;$m++)
            <option value="{{ $m }}" @selected(request('month')==$m)>{{ $m }}</option>
            @endfor
        </select>
        <input type="number" name="year" value="{{ request('year') }}" placeholder="{{ __('Year') }}" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
        <button class="bg-blue-900 text-white px-4 py-2 rounded-lg text-sm">{{ __('Search') }}</button>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">{{ __('Property') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Owner') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Period') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Amount') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Due Date') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($dues as $due)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $due->association->property->name }}</td>
                        <td class="px-4 py-3">{{ $due->owner?->user?->name ?? '—' }}</td>
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
                        <td class="px-4 py-3 text-xs">
                            <div class="flex flex-wrap gap-1.5">
                            @if($due->status !== 'paid')
                            <form method="POST" action="{{ route('manager.dues.paid', $due) }}">@csrf @method('PATCH')<button class="text-green-700 hover:text-green-900">{{ __('Mark as Paid') }}</button></form>
                            <form method="POST" action="{{ route('manager.dues.waived', $due) }}">@csrf @method('PATCH')<button class="text-gray-600 hover:text-gray-800">{{ __('Mark as Waived') }}</button></form>
                            @endif
                            <x-whatsapp-button size="sm" :phone="$due->owner?->phone" :message="__('Dues').': '.$due->periodLabel().' — '.number_format($due->amount,2)" />
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-10 text-center text-gray-400">{{ __('No data available') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">{{ $dues->links() }}</div>
    </div>
</x-app-layout>
