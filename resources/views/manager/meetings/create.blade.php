<x-app-layout>
    <x-slot name="title">{{ __('Schedule Meeting') }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">{{ __('Schedule Meeting') }}</h2>
        <a href="{{ route('manager.meetings.index') }}" class="text-sm text-gray-600">{{ __('Back') }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-3xl">
        <form method="POST" action="{{ route('manager.meetings.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Owners Association') }}</label>
                <select name="association_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">--</option>
                    @foreach($associations as $a)
                    <option value="{{ $a->id }}" @selected($selected==$a->id)>{{ $a->name }} — {{ $a->property->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title') }} (AR)</label>
                    <input type="text" name="title_ar" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title') }} (EN)</label>
                    <input type="text" name="title_en" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Scheduled At') }}</label>
                    <input type="datetime-local" name="scheduled_at" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                    <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="scheduled">{{ __('Scheduled') }}</option>
                        <option value="completed">{{ __('Completed') }}</option>
                        <option value="cancelled">{{ __('Cancelled') }}</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Location') }} (AR)</label>
                    <input type="text" name="location_ar" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Location') }} (EN)</label>
                    <input type="text" name="location_en" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Agenda') }} (AR)</label>
                    <textarea name="agenda_ar" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Agenda') }} (EN)</label>
                    <textarea name="agenda_en" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"></textarea>
                </div>
            </div>

            <div class="flex gap-2 pt-3">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ __('Save') }}</button>
                <a href="{{ route('manager.meetings.index') }}" class="text-gray-600 px-3 py-2 text-sm">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>
