<x-app-layout>
    <x-slot name="title">{{ __('Salaries') }}</x-slot>

    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ __('Salaries') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('Total') }}: {{ number_format($totalPaid, 2) }}</p>
        </div>
        <a href="{{ route('manager.salaries.create') }}" class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium">{{ __('Add') }}</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-5">
        <h3 class="text-sm font-bold text-gray-800 mb-3">{{ __('Generate Monthly Salaries') }}</h3>
        <form method="POST" action="{{ route('manager.salaries.generate') }}" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs text-gray-600 mb-1">{{ __('Month') }}</label>
                <select name="period_month" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    @for($m=1;$m<=12;$m++)<option value="{{ $m }}" @selected($m===now()->month)>{{ $m }}</option>@endfor
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">{{ __('Year') }}</label>
                <input type="number" name="period_year" value="{{ now()->year }}" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-24">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">{{ __('Base Salary') }}</label>
                <input type="number" step="0.01" name="default_base" value="5000" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-32">
            </div>
            <button class="bg-blue-900 text-white px-4 py-2 rounded-lg text-sm">{{ __('Generate Monthly Salaries') }}</button>
        </form>
    </div>

    <form method="GET" class="bg-white rounded-xl border border-gray-100 p-4 mb-5 grid grid-cols-1 md:grid-cols-4 gap-3">
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('Status') }}</option>
            <option value="draft" @selected(request('status')==='draft')>{{ __('Draft') }}</option>
            <option value="pending" @selected(request('status')==='pending')>{{ __('Pending') }}</option>
            <option value="paid" @selected(request('status')==='paid')>{{ __('Paid') }}</option>
        </select>
        <select name="month" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('Month') }}</option>
            @for($m=1;$m<=12;$m++)<option value="{{ $m }}" @selected(request('month')==$m)>{{ $m }}</option>@endfor
        </select>
        <input type="number" name="year" value="{{ request('year') }}" placeholder="{{ __('Year') }}" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
        <button class="bg-blue-900 text-white px-4 py-2 rounded-lg text-sm">{{ __('Search') }}</button>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">{{ __('Employees') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Period') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Base Salary') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Bonuses') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Deductions') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Net Paid') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($salaries as $s)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $s->employee?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs">{{ $s->periodLabel() }}</td>
                        <td class="px-4 py-3">{{ number_format($s->base_salary, 2) }}</td>
                        <td class="px-4 py-3 text-green-700">{{ number_format($s->bonuses, 2) }}</td>
                        <td class="px-4 py-3 text-red-600">{{ number_format($s->deductions, 2) }}</td>
                        <td class="px-4 py-3 font-bold">{{ number_format($s->net_paid, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                @if($s->status==='paid') bg-green-50 text-green-700
                                @elseif($s->status==='pending') bg-yellow-50 text-yellow-700
                                @else bg-gray-100 text-gray-600 @endif">{{ $s->statusLabel() }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs">
                            <div class="flex gap-1.5 flex-wrap">
                            @if($s->status !== 'paid')
                            <form method="POST" action="{{ route('manager.salaries.pay', $s) }}">@csrf @method('PATCH')<button class="text-green-700 hover:text-green-900">{{ __('Pay Salary') }}</button></form>
                            @endif
                            <x-whatsapp-button size="sm" :phone="$s->employee?->phone" :message="__('Salaries').' '.$s->periodLabel().' — '.number_format($s->net_paid,2)" />
                            <form method="POST" action="{{ route('manager.salaries.destroy', $s) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-red-600 hover:text-red-800">{{ __('Delete') }}</button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="py-10 text-center text-gray-400">{{ __('No data available') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">{{ $salaries->links() }}</div>
    </div>
</x-app-layout>
