<x-app-layout>
    <x-slot name="title">{{ __('My Properties') }}</x-slot>

    <h2 class="text-xl font-bold text-gray-800 mb-5">{{ __('My Properties') }}</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($properties as $property)
        @php $share = $property->owners->first()?->pivot?->ownership_percentage ?? ($property->owner_id === $owner->id ? 100 : 0); @endphp
        <a href="{{ route('owner.properties.show', $property) }}" class="bg-white rounded-xl border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="font-bold text-gray-800">{{ $property->name }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $property->code }} — {{ $property->typeLabel() }}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full bg-blue-50 text-blue-700">{{ number_format($share, 2) }}%</span>
            </div>
            <p class="text-sm text-gray-600">{{ $property->address }}</p>
            <div class="mt-3 grid grid-cols-3 gap-2 text-center text-xs">
                <div class="bg-gray-50 rounded p-2"><div class="font-bold text-gray-800">{{ $property->units->count() }}</div><div class="text-gray-500">{{ __('Total') }}</div></div>
                <div class="bg-green-50 rounded p-2"><div class="font-bold text-green-700">{{ $property->units->where('status','rented')->count() }}</div><div class="text-gray-500">{{ __('Active') }}</div></div>
                <div class="bg-yellow-50 rounded p-2"><div class="font-bold text-yellow-700">{{ $property->units->where('status','available')->count() }}</div><div class="text-gray-500">{{ __('Available') }}</div></div>
            </div>
        </a>
        @empty
        <div class="col-span-full bg-white rounded-xl border border-gray-100 p-10 text-center text-gray-400">{{ __('No data available') }}</div>
        @endforelse
    </div>
</x-app-layout>
