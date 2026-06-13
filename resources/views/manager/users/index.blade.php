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
            'manager'   => $tr('مدير', 'Manager'),
            'employee'  => $tr('موظف', 'Employee'),
            'accountant'=> $tr('محاسب', 'Accountant'),
            'tenant'    => $tr('مستأجر', 'Tenant'),
            'owner'     => $tr('مالك', 'Owner'),
            'buyer'     => $tr('مشتري', 'Buyer'),
        ];
    @endphp
    <x-slot name="title">{{ $tr('إدارة المستخدمين', 'User Management') }}</x-slot>

    {{-- Hidden CSRF token used by bulk-delete JS --}}
    <meta name="csrf-token" content="{{ csrf_token() }}" id="csrf-meta">

    <div class="py-4">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('إدارة المستخدمين', 'User Management') }}</h2>
            <a href="{{ route('manager.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $tr('إنشاء مستخدم', 'Create User') }}
            </a>
        </div>

        {{-- Search / filter --}}
        <form method="GET" action="{{ route('manager.users.index') }}" class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="{{ $tr('ابحث بالاسم أو البريد أو الجوال...', 'Search by name, email, or phone...') }}"
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
                <option value="active"   {{ ($status ?? '') === 'active'   ? 'selected' : '' }}>{{ $tr('نشط', 'Active') }}</option>
                <option value="blocked"  {{ ($status ?? '') === 'blocked'  ? 'selected' : '' }}>{{ $tr('محظور', 'Blocked') }}</option>
            </select>
            <div class="md:col-span-4 flex flex-wrap gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">{{ $tr('بحث', 'Search') }}</button>
                <a href="{{ route('manager.users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">{{ $tr('مسح', 'Clear') }}</a>
            </div>
        </form>

        {{-- Bulk toolbar — hidden until a checkbox is ticked --}}
        <div id="bulk-toolbar" class="hidden mb-3 items-center gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
            <span id="bulk-count" class="text-sm font-semibold text-red-700"></span>
            <button type="button" onclick="submitBulkDelete()"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                {{ $tr('حذف المحدد', 'Delete selected') }}
            </button>
            <button type="button" onclick="clearSelection()"
                    class="text-sm text-gray-500 hover:text-gray-700 transition">{{ $tr('إلغاء التحديد', 'Clear') }}</button>
        </div>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 w-10">
                                <input type="checkbox" id="select-all" onchange="toggleAll(this)"
                                       class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            </th>
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
                            @php $userRole = $managedUser->roles->first()?->name; $isSelf = auth()->id() === $managedUser->id; @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    @if(!$isSelf)
                                        <input type="checkbox" value="{{ $managedUser->id }}"
                                               onchange="updateBulkToolbar()"
                                               class="row-checkbox rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    @endif
                                </td>
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
                                        <a href="{{ route('manager.users.edit', $managedUser) }}"
                                           class="text-yellow-600 hover:text-yellow-800 text-xs font-medium">{{ $tr('تعديل', 'Edit') }}</a>

                                        @if($isSelf)
                                            <span class="text-xs text-gray-400">{{ $tr('حسابك الحالي', 'Current account') }}</span>
                                        @else
                                            <form method="POST" action="{{ route('manager.users.toggle-block', $managedUser) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                        class="text-xs font-medium {{ $managedUser->is_blocked ? 'text-green-600 hover:text-green-800' : 'text-orange-600 hover:text-orange-800' }}">
                                                    {{ $managedUser->is_blocked ? $tr('إلغاء الحظر', 'Unblock') : $tr('حظر', 'Block') }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('manager.users.destroy', $managedUser) }}"
                                                  onsubmit="return confirm('{{ $tr('هل أنت متأكد؟', 'Are you sure?') }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800">{{ $tr('حذف', 'Delete') }}</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-400">{{ $tr('لا يوجد مستخدمون', 'No users found') }}</td>
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

    <script>
    const BULK_URL  = '{{ route('manager.users.bulk-destroy') }}';
    const CSRF      = '{{ csrf_token() }}';
    const CONFIRM_MSG = '{{ $tr('هل أنت متأكد من حذف المستخدمين المحددين؟', 'Delete selected users? This cannot be undone.') }}';

    function submitBulkDelete() {
        const checked = [...document.querySelectorAll('.row-checkbox:checked')];
        if (!checked.length) return;
        if (!confirm(CONFIRM_MSG)) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = BULK_URL;

        const token = document.createElement('input');
        token.type  = 'hidden';
        token.name  = '_token';
        token.value = CSRF;
        form.appendChild(token);

        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'ids[]';
            input.value = cb.value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }

    function toggleAll(master) {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = master.checked);
        updateBulkToolbar();
    }

    function updateBulkToolbar() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        const toolbar = document.getElementById('bulk-toolbar');
        const countEl = document.getElementById('bulk-count');
        const master  = document.getElementById('select-all');
        const all     = document.querySelectorAll('.row-checkbox');
        if (checked.length > 0) {
            toolbar.classList.remove('hidden');
            toolbar.classList.add('flex');
            countEl.textContent = document.documentElement.lang === 'ar'
                ? `تم تحديد ${checked.length} مستخدم`
                : `${checked.length} user(s) selected`;
        } else {
            toolbar.classList.add('hidden');
            toolbar.classList.remove('flex');
        }
        master.indeterminate = checked.length > 0 && checked.length < all.length;
        master.checked = all.length > 0 && checked.length === all.length;
    }

    function clearSelection() {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        updateBulkToolbar();
    }
    </script>
</x-app-layout>
