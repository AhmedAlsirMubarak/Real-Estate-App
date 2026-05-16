<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('طلب صيانة', 'Maintenance Request') }} #{{ $maintenanceRequest->id }}</x-slot>
    <div class="py-4 max-w-xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('tenant.maintenance.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('طلب صيانة', 'Maintenance Request') }} #{{ $maintenanceRequest->id }}</h2>
        </div>

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

        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-lg">{{ $maintenanceRequest->title }}</h3>
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $sc[$maintenanceRequest->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $sl[$maintenanceRequest->status] ?? $maintenanceRequest->status }}</span>
            </div>

            <div class="space-y-4">
                <div>
                    <p class="text-xs text-gray-500 mb-1">{{ $tr('الوصف', 'Description') }}</p>
                    <p class="text-gray-700 bg-gray-50 rounded-lg p-3">{{ $maintenanceRequest->description }}</p>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">{{ $tr('الأولوية', 'Priority') }}</span>
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $pc[$maintenanceRequest->priority] ?? 'bg-gray-100 text-gray-700' }}">{{ $pl[$maintenanceRequest->priority] ?? $maintenanceRequest->priority }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">{{ $tr('تاريخ الطلب', 'Request Date') }}</span>
                    <span class="text-sm font-medium">{{ $maintenanceRequest->created_at->format('Y/m/d') }}</span>
                </div>
                @if($maintenanceRequest->updated_at && $maintenanceRequest->updated_at != $maintenanceRequest->created_at)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">{{ $tr('آخر تحديث', 'Last Updated') }}</span>
                    <span class="text-sm font-medium">{{ $maintenanceRequest->updated_at->format('Y/m/d') }}</span>
                </div>
                @endif
                @if($maintenanceRequest->employee_notes)
                <div>
                    <p class="text-xs text-gray-500 mb-1">{{ $tr('ملاحظات الموظف', 'Employee Notes') }}</p>
                    <p class="text-gray-700 bg-blue-50 border border-blue-100 rounded-lg p-3">{{ $maintenanceRequest->employee_notes }}</p>
                </div>
                @endif

                @if($maintenanceRequest->images->isNotEmpty())
                <div>
                    <p class="text-xs text-gray-500 mb-2">{{ $tr('الصور المرفقة', 'Attached Photos') }} ({{ $maintenanceRequest->images->count() }})</p>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach($maintenanceRequest->images as $image)
                        <a href="{{ $image->url() }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 aspect-square hover:opacity-90 transition">
                            <img src="{{ $image->url() }}" alt="" class="w-full h-full object-cover">
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
