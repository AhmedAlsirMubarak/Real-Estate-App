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
    <x-slot name="title">{{ $tr('الموظفون', 'Employees') }}</x-slot>
    <div class="py-4">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('قائمة الموظفين', 'Employees List') }}</h2>
            <a href="{{ route('manager.employees.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $tr('إضافة موظف', 'Add Employee') }}
            </a>
        </div>

        <form method="GET" action="{{ route('manager.employees.index') }}" class="mb-4 flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ $tr('ابحث بالاسم أو البريد الإلكتروني...', 'Search by name or email...') }}"
                   class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">{{ $tr('بحث', 'Search') }}</button>
            @if($search ?? null)
            <a href="{{ route('manager.employees.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">{{ $tr('مسح', 'Clear') }}</a>
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
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الدور', 'Role') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('العقارات المُسنَدة', 'Managed') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('إحالات', 'Referrals') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('نسبة العمولة الافتراضية', 'Default Comm. Rate') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('عمولة الإحالة', 'Referral Comm.') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الإجراءات', 'Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($employees as $employee)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $displayName($employee->name, $tr('موظف', 'Employee')) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $employee->email }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $employee->phone ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $employee->hasRole('accountant') ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $employee->hasRole('accountant') ? $tr('محاسب', 'Accountant') : $tr('موظف', 'Employee') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs font-medium">{{ $employee->managed_properties_count }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @php $hasReferrals = ($employee->referred_properties_count + $employee->referred_tenants_count) > 0; @endphp
                                @if($hasReferrals)
                                    <div class="flex flex-wrap gap-1">
                                        @if($employee->referred_properties_count > 0)
                                            <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-medium">{{ $employee->referred_properties_count }} {{ $tr('عقار', 'props') }}</span>
                                        @endif
                                        @if($employee->referred_tenants_count > 0)
                                            <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full text-xs font-medium">{{ $employee->referred_tenants_count }} {{ $tr('مستأجر', 'tenants') }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($employee->commission_rate)
                                    <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs font-semibold">{{ $employee->commission_rate }}%</span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($employee->referral_commission_earned > 0)
                                    <div class="text-sm font-semibold text-green-700">
                                        {{ number_format($employee->referral_commission_earned, 2) }}
                                        <span class="text-xs font-normal text-gray-500">{{ $tr('ر.ع', 'OMR') }}</span>
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $tr('إجمالي الإحالات', 'all referrals') }}</div>
                                @elseif($hasReferrals)
                                    <span class="text-gray-400 text-xs">0.00 {{ $tr('ر.ع', 'OMR') }}</span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('manager.employees.show', $employee) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">{{ $tr('عرض', 'View') }}</a>
                                    <a href="{{ route('manager.employees.edit', $employee) }}" class="text-yellow-600 hover:text-yellow-800 text-xs font-medium">{{ $tr('تعديل', 'Edit') }}</a>
                                    <form method="POST" action="{{ route('manager.employees.destroy', $employee) }}" onsubmit="return confirm('{{ $tr('هل أنت متأكد؟', 'Are you sure?') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">{{ $tr('حذف', 'Delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="px-4 py-8 text-center text-gray-400">{{ $tr('لا يوجد موظفون مسجلون', 'No employees found') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($employees->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $employees->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
