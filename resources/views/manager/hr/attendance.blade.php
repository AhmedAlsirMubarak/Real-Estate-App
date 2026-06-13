<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn(string $ar, string $en) => $isAr ? $ar : $en;
        $statusColors = [
            'present'  => 'bg-green-100 text-green-700',
            'absent'   => 'bg-red-100 text-red-700',
            'late'     => 'bg-orange-100 text-orange-700',
            'half_day' => 'bg-blue-100 text-blue-700',
            'holiday'  => 'bg-purple-100 text-purple-700',
        ];
        $statusLabels = [
            'present'  => $tr('حاضر', 'Present'),
            'absent'   => $tr('غائب', 'Absent'),
            'late'     => $tr('متأخر', 'Late'),
            'half_day' => $tr('نصف يوم', 'Half Day'),
            'holiday'  => $tr('إجازة', 'Holiday'),
        ];
    @endphp
    <x-slot name="title">{{ $tr('سجل الحضور والغياب', 'Attendance') }}</x-slot>
    <div class="py-4">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $tr('سجل الحضور والغياب', 'Attendance') }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $tr('حاضر اليوم:', 'Present today:') }} <span class="font-semibold text-green-600">{{ $presentToday }}</span></p>
            </div>
            <a href="{{ route('manager.employees.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← {{ $tr('العودة للموظفين', 'Back to employees') }}</a>
        </div>

        {{-- Date filter --}}
        <form method="GET" class="bg-white rounded-xl shadow p-4 mb-5 flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">{{ $tr('التاريخ', 'Date') }}</label>
                <input type="date" name="date" value="{{ $date }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">{{ $tr('الموظف', 'Employee') }}</label>
                <select name="employee_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">{{ $tr('الكل', 'All') }}</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">{{ $tr('عرض', 'Show') }}</button>
        </form>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        {{-- Summary chips --}}
        @php
            $summary = $records->groupBy('status');
        @endphp
        @if($records->isNotEmpty())
        <div class="flex flex-wrap gap-3 mb-5">
            @foreach($statusLabels as $key => $label)
                @php $cnt = $summary->get($key, collect())->count(); @endphp
                @if($cnt > 0)
                <span class="px-3 py-1.5 rounded-full text-sm font-semibold {{ $statusColors[$key] }}">{{ $label }}: {{ $cnt }}</span>
                @endif
            @endforeach
        </div>
        @endif

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الموظف', 'Employee') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الحالة', 'Status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('وقت الدخول', 'Check In') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('وقت الخروج', 'Check Out') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('ساعات العمل', 'Hours') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('ملاحظات', 'Notes') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($records as $rec)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">
                                <a href="{{ route('manager.employees.show', $rec->employee) }}" class="text-blue-600 hover:underline">{{ $rec->employee->name ?? '-' }}</a>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$rec->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $statusLabels[$rec->status] ?? $rec->status }}</span>
                            </td>
                            <td class="px-4 py-3 font-mono text-gray-700">{{ $rec->check_in ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono text-gray-700">{{ $rec->check_out ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $rec->hours_worked ? number_format($rec->hours_worked, 1) . ' h' : '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ $rec->notes ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">{{ $tr('لا توجد سجلات لهذا التاريخ', 'No attendance records for this date') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
