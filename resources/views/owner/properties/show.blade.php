<x-app-layout>
    <x-slot name="title">{{ $property->name }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $property->name }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $property->code }} — {{ $property->typeLabel() }}</p>
        </div>
        <a href="{{ route('owner.properties.index') }}" class="text-sm text-gray-600">{{ __('Back') }}</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <h3 class="text-sm font-bold mb-3">{{ __('Description') }}</h3>
            <p class="text-sm text-gray-700">{{ $property->description }}</p>
            <div class="mt-3 text-sm text-gray-600">{{ $property->address }} — {{ $property->city }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <h3 class="text-sm font-bold mb-3">{{ __('Owners Association') }}</h3>
            @forelse($property->associations as $assoc)
                <p class="text-sm font-medium">{{ $assoc->name }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('Monthly Fee per Unit') }}: {{ number_format($assoc->monthly_fee_per_unit, 2) }}</p>
                @if(!$loop->last)<hr class="my-2 border-gray-100">@endif
            @empty
                <p class="text-sm text-gray-400">—</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-5">
        <h3 class="text-sm font-bold mb-3">{{ __('Owners') }}</h3>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="text-xs text-gray-500"><tr><th class="text-right py-2">{{ __('Name') }}</th><th class="text-right py-2">{{ __('Ownership %') }}</th><th class="text-right py-2">{{ __('Primary Owner') }}</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
            @foreach($property->owners as $o)
            <tr><td class="py-2">{{ $o->user?->name }}</td><td class="py-2">{{ $o->pivot->ownership_percentage }}%</td><td class="py-2">{{ $o->pivot->is_primary ? '✓' : '' }}</td></tr>
            @endforeach
            </tbody>
        </table></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 p-5">
        <h3 class="text-sm font-bold mb-3">{{ __('Property') }} — {{ __('Properties') }}</h3>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="text-xs text-gray-500"><tr><th class="text-right py-2">#</th><th class="text-right py-2">{{ __('Status') }}</th><th class="text-right py-2">{{ __('Amount') }}</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
            @foreach($property->units as $u)
            <tr><td class="py-2">{{ $u->unit_number ?? '—' }}</td><td class="py-2 text-xs">{{ $u->status }}</td><td class="py-2">{{ number_format($u->rent_price ?? $u->sale_price ?? 0, 0) }}</td></tr>
            @endforeach
            </tbody>
        </table></div>
    </div>
</x-app-layout>
