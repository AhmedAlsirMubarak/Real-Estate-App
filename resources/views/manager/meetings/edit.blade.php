<x-app-layout>
    <x-slot name="title">{{ __('Edit') }} — {{ $meeting->title }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">{{ __('Edit') }} — {{ __('Meetings') }}</h2>
        <a href="{{ route('manager.meetings.show', $meeting) }}" class="text-sm text-gray-600">{{ __('Back') }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-3xl">
        <form method="POST" action="{{ route('manager.meetings.update', $meeting) }}" class="space-y-4">
            @csrf @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title') }} (AR)</label>
                    <input type="text" name="title_ar" value="{{ $meeting->getRawOriginal('title_ar') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title') }} (EN)</label>
                    <input type="text" name="title_en" value="{{ $meeting->getRawOriginal('title_en') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Scheduled At') }}</label>
                    <input type="datetime-local" name="scheduled_at" value="{{ $meeting->scheduled_at->format('Y-m-d\TH:i') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                    <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="scheduled" @selected($meeting->status==='scheduled')>{{ __('Scheduled') }}</option>
                        <option value="completed" @selected($meeting->status==='completed')>{{ __('Completed') }}</option>
                        <option value="cancelled" @selected($meeting->status==='cancelled')>{{ __('Cancelled') }}</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Location') }} (AR)</label>
                    <input type="text" name="location_ar" value="{{ $meeting->getRawOriginal('location_ar') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Location') }} (EN)</label>
                    <input type="text" name="location_en" value="{{ $meeting->getRawOriginal('location_en') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Agenda') }} (AR)</label>
                    <textarea name="agenda_ar" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ $meeting->getRawOriginal('agenda_ar') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Agenda') }} (EN)</label>
                    <textarea name="agenda_en" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ $meeting->getRawOriginal('agenda_en') }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Minutes') }} (AR)</label>
                    <textarea name="minutes_ar" rows="4" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ $meeting->getRawOriginal('minutes_ar') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Minutes') }} (EN)</label>
                    <textarea name="minutes_en" rows="4" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ $meeting->getRawOriginal('minutes_en') }}</textarea>
                </div>
            </div>

            <div class="flex gap-2 pt-3">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ __('Update') }}</button>
                <a href="{{ route('manager.meetings.show', $meeting) }}" class="text-gray-600 px-3 py-2 text-sm">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>
