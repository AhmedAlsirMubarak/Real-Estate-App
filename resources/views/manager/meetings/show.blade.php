<x-app-layout>
    <x-slot name="title">{{ $meeting->title }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $meeting->title }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $meeting->association->property->name }} — {{ $meeting->scheduled_at->format('Y/m/d H:i') }}</p>
        </div>
        <a href="{{ route('manager.meetings.edit', $meeting) }}" class="bg-gray-100 text-gray-700 px-3 py-2 rounded-lg text-sm hover:bg-gray-200">{{ __('Edit') }}</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 p-6 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div><p class="text-xs text-gray-500">{{ __('Status') }}</p><p class="text-sm font-semibold">{{ $meeting->statusLabel() }}</p></div>
            <div><p class="text-xs text-gray-500">{{ __('Location') }}</p><p class="text-sm font-semibold">{{ $meeting->location ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500">{{ __('Scheduled At') }}</p><p class="text-sm font-semibold">{{ $meeting->scheduled_at->format('Y/m/d H:i') }}</p></div>
        </div>
        @if($meeting->agenda)
        <div class="mb-4">
            <h3 class="text-sm font-bold text-gray-800 mb-2">{{ __('Agenda') }}</h3>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $meeting->agenda }}</p>
        </div>
        @endif
        @if($meeting->minutes)
        <div>
            <h3 class="text-sm font-bold text-gray-800 mb-2">{{ __('Minutes') }}</h3>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $meeting->minutes }}</p>
        </div>
        @endif
    </div>

    <form method="POST" action="{{ route('manager.meetings.destroy', $meeting) }}" onsubmit="return confirm('Delete?')">
        @csrf @method('DELETE')
        <button class="text-red-600 hover:text-red-800 text-sm">{{ __('Delete') }}</button>
    </form>
</x-app-layout>
