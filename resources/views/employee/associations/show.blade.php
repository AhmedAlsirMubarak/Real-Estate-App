<x-app-layout>
@php
    $isAr = app()->getLocale() === 'ar';
    $tr   = fn(string $ar, string $en) => $isAr ? $ar : $en;
@endphp
<x-slot name="title">{{ $association->name }}</x-slot>

<div class="py-4">
    <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
        <div>
            <a href="{{ route('employee.associations.index') }}"
               class="text-sm text-gray-500 hover:text-gray-700 inline-flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                {{ $tr('الجمعيات', 'Associations') }}
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ $association->name }}</h2>
            @if($association->property)
            <p class="text-sm text-gray-500 mt-0.5">{{ $association->property->name }}</p>
            @endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('employee.associations.edit', $association) }}"
               class="inline-flex items-center gap-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                {{ $tr('تعديل', 'Edit') }}
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Main info --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">{{ $tr('معلومات الجمعية', 'Association Info') }}</h3>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('الاسم (عربي)', 'Name (AR)') }}</dt>
                        <dd class="font-medium text-gray-800">{{ $association->name_ar }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('الاسم (إنجليزي)', 'Name (EN)') }}</dt>
                        <dd class="font-medium text-gray-800">{{ $association->name_en }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('تاريخ التأسيس', 'Established') }}</dt>
                        <dd class="text-gray-700">{{ $association->established_date?->format('Y-m-d') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('الرسوم الشهرية / وحدة', 'Monthly Fee / Unit') }}</dt>
                        <dd class="text-gray-700 font-semibold">{{ number_format($association->monthly_fee_per_unit, 2) }} {{ $tr('ريال', 'OMR') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('رقم حساب الكهرباء', 'Electricity Account') }}</dt>
                        <dd class="text-gray-700">{{ $association->electricity_account_number ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('رقم حساب الماء', 'Water Account') }}</dt>
                        <dd class="text-gray-700">{{ $association->water_account_number ?: '—' }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('الحالة', 'Status') }}</dt>
                        <dd>
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $association->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $association->status === 'active' ? $tr('نشط', 'Active') : $tr('غير نشط', 'Inactive') }}
                            </span>
                        </dd>
                    </div>
                    @if($association->description_ar || $association->description_en)
                    <div class="col-span-2">
                        <dt class="text-xs text-gray-500 mb-0.5">{{ $tr('الوصف', 'Description') }}</dt>
                        <dd class="text-gray-700">{{ $isAr ? $association->description_ar : $association->description_en }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Owners --}}
            @if($association->property?->owners?->count())
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ $tr('الملاك', 'Owners') }}</h3>
                <ul class="divide-y divide-gray-50">
                    @foreach($association->property->owners as $owner)
                    <li class="py-2 flex items-center justify-between text-sm">
                        <span class="font-medium text-gray-800">{{ $owner->user?->name ?? '—' }}</span>
                        <span class="text-gray-500 text-xs">{{ $owner->ownership_percentage ?? '' }}{{ $owner->ownership_percentage ? '%' : '' }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Units --}}
            @if($association->property?->units?->count())
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ $tr('الوحدات', 'Units') }} ({{ $association->property->units->count() }})</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-50 text-gray-500">
                            <tr>
                                <th class="px-3 py-2 text-start">{{ $tr('الكود', 'Code') }}</th>
                                <th class="px-3 py-2 text-start">{{ $tr('النوع', 'Type') }}</th>
                                <th class="px-3 py-2 text-start">{{ $tr('الغرف', 'Beds') }}</th>
                                <th class="px-3 py-2 text-start">{{ $tr('الحالة', 'Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($association->property->units->take(15) as $unit)
                            <tr>
                                <td class="px-3 py-2 text-gray-700 font-medium">{{ $unit->code ?? $unit->id }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $unit->type }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $unit->bedrooms ?? '—' }}</td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-0.5 rounded-full text-xs
                                        {{ $unit->status === 'available' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $unit->status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Meetings --}}
            @if($association->meetings?->count())
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ $tr('الاجتماعات', 'Meetings') }}</h3>
                <ul class="divide-y divide-gray-50 text-sm">
                    @foreach($association->meetings as $meeting)
                    <li class="py-2.5">
                        <p class="font-medium text-gray-800">{{ $meeting->title ?? $meeting->subject ?? '—' }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $meeting->scheduled_at?->format('Y-m-d H:i') }}</p>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            {{-- Property card --}}
            @if($association->property)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ $tr('العقار', 'Property') }}</h3>
                <p class="text-sm font-medium text-gray-800">{{ $association->property->name }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $association->property->code }}</p>
                @if($association->property->address)
                <p class="text-xs text-gray-500 mt-1">{{ $association->property->address }}</p>
                @endif
            </div>
            @endif

            {{-- Documents --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ $tr('المستندات', 'Documents') }}</h3>
                @php
                    $docs = [
                        [$tr('ملكية', 'Ownership'),                       $association->no_objection_certificate_path],
                        [$tr('المخطط', 'Sketch'),                          $association->sketch_path],
                        [$tr('شهادة جمعية الملاك', 'Association Cert.'), $association->association_certificate_path],
                        [$tr('الهوية الشخصية', 'Personal ID'),             $association->personal_id_path],
                        [$tr('هوية مدير الجمعية', "Manager's ID"),         $association->manager_id_path],
                    ];
                @endphp
                <ul class="space-y-2">
                    @foreach($docs as [$label, $path])
                    <li class="flex items-center justify-between text-xs">
                        <span class="text-gray-600">{{ $label }}</span>
                        @if($path)
                        <a href="{{ asset('storage/' . $path) }}" target="_blank"
                           class="text-blue-600 hover:underline font-medium">{{ $tr('عرض', 'View') }}</a>
                        @else
                        <span class="text-gray-300">—</span>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Dues summary --}}
            @if($association->dues?->count())
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ $tr('الاشتراكات الأخيرة', 'Recent Dues') }}</h3>
                <ul class="divide-y divide-gray-50 text-xs">
                    @foreach($association->dues->take(5) as $due)
                    <li class="py-2 flex justify-between">
                        <span class="text-gray-600">{{ $due->owner?->user?->name ?? '—' }}</span>
                        <span class="font-medium text-gray-800">{{ number_format($due->amount, 2) }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
</x-app-layout>
