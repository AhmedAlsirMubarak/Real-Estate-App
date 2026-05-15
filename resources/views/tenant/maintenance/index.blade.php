<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('طلبات الصيانة', 'Maintenance Requests') }}</x-slot>
    <div class="py-4">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('طلبات الصيانة', 'Maintenance Requests') }}</h2>
            <a href="{{ route('tenant.maintenance.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $tr('تقديم طلب صيانة جديد', 'Submit New Maintenance Request') }}
            </a>
        </div>
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('العنوان', 'Title') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الأولوية', 'Priority') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الحالة', 'Status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('التاريخ', 'Date') }}</th>
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
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $sc[$req->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $sl[$req->status] ?? $req->status }}</span></td>
                            <td class="px-4 py-3 text-gray-600">{{ $req->created_at->format('Y/m/d') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('tenant.maintenance.show', $req) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">{{ $tr('عرض', 'View') }}</a>
                                    @if($req->status === 'pending')
                                    <form method="POST" action="{{ route('tenant.maintenance.destroy', $req) }}" onsubmit="return confirm('{{ $tr('هل أنت متأكد من حذف هذا الطلب؟', 'Are you sure you want to delete this request?') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">{{ $tr('حذف', 'Delete') }}</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">{{ $tr('لا توجد طلبات صيانة', 'No maintenance requests found') }}</td></tr>
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
