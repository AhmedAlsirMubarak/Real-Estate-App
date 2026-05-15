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
    <x-slot name="title">{{ $tr('طلبات الصيانة', 'Maintenance Requests') }}</x-slot>
    <div class="py-4" x-data="{ activeTab: '{{ request('status', 'all') }}' }">
        <h2 class="text-xl font-bold text-gray-800 mb-6">{{ $tr('طلبات الصيانة', 'Maintenance Requests') }}</h2>

        {{-- Tabs --}}
        <div class="flex flex-wrap gap-2 mb-6 bg-white rounded-xl shadow p-2">
            @foreach([
                'all' => $tr('الكل', 'All'),
                'pending' => $tr('معلق', 'Pending'),
                'in_progress' => $tr('جاري', 'In Progress'),
                'completed' => $tr('مكتمل', 'Completed'),
                'rejected' => $tr('مرفوض', 'Rejected'),
            ] as $status => $label)
            <a href="{{ route('employee.maintenance.index', ['status'=>$status]) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status',$status==='all'?'all':'x') === $status || (request('status')===null && $status==='all') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('العنوان', 'Title') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الأولوية', 'Priority') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('المستأجر', 'Tenant') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('المبنى / الوحدة', 'Property / Unit') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('التاريخ', 'Date') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الحالة', 'Status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('موارد التنفيذ', 'Execution Resources') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('إجراء', 'Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($requests as $req)
                        @php
                            $pc=['low'=>'bg-gray-100 text-gray-700','medium'=>'bg-blue-100 text-blue-700','high'=>'bg-orange-100 text-orange-700','urgent'=>'bg-red-100 text-red-700'];
                            $pl = $isAr
                                ? ['low' => 'منخفضة', 'medium' => 'متوسطة', 'high' => 'عالية', 'urgent' => 'عاجل']
                                : ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'];
                            $sc=['pending'=>'bg-yellow-100 text-yellow-700','in_progress'=>'bg-blue-100 text-blue-700','completed'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'];
                            $sl = $isAr
                                ? ['pending' => 'معلق', 'in_progress' => 'جاري', 'completed' => 'مكتمل', 'rejected' => 'مرفوض']
                                : ['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'rejected' => 'Rejected'];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $req->title }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $pc[$req->priority] ?? 'bg-gray-100 text-gray-700' }}">{{ $pl[$req->priority] ?? $req->priority }}</span></td>
                            <td class="px-4 py-3 text-gray-600">{{ $displayName($req->tenant->user->name ?? null, $tr('مستأجر', 'Tenant')) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $req->unit->property->name ?? '-' }} / {{ $req->unit->unit_number ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $req->created_at->format('Y/m/d') }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $sc[$req->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $sl[$req->status] ?? $req->status }}</span></td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                @if($req->requires_external_worker)
                                    <span class="inline-flex px-2 py-1 rounded-full bg-purple-100 text-purple-700">{{ $tr('عامل خارجي', 'External Worker') }}</span>
                                @elseif($req->required_tools)
                                    <span class="inline-flex px-2 py-1 rounded-full bg-blue-100 text-blue-700">{{ $tr('أدوات فقط', 'Tools Only') }}</span>
                                @else
                                    <span class="text-gray-400">{{ $tr('غير محدد', 'Not set') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3"><a href="{{ route('employee.maintenance.show', $req) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">{{ $tr('عرض', 'View') }}</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">{{ $tr('لا توجد طلبات صيانة', 'No maintenance requests') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($requests->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $requests->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
