<x-app-layout>
    <x-slot name="title">{{ $association->name }}</x-slot>

    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $association->name }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $association->property->name }} — {{ $association->property->code }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('manager.associations.edit', $association) }}" class="bg-gray-100 text-gray-700 px-3 py-2 rounded-lg text-sm hover:bg-gray-200">{{ __('Edit') }}</a>
            <a href="{{ route('manager.meetings.create', ['association' => $association->id]) }}" class="bg-amber-100 text-amber-800 px-3 py-2 rounded-lg text-sm hover:bg-amber-200">{{ __('Schedule Meeting') }}</a>
        </div>
    </div>

    {{-- Info cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ __('Established Date') }}</p>
            <p class="text-base font-semibold text-gray-800 mt-1">{{ $association->established_date?->format('Y/m/d') ?? '—' }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ __('Monthly Fee per Unit') }}</p>
            <p class="text-base font-semibold text-gray-800 mt-1">{{ number_format($association->monthly_fee_per_unit, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ __('Owners') }}</p>
            <p class="text-base font-semibold text-gray-800 mt-1">{{ $association->property->owners->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">{{ __('Status') }}</p>
            <p class="text-base font-semibold text-gray-800 mt-1">{{ $association->status === 'active' ? __('Active') : __('Inactive') }}</p>
        </div>
    </div>

    {{-- Documents --}}
    @php
        $docsList = [
            ['label' => __('No Objection Certificate'),       'path' => $association->no_objection_certificate_path],
            ['label' => __('Sketch'),                         'path' => $association->sketch_path],
            ['label' => __('Owners Association Certificate'), 'path' => $association->association_certificate_path],
            ['label' => __('Personal ID'),                    'path' => $association->personal_id_path],
            ['label' => __("Association Manager's ID"),       'path' => $association->manager_id_path],
        ];
        $hasAnyDoc = collect($docsList)->contains(fn($d) => !empty($d['path']));
    @endphp
    @if($hasAnyDoc)
    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-6">
        <h3 class="text-sm font-bold text-gray-800 mb-3">{{ __('Documents') }}</h3>
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

    {{-- No Objection Certificate --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-6">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-800">{{ __('No Objection Certificate for Renting') }}</h3>
                <p class="text-xs text-gray-400">{{ __('Enter lessor details to generate the certificate') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('manager.associations.no-objection-pdf', $association) }}" target="_blank">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Lessor Full Name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="lessor_name" required placeholder="{{ __('Full Name') }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    @error('lessor_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Phone Number') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="lessor_phone" required placeholder="+968 XXXX XXXX"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    @error('lessor_phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('ID / Residence Number') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="lessor_id" required placeholder="{{ __('National ID or Iqama') }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    @error('lessor_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-4 flex items-center gap-3">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ __('Generate & Download PDF') }}
                </button>
                <p class="text-xs text-gray-400">{{ __('Opens in a new tab — includes owner details, attached documents, and signature block') }}</p>
            </div>
        </form>

        {{-- NOC History --}}
        @if($association->noObjectionCertificates->count())
        <div class="mt-5 pt-4 border-t border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">{{ __('Generated Certificates') }}</p>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-gray-500 bg-gray-50">
                        <tr>
                            <th class="text-right px-3 py-2">{{ __('Ref. No.') }}</th>
                            <th class="text-right px-3 py-2">{{ __('Lessor Name') }}</th>
                            <th class="text-right px-3 py-2">{{ __('Phone') }}</th>
                            <th class="text-right px-3 py-2">{{ __('ID Number') }}</th>
                            <th class="text-right px-3 py-2">{{ __('Generated By') }}</th>
                            <th class="text-right px-3 py-2">{{ __('Date') }}</th>
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
                                @if($noc->file_path && file_exists(storage_path('app/' . $noc->file_path)))
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('manager.associations.noc.download', $noc) }}?preview=1" target="_blank"
                                       class="inline-flex items-center gap-1 text-xs text-gray-600 hover:text-gray-900 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        {{ __('Preview') }}
                                    </a>
                                    <a href="{{ route('manager.associations.noc.download', $noc) }}"
                                       class="inline-flex items-center gap-1 text-xs text-blue-700 hover:text-blue-900 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        {{ __('Download') }}
                                    </a>
                                </div>
                                @else
                                <span class="text-xs text-gray-400">—</span>
                                @endif
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
                <h3 class="text-sm font-bold text-gray-800">{{ __('No Objection Certificate for Sale') }}</h3>
                <p class="text-xs text-gray-400">{{ __('Enter buyer details to generate the certificate') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('manager.associations.no-objection-sale-pdf', $association) }}" target="_blank">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __("Buyer's Full Name") }} <span class="text-red-500">*</span></label>
                    <input type="text" name="buyer_name" required placeholder="{{ __('Full Name') }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200">
                    @error('buyer_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Phone Number') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="buyer_phone" required placeholder="+968 XXXX XXXX"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200">
                    @error('buyer_phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('ID / Residence Number') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="buyer_id" required placeholder="{{ __('National ID or Iqama') }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200">
                    @error('buyer_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-4 flex items-center gap-3">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-teal-700 hover:bg-teal-600 text-white px-5 py-2 rounded-lg text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ __('Generate & Download PDF') }}
                </button>
                <p class="text-xs text-gray-400">{{ __('Opens in a new tab — includes owner details, attached documents, and signature block') }}</p>
            </div>
        </form>

        {{-- Sale NOC History --}}
        @if($association->noObjectionSaleCertificates->count())
        <div class="mt-5 pt-4 border-t border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">{{ __('Generated Certificates') }}</p>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-gray-500 bg-gray-50">
                        <tr>
                            <th class="text-right px-3 py-2">{{ __('Ref. No.') }}</th>
                            <th class="text-right px-3 py-2">{{ __("Buyer's Name") }}</th>
                            <th class="text-right px-3 py-2">{{ __('Phone') }}</th>
                            <th class="text-right px-3 py-2">{{ __('ID Number') }}</th>
                            <th class="text-right px-3 py-2">{{ __('Generated By') }}</th>
                            <th class="text-right px-3 py-2">{{ __('Date') }}</th>
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
                                @if($noc->file_path && file_exists(storage_path('app/' . $noc->file_path)))
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('manager.associations.noc-sale.download', $noc) }}?preview=1" target="_blank"
                                       class="inline-flex items-center gap-1 text-xs text-gray-600 hover:text-gray-900 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        {{ __('Preview') }}
                                    </a>
                                    <a href="{{ route('manager.associations.noc-sale.download', $noc) }}"
                                       class="inline-flex items-center gap-1 text-xs text-teal-700 hover:text-teal-900 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        {{ __('Download') }}
                                    </a>
                                </div>
                                @else
                                <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    {{-- Generate dues --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-6">
        <h3 class="text-sm font-bold text-gray-800 mb-3">{{ __('Generate Monthly Dues') }}</h3>
        <form method="POST" action="{{ route('manager.associations.dues.generate', $association) }}" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs text-gray-600 mb-1">{{ __('Month') }}</label>
                <select name="period_month" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" @selected($m === now()->month)>{{ $m }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">{{ __('Year') }}</label>
                <input type="number" name="period_year" value="{{ now()->year }}" min="2020" max="2100" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-24">
            </div>
            <button class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm">{{ __('Generate Monthly Dues') }}</button>
        </form>
    </div>

    {{-- Owners --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-6">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-sm font-bold text-gray-800">{{ __('Owners') }}</h3>
            <a href="{{ route('manager.properties.owners.index', $association->property) }}" class="text-xs text-blue-700 hover:text-blue-900">{{ __('Edit') }}</a>
        </div>
        <table class="w-full text-sm">
            <thead class="text-xs text-gray-500 uppercase">
                <tr><th class="text-right py-2">{{ __('Name') }}</th><th class="text-right py-2">{{ __('Ownership %') }}</th><th class="text-right py-2">{{ __('Phone') }}</th><th class="text-right py-2">{{ __('Primary Owner') }}</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($association->property->owners as $owner)
                <tr>
                    <td class="py-2">{{ $owner->user?->name ?? '—' }}</td>
                    <td class="py-2">{{ $owner->pivot->ownership_percentage }}%</td>
                    <td class="py-2 text-xs text-gray-600">{{ $owner->phone ?? '—' }}</td>
                    <td class="py-2">{{ $owner->pivot->is_primary ? '✓' : '' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-4 text-center text-gray-400 text-xs">{{ __('No data available') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Recent dues --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-6">
        <h3 class="text-sm font-bold text-gray-800 mb-3">{{ __('Dues') }}</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-gray-500 uppercase">
                    <tr><th class="text-right py-2">{{ __('Owner') }}</th><th class="text-right py-2">{{ __('Period') }}</th><th class="text-right py-2">{{ __('Amount') }}</th><th class="text-right py-2">{{ __('Status') }}</th><th class="text-right py-2">{{ __('Due Date') }}</th><th class="text-right py-2">{{ __('Actions') }}</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($association->dues as $due)
                    <tr>
                        <td class="py-2">{{ $due->owner?->user?->name ?? '—' }}</td>
                        <td class="py-2 text-xs">{{ $due->periodLabel() }}</td>
                        <td class="py-2 font-semibold">{{ number_format($due->amount, 2) }}</td>
                        <td class="py-2"><span class="text-xs px-2 py-0.5 rounded-full
                            @if($due->status==='paid') bg-green-50 text-green-700
                            @elseif($due->status==='overdue') bg-red-50 text-red-700
                            @elseif($due->status==='waived') bg-gray-100 text-gray-600
                            @else bg-yellow-50 text-yellow-700 @endif">{{ $due->statusLabel() }}</span></td>
                        <td class="py-2 text-xs text-gray-600">{{ $due->due_date->format('Y/m/d') }}</td>
                        <td class="py-2 text-xs flex gap-1 flex-wrap">
                            @if($due->status !== 'paid')
                            <form method="POST" action="{{ route('manager.dues.paid', $due) }}">@csrf @method('PATCH')<button class="text-green-700 hover:text-green-900">{{ __('Mark as Paid') }}</button></form>
                            @endif
                            <x-whatsapp-button size="sm" :phone="$due->owner?->phone" :message="__('Dues').' '.$due->periodLabel().' — '.number_format($due->amount,2)" />
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-4 text-center text-gray-400 text-xs">{{ __('No data available') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Meetings --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-6">
        <h3 class="text-sm font-bold text-gray-800 mb-3">{{ __('Meetings') }}</h3>
        <table class="w-full text-sm">
            <thead class="text-xs text-gray-500 uppercase">
                <tr><th class="text-right py-2">{{ __('Title') }}</th><th class="text-right py-2">{{ __('Scheduled At') }}</th><th class="text-right py-2">{{ __('Status') }}</th><th class="text-right py-2">{{ __('Actions') }}</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($association->meetings as $m)
                <tr>
                    <td class="py-2">{{ $m->title }}</td>
                    <td class="py-2 text-xs text-gray-600">{{ $m->scheduled_at->format('Y/m/d H:i') }}</td>
                    <td class="py-2 text-xs">{{ $m->statusLabel() }}</td>
                    <td class="py-2 text-xs"><a href="{{ route('manager.meetings.show', $m) }}" class="text-blue-700">{{ __('View Details') }}</a></td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-4 text-center text-gray-400 text-xs">{{ __('No data available') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
