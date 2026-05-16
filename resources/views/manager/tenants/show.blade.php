<x-app-layout>
    <x-slot name="title">{{ $tenant->user->name ?? 'المستأجر' }}</x-slot>
    <div class="py-4">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('manager.tenants.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ $tenant->user->name ?? 'المستأجر' }}</h2>
            <a href="{{ route('manager.tenants.edit', $tenant) }}" class="mr-auto bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">تعديل</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Tenant Info --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">بيانات المستأجر</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">الاسم</dt><dd class="font-medium">{{ $tenant->user->name ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">البريد الإلكتروني</dt><dd class="font-medium">{{ $tenant->user->email ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">الهاتف</dt><dd class="font-medium">{{ $tenant->user->phone ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">رقم الهوية</dt><dd class="font-medium">{{ $tenant->national_id ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">طوارئ</dt><dd class="font-medium">{{ $tenant->emergency_contact ?? '-' }}</dd></div>
                </dl>
            </div>

            {{-- Contract Info --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">بيانات العقد النشط</h3>
                @if($tenant->activeContract)
                @php $c = $tenant->activeContract; @endphp
                <dl class="space-y-3">
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">العقار</dt><dd class="font-medium">{{ $c->unit->property->name ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">الوحدة</dt><dd class="font-medium">{{ $c->unit->unit_number ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">تاريخ البدء</dt><dd class="font-medium">{{ $c->start_date ? $c->start_date->format('Y/m/d') : '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">تاريخ الانتهاء</dt><dd class="font-medium">{{ $c->end_date ? $c->end_date->format('Y/m/d') : '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">الإيجار الشهري</dt><dd class="font-bold text-blue-700">{{ number_format($c->monthly_rent) }} ر.ع</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">مبلغ التأمين</dt><dd class="font-medium">{{ number_format($c->deposit ?? 0) }} ر.ع</dd></div>
                </dl>
                @else
                <p class="text-gray-400 text-center py-4">لا يوجد عقد نشط</p>
                @endif
            </div>
        </div>

        {{-- Maintenance Requests --}}
        <div class="bg-white rounded-xl shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-100"><h3 class="font-bold text-gray-800">طلبات الصيانة</h3></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium">العنوان</th>
                            <th class="px-4 py-3 text-right font-medium">الأولوية</th>
                            <th class="px-4 py-3 text-right font-medium">الحالة</th>
                            <th class="px-4 py-3 text-right font-medium">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($tenant->maintenanceRequests ?? [] as $req)
                        @php
                            $pc=['low'=>'bg-gray-100 text-gray-700','medium'=>'bg-blue-100 text-blue-700','high'=>'bg-orange-100 text-orange-700','urgent'=>'bg-red-100 text-red-700'];
                            $pl=['low'=>'منخفضة','medium'=>'متوسطة','high'=>'عالية','urgent'=>'عاجل'];
                            $sc=['pending'=>'bg-yellow-100 text-yellow-700','in_progress'=>'bg-blue-100 text-blue-700','completed'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'];
                            $sl=['pending'=>'معلق','in_progress'=>'جاري','completed'=>'مكتمل','rejected'=>'مرفوض'];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $req->title }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $pc[$req->priority] ?? 'bg-gray-100 text-gray-700' }}">{{ $pl[$req->priority] ?? $req->priority }}</span></td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $sc[$req->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $sl[$req->status] ?? $req->status }}</span></td>
                            <td class="px-4 py-3 text-gray-600">{{ $req->created_at->format('Y/m/d') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">لا توجد طلبات صيانة</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Payments --}}
        <div class="bg-white rounded-xl shadow">
            <div class="px-6 py-4 border-b border-gray-100"><h3 class="font-bold text-gray-800">سجل المدفوعات</h3></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium">الشهر / السنة</th>
                            <th class="px-4 py-3 text-right font-medium">المبلغ</th>
                            <th class="px-4 py-3 text-right font-medium">الحالة</th>
                            <th class="px-4 py-3 text-right font-medium">تاريخ الدفع</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($tenant->payments ?? [] as $pay)
                        @php
                            $pc2=['pending'=>'bg-yellow-100 text-yellow-700','paid'=>'bg-green-100 text-green-700','overdue'=>'bg-red-100 text-red-700'];
                            $pl2=['pending'=>'معلق','paid'=>'مدفوع','overdue'=>'متأخر'];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $pay->month }}/{{ $pay->year }}</td>
                            <td class="px-4 py-3">{{ number_format($pay->amount) }} ر.ع</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $pc2[$pay->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $pl2[$pay->status] ?? $pay->status }}</span></td>
                            <td class="px-4 py-3 text-gray-600">{{ $pay->paid_at ? $pay->paid_at->format('Y/m/d') : '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">لا توجد مدفوعات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>