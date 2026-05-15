<x-app-layout>
    <x-slot name="title">{{ $association->name }}</x-slot>

    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $association->name }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $association->property->name }} — {{ $association->property->code }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('manager.associations.edit', $association) }}" class="bg-gray-100 text-gray-700 px-3 py-2 rounded-lg text-sm hover:bg-gray-200">{{ __('Edit') }}</a>
            <a href="{{ route('manager.meetings.create', ['association' => $association->id]) }}" class="bg-amber-100 text-amber-800 px-3 py-2 rounded-lg text-sm hover:bg-amber-200">{{ __('Schedule Meeting') }}</a>
        </div>
    </div>

    {{-- Info cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ __('Established Date') }}</p>
            <p class="text-base font-semibold text-gray-800 mt-1">{{ $association->established_date?->format('Y/m/d') ?? '—' }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ __('Monthly Fee per Unit') }}</p>
            <p class="text-base font-semibold text-gray-800 mt-1">{{ number_format($association->monthly_fee_per_unit, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ __('Owners') }}</p>
            <p class="text-base font-semibold text-gray-800 mt-1">{{ $association->property->owners->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ __('Status') }}</p>
            <p class="text-base font-semibold text-gray-800 mt-1">{{ $association->status === 'active' ? __('Active') : __('Inactive') }}</p>
        </div>
    </div>

    {{-- Generate dues --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-6">
        <h3 class="text-sm font-bold text-gray-800 mb-3">{{ __('Generate Monthly Dues') }}</h3>
        <form method="POST" action="{{ route('manager.associations.dues.generate', $association) }}" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs text-gray-600 mb-1">{{ __('Month') }}</label>
                <select name="period_month" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" @selected($m === now()->month)>{{ $m }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">{{ __('Year') }}</label>
                <input type="number" name="period_year" value="{{ now()->year }}" min="2020" max="2100" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-24">
            </div>
            <button class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm">{{ __('Generate Monthly Dues') }}</button>
        </form>
    </div>

    {{-- Owners --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-6">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-sm font-bold text-gray-800">{{ __('Owners') }}</h3>
            <a href="{{ route('manager.properties.owners.index', $association->property) }}" class="text-xs text-blue-700 hover:text-blue-900">{{ __('Edit') }}</a>
        </div>
        <table class="w-full text-sm">
            <thead class="text-xs text-gray-500 uppercase">
                <tr><th class="text-right py-2">{{ __('Name') }}</th><th class="text-right py-2">{{ __('Ownership %') }}</th><th class="text-right py-2">{{ __('Phone') }}</th><th class="text-right py-2">{{ __('Primary Owner') }}</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($association->property->owners as $owner)
                <tr>
                    <td class="py-2">{{ $owner->user?->name ?? '—' }}</td>
                    <td class="py-2">{{ $owner->pivot->ownership_percentage }}%</td>
                    <td class="py-2 text-xs text-gray-600">{{ $owner->phone ?? '—' }}</td>
                    <td class="py-2">{{ $owner->pivot->is_primary ? '✓' : '' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-4 text-center text-gray-400 text-xs">{{ __('No data available') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Recent dues --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-6">
        <h3 class="text-sm font-bold text-gray-800 mb-3">{{ __('Dues') }}</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-gray-500 uppercase">
                    <tr><th class="text-right py-2">{{ __('Owner') }}</th><th class="text-right py-2">{{ __('Period') }}</th><th class="text-right py-2">{{ __('Amount') }}</th><th class="text-right py-2">{{ __('Status') }}</th><th class="text-right py-2">{{ __('Due Date') }}</th><th class="text-right py-2">{{ __('Actions') }}</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($association->dues as $due)
                    <tr>
                        <td class="py-2">{{ $due->owner?->user?->name ?? '—' }}</td>
                        <td class="py-2 text-xs">{{ $due->periodLabel() }}</td>
                        <td class="py-2 font-semibold">{{ number_format($due->amount, 2) }}</td>
                        <td class="py-2"><span class="text-xs px-2 py-0.5 rounded-full
                            @if($due->status==='paid') bg-green-50 text-green-700
                            @elseif($due->status==='overdue') bg-red-50 text-red-700
                            @elseif($due->status==='waived') bg-gray-100 text-gray-600
                            @else bg-yellow-50 text-yellow-700 @endif">{{ $due->statusLabel() }}</span></td>
                        <td class="py-2 text-xs text-gray-600">{{ $due->due_date->format('Y/m/d') }}</td>
                        <td class="py-2 text-xs flex gap-1 flex-wrap">
                            @if($due->status !== 'paid')
                            <form method="POST" action="{{ route('manager.dues.paid', $due) }}">@csrf @method('PATCH')<button class="text-green-700 hover:text-green-900">{{ __('Mark as Paid') }}</button></form>
                            @endif
                            <x-whatsapp-button size="sm" :phone="$due->owner?->phone" :message="__('Dues').' '.$due->periodLabel().' — '.number_format($due->amount,2)" />
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-4 text-center text-gray-400 text-xs">{{ __('No data available') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Meetings --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-6">
        <h3 class="text-sm font-bold text-gray-800 mb-3">{{ __('Meetings') }}</h3>
        <table class="w-full text-sm">
            <thead class="text-xs text-gray-500 uppercase">
                <tr><th class="text-right py-2">{{ __('Title') }}</th><th class="text-right py-2">{{ __('Scheduled At') }}</th><th class="text-right py-2">{{ __('Status') }}</th><th class="text-right py-2">{{ __('Actions') }}</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($association->meetings as $m)
                <tr>
                    <td class="py-2">{{ $m->title }}</td>
                    <td class="py-2 text-xs text-gray-600">{{ $m->scheduled_at->format('Y/m/d H:i') }}</td>
                    <td class="py-2 text-xs">{{ $m->statusLabel() }}</td>
                    <td class="py-2 text-xs"><a href="{{ route('manager.meetings.show', $m) }}" class="text-blue-700">{{ __('View Details') }}</a></td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-4 text-center text-gray-400 text-xs">{{ __('No data available') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
