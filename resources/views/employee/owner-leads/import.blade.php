<x-app-layout>
@php
    $isAr = app()->getLocale() === 'ar';
    $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    $results = session('import_results');
@endphp
<x-slot name="title">{{ $tr('استيراد ملاك', 'Import Owners') }}</x-slot>

<div class="max-w-3xl mx-auto py-4">
    <a href="{{ route('employee.owner-leads.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-5 inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        {{ $tr('رجوع إلى الملاك', 'Back to Owners') }}
    </a>

    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">{{ $tr('استيراد ملاك من CSV', 'Import Owners from CSV') }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $tr('ارفع ملف CSV لاستيراد الملاك دفعة واحدة', 'Upload a CSV file to import owners in bulk') }}</p>
    </div>

    @if($results)
    <div class="mb-6 space-y-3">
        @if($results['imported'] > 0)
        <div class="flex items-start gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
            <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-green-800 text-sm font-medium">{{ $tr('تم استيراد ' . $results['imported'] . ' مالك بنجاح.', $results['imported'] . ' owner(s) imported successfully.') }}</p>
        </div>
        @else
        <div class="flex items-start gap-3 bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3">
            <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-yellow-800 text-sm font-medium">{{ $tr('لم يتم استيراد أي مالك.', 'No owners were imported.') }}</p>
        </div>
        @endif
        @if(!empty($results['warnings']))
        <div class="bg-orange-50 border border-orange-200 rounded-xl px-4 py-3 space-y-1">
            @foreach($results['warnings'] as $w)
            <p class="text-sm text-orange-700">⚠ {{ $w }}</p>
            @endforeach
        </div>
        @endif
        @if(!empty($results['errors']))
        <div class="bg-red-50 border border-red-200 rounded-xl overflow-hidden">
            <div class="px-4 py-3 border-b border-red-200 flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-semibold text-red-700">{{ $tr('أخطاء التحقق', 'Validation Errors') }} ({{ count($results['errors']) }})</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-red-100 text-red-700">
                        <tr>
                            <th class="px-3 py-2 text-start font-semibold w-16">{{ $tr('الصف', 'Row') }}</th>
                            <th class="px-3 py-2 text-start font-semibold w-36">{{ $tr('الحقل', 'Field') }}</th>
                            <th class="px-3 py-2 text-start font-semibold w-40">{{ $tr('القيمة', 'Value') }}</th>
                            <th class="px-3 py-2 text-start font-semibold">{{ $tr('الخطأ', 'Error') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-red-100">
                        @foreach($results['errors'] as $err)
                        <tr class="hover:bg-red-50">
                            <td class="px-3 py-2 font-mono text-red-600 font-bold">{{ $err['row'] }}</td>
                            <td class="px-3 py-2 font-mono text-gray-700">{{ $err['field'] }}</td>
                            <td class="px-3 py-2 text-gray-500">{{ $err['value'] !== '' ? '"'.$err['value'].'"' : $tr('(فارغ)', '(empty)') }}</td>
                            <td class="px-3 py-2 text-red-700">{{ $err['error'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4">
        <div class="flex items-start gap-4">
            <div class="w-9 h-9 rounded-full bg-blue-900 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">1</div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-800 text-sm mb-1">{{ $tr('تحميل القالب', 'Download the Template') }}</h3>
                <p class="text-xs text-gray-500 mb-3">{{ $tr('حمّل القالب الذي يحتوي على أسماء الأعمدة الصحيحة.', 'Download the template with correct column headers.') }}</p>
                <a href="{{ route('employee.owner-leads.import.template') }}"
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    {{ $tr('تحميل القالب (.csv)', 'Download Template (.csv)') }}
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4">
        <div class="flex items-start gap-4">
            <div class="w-9 h-9 rounded-full bg-blue-900 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">2</div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-800 text-sm mb-3">{{ $tr('ملء البيانات', 'Fill in Your Data') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                    <div class="bg-red-50 border border-red-100 rounded-lg p-3">
                        <p class="font-bold text-red-700 mb-1.5">{{ $tr('الحقول المطلوبة *', 'Required Fields *') }}</p>
                        <ul class="space-y-0.5 text-red-600 font-mono"><li>name</li></ul>
                    </div>
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-3">
                        <p class="font-bold text-blue-700 mb-1.5">{{ $tr('الحقول الاختيارية', 'Optional Fields') }}</p>
                        <ul class="space-y-0.5 text-blue-600 font-mono text-xs">
                            <li>mobile · email · location</li>
                            <li>property_type · purpose</li>
                            <li>min_budget · max_budget · bedrooms</li>
                            <li>status · source · notes</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-3 text-xs text-gray-500 space-y-1">
                    <p><span class="font-semibold">property_type:</span> any, apartment_building, villa, farm, chalet, office, shop</p>
                    <p><span class="font-semibold">purpose:</span> rent, sale, both</p>
                    <p><span class="font-semibold">status:</span> new, contacted, interested, closed, done</p>
                    <p><span class="font-semibold">location:</span> {{ $tr('الاسم بالعربي مثل: بوشر، القرم…', 'Arabic name e.g. بوشر, القرم…') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start gap-4">
            <div class="w-9 h-9 rounded-full bg-blue-900 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">3</div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-800 text-sm mb-3">{{ $tr('رفع الملف', 'Upload the File') }}</h3>
                @if($errors->has('file'))
                <div class="mb-3 bg-red-50 border border-red-200 rounded-lg px-3 py-2 text-sm text-red-700">{{ $errors->first('file') }}</div>
                @endif
                <form method="POST" action="{{ route('employee.owner-leads.import') }}" enctype="multipart/form-data"
                      x-data="{ fileName: '' }">
                    @csrf
                    <label class="relative flex flex-col items-center justify-center border-2 border-dashed border-gray-300 hover:border-blue-400 hover:bg-gray-50 rounded-xl p-8 cursor-pointer transition">
                        <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-sm text-gray-600 font-medium" x-text="fileName || '{{ $tr('اسحب الملف هنا أو انقر للاختيار', 'Drag file here or click to choose') }}'"></p>
                        <p class="text-xs text-gray-400 mt-1">CSV — {{ $tr('بحد أقصى 10 ميجابايت', 'max 10 MB') }}</p>
                        <input type="file" name="file" accept=".csv,.txt" @change="fileName = $event.target.files[0]?.name || ''" class="absolute inset-0 opacity-0 cursor-pointer">
                    </label>
                    <div class="flex gap-3 mt-4">
                        <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium transition">{{ $tr('استيراد الآن', 'Import Now') }}</button>
                        <a href="{{ route('employee.owner-leads.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm transition">{{ $tr('إلغاء', 'Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
