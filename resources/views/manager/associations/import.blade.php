<x-app-layout>
@php
    $isAr = app()->getLocale() === 'ar';
    $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    $results = session('import_results');
@endphp
<x-slot name="title">{{ $tr('استيراد جمعيات من Excel', 'Import Associations from Excel') }}</x-slot>

<div class="max-w-3xl mx-auto py-4">
    {{-- Back link --}}
    <a href="{{ route('manager.associations.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-5 inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        {{ $tr('رجوع إلى الجمعيات', 'Back to Associations') }}
    </a>

    {{-- Page title --}}
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">
            {{ $tr('استيراد جمعيات من Excel', 'Import Associations from Excel') }}
        </h2>
        <p class="text-sm text-gray-500 mt-1">
            {{ $tr('ارفع ملف Excel يحتوي على بيانات جمعيات الملاك لاستيرادها دفعة واحدة', 'Upload an Excel file containing owners association data to import them in bulk') }}
        </p>
    </div>

    {{-- ── Results panel ──────────────────────────────────────────────────── --}}
    @if($results)
    <div class="mb-6 space-y-3">
        @if($results['imported'] > 0)
        <div class="flex items-start gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
            <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-green-800 text-sm font-medium">
                {{ $tr(
                    'تم استيراد ' . $results['imported'] . ' جمعية بنجاح.',
                    $results['imported'] . ' ' . ($results['imported'] === 1 ? 'association' : 'associations') . ' imported successfully.'
                ) }}
            </p>
        </div>
        @else
        <div class="flex items-start gap-3 bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3">
            <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-yellow-800 text-sm font-medium">
                {{ $tr('لم يتم استيراد أي جمعية. راجع الأخطاء أدناه.', 'No associations were imported. Review the errors below.') }}
            </p>
        </div>
        @endif

        @if(!empty($results['warnings']))
        <div class="bg-orange-50 border border-orange-200 rounded-xl px-4 py-3 space-y-1">
            <p class="text-sm font-semibold text-orange-700 mb-1">Diagnostic Info</p>
            @foreach($results['warnings'] as $w)
            <p class="text-sm text-orange-700">⚠ {{ $w }}</p>
            @endforeach
        </div>
        @endif

        @if(!empty($results['errors']))
        <div class="bg-red-50 border border-red-200 rounded-xl overflow-hidden">
            <div class="px-4 py-3 border-b border-red-200 flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-semibold text-red-700">
                    {{ $tr('أخطاء التحقق', 'Validation Errors') }}
                    ({{ count($results['errors']) }})
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-red-100 text-red-700">
                        <tr>
                            <th class="px-3 py-2 text-start font-semibold w-16">{{ $tr('السطر', 'Row') }}</th>
                            <th class="px-3 py-2 text-start font-semibold w-36">{{ $tr('الحقل', 'Field') }}</th>
                            <th class="px-3 py-2 text-start font-semibold w-40">{{ $tr('القيمة المُدخلة', 'Value Given') }}</th>
                            <th class="px-3 py-2 text-start font-semibold">{{ $tr('الخطأ', 'Error') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-red-100">
                        @foreach($results['errors'] as $err)
                        <tr class="hover:bg-red-50">
                            <td class="px-3 py-2 font-mono text-red-600 font-bold">{{ $err['row'] }}</td>
                            <td class="px-3 py-2 font-mono text-gray-700">{{ $err['field'] }}</td>
                            <td class="px-3 py-2 text-gray-500 truncate max-w-[160px]">
                                {{ $err['value'] !== '' ? '"' . $err['value'] . '"' : $tr('(فارغ)', '(empty)') }}
                            </td>
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

    {{-- ── Step 1: Download template ──────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4">
        <div class="flex items-start gap-4">
            <div class="w-9 h-9 rounded-full bg-blue-900 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">1</div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-800 text-sm mb-1">
                    {{ $tr('تحميل القالب', 'Download the Template') }}
                </h3>
                <p class="text-xs text-gray-500 mb-3">
                    {{ $tr(
                        'حمّل ملف Excel القالب الذي يحتوي على أسماء الأعمدة الصحيحة. بيانات الجمعيات تبدأ من الصف الثاني.',
                        'Download the Excel template with the correct column headers. Row 1 is the header — enter your data from row 2 onwards.'
                    ) }}
                </p>
                <a href="{{ route('manager.associations.import.template') }}"
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    </svg>
                    {{ $tr('تحميل القالب (.xlsx)', 'Download Template (.xlsx)') }}
                </a>
            </div>
        </div>
    </div>

    {{-- ── Step 2: Fill template ──────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4">
        <div class="flex items-start gap-4">
            <div class="w-9 h-9 rounded-full bg-blue-900 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">2</div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-800 text-sm mb-3">
                    {{ $tr('ملء البيانات', 'Fill in Your Data') }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                    <div class="bg-red-50 border border-red-100 rounded-lg p-3">
                        <p class="text-xs font-bold text-red-700 mb-1.5">
                            {{ $tr('الحقول المطلوبة *', 'Required Fields *') }}
                        </p>
                        <ul class="space-y-0.5 text-xs text-red-600">
                            <li class="font-mono">property_code</li>
                            <li class="font-mono">name_ar</li>
                            <li class="font-mono">monthly_fee_per_unit</li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-lg p-3">
                        <p class="text-xs font-bold text-gray-600 mb-1.5">
                            {{ $tr('الحقول الاختيارية', 'Optional Fields') }}
                        </p>
                        <ul class="space-y-0.5 text-xs text-gray-500 columns-2">
                            <li class="font-mono">name_en</li>
                            <li class="font-mono">established_date</li>
                            <li class="font-mono">status</li>
                            <li class="font-mono">electricity_account_number</li>
                            <li class="font-mono">water_account_number</li>
                            <li class="font-mono">description_ar</li>
                            <li class="font-mono">description_en</li>
                        </ul>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                    <div class="bg-indigo-50 rounded-lg p-2.5 text-xs">
                        <p class="font-bold text-indigo-700 mb-1">property_code</p>
                        <p class="text-indigo-600 leading-relaxed">{{ $tr('يجب أن يطابق رمز عقار موجود لا يملك جمعية بالفعل', 'Must match an existing property\'s code that doesn\'t already have an association') }}</p>
                    </div>
                    <div class="bg-amber-50 rounded-lg p-2.5 text-xs">
                        <p class="font-bold text-amber-700 mb-1">status <span class="font-normal">(optional)</span></p>
                        <p class="font-mono text-amber-600 leading-relaxed">active<br>inactive</p>
                        <p class="text-amber-500 mt-1">{{ $tr('يُعيَّن "active" إذا تُرك فارغاً', 'Defaults to "active" if blank') }}</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-2.5 text-xs">
                        <p class="font-bold text-purple-700 mb-1">established_date <span class="font-normal">(optional)</span></p>
                        <p class="font-mono text-purple-600 leading-relaxed">YYYY-MM-DD</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Step 3: Upload ─────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start gap-4">
            <div class="w-9 h-9 rounded-full bg-blue-900 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">3</div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-800 text-sm mb-3">
                    {{ $tr('رفع الملف', 'Upload the File') }}
                </h3>

                @if($errors->has('file'))
                <div class="mb-3 bg-red-50 border border-red-200 rounded-lg px-3 py-2 text-sm text-red-700">
                    {{ $errors->first('file') }}
                </div>
                @endif

                <form method="POST" action="{{ route('manager.associations.import') }}"
                      enctype="multipart/form-data"
                      x-data="{
                          fileName: '',
                          dragging: false,
                          onFile(e) {
                              const f = e.target.files[0] ?? e.dataTransfer?.files[0];
                              if (f) this.fileName = f.name;
                          }
                      }">
                    @csrf

                    <label
                        class="relative flex flex-col items-center justify-center border-2 border-dashed rounded-xl p-8 cursor-pointer transition"
                        :class="dragging ? 'border-blue-400 bg-blue-50' : 'border-gray-300 hover:border-blue-400 hover:bg-gray-50'"
                        @dragover.prevent="dragging = true"
                        @dragleave.prevent="dragging = false"
                        @drop.prevent="dragging = false; onFile($event); $refs.fileInput.files = $event.dataTransfer.files">

                        <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>

                        <p class="text-sm text-gray-600 font-medium" x-text="fileName || '{{ $tr('اسحب الملف هنا أو انقر للاختيار', 'Drag file here or click to choose') }}'"></p>
                        <p class="text-xs text-gray-400 mt-1">{{ $tr('Excel (.xlsx, .xls) — بحد أقصى 10 ميجابايت', 'Excel (.xlsx, .xls) — max 10 MB') }}</p>

                        <input type="file" name="file" accept=".xlsx,.xls"
                               x-ref="fileInput"
                               @change="onFile($event)"
                               class="absolute inset-0 opacity-0 cursor-pointer">
                    </label>

                    <div class="flex gap-3 mt-4">
                        <button type="submit"
                                class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                            {{ $tr('استيراد الآن', 'Import Now') }}
                        </button>
                        <a href="{{ route('manager.associations.index') }}"
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm transition">
                            {{ $tr('إلغاء', 'Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
