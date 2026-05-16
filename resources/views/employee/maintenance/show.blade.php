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
    <x-slot name="title">{{ $tr('طلب صيانة', 'Maintenance Request') }} #{{ $maintenanceRequest->id }}</x-slot>
    <div class="py-4 max-w-2xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('employee.maintenance.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('طلب صيانة', 'Maintenance Request') }} #{{ $maintenanceRequest->id }}</h2>
        </div>

        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">{{ $tr('تفاصيل الطلب', 'Request Details') }}</h3>
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
            <dl class="space-y-4">
                <div class="flex justify-between"><dt class="text-gray-500">{{ $tr('العنوان', 'Title') }}</dt><dd class="font-bold">{{ $maintenanceRequest->title }}</dd></div>
                <div><dt class="text-gray-500 mb-1">{{ $tr('الوصف', 'Description') }}</dt><dd class="text-gray-700 bg-gray-50 rounded-lg p-3 mt-1">{{ $maintenanceRequest->description }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">{{ $tr('المستأجر', 'Tenant') }}</dt><dd class="font-medium">{{ $displayName($maintenanceRequest->tenant->user->name ?? null, $tr('مستأجر', 'Tenant')) }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">{{ $tr('العقار / الوحدة', 'Property / Unit') }}</dt><dd class="font-medium">{{ $maintenanceRequest->unit->property->name ?? '-' }} / {{ $maintenanceRequest->unit->unit_number ?? '-' }}</dd></div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">{{ $tr('الأولوية', 'Priority') }}</dt>
                    <dd><span class="px-2 py-1 rounded-full text-xs font-medium {{ $pc[$maintenanceRequest->priority] ?? 'bg-gray-100 text-gray-700' }}">{{ $pl[$maintenanceRequest->priority] ?? $maintenanceRequest->priority }}</span></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">{{ $tr('الحالة الحالية', 'Current Status') }}</dt>
                    <dd><span class="px-2 py-1 rounded-full text-xs font-medium {{ $sc[$maintenanceRequest->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $sl[$maintenanceRequest->status] ?? $maintenanceRequest->status }}</span></dd>
                </div>
                <div class="flex justify-between"><dt class="text-gray-500">{{ $tr('تاريخ الطلب', 'Request Date') }}</dt><dd class="font-medium">{{ $maintenanceRequest->created_at->format('Y/m/d') }}</dd></div>
                @if($maintenanceRequest->employee_notes)
                <div><dt class="text-gray-500 mb-1">{{ $tr('ملاحظات الموظف', 'Employee Notes') }}</dt><dd class="text-gray-700 bg-blue-50 rounded-lg p-3 mt-1">{{ $maintenanceRequest->employee_notes }}</dd></div>
                @endif
                @if($maintenanceRequest->required_tools)
                <div><dt class="text-gray-500 mb-1">{{ $tr('الأدوات المطلوبة', 'Required Tools') }}</dt><dd class="text-gray-700 bg-gray-50 rounded-lg p-3 mt-1">{{ $maintenanceRequest->required_tools }}</dd></div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-gray-500">{{ $tr('عامل خارجي مطلوب', 'External Worker Required') }}</dt>
                    <dd class="font-medium">{{ $maintenanceRequest->requires_external_worker ? $tr('نعم', 'Yes') : $tr('لا', 'No') }}</dd>
                </div>
                @if($maintenanceRequest->requires_external_worker)
                <div class="flex justify-between">
                    <dt class="text-gray-500">{{ $tr('اسم العامل', 'Worker Name') }}</dt>
                    <dd class="font-medium">{{ $maintenanceRequest->external_worker_name ?: '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">{{ $tr('تكلفة العامل', 'Worker Cost') }}</dt>
                    <dd class="font-medium">{{ $maintenanceRequest->external_worker_cost ? number_format($maintenanceRequest->external_worker_cost, 2) : '0.00' }} {{ $isAr ? 'ر.ع' : 'OMR' }}</dd>
                </div>
                @endif

                @if($maintenanceRequest->images->isNotEmpty())
                <div>
                    <dt class="text-gray-500 mb-2">{{ $tr('صور المشكلة', 'Issue Photos') }} ({{ $maintenanceRequest->images->count() }})</dt>
                    <dd>
                        <div class="grid grid-cols-3 gap-2 mt-1">
                            @foreach($maintenanceRequest->images as $image)
                            <a href="{{ $image->url() }}" target="_blank"
                               class="block rounded-lg overflow-hidden border border-gray-200 aspect-square hover:opacity-90 transition">
                                <img src="{{ $image->url() }}" alt="" class="w-full h-full object-cover">
                            </a>
                            @endforeach
                        </div>
                    </dd>
                </div>
                @endif
            </dl>
        </div>

        @if(!in_array($maintenanceRequest->status, ['completed','rejected']))
        <div class="bg-white rounded-xl shadow p-6" x-data="{ needsWorker: {{ $maintenanceRequest->requires_external_worker ? 'true' : 'false' }} }">
            <h3 class="font-bold text-gray-800 mb-4">{{ $tr('تحديث حالة الطلب', 'Update Request Status') }}</h3>
            <form method="POST" action="{{ route('employee.maintenance.update-status', $maintenanceRequest) }}" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحالة الجديدة', 'New Status') }} <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-right focus:ring-2 focus:ring-blue-500">
                        <option value="in_progress" {{ $maintenanceRequest->status==='in_progress'?'selected':'' }}>{{ $tr('جاري التنفيذ', 'In Progress') }}</option>
                        <option value="completed">{{ $tr('مكتمل', 'Completed') }}</option>
                        <option value="rejected">{{ $tr('مرفوض', 'Rejected') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('ملاحظات الموظف', 'Employee Notes') }}</label>
                    <textarea name="employee_notes" rows="3" placeholder="{{ $tr('أضف ملاحظاتك هنا...', 'Add your notes here...') }}"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-right focus:ring-2 focus:ring-blue-500">{{ $maintenanceRequest->employee_notes }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الأدوات المطلوبة', 'Required Tools') }}</label>
                    <textarea name="required_tools" rows="2" placeholder="{{ $tr('مثل: مفكات، سلم، مواد سباكة...', 'e.g. screwdrivers, ladder, plumbing supplies...') }}"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-right focus:ring-2 focus:ring-blue-500">{{ old('required_tools', $maintenanceRequest->required_tools) }}</textarea>
                </div>
                <div>
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input type="checkbox" name="requires_external_worker" value="1" x-model="needsWorker" {{ old('requires_external_worker', $maintenanceRequest->requires_external_worker) ? 'checked' : '' }}>
                        <span>{{ $tr('يحتاج عامل خارجي', 'Needs External Worker') }}</span>
                    </label>
                </div>
                <div x-show="needsWorker" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('اسم العامل', 'Worker Name') }}</label>
                        <input type="text" name="external_worker_name" value="{{ old('external_worker_name', $maintenanceRequest->external_worker_name) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-right focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('تكلفة العامل', 'Worker Cost') }} ({{ $isAr ? 'ر.ع' : 'OMR' }})</label>
                        <input type="number" step="0.01" min="0" name="external_worker_cost" value="{{ old('external_worker_cost', $maintenanceRequest->external_worker_cost) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-right focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition">{{ $tr('تحديث الحالة', 'Update Status') }}</button>
            </form>
        </div>
        @endif
    </div>
</x-app-layout>
