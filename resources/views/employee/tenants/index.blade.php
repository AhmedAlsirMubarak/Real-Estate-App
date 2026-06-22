<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn(string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
    @endphp
    <x-slot name="title">{{ $tr('مستأجريّ', 'My Tenants') }}</x-slot>

    <div class="py-4">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('مستأجريّ', 'My Tenants') }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('employee.tenants.export') }}"
                   class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    {{ $tr('تصدير Excel', 'Export Excel') }}
                </a>
                <a href="{{ route('employee.tenants.create') }}"
                   class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    {{ $tr('إضافة مستأجر', 'Add Tenant') }}
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('المستأجر', 'Tenant') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الهاتف', 'Phone') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('العقار / الوحدة', 'Property / Unit') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('انتهاء العقد', 'Contract End') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الإيجار الشهري', 'Monthly Rent') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('إجراء', 'Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($tenants as $tenant)
                        @php
                            $contract = $tenant->activeContract;
                            $daysLeft = $contract?->end_date ? (int) now()->diffInDays($contract->end_date, false) : null;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $tenant->user->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $tenant->user->phone ?? $tenant->phone ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">
                                @if($contract)
                                    <span class="font-medium">{{ $contract->unit->property->name ?? '-' }}</span>
                                    @if($contract->unit?->unit_number)
                                        <span class="text-gray-400 text-xs"> — {{ $tr('وحدة', 'Unit') }} {{ $contract->unit->unit_number }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-400 text-xs">{{ $tr('لا يوجد عقد', 'No contract') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($contract?->end_date)
                                    <span class="text-gray-700">{{ $contract->end_date->format('Y/m/d') }}</span>
                                    @if($daysLeft !== null && $daysLeft < 0)
                                        <span class="ms-1 px-1.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">{{ $tr('منتهي', 'Expired') }}</span>
                                    @elseif($daysLeft !== null && $daysLeft <= 7)
                                        <span class="ms-1 px-1.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">{{ $daysLeft }}d</span>
                                    @elseif($daysLeft !== null && $daysLeft <= 30)
                                        <span class="ms-1 px-1.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700">{{ $daysLeft }}d</span>
                                    @endif
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-semibold text-blue-700">
                                @if($contract)
                                    {{ number_format($contract->monthly_rent, 2) }} {{ $currency }}
                                @else —
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('employee.tenants.show', $tenant) }}"
                                       class="text-blue-600 hover:text-blue-800 text-xs font-medium">{{ $tr('عرض', 'View') }}</a>
                                    @if($tenant->created_by === auth()->id())
                                    <form method="POST" action="{{ route('employee.tenants.destroy', $tenant) }}"
                                          onsubmit="return confirm('{{ $tr('هل أنت متأكد من حذف هذا المستأجر؟', 'Are you sure you want to delete this tenant?') }}')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium px-2.5 py-1.5 rounded-lg transition">
                                            {{ $tr('حذف', 'Delete') }}
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400">{{ $tr('لا يوجد مستأجرون مرتبطون بك', 'No tenants linked to you yet') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($tenants->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">{{ $tenants->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
