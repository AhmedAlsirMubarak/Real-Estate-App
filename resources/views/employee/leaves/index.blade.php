<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn(string $ar, string $en) => $isAr ? $ar : $en;
        $typeColors = [
            'annual'    => 'bg-blue-100 text-blue-700',
            'sick'      => 'bg-orange-100 text-orange-700',
            'unpaid'    => 'bg-gray-100 text-gray-600',
            'emergency' => 'bg-red-100 text-red-700',
        ];
        $statusColors = [
            'pending'  => 'bg-yellow-100 text-yellow-700',
            'approved' => 'bg-green-100 text-green-700',
            'rejected' => 'bg-red-100 text-red-700',
        ];
    @endphp
    <x-slot name="title">{{ $tr('سجل إجازاتي', 'My Leave History') }}</x-slot>

    <div class="py-4">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('سجل إجازاتي', 'My Leave History') }}</h2>
            <a href="{{ route('employee.leaves.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $tr('طلب إجازة', 'Request Leave') }}
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        {{-- Summary chips --}}
        @php
            $all = $leaves->getCollection();
            $pending  = $all->where('status', 'pending')->count();
            $approved = $all->where('status', 'approved')->count();
            $rejected = $all->where('status', 'rejected')->count();
            $totalDays = $all->where('status', 'approved')->sum('days');
        @endphp
        @if($leaves->total() > 0)
        <div class="flex flex-wrap gap-3 mb-5">
            <div class="bg-white border border-gray-100 rounded-xl px-4 py-3 shadow-sm text-center min-w-[90px]">
                <p class="text-2xl font-black text-gray-800">{{ $leaves->total() }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $tr('إجمالي الطلبات', 'Total Requests') }}</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl px-4 py-3 shadow-sm text-center min-w-[90px]">
                <p class="text-2xl font-black text-green-700">{{ $totalDays }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $tr('أيام موافق عليها', 'Approved Days') }}</p>
            </div>
            @if($pending)
            <div class="bg-yellow-50 border border-yellow-100 rounded-xl px-4 py-3 shadow-sm text-center min-w-[90px]">
                <p class="text-2xl font-black text-yellow-700">{{ $pending }}</p>
                <p class="text-xs text-yellow-600 mt-0.5">{{ $tr('قيد المراجعة', 'Pending') }}</p>
            </div>
            @endif
        </div>
        @endif

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('النوع', 'Type') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('من', 'From') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('إلى', 'To') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الأيام', 'Days') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الحالة', 'Status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('السبب', 'Reason') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('ملاحظات الإدارة', 'Manager Notes') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($leaves as $leave)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $typeColors[$leave->type] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $leave->typeLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $leave->start_date->format('Y/m/d') }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $leave->end_date->format('Y/m/d') }}</td>
                            <td class="px-4 py-3">
                                <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $leave->days }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$leave->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $leave->statusLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ $leave->reason ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-400 text-xs max-w-xs truncate">{{ $leave->notes ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <p class="text-gray-400 mb-3">{{ $tr('لم تقدم أي طلبات إجازة بعد', 'You have not submitted any leave requests yet') }}</p>
                                <a href="{{ route('employee.leaves.create') }}"
                                   class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    {{ $tr('تقديم طلب إجازة', 'Submit a leave request') }}
                                </a>
                            </td>
                        </tr>
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
