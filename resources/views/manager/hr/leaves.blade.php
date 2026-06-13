<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn(string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
        $typeColors = ['annual'=>'bg-blue-100 text-blue-700','sick'=>'bg-orange-100 text-orange-700','unpaid'=>'bg-gray-100 text-gray-700','emergency'=>'bg-red-100 text-red-700'];
        $statusColors = ['pending'=>'bg-yellow-100 text-yellow-700','approved'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'];
    @endphp
    <x-slot name="title">{{ $tr('سجل الإجازات', 'Leave Requests') }}</x-slot>
    <div class="py-4">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $tr('سجل الإجازات', 'Leave Requests') }}</h2>
                @if($pending > 0)
                    <p class="text-sm text-yellow-600 mt-0.5">{{ $pending }} {{ $tr('طلب قيد المراجعة', 'pending requests') }}</p>
                @endif
            </div>
            <a href="{{ route('manager.employees.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← {{ $tr('العودة للموظفين', 'Back to employees') }}</a>
        </div>

        {{-- Filters --}}
        <form method="GET" class="bg-white rounded-xl shadow p-4 mb-5 flex flex-wrap gap-3">
            <select name="employee_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">{{ $tr('كل الموظفين', 'All employees') }}</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                @endforeach
            </select>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">{{ $tr('كل الحالات', 'All statuses') }}</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ $tr('قيد المراجعة', 'Pending') }}</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ $tr('موافق عليها', 'Approved') }}</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>{{ $tr('مرفوضة', 'Rejected') }}</option>
            </select>
            <select name="type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">{{ $tr('كل الأنواع', 'All types') }}</option>
                <option value="annual" {{ request('type') === 'annual' ? 'selected' : '' }}>{{ $tr('سنوية', 'Annual') }}</option>
                <option value="sick" {{ request('type') === 'sick' ? 'selected' : '' }}>{{ $tr('مرضية', 'Sick') }}</option>
                <option value="unpaid" {{ request('type') === 'unpaid' ? 'selected' : '' }}>{{ $tr('بدون راتب', 'Unpaid') }}</option>
                <option value="emergency" {{ request('type') === 'emergency' ? 'selected' : '' }}>{{ $tr('طارئة', 'Emergency') }}</option>
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">{{ $tr('بحث', 'Filter') }}</button>
            @if(request()->hasAny(['employee_id','status','type']))
                <a href="{{ route('manager.hr.leaves.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">{{ $tr('مسح', 'Clear') }}</a>
            @endif
        </form>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الموظف', 'Employee') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('النوع', 'Type') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('من', 'From') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('إلى', 'To') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الأيام', 'Days') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الحالة', 'Status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('السبب', 'Reason') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الإجراءات', 'Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($leaves as $leave)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">
                                <a href="{{ route('manager.employees.show', $leave->employee) }}" class="text-blue-600 hover:underline">{{ $leave->employee->name ?? '-' }}</a>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $typeColors[$leave->type] ?? 'bg-gray-100 text-gray-700' }}">{{ $leave->typeLabel() }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $leave->start_date->format('Y/m/d') }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $leave->end_date->format('Y/m/d') }}</td>
                            <td class="px-4 py-3">
                                <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $leave->days }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$leave->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $leave->statusLabel() }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $leave->reason ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @if($leave->status === 'pending')
                                        <form method="POST" action="{{ route('manager.employees.leaves.approve', [$leave->employee, $leave]) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-xs font-medium text-green-600 hover:text-green-800 border border-green-300 px-2 py-1 rounded-lg hover:bg-green-50 transition">{{ $tr('موافقة', 'Approve') }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('manager.employees.leaves.reject', [$leave->employee, $leave]) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800 border border-red-300 px-2 py-1 rounded-lg hover:bg-red-50 transition">{{ $tr('رفض', 'Reject') }}</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('manager.employees.leaves.destroy', [$leave->employee, $leave]) }}"
                                          onsubmit="return confirm('{{ $tr('حذف طلب الإجازة؟', 'Delete leave request?') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-gray-400 hover:text-red-600 transition">{{ $tr('حذف', 'Delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-4 py-10 text-center text-gray-400">{{ $tr('لا توجد طلبات إجازة', 'No leave requests found') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($leaves->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">{{ $leaves->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
