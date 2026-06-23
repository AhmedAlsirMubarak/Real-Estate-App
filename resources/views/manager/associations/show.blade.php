<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr   = fn(string $ar, string $en) => $isAr ? $ar : $en;

        // Owners can be linked two ways:
        // 1. Many-to-many via property_owners pivot (owners())
        // 2. Direct single FK via owner_id (owner())
        // The generate-dues code handles both; the UI must too.
        $pivotOwners = $association->property->owners;
        $directOwner = $association->property->owner ?? null;
        $hasAnyOwner = $pivotOwners->isNotEmpty() || $directOwner;
        $ownersCount = $pivotOwners->isNotEmpty() ? $pivotOwners->count() : ($directOwner ? 1 : 0);
    @endphp
    <x-slot name="title">{{ $association->name }}</x-slot>

    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $association->name }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $association->property?->name ?? '—' }} — {{ $association->property?->code ?? '—' }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('manager.associations.report.create', ['association_id' => $association->id]) }}"
               class="inline-flex items-center gap-1.5 bg-blue-700 hover:bg-blue-800 text-white px-3 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                </svg>
                {{ $tr('التقرير الشامل', 'Comprehensive Report') }}
            </a>
            <a href="{{ route('manager.associations.edit', $association) }}" class="bg-gray-100 text-gray-700 px-3 py-2 rounded-lg text-sm hover:bg-gray-200">{{ $tr('تعديل', 'Edit') }}</a>
            <a href="{{ route('manager.meetings.create', ['association' => $association->id]) }}" class="bg-amber-100 text-amber-800 px-3 py-2 rounded-lg text-sm hover:bg-amber-200">{{ $tr('جدولة اجتماع', 'Schedule Meeting') }}</a>
        </div>
    </div>

    {{-- Info cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ $tr('تاريخ التأسيس', 'Established Date') }}</p>
            <p class="text-base font-semibold text-gray-800 mt-1">{{ $association->established_date?->format('Y/m/d') ?? '—' }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ $tr('الرسوم الشهرية لكل وحدة', 'Monthly Fee per Unit') }}</p>
            <p class="text-base font-semibold text-gray-800 mt-1">{{ number_format($association->monthly_fee_per_unit, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ $tr('الحالة', 'Status') }}</p>
            <p class="text-base font-semibold text-gray-800 mt-1">{{ $association->status === 'active' ? $tr('نشط', 'Active') : $tr('غير نشط', 'Inactive') }}</p>
        </div>
    </div>

    {{-- Documents --}}
    @php
        $docsList = [
            ['label' => $tr('ملكية', 'Ownership'),                                   'path' => $association->no_objection_certificate_path],
            ['label' => $tr('المخطط', 'Sketch'),                                     'path' => $association->sketch_path],
            ['label' => $tr('شهادة جمعية الملاك', 'Owners Association Certificate'), 'path' => $association->association_certificate_path],
            ['label' => $tr('الهوية الشخصية', 'Personal ID'),                       'path' => $association->personal_id_path],
            ['label' => $tr('هوية مدير الجمعية', "Association Manager's ID"),        'path' => $association->manager_id_path],
        ];
        $hasAnyDoc = collect($docsList)->contains(fn($d) => !empty($d['path']));
    @endphp
    @if($hasAnyDoc)
    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-6">
        <h3 class="text-sm font-bold text-gray-800 mb-3">{{ $tr('المستندات', 'Documents') }}</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($docsList as $doc)
            @if($doc['path'])
            @php $ext = strtolower(pathinfo($doc['path'], PATHINFO_EXTENSION)); @endphp
            <a href="{{ asset('storage/' . $doc['path']) }}" target="_blank"
               class="flex items-center gap-3 p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition group">
                @if(in_array($ext, ['jpg','jpeg','png']))
                    <svg class="w-8 h-8 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                @else
                    <svg class="w-8 h-8 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                @endif
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-700 group-hover:text-blue-700">{{ $doc['label'] }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ basename($doc['path']) }}</p>
                </div>
            </a>
            @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- No Objection Certificate for Renting --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-6">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-800">{{ $tr('شهادة عدم الممانعة للتأجير', 'No Objection Certificate for Renting') }}</h3>
                <p class="text-xs text-gray-400">{{ $tr('أدخل بيانات المؤجر لإنشاء الشهادة', 'Enter lessor details to generate the certificate') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('manager.associations.no-objection-pdf', $association) }}" target="_blank">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                <div class="md:col-span-2">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">الطرف الأول — المالك (المؤجر)</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">اسم المالك (الطرف الأول) <span class="text-red-500">*</span></label>
                    <input type="text" name="lessor_name" required placeholder="الاسم الكامل"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">الرقم المدني للمالك <span class="text-red-500">*</span></label>
                    <input type="text" name="lessor_id" required placeholder="الرقم المدني / الإقامة"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">هاتف المالك <span class="text-red-500">*</span></label>
                    <input type="text" name="lessor_phone" required placeholder="+968 XXXX XXXX"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">رقم الوحدة العقارية</label>
                    <input type="text" name="unit_number" placeholder="مثال: 101"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">الطرف الثاني — المستأجر</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">اسم المستأجر (الطرف الثاني) <span class="text-red-500">*</span></label>
                    <input type="text" name="lessee_name" required placeholder="الاسم الكامل"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">الرقم المدني للمستأجر <span class="text-red-500">*</span></label>
                    <input type="text" name="lessee_id" required placeholder="الرقم المدني / الإقامة"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
            </div>

            <div class="mt-4 flex items-center gap-3">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ $tr('إنشاء وتحميل PDF', 'Generate & Download PDF') }}
                </button>
                <p class="text-xs text-gray-400">{{ $tr('يفتح في تبويب جديد', 'Opens in a new tab — includes owner details, attached documents, and signature block') }}</p>
            </div>
        </form>

        {{-- NOC History --}}
        @if($association->noObjectionCertificates->count())
        <div class="mt-5 pt-4 border-t border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">{{ $tr('الشهادات المُنشأة', 'Generated Certificates') }}</p>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-gray-500 bg-gray-50">
                        <tr>
                            <th class="text-right px-3 py-2">{{ $tr('رقم المرجع', 'Ref. No.') }}</th>
                            <th class="text-right px-3 py-2">{{ $tr('اسم المؤجر', 'Lessor Name') }}</th>
                            <th class="text-right px-3 py-2">{{ $tr('الهاتف', 'Phone') }}</th>
                            <th class="text-right px-3 py-2">{{ $tr('رقم الهوية', 'ID Number') }}</th>
                            <th class="text-right px-3 py-2">{{ $tr('أُنشئت بواسطة', 'Generated By') }}</th>
                            <th class="text-right px-3 py-2">{{ $tr('التاريخ', 'Date') }}</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($association->noObjectionCertificates as $noc)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-mono text-xs text-blue-700 font-semibold">{{ $noc->ref_number }}</td>
                            <td class="px-3 py-2 font-medium">{{ $noc->lessor_name }}</td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ $noc->lessor_phone }}</td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ $noc->lessor_id }}</td>
                            <td class="px-3 py-2 text-xs text-gray-500">{{ $noc->generatedBy?->name ?? '—' }}</td>
                            <td class="px-3 py-2 text-xs text-gray-500 whitespace-nowrap">{{ $noc->created_at->format('Y/m/d H:i') }}</td>
                            <td class="px-3 py-2 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    @if($noc->file_path && file_exists(storage_path('app/' . $noc->file_path)))
                                    <a href="{{ route('manager.associations.noc.download', $noc) }}?preview=1" target="_blank"
                                       class="inline-flex items-center gap-1 text-xs text-gray-600 hover:text-gray-900 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        {{ $tr('معاينة', 'Preview') }}
                                    </a>
                                    <a href="{{ route('manager.associations.noc.download', $noc) }}"
                                       class="inline-flex items-center gap-1 text-xs text-blue-700 hover:text-blue-900 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        {{ $tr('تحميل', 'Download') }}
                                    </a>
                                    @endif
                                    <form method="POST" action="{{ route('manager.associations.noc.delete', $noc) }}"
                                          onsubmit="return confirm('{{ $tr('حذف هذه الشهادة؟', 'Delete this certificate?') }}')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-xs text-red-600 hover:text-red-800 font-medium">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            {{ $tr('حذف', 'Delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    {{-- No Objection to Sale --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-6">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-lg bg-teal-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-800">{{ $tr('شهادة عدم الممانعة على البيع', 'No Objection Certificate for Sale') }}</h3>
                <p class="text-xs text-gray-400">{{ $tr('أدخل بيانات المشتري لإنشاء الشهادة', 'Enter buyer details to generate the certificate') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('manager.associations.no-objection-sale-pdf', $association) }}" target="_blank">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                <div class="md:col-span-2">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">الطرف الأول — المالك (البائع)</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">اسم المالك (الطرف الأول) <span class="text-red-500">*</span></label>
                    <input type="text" name="seller_name" required placeholder="الاسم الكامل"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">الرقم المدني للمالك <span class="text-red-500">*</span></label>
                    <input type="text" name="seller_id" required placeholder="الرقم المدني / الإقامة"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">رقم الوحدة العقارية</label>
                    <input type="text" name="unit_number" placeholder="مثال: 101"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">الطرف الثاني — المشتري</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('الاسم الكامل للمشتري', "Buyer's Full Name") }} <span class="text-red-500">*</span></label>
                    <input type="text" name="buyer_name" required placeholder="{{ $tr('الاسم الكامل', 'Full Name') }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200">
                    @error('buyer_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('رقم الهوية / الإقامة', 'ID / Residence Number') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="buyer_id" required placeholder="{{ $tr('الرقم المدني أو الإقامة', 'National ID or Iqama') }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200">
                    @error('buyer_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('رقم الهاتف', 'Phone Number') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="buyer_phone" required placeholder="+968 XXXX XXXX"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200">
                    @error('buyer_phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-4 flex items-center gap-3">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-teal-700 hover:bg-teal-600 text-white px-5 py-2 rounded-lg text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ $tr('إنشاء وتحميل PDF', 'Generate & Download PDF') }}
                </button>
                <p class="text-xs text-gray-400">{{ $tr('يفتح في تبويب جديد', 'Opens in a new tab — includes owner details, attached documents, and signature block') }}</p>
            </div>
        </form>

        {{-- Sale NOC History --}}
        @if($association->noObjectionSaleCertificates->count())
        <div class="mt-5 pt-4 border-t border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">{{ $tr('الشهادات المُنشأة', 'Generated Certificates') }}</p>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-gray-500 bg-gray-50">
                        <tr>
                            <th class="text-right px-3 py-2">{{ $tr('رقم المرجع', 'Ref. No.') }}</th>
                            <th class="text-right px-3 py-2">{{ $tr('اسم المشتري', "Buyer's Name") }}</th>
                            <th class="text-right px-3 py-2">{{ $tr('الهاتف', 'Phone') }}</th>
                            <th class="text-right px-3 py-2">{{ $tr('رقم الهوية', 'ID Number') }}</th>
                            <th class="text-right px-3 py-2">{{ $tr('أُنشئت بواسطة', 'Generated By') }}</th>
                            <th class="text-right px-3 py-2">{{ $tr('التاريخ', 'Date') }}</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($association->noObjectionSaleCertificates as $noc)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-mono text-xs text-teal-700 font-semibold">{{ $noc->ref_number }}</td>
                            <td class="px-3 py-2 font-medium">{{ $noc->buyer_name }}</td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ $noc->buyer_phone }}</td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ $noc->buyer_id }}</td>
                            <td class="px-3 py-2 text-xs text-gray-500">{{ $noc->generatedBy?->name ?? '—' }}</td>
                            <td class="px-3 py-2 text-xs text-gray-500 whitespace-nowrap">{{ $noc->created_at->format('Y/m/d H:i') }}</td>
                            <td class="px-3 py-2 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    @if($noc->file_path && file_exists(storage_path('app/' . $noc->file_path)))
                                    <a href="{{ route('manager.associations.noc-sale.download', $noc) }}?preview=1" target="_blank"
                                       class="inline-flex items-center gap-1 text-xs text-gray-600 hover:text-gray-900 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        {{ $tr('معاينة', 'Preview') }}
                                    </a>
                                    <a href="{{ route('manager.associations.noc-sale.download', $noc) }}"
                                       class="inline-flex items-center gap-1 text-xs text-teal-700 hover:text-teal-900 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        {{ $tr('تحميل', 'Download') }}
                                    </a>
                                    @endif
                                    <form method="POST" action="{{ route('manager.associations.noc-sale.delete', $noc) }}"
                                          onsubmit="return confirm('{{ $tr('حذف هذه الشهادة؟', 'Delete this certificate?') }}')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-xs text-red-600 hover:text-red-800 font-medium">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            {{ $tr('حذف', 'Delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    {{-- Generate Invoice PDF --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-6">
        <h3 class="text-sm font-bold text-gray-800 mb-3">{{ $tr('إنشاء فاتورة PDF', 'Generate Invoice PDF') }}</h3>
        <form method="POST" action="{{ route('manager.associations.invoice-pdf', $association) }}" target="_blank" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs text-gray-600 mb-1">{{ $tr('الشهر', 'Month') }}</label>
                <select name="period_month" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    @php $monthNames = $isAr
                        ? ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر']
                        : ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    @endphp
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" @selected($m === now()->month)>{{ $monthNames[$m - 1] }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">{{ $tr('السنة', 'Year') }}</label>
                <input type="number" name="period_year" value="{{ now()->year }}" min="2020" max="2100" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-24">
            </div>
            <button class="inline-flex items-center gap-2 bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                {{ $tr('إنشاء فاتورة PDF', 'Generate Invoice PDF') }}
            </button>
        </form>
    </div>

    {{-- Quick WhatsApp Invoice --}}
    @php
        $waMonthNamesAr = ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        $waMonthNamesEn = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $waAppName      = str_replace('_', ' ', ucwords(config('app.name'), '_'));
        $waPropName     = addslashes($association->property?->name ?? '');
        $waAssocName    = addslashes($association->name);
        $waDefaultAmount = (float) $association->monthly_fee_per_unit;
    @endphp
    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-6"
         x-data="{
            ownerName: '',
            phone: '',
            month: {{ now()->month }},
            year: {{ now()->year }},
            amount: {{ $waDefaultAmount }},
            dueDate: '',
            appName: '{{ $waAppName }}',
            propName: '{{ $waPropName }}',
            assocName: '{{ $waAssocName }}',
            monthNamesAr: {{ json_encode($waMonthNamesAr) }},
            monthNamesEn: {{ json_encode($waMonthNamesEn) }},
            get periodLabelAr() { return (this.monthNamesAr[this.month] || '') + ' ' + this.year; },
            get periodLabelEn() { return (this.monthNamesEn[this.month] || '') + ' ' + this.year; },
            get formattedAmount() {
                const n = parseFloat(this.amount || 0).toFixed(2);
                return n + ' ر.ع / ' + n + ' OMR';
            },
            get formattedDueDate() {
                if (!this.dueDate) return '—';
                const d = new Date(this.dueDate);
                const day = d.getDate().toString().padStart(2,'0');
                return day + ' ' + (this.monthNamesAr[d.getMonth()+1] || '') + ' ' + d.getFullYear()
                     + ' / ' + day + ' ' + (this.monthNamesEn[d.getMonth()+1] || '') + ' ' + d.getFullYear();
            },
            buildMessage() {
                const amt = parseFloat(this.amount || 0).toFixed(2);
                return 'السيد/ة ' + this.ownerName + '،\n\nتحية طيبة،\n\n📋 *إشعار فاتورة — رسوم جمعية الملاك*\n──────────────────\n• العقار: ' + this.propName + '\n• الجمعية: ' + this.assocName + '\n• الفترة: ' + this.periodLabelAr + '\n• المبلغ المستحق: *' + amt + ' ر.ع*\n• تاريخ الاستحقاق: ' + this.formattedDueDate + '\n──────────────────\nيُرجى سداد المبلغ قبل تاريخ الاستحقاق لتجنب أي رسوم إضافية.\n\n━━━━━━━━━━━━━━━━━━\n\nDear ' + this.ownerName + ',\n\n📋 *Invoice Notice — Owners\' Association Monthly Fee*\n──────────────────\n• Property: ' + this.propName + '\n• Association: ' + this.assocName + '\n• Period: ' + this.periodLabelEn + '\n• Amount Due: *' + amt + ' OMR*\n• Due Date: ' + this.formattedDueDate + '\n──────────────────\nPlease arrange payment before the due date.\n\n' + this.appName;
            },
            sendWhatsApp() {
                let n = this.phone.replace(/[^0-9]/g, '');
                if (n.startsWith('00')) n = n.slice(2);
                else if (n.startsWith('0')) n = '968' + n.slice(1);
                else if (!n.startsWith('968') && n.length <= 8) n = '968' + n;
                window.open('https://api.whatsapp.com/send?phone=' + n + '&text=' + encodeURIComponent(this.buildMessage()), '_blank');
            }
         }">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-green-700" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.71.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.999-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-800">{{ $tr('إرسال فاتورة الرسوم الشهرية عبر واتساب', 'Send Monthly Fee Invoice via WhatsApp') }}</h3>
                <p class="text-xs text-gray-400">{{ $tr('أرسل إشعار رسوم لأي مالك مباشرة — لا يلزم وجود سجل رسوم مسبق', 'Send a fee notice to any owner directly — no due record required') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('اسم المالك', "Owner's Name") }} <span class="text-red-500">*</span></label>
                <input type="text" x-model="ownerName" placeholder="{{ $tr('الاسم الكامل', 'Full name') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-200">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('رقم الهاتف (واتساب)', 'Phone (WhatsApp)') }} <span class="text-red-500">*</span></label>
                <input type="text" x-model="phone" placeholder="+968 XXXX XXXX"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-200">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('الشهر', 'Month') }}</label>
                <select x-model.number="month" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $m === now()->month ? 'selected' : '' }}>{{ $waMonthNamesEn[$m] }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('السنة', 'Year') }}</label>
                <input type="number" x-model.number="year" min="2020" max="2100"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-200">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('المبلغ', 'Amount') }} (OMR)</label>
                <input type="number" x-model.number="amount" step="0.01" min="0"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-200">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('تاريخ الاستحقاق', 'Due Date') }}</label>
                <input type="date" x-model="dueDate"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-200">
            </div>
        </div>

        <button @click="sendWhatsApp()" :disabled="!phone.trim() || !ownerName.trim()"
                class="inline-flex items-center gap-2 bg-[#25D366] hover:bg-[#1ebe5d] disabled:opacity-40 disabled:cursor-not-allowed text-white px-5 py-2 rounded-lg text-sm font-semibold transition shadow-sm">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.71.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.999-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
            </svg>
            {{ $tr('إرسال عبر واتساب', 'Send via WhatsApp') }}
        </button>
    </div>

    {{-- Recent dues --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-6">
        <h3 class="text-sm font-bold text-gray-800 mb-3">{{ $tr('الرسوم', 'Dues') }}</h3>
        @php
            $currency = $isAr ? 'ر.ع' : 'OMR';
            $appName  = str_replace('_', ' ', ucwords(config('app.name'), '_'));
        @endphp
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="text-right py-2">{{ $tr('المالك', 'Owner') }}</th>
                        <th class="text-right py-2">{{ $tr('الفترة', 'Period') }}</th>
                        <th class="text-right py-2">{{ $tr('المبلغ', 'Amount') }}</th>
                        <th class="text-right py-2">{{ $tr('الحالة', 'Status') }}</th>
                        <th class="text-right py-2">{{ $tr('تاريخ الاستحقاق', 'Due Date') }}</th>
                        <th class="text-right py-2">{{ $tr('إجراءات', 'Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($association->dues as $due)
                    @php
                        $dueOwnerName  = $due->owner?->user?->name ?? ($isAr ? 'المالك' : 'Owner');
                        $dueOwnerPhone = $due->owner?->phone ?? $due->owner?->user?->phone ?? null;
                        $invoiceNo     = '#INV-' . str_pad($due->id, 6, '0', STR_PAD_LEFT);
                        $amountAr      = number_format($due->amount, 2) . ' ر.ع';
                        $amountEn      = number_format($due->amount, 2) . ' OMR';
                        $dueDateFmtAr  = $due->due_date->translatedFormat('d F Y');
                        $dueDateFmtEn  = $due->due_date->format('d M Y');
                        $periodLabelAr = $due->due_date->translatedFormat('F Y');
                        $periodLabelEn = $due->due_date->format('M Y');
                        $propName      = $association->property?->name ?? '';

                        $waMsg = "السيد/ة {$dueOwnerName}،\n\nتحية طيبة،\n\n📋 *إشعار فاتورة — رسوم جمعية الملاك*\n──────────────────\n• رقم الفاتورة: {$invoiceNo}\n• العقار: {$propName}\n• الفترة: {$periodLabelAr}\n• المبلغ المستحق: *{$amountAr}*\n• تاريخ الاستحقاق: {$dueDateFmtAr}\n──────────────────\nيُرجى سداد المبلغ قبل تاريخ الاستحقاق لتجنب أي رسوم إضافية.\n\n━━━━━━━━━━━━━━━━━━\n\nDear {$dueOwnerName},\n\n📋 *Invoice Notice — Owners' Association Dues*\n──────────────────\n• Invoice No.: {$invoiceNo}\n• Property: {$propName}\n• Period: {$periodLabelEn}\n• Amount Due: *{$amountEn}*\n• Due Date: {$dueDateFmtEn}\n──────────────────\nPlease arrange payment before the due date.\n\n{$appName}";
                    @endphp
                    <tr>
                        <td class="py-2">{{ $dueOwnerName }}</td>
                        <td class="py-2 text-xs">{{ $due->periodLabel() }}</td>
                        <td class="py-2 font-semibold">{{ number_format($due->amount, 2) }} {{ $currency }}</td>
                        <td class="py-2"><span class="text-xs px-2 py-0.5 rounded-full
                            @if($due->status==='paid') bg-green-50 text-green-700
                            @elseif($due->status==='overdue') bg-red-50 text-red-700
                            @elseif($due->status==='waived') bg-gray-100 text-gray-600
                            @else bg-yellow-50 text-yellow-700 @endif">{{ $due->statusLabel() }}</span></td>
                        <td class="py-2 text-xs text-gray-600">{{ $due->due_date->format('Y/m/d') }}</td>
                        <td class="py-2">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <a href="{{ route('manager.dues.invoice', $due) }}" target="_blank"
                                   class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                    </svg>
                                    {{ $tr('فاتورة PDF', 'PDF Invoice') }}
                                </a>
                                @if($due->status !== 'paid')
                                <form method="POST" action="{{ route('manager.dues.paid', $due) }}">
                                    @csrf @method('PATCH')
                                    <button class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-green-50 text-green-700 hover:bg-green-100 transition">
                                        {{ $tr('مدفوع', 'Mark Paid') }}
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('manager.dues.pending', $due) }}">
                                    @csrf @method('PATCH')
                                    <button class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-yellow-50 text-yellow-700 hover:bg-yellow-100 transition">
                                        {{ $tr('معلق', 'Mark Pending') }}
                                    </button>
                                </form>
                                @endif
                                <x-whatsapp-button size="sm" :phone="$dueOwnerPhone" :message="$waMsg" />
                                <form method="POST" action="{{ route('manager.dues.destroy', $due) }}"
                                      onsubmit="return confirm('{{ $tr('حذف هذا السجل؟', 'Delete this record?') }}')">
                                    @csrf @method('DELETE')
                                    <button class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-red-50 text-red-600 hover:bg-red-100 transition">
                                        {{ $tr('حذف', 'Delete') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-4 text-center text-gray-400 text-xs">{{ $tr('لا توجد بيانات', 'No data available') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Meetings --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-6">
        <h3 class="text-sm font-bold text-gray-800 mb-3">{{ $tr('الاجتماعات', 'Meetings') }}</h3>
        <table class="w-full text-sm">
            <thead class="text-xs text-gray-500 uppercase">
                <tr>
                    <th class="text-right py-2">{{ $tr('العنوان', 'Title') }}</th>
                    <th class="text-right py-2">{{ $tr('موعد الاجتماع', 'Scheduled At') }}</th>
                    <th class="text-right py-2">{{ $tr('الحالة', 'Status') }}</th>
                    <th class="text-right py-2">{{ $tr('إجراءات', 'Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($association->meetings as $m)
                <tr>
                    <td class="py-2">{{ $m->title }}</td>
                    <td class="py-2 text-xs text-gray-600">{{ $m->scheduled_at->format('Y/m/d H:i') }}</td>
                    <td class="py-2 text-xs">{{ $m->statusLabel() }}</td>
                    <td class="py-2 text-xs"><a href="{{ route('manager.meetings.show', $m) }}" class="text-blue-700">{{ $tr('عرض التفاصيل', 'View Details') }}</a></td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-4 text-center text-gray-400 text-xs">{{ $tr('لا توجد بيانات', 'No data available') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
