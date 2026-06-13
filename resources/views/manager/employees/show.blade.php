<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr   = fn(string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
        $activeTab = request('tab', 'profile');

        $leaveTypeColors  = ['annual'=>'bg-blue-100 text-blue-700','sick'=>'bg-orange-100 text-orange-700','unpaid'=>'bg-gray-100 text-gray-700','emergency'=>'bg-red-100 text-red-700'];
        $leaveStatusColors= ['pending'=>'bg-yellow-100 text-yellow-700','approved'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'];
        $attColors        = ['present'=>'bg-green-100 text-green-700','absent'=>'bg-red-100 text-red-700','late'=>'bg-orange-100 text-orange-700','half_day'=>'bg-blue-100 text-blue-700','holiday'=>'bg-purple-100 text-purple-700'];
        $attLabels        = ['present'=>$tr('حاضر','Present'),'absent'=>$tr('غائب','Absent'),'late'=>$tr('متأخر','Late'),'half_day'=>$tr('نصف يوم','Half Day'),'holiday'=>$tr('إجازة','Holiday')];
        $salStatusColors  = ['draft'=>'bg-gray-100 text-gray-700','pending'=>'bg-yellow-100 text-yellow-700','paid'=>'bg-green-100 text-green-700'];
    @endphp
    <x-slot name="title">{{ $employee->name }}</x-slot>
    <div class="py-4">

        {{-- Header --}}
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <a href="{{ route('manager.employees.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <div class="flex items-center gap-3 flex-1">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg">
                    {{ mb_substr($employee->name ?? 'E', 0, 1) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $employee->name }}</h2>
                    <span class="text-sm text-gray-500">{{ $employee->email }}</span>
                </div>
            </div>
            <a href="{{ route('manager.employees.edit', $employee) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                {{ $tr('تعديل', 'Edit') }}
            </a>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
        @endif

        {{-- Tab nav --}}
        <div class="flex flex-wrap gap-1 bg-white rounded-xl shadow px-4 py-2 mb-6 border-b border-gray-100">
            @php
                $tabs = [
                    'profile'    => ['icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'label'=>$tr('الملف الشخصي','Profile')],
                    'leaves'     => ['icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'label'=>$tr('الإجازات','Leaves')],
                    'attendance' => ['icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'label'=>$tr('الحضور والغياب','Attendance')],
                    'salary'     => ['icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'label'=>$tr('الرواتب','Salary')],
                ];
            @endphp
            @foreach($tabs as $key => $tab)
                <a href="{{ route('manager.employees.show', $employee) }}?tab={{ $key }}"
                   class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition {{ $activeTab === $key ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/></svg>
                    {{ $tab['label'] }}
                    @if($key === 'leaves' && $employee->leaves->where('status','pending')->count() > 0)
                        <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $employee->leaves->where('status','pending')->count() }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- ═══════════════ PROFILE TAB ═══════════════ --}}
        @if($activeTab === 'profile')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="space-y-4">
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">{{ $tr('بيانات الموظف', 'Employee Details') }}</h3>
                    <dl class="space-y-3">
                        <div><dt class="text-xs text-gray-500 mb-1">{{ $tr('الاسم', 'Name') }}</dt><dd class="font-medium">{{ $employee->name }}</dd></div>
                        <div><dt class="text-xs text-gray-500 mb-1">{{ $tr('البريد الإلكتروني', 'Email') }}</dt><dd class="font-medium text-sm break-all">{{ $employee->email }}</dd></div>
                        <div><dt class="text-xs text-gray-500 mb-1">{{ $tr('الهاتف', 'Phone') }}</dt><dd class="font-medium">{{ $employee->phone ?? '-' }}</dd></div>
                        <div><dt class="text-xs text-gray-500 mb-1">{{ $tr('الدور', 'Role') }}</dt>
                            <dd><span class="px-2 py-1 rounded-full text-xs font-medium {{ $employee->hasRole('accountant') ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $employee->hasRole('accountant') ? $tr('محاسب', 'Accountant') : $tr('موظف', 'Employee') }}
                            </span></dd>
                        </div>
                        <div><dt class="text-xs text-gray-500 mb-1">{{ $tr('الراتب الأساسي', 'Base Salary') }}</dt><dd class="font-semibold text-blue-700">{{ number_format((float)($employee->base_salary ?? 0), 2) }} {{ $currency }}</dd></div>
                        <div><dt class="text-xs text-gray-500 mb-1">{{ $tr('نسبة العمولة', 'Commission Rate') }}</dt><dd class="font-medium">{{ $employee->commission_rate ? $employee->commission_rate.'%' : '—' }}</dd></div>
                        <div><dt class="text-xs text-gray-500 mb-1">{{ $tr('تاريخ التسجيل', 'Joined') }}</dt><dd class="font-medium">{{ $employee->created_at->format('Y/m/d') }}</dd></div>
                    </dl>
                </div>

                @if($employee->referredProperties->isNotEmpty() || $employee->referredTenants->isNotEmpty())
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-xl p-5">
                    <h3 class="font-bold text-blue-800 mb-4 pb-2 border-b border-blue-100">{{ $tr('عمولة الإحالة', 'Referral Commission') }}</h3>
                    <dl class="space-y-3">
                        @if($employee->referredProperties->isNotEmpty())
                        <div>
                            <dt class="text-xs text-blue-600 mb-1">{{ $tr('عقارات مُحالة', 'Referred properties') }}</dt>
                            <dd class="text-xl font-black text-blue-800">{{ $employee->referredProperties->count() }}</dd>
                        </div>
                        @endif
                        @if($employee->referredTenants->isNotEmpty())
                        <div>
                            <dt class="text-xs text-emerald-600 mb-1">{{ $tr('مستأجرون مُحالون', 'Referred tenants') }}</dt>
                            <dd class="text-xl font-black text-emerald-800">{{ $employee->referredTenants->count() }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-xs text-blue-600 mb-1">{{ $tr('إجمالي العمولة', 'Total Commission') }}</dt>
                            <dd class="text-2xl font-black {{ $referralCommissionTotal > 0 ? 'text-green-700' : 'text-gray-500' }}">
                                {{ number_format($referralCommissionTotal, 2) }}
                                <span class="text-sm font-normal text-gray-500">{{ $currency }}</span>
                            </dd>
                        </div>
                    </dl>
                </div>
                @endif
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-800">{{ $tr('العقارات المسندة', 'Managed Properties') }} ({{ $employee->managedProperties->count() }})</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600"><tr>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('اسم العقار', 'Property') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('النوع', 'Type') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('الوحدات', 'Units') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('إجراء', 'Action') }}</th>
                            </tr></thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($employee->managedProperties as $property)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium">{{ $property->name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $property->typeLabel() }}</td>
                                    <td class="px-4 py-3"><span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs">{{ $property->units->count() }}</span></td>
                                    <td class="px-4 py-3"><a href="{{ route('manager.properties.show', $property) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">{{ $tr('عرض', 'View') }}</a></td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">{{ $tr('لم يُسند له أي عقار', 'No managed properties') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($employee->referredProperties->isNotEmpty())
                <div class="bg-white rounded-xl shadow">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-bold text-gray-800">{{ $tr('العقارات المُحالة', 'Referred Properties') }} ({{ $employee->referredProperties->count() }})</h3>
                        @if($propertyReferralCommissionTotal > 0)
                        <span class="text-sm font-bold text-blue-700 bg-blue-50 border border-blue-200 px-3 py-1 rounded-full">{{ number_format($propertyReferralCommissionTotal, 2) }} {{ $currency }}</span>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600"><tr>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('اسم العقار', 'Property') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('النوع', 'Type') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('نسبة العمولة', 'Comm. Rate') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('الإيجار المحصَّل', 'Collected') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('العمولة', 'Commission') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('إجراء', 'Action') }}</th>
                            </tr></thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($employee->referredProperties as $property)
                                @php $collected = $propertyRevenue[$property->id] ?? 0; $earned = ($property->referral_commission_rate ?? 0) / 100 * $collected; @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium">{{ $property->name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $property->typeLabel() }}</td>
                                    <td class="px-4 py-3">
                                        @if($property->referral_commission_rate)
                                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-semibold">{{ $property->referral_commission_rate }}%</span>
                                        @else <span class="text-gray-400 text-xs">—</span> @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ number_format($collected, 2) }} {{ $currency }}</td>
                                    <td class="px-4 py-3 font-semibold {{ $earned > 0 ? 'text-green-700' : 'text-gray-400' }}">{{ number_format($earned, 2) }} {{ $currency }}</td>
                                    <td class="px-4 py-3"><a href="{{ route('manager.properties.show', $property) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">{{ $tr('عرض', 'View') }}</a></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @if($employee->referredTenants->isNotEmpty())
                <div class="bg-white rounded-xl shadow">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-bold text-gray-800">{{ $tr('المستأجرون المُحالون', 'Referred Tenants') }} ({{ $employee->referredTenants->count() }})</h3>
                        @if($tenantReferralCommissionTotal > 0)
                        <span class="text-sm font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full">{{ number_format($tenantReferralCommissionTotal, 2) }} {{ $currency }}</span>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600"><tr>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('المستأجر', 'Tenant') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('نسبة العمولة', 'Comm. Rate') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('الإيجار المحصَّل', 'Collected') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('العمولة', 'Commission') }}</th>
                            </tr></thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($employee->referredTenants as $tenant)
                                @php $collected = $tenantRevenue[$tenant->id] ?? 0; $earned = ($tenant->referral_commission_rate ?? 0) / 100 * $collected; @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium">{{ $tenant->user->name ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        @if($tenant->referral_commission_rate)
                                            <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs font-semibold">{{ $tenant->referral_commission_rate }}%</span>
                                        @else <span class="text-gray-400 text-xs">—</span> @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ number_format($collected, 2) }} {{ $currency }}</td>
                                    <td class="px-4 py-3 font-semibold {{ $earned > 0 ? 'text-emerald-700' : 'text-gray-400' }}">{{ number_format($earned, 2) }} {{ $currency }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ═══════════════ LEAVES TAB ═══════════════ --}}
        @elseif($activeTab === 'leaves')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Leave summary --}}
            <div class="space-y-4">
                @php
                    $approvedLeaves = $employee->leaves->where('status','approved');
                    $pendingLeaves  = $employee->leaves->where('status','pending');
                    $byType = $approvedLeaves->groupBy('type');
                @endphp
                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">{{ $tr('ملخص الإجازات', 'Leave Summary') }}</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between"><dt class="text-sm text-gray-500">{{ $tr('إجمالي الأيام المعتمدة', 'Total Approved Days') }}</dt><dd class="font-bold text-blue-700">{{ $approvedLeaves->sum('days') }}</dd></div>
                        <div class="flex justify-between"><dt class="text-sm text-gray-500">{{ $tr('قيد المراجعة', 'Pending') }}</dt><dd class="font-medium text-yellow-600">{{ $pendingLeaves->count() }}</dd></div>
                        @foreach(['annual'=>$tr('سنوية','Annual'),'sick'=>$tr('مرضية','Sick'),'unpaid'=>$tr('بدون راتب','Unpaid'),'emergency'=>$tr('طارئة','Emergency')] as $type => $label)
                        <div class="flex justify-between items-center">
                            <dt class="text-sm text-gray-500">{{ $label }}</dt>
                            <dd><span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $leaveTypeColors[$type] }}">{{ $byType->get($type, collect())->sum('days') }} {{ $tr('يوم','d') }}</span></dd>
                        </div>
                        @endforeach
                    </dl>
                </div>

                {{-- Add leave form --}}
                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">{{ $tr('تسجيل إجازة جديدة', 'Add Leave Request') }}</h3>
                    <form method="POST" action="{{ route('manager.employees.leaves.store', $employee) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">{{ $tr('نوع الإجازة', 'Type') }}</label>
                            <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" required>
                                <option value="annual">{{ $tr('سنوية', 'Annual') }}</option>
                                <option value="sick">{{ $tr('مرضية', 'Sick') }}</option>
                                <option value="unpaid">{{ $tr('بدون راتب', 'Unpaid') }}</option>
                                <option value="emergency">{{ $tr('طارئة', 'Emergency') }}</option>
                            </select>
                            @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ $tr('من', 'From') }}</label>
                                <input type="date" name="start_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" required>
                                @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ $tr('إلى', 'To') }}</label>
                                <input type="date" name="end_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" required>
                                @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">{{ $tr('السبب', 'Reason') }}</label>
                            <textarea name="reason" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" placeholder="{{ $tr('اختياري...', 'Optional...') }}"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-medium transition">
                            {{ $tr('تسجيل الإجازة', 'Submit Leave') }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Leaves list --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-800">{{ $tr('سجل الإجازات', 'Leave History') }}</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600"><tr>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('النوع', 'Type') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('من', 'From') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('إلى', 'To') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('الأيام', 'Days') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('الحالة', 'Status') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('الإجراءات', 'Actions') }}</th>
                            </tr></thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($employee->leaves->sortByDesc('created_at') as $leave)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $leaveTypeColors[$leave->type] ?? 'bg-gray-100 text-gray-700' }}">{{ $leave->typeLabel() }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $leave->start_date->format('Y/m/d') }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $leave->end_date->format('Y/m/d') }}</td>
                                    <td class="px-4 py-3"><span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full text-xs font-semibold">{{ $leave->days }}</span></td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $leaveStatusColors[$leave->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $leave->statusLabel() }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-1.5">
                                            @if($leave->status === 'pending')
                                                <form method="POST" action="{{ route('manager.employees.leaves.approve', [$employee, $leave]) }}">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="text-xs text-green-600 hover:text-green-800 border border-green-300 px-2 py-1 rounded hover:bg-green-50">{{ $tr('موافقة', 'Approve') }}</button>
                                                </form>
                                                <form method="POST" action="{{ route('manager.employees.leaves.reject', [$employee, $leave]) }}">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="text-xs text-red-600 hover:text-red-800 border border-red-300 px-2 py-1 rounded hover:bg-red-50">{{ $tr('رفض', 'Reject') }}</button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('manager.employees.leaves.destroy', [$employee, $leave]) }}"
                                                  onsubmit="return confirm('{{ $tr('حذف؟','Delete?') }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-gray-400 hover:text-red-600">{{ $tr('حذف', 'Del') }}</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">{{ $tr('لا توجد إجازات مسجلة', 'No leave records') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════ ATTENDANCE TAB ═══════════════ --}}
        @elseif($activeTab === 'attendance')
        @php
            $attendanceMonth = request('att_month', now()->format('Y-m'));
            [$attYear, $attMonth] = explode('-', $attendanceMonth);
            $monthAttendance = $employee->attendance->filter(fn($a) => $a->date->format('Y-m') === $attendanceMonth)->sortBy('date');
            $attSummary = $monthAttendance->groupBy('status');
            $presentDays = $monthAttendance->whereIn('status', ['present','late'])->count();
        @endphp
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Attendance summary + add form --}}
            <div class="space-y-4">
                {{-- Month picker --}}
                <form method="GET" class="bg-white rounded-xl shadow p-4 flex items-end gap-3">
                    <input type="hidden" name="tab" value="attendance">
                    <div class="flex-1">
                        <label class="block text-xs text-gray-500 mb-1">{{ $tr('الشهر', 'Month') }}</label>
                        <input type="month" name="att_month" value="{{ $attendanceMonth }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">{{ $tr('عرض', 'Show') }}</button>
                </form>

                {{-- Month summary --}}
                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">{{ $tr('ملخص الشهر', 'Month Summary') }}</h3>
                    <dl class="space-y-2">
                        @foreach($attLabels as $key => $label)
                        @php $cnt = $attSummary->get($key, collect())->count(); @endphp
                        <div class="flex justify-between items-center">
                            <dt class="text-sm text-gray-500">{{ $label }}</dt>
                            <dd><span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $attColors[$key] }}">{{ $cnt }}</span></dd>
                        </div>
                        @endforeach
                        <div class="flex justify-between items-center border-t border-gray-100 pt-2 mt-2">
                            <dt class="text-sm font-semibold text-gray-700">{{ $tr('إجمالي الحضور', 'Total Present') }}</dt>
                            <dd class="font-bold text-blue-700">{{ $presentDays }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Add attendance record --}}
                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">{{ $tr('تسجيل حضور', 'Record Attendance') }}</h3>
                    <form method="POST" action="{{ route('manager.employees.attendance.store', $employee) }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="tab" value="attendance">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">{{ $tr('التاريخ', 'Date') }}</label>
                            <input type="date" name="date" value="{{ now()->format('Y-m-d') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">{{ $tr('الحالة', 'Status') }}</label>
                            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" required>
                                @foreach($attLabels as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ $tr('وقت الدخول', 'Check In') }}</label>
                                <input type="time" name="check_in" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ $tr('وقت الخروج', 'Check Out') }}</label>
                                <input type="time" name="check_out" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">{{ $tr('ساعات العمل', 'Hours Worked') }}</label>
                            <input type="number" name="hours_worked" min="0" max="24" step="0.5" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">{{ $tr('ملاحظات', 'Notes') }}</label>
                            <input type="text" name="notes" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="{{ $tr('اختياري...', 'Optional...') }}">
                        </div>
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded-lg text-sm font-medium transition">
                            {{ $tr('تسجيل', 'Save Record') }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Attendance table --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-800">{{ $tr('سجل الحضور', 'Attendance Records') }} — {{ $attendanceMonth }}</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600"><tr>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('التاريخ', 'Date') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('الحالة', 'Status') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('دخول', 'In') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('خروج', 'Out') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('ساعات', 'Hours') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('ملاحظات', 'Notes') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('حذف', 'Del') }}</th>
                            </tr></thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($monthAttendance as $att)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium">{{ $att->date->format('Y/m/d') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $attColors[$att->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $attLabels[$att->status] ?? $att->status }}</span>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-gray-700">{{ $att->check_in ?? '—' }}</td>
                                    <td class="px-4 py-3 font-mono text-gray-700">{{ $att->check_out ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $att->hours_worked ? number_format($att->hours_worked, 1) : '—' }}</td>
                                    <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ $att->notes ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('manager.employees.attendance.destroy', [$employee, $att]) }}"
                                              onsubmit="return confirm('{{ $tr('حذف؟','Delete?') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-gray-400 hover:text-red-600">{{ $tr('حذف', 'Del') }}</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">{{ $tr('لا توجد سجلات لهذا الشهر', 'No attendance for this month') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════ SALARY TAB ═══════════════ --}}
        @elseif($activeTab === 'salary')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Salary summary --}}
            <div class="space-y-4">
                @php
                    $paidSalaries  = $employee->salaries->where('status','paid');
                    $draftSalaries = $employee->salaries->whereIn('status',['draft','pending']);
                @endphp
                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">{{ $tr('ملخص الرواتب', 'Salary Summary') }}</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between"><dt class="text-sm text-gray-500">{{ $tr('الراتب الأساسي', 'Base Salary') }}</dt><dd class="font-bold text-blue-700">{{ number_format((float)($employee->base_salary ?? 0), 2) }} {{ $currency }}</dd></div>
                        <div class="flex justify-between"><dt class="text-sm text-gray-500">{{ $tr('رواتب مدفوعة', 'Paid Salaries') }}</dt><dd class="font-medium text-green-600">{{ $paidSalaries->count() }}</dd></div>
                        <div class="flex justify-between"><dt class="text-sm text-gray-500">{{ $tr('إجمالي مدفوع', 'Total Paid') }}</dt><dd class="font-bold text-green-700">{{ number_format($paidSalaries->sum('net_paid'), 2) }} {{ $currency }}</dd></div>
                        <div class="flex justify-between"><dt class="text-sm text-gray-500">{{ $tr('بانتظار الدفع', 'Pending') }}</dt><dd class="font-medium text-yellow-600">{{ $draftSalaries->count() }}</dd></div>
                    </dl>
                </div>
                <a href="{{ route('manager.salaries.create') }}?employee_id={{ $employee->id }}"
                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2.5 rounded-xl text-sm font-medium transition">
                    + {{ $tr('إضافة راتب جديد', 'Add Salary Record') }}
                </a>
                <a href="{{ route('manager.salaries.index') }}?employee_id={{ $employee->id }}"
                   class="block w-full bg-white border border-gray-300 hover:border-gray-400 text-gray-700 text-center py-2.5 rounded-xl text-sm font-medium transition">
                    {{ $tr('عرض كل الرواتب', 'View All Salaries') }}
                </a>
            </div>

            {{-- Salary records table --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-800">{{ $tr('سجل الرواتب', 'Salary Records') }}</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600"><tr>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('الفترة', 'Period') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('الأساسي', 'Base') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('البدلات', 'Allowances') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('الخصومات', 'Deductions') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('الصافي', 'Net') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('الحالة', 'Status') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ $tr('إجراء', 'Action') }}</th>
                            </tr></thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($employee->salaries->sortByDesc(fn($s) => $s->period_year * 100 + $s->period_month) as $sal)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium">{{ $sal->periodLabel() }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ number_format($sal->base_salary, 2) }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ number_format($sal->totalAllowances(), 2) }}</td>
                                    <td class="px-4 py-3 text-red-600">{{ $sal->deductions > 0 ? '−'.number_format($sal->deductions, 2) : '—' }}</td>
                                    <td class="px-4 py-3 font-bold text-blue-700">{{ number_format($sal->net_paid, 2) }} {{ $currency }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $salStatusColors[$sal->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $sal->statusLabel() }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('manager.salaries.show', $sal) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">{{ $tr('عرض', 'View') }}</a>
                                            @if($sal->status !== 'paid')
                                                <form method="POST" action="{{ route('manager.salaries.pay', $sal) }}">
                                                    @csrf
                                                    <button type="submit" class="text-xs text-green-600 hover:text-green-800 font-medium">{{ $tr('صرف', 'Pay') }}</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">{{ $tr('لا توجد سجلات رواتب', 'No salary records') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
