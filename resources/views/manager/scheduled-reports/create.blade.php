<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('تقرير مجدول جديد', 'New Scheduled Report') }}</x-slot>

    <div class="max-w-3xl mx-auto">
        <a href="{{ route('manager.scheduled-reports.index', ['section' => $section]) }}"
           class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">← {{ $tr('رجوع', 'Back') }}</a>

        <form method="POST" action="{{ route('manager.scheduled-reports.store') }}"
              class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            @csrf
            <h2 class="text-lg font-bold text-gray-800">{{ $tr('تقرير مجدول جديد', 'New Scheduled Report') }}</h2>
            @include('manager.scheduled-reports._form')

            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('manager.scheduled-reports.index', ['section' => $section]) }}"
                   class="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50">{{ $tr('إلغاء', 'Cancel') }}</a>
                <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    {{ $tr('حفظ', 'Save') }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
