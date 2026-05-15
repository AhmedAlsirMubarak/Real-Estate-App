<x-app-layout>
    <x-slot name="title">{{ $meeting->title }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $meeting->title }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $meeting->association->property->name }} — {{ $meeting->scheduled_at->format('Y/m/d H:i') }}</p>
        </div>
        <a href="{{ route('owner.meetings.index') }}" class="text-sm text-gray-600">{{ __('Back') }}</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
            <div><p class="text-xs text-gray-500">{{ __('Status') }}</p><p class="text-sm font-semibold">{{ $meeting->statusLabel() }}</p></div>
            <div><p class="text-xs text-gray-500">{{ __('Location') }}</p><p class="text-sm font-semibold">{{ $meeting->location ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500">{{ __('Scheduled At') }}</p><p class="text-sm font-semibold">{{ $meeting->scheduled_at->format('Y/m/d H:i') }}</p></div>
        </div>
        @if($meeting->agenda)
        <div class="mb-4"><h3 class="text-sm font-bold mb-2">{{ __('Agenda') }}</h3><p class="text-sm text-gray-700 whitespace-pre-line">{{ $meeting->agenda }}</p></div>
        @endif
        @if($meeting->minutes)
        <div><h3 class="text-sm font-bold mb-2">{{ __('Minutes') }}</h3><p class="text-sm text-gray-700 whitespace-pre-line">{{ $meeting->minutes }}</p></div>
        @endif
    </div>
</x-app-layout>
