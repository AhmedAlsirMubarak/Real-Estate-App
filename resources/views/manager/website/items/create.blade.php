<x-app-layout>
@php $title = 'إضافة عنصر'; @endphp

<div class="max-w-3xl mx-auto py-8 px-4">

    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
        <a href="{{ route('manager.website.index') }}" class="hover:text-navy">محتوى الموقع</a>
        <span>/</span>
        <a href="{{ route('manager.website.section.edit', [$page, $key]) }}" class="hover:text-navy">تعديل القسم</a>
        <span>/</span>
        <span class="text-gray-700 font-medium">إضافة عنصر</span>
    </nav>

    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <h1 class="text-lg font-bold text-gray-900 mb-6">إضافة عنصر جديد</h1>

        <form method="POST" action="{{ route('manager.website.items.store', [$page, $key]) }}" enctype="multipart/form-data">
            @csrf
            @include('manager.website.items._form', ['item' => null])
            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-navy text-white text-sm font-bold hover:bg-navy-mid transition">إضافة</button>
                <a href="{{ route('manager.website.section.edit', [$page, $key]) }}"
                   class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm hover:bg-gray-50 transition">إلغاء</a>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
