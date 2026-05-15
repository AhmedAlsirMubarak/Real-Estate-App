<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $displayName = function (?string $name, string $fallback = 'User') use ($isAr) {
            if ($name === null || $name === '') return $fallback;
            if ($isAr || ! preg_match('/\p{Arabic}/u', $name)) return $name;
            return $fallback;
        };
    @endphp
    <x-slot name="title">{{ $tr('المستأجرون', 'Tenants') }}</x-slot>
    <div class="py-4">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('قائمة المستأجرين', 'Tenants List') }}</h2>
            <a href="{{ route('manager.tenants.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $tr('إضافة مستأجر', 'Add Tenant') }}
            </a>
        </div>

        <form method="GET" action="{{ route('manager.tenants.index') }}" class="mb-4 flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ $tr('ابحث بالاسم أو البريد أو الهاتف...', 'Search by name, email, or phone...') }}"
                   class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">{{ $tr('بحث', 'Search') }}</button>
            @if($search ?? null)
            <a href="{{ route('manager.tenants.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">{{ $tr('مسح', 'Clear') }}</a>
            @endif
        </form>
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الاسم', 'Name') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('البريد الإلكتروني', 'Email') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الهاتف', 'Phone') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('العقار', 'Property') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الوحدة', 'Unit') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('حالة العقد', 'Contract Status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الإجراءات', 'Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($tenants as $tenant)
                        @php
                            $contract = $tenant->activeContract ?? $tenant->contracts->first();
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $displayName($tenant->user->name ?? null, $tr('مستأجر', 'Tenant')) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $tenant->user->email ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $tenant->user->phone ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $contract->unit->property->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $contract->unit->unit_number ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($contract)
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $contract->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $contract->status === 'active' ? $tr('نشط', 'Active') : $tr('منتهي', 'Expired') }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">{{ $tr('لا يوجد عقد', 'No contract') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('manager.tenants.show', $tenant) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">{{ $tr('عرض', 'View') }}</a>
                                    <a href="{{ route('manager.tenants.edit', $tenant) }}" class="text-yellow-600 hover:text-yellow-800 text-xs font-medium">{{ $tr('تعديل', 'Edit') }}</a>
                                    <x-whatsapp-button size="sm"
                                        :phone="$tenant->user?->phone ?? $tenant->phone"
                                        :message="$tr('السلام عليكم — تذكير من شركة ثروة', 'Hello — reminder from Tharwa Real Estate')" />
                                    <form method="POST" action="{{ route('manager.tenants.destroy', $tenant) }}" onsubmit="return confirm('{{ $tr('هل أنت متأكد من الحذف؟', 'Are you sure you want to delete?') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">{{ $tr('حذف', 'Delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">{{ $tr('لا يوجد مستأجرون مسجلون', 'No tenants found') }}</td></tr>
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
