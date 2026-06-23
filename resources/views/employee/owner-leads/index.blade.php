<x-app-layout>
@php
    $isAr = app()->getLocale() === 'ar';
    $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    $locale = $isAr ? 'ar' : 'en';
@endphp
<x-slot name="title">{{ $tr('الملاك', 'Owners') }}</x-slot>

<div class="py-4">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('الملاك', 'Owners') }}</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $tr('سجل بيانات الملاك الباحثين عن عقارات', 'Track property owners and their requirements') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('employee.owner-leads.import.form') }}"
               class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                {{ $tr('استيراد CSV', 'Import CSV') }}
            </a>
            <a href="{{ route('employee.owner-leads.export', request()->only('search', 'status', 'purpose')) }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                {{ $tr('تصدير CSV', 'Export CSV') }}
            </a>
            <a href="{{ route('employee.owner-leads.create') }}"
               class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $tr('إضافة مالك', 'Add Owner') }}
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('employee.owner-leads.index') }}"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('بحث', 'Search') }}</label>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="{{ $tr('اسم، هاتف، بريد، موقع…', 'Name, phone, email, location…') }}"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none">
        </div>
        <div class="w-36">
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('الحالة', 'Status') }}</label>
            <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none">
                <option value="">{{ $tr('الكل', 'All') }}</option>
                @foreach(\App\Models\OwnerLead::$statuses as $val => $labels)
                <option value="{{ $val }}" @selected($status === $val)>{{ $labels[$locale] }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-36">
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('الغرض', 'Purpose') }}</label>
            <select name="purpose" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none">
                <option value="">{{ $tr('الكل', 'All') }}</option>
                @foreach(\App\Models\OwnerLead::$purposes as $val => $labels)
                <option value="{{ $val }}" @selected($purpose === $val)>{{ $labels[$locale] }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                {{ $tr('بحث', 'Search') }}
            </button>
            @if($search || $status || $purpose)
            <a href="{{ route('employee.owner-leads.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition">
                {{ $tr('مسح', 'Clear') }}
            </a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if($ownerLeads->isEmpty())
        <div class="py-16 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <p class="text-gray-400 text-sm">{{ $tr('لا يوجد ملاك بعد', 'No owners yet') }}</p>
            <a href="{{ route('employee.owner-leads.create') }}" class="inline-block mt-3 text-blue-600 hover:underline text-sm">{{ $tr('إضافة أول مالك', 'Add first owner') }}</a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 text-xs">
                    <tr>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('المالك', 'Owner') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('الموقع', 'Location') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('المتطلبات', 'Requirements') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('الميزانية', 'Budget') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('الحالة', 'Status') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ $tr('الإجراءات', 'Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($ownerLeads as $ownerLead)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-800">{{ $ownerLead->name }}</div>
                            @if($ownerLead->mobile)
                            <div class="text-xs text-gray-500 mt-0.5">{{ $ownerLead->mobile }}</div>
                            @endif
                            @if($ownerLead->email)
                            <div class="text-xs text-gray-400">{{ $ownerLead->email }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $ownerLead->locationLabel($locale) }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                <span class="bg-blue-50 text-blue-700 text-xs px-2 py-0.5 rounded-full font-medium">{{ $ownerLead->typeLabel($locale) }}</span>
                                <span class="bg-purple-50 text-purple-700 text-xs px-2 py-0.5 rounded-full font-medium">{{ $ownerLead->purposeLabel($locale) }}</span>
                                @if($ownerLead->bedrooms)
                                <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $ownerLead->bedrooms }} {{ $tr('غرف', 'bed') }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-700 text-xs">
                            @if($ownerLead->min_budget || $ownerLead->max_budget)
                            {{ $ownerLead->min_budget ? number_format($ownerLead->min_budget) : '' }}{{ ($ownerLead->min_budget && $ownerLead->max_budget) ? ' — ' : '' }}{{ $ownerLead->max_budget ? number_format($ownerLead->max_budget) : '' }}
                            @else —
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $ownerLead->statusColor() }}">{{ $ownerLead->statusLabel($locale) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @php
                                    $waMsg = $tr('مرحباً ' . $ownerLead->name . '، لدينا عروض عقارية قد تناسب متطلباتك.', 'Hello ' . $ownerLead->name . ', we have property offers that may match your requirements.');
                                    $waUrl = $ownerLead->whatsappUrl($waMsg);
                                @endphp
                                @if($waUrl)
                                <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold px-2.5 py-1.5 rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.71.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.999-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                    {{ $tr('واتساب', 'WA') }}
                                </a>
                                @endif
                                <a href="{{ route('employee.owner-leads.show', $ownerLead) }}"
                                   class="inline-flex items-center bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-2.5 py-1.5 rounded-lg transition">
                                    {{ $tr('عرض', 'View') }}
                                </a>
                                <a href="{{ route('employee.owner-leads.edit', $ownerLead) }}"
                                   class="inline-flex items-center bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-medium px-2.5 py-1.5 rounded-lg transition">
                                    {{ $tr('تعديل', 'Edit') }}
                                </a>
                                @if($ownerLead->created_by === auth()->id())
                                <form method="POST" action="{{ route('employee.owner-leads.destroy', $ownerLead) }}"
                                      onsubmit="return confirm('{{ $tr('هل أنت متأكد من حذف هذا المالك؟', 'Are you sure you want to delete this owner?') }}')"
                                      class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium px-2.5 py-1.5 rounded-lg transition">
                                        {{ $tr('حذف', 'Delete') }}
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @if($ownerLead->notes)
                    <tr class="bg-amber-50/50">
                        <td colspan="6" class="px-4 py-2 text-xs text-gray-600 italic">
                            <span class="font-semibold text-gray-500">{{ $tr('ملاحظات:', 'Notes:') }}</span> {{ $ownerLead->notes }}
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($ownerLeads->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $ownerLeads->links() }}</div>
        @endif
        @endif
    </div>
</div>
</x-app-layout>
