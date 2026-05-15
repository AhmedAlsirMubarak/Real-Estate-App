<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $displayName = function (?string $name, string $fallback = 'User') use ($isAr) {
            if ($name === null || $name === '') return $fallback;
            if ($isAr || ! preg_match('/\p{Arabic}/u', $name)) return $name;
            return $fallback;
        };
        $roleLabels = [
            'manager' => $tr('مدير', 'Manager'),
            'employee' => $tr('موظف', 'Employee'),
            'accountant' => $tr('محاسب', 'Accountant'),
            'tenant' => $tr('مستأجر', 'Tenant'),
            'owner' => $tr('مالك', 'Owner'),
            'buyer' => $tr('مشتري', 'Buyer'),
        ];
    @endphp
    <x-slot name="title">{{ $tr('إدارة المستخدمين', 'User Management') }}</x-slot>

    <div class="py-4">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('إدارة المستخدمين', 'User Management') }}</h2>
            <a href="{{ route('manager.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $tr('إنشاء مستخدم', 'Create User') }}
            </a>
        </div>

        <form method="GET" action="{{ route('manager.users.index') }}" class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ $tr('ابحث بالاسم أو البريد أو الجوال...', 'Search by name, email, or phone...') }}"
                   class="md:col-span-2 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">

            <select name="role" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">{{ $tr('كل الأدوار', 'All roles') }}</option>
                @foreach($roles as $availableRole)
                    <option value="{{ $availableRole }}" {{ ($role ?? '') === $availableRole ? 'selected' : '' }}>
                        {{ $roleLabels[$availableRole] ?? $availableRole }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">{{ $tr('كل الحالات', 'All statuses') }}</option>
                <option value="active" {{ ($status ?? '') === 'active' ? 'selected' : '' }}>{{ $tr('نشط', 'Active') }}</option>
                <option value="blocked" {{ ($status ?? '') === 'blocked' ? 'selected' : '' }}>{{ $tr('محظور', 'Blocked') }}</option>
            </select>

            <div class="md:col-span-4 flex flex-wrap gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">{{ $tr('بحث', 'Search') }}</button>
                <a href="{{ route('manager.users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">{{ $tr('مسح', 'Clear') }}</a>
            </div>
        </form>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الاسم', 'Name') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('البريد الإلكتروني', 'Email') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الجوال', 'Phone') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الدور', 'Role') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الحالة', 'Status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الإجراءات', 'Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($users as $managedUser)
                            @php $userRole = $managedUser->roles->first()?->name; @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">{{ $displayName($managedUser->name) }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $managedUser->email }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $managedUser->phone ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                        {{ $roleLabels[$userRole] ?? ($userRole ?: $tr('بدون دور', 'No role')) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($managedUser->is_blocked)
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">{{ $tr('محظور', 'Blocked') }}</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">{{ $tr('نشط', 'Active') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('manager.users.edit', $managedUser) }}" class="text-yellow-600 hover:text-yellow-800 text-xs font-medium">{{ $tr('تعديل', 'Edit') }}</a>

                                        @if(auth()->id() === $managedUser->id)
                                            <span class="text-xs text-gray-400">{{ $tr('حسابك الحالي', 'Current account') }}</span>
                                        @else
                                            <form method="POST" action="{{ route('manager.users.toggle-block', $managedUser) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="text-xs font-medium {{ $managedUser->is_blocked ? 'text-green-600 hover:text-green-800' : 'text-red-600 hover:text-red-800' }}">
                                                    {{ $managedUser->is_blocked ? $tr('إلغاء الحظر', 'Unblock') : $tr('حظر المستخدم', 'Block user') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-400">{{ $tr('لا يوجد مستخدمون', 'No users found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">{{ $users->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
