<x-app-layout>
    <x-slot name="title">{{ __('Salaries') }}</x-slot>

    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ __('Salaries') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('Total') }}: {{ number_format($totalPaid, 2) }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('manager.salaries.export', request()->only(['status','month','year'])) }}"
               class="flex items-center gap-1.5 border border-blue-200 text-blue-700 hover:bg-blue-50 px-4 py-2 rounded-lg text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                {{ __('Export PDF') }}
            </a>
            <a href="{{ route('manager.salaries.create') }}" class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium">{{ __('Add') }}</a>
        </div>
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
                        <th class="px-4 py-3 text-right">{{ __('Total Allowances') }}</th>
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
                        <td class="px-4 py-3">
                            @php $allowances = $s->totalAllowances(); @endphp
                            @if($allowances > 0)
                            <span class="text-blue-700 font-medium" title="{{ app()->getLocale()==='ar' ? 'سكن: ' : 'Housing: ' }}{{ number_format($s->housing_allowance,2) }} | {{ app()->getLocale()==='ar' ? 'مواصلات: ' : 'Transport: ' }}{{ number_format($s->transport_allowance,2) }} | {{ app()->getLocale()==='ar' ? 'طعام: ' : 'Food: ' }}{{ number_format($s->food_allowance,2) }} | {{ app()->getLocale()==='ar' ? 'أخرى: ' : 'Other: ' }}{{ number_format($s->other_allowances,2) }}">{{ number_format($allowances, 2) }}</span>
                            @else
                            <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-green-700">{{ number_format($s->bonuses, 2) }}</td>
                        <td class="px-4 py-3 text-red-600">{{ number_format($s->deductions, 2) }}</td>
                        <td class="px-4 py-3 font-bold">{{ number_format($s->net_paid, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                @if($s->status==='paid') bg-green-50 text-green-700
                                @elseif($s->status==='pending') bg-yellow-50 text-yellow-700
                                @else bg-gray-100 text-gray-600 @endif">{{ $s->statusLabel() }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                {{-- View --}}
                                <a href="{{ route('manager.salaries.show', $s) }}"
                                   title="{{ __('View Details') }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                {{-- Edit --}}
                                <a href="{{ route('manager.salaries.edit', $s) }}"
                                   title="{{ __('Edit') }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                {{-- Pay --}}
                                @if($s->status !== 'paid')
                                <form method="POST" action="{{ route('manager.salaries.pay', $s) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="{{ __('Pay Salary') }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                </form>
                                @endif
                                {{-- WhatsApp --}}
                                <x-whatsapp-button size="sm" :phone="$s->employee?->phone" :message="__('Salaries').' '.$s->periodLabel().' — '.number_format($s->net_paid,2)" />
                                {{-- Delete --}}
                                <form method="POST" action="{{ route('manager.salaries.destroy', $s) }}" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="{{ __('Delete') }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
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
        <div class="p-3 border-t border-gray-100">{{ $salaries->links() }}</div>
    </div>
</x-app-layout>
