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
                @php
                    $c = $tenant->activeContract;
                    $daysLeft = $c->end_date ? now()->diffInDays($c->end_date, false) : null;
                @endphp
                <dl class="space-y-3">
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">العقار</dt><dd class="font-medium">{{ $c->unit->property->name ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">الوحدة</dt><dd class="font-medium">{{ $c->unit->unit_number ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">تاريخ البدء</dt><dd class="font-medium">{{ $c->start_date ? $c->start_date->format('Y/m/d') : '-' }}</dd></div>
                    <div class="flex justify-between items-center">
                        <dt class="text-gray-500 text-sm">تاريخ الانتهاء</dt>
                        <dd class="font-medium flex items-center gap-2">
                            {{ $c->end_date ? $c->end_date->format('Y/m/d') : '-' }}
                            @if($daysLeft !== null && $daysLeft >= 0 && $daysLeft <= 30)
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $daysLeft <= 7 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $daysLeft }} يوم
                                </span>
                            @elseif($daysLeft !== null && $daysLeft < 0)
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">منتهي</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">الإيجار الشهري</dt><dd class="font-bold text-blue-700">{{ number_format($c->monthly_rent) }} ر.ع</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">مبلغ التأمين</dt><dd class="font-medium">{{ number_format($c->deposit ?? 0) }} ر.ع</dd></div>
                    @if($c->electricity_account_number)
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm flex items-center gap-1"><svg class="w-3.5 h-3.5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>رقم حساب الكهرباء</dt><dd class="font-medium font-mono">{{ $c->electricity_account_number }}</dd></div>
                    @endif
                    @if($c->water_account_number)
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm flex items-center gap-1"><svg class="w-3.5 h-3.5 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c-5.33 4.55-8 8.48-8 11.8C4 17.78 7.58 22 12 22s8-4.22 8-8.2C20 10.48 17.33 6.55 12 2z"/></svg>رقم حساب الماء</dt><dd class="font-medium font-mono">{{ $c->water_account_number }}</dd></div>
                    @endif
                </dl>

                {{-- Contract file section --}}
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">ملف العقد</p>
                    @if($c->contract_file)
                        <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg mb-3">
                            <svg class="w-8 h-8 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ basename($c->contract_file) }}</p>
                                <p class="text-xs text-gray-500">ملف العقد محفوظ</p>
                            </div>
                            <a href="{{ asset('storage/' . $c->contract_file) }}" target="_blank"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                تحميل
                            </a>
                            <form method="POST" action="{{ route('manager.rental-contracts.delete-file', $c) }}"
                                  onsubmit="return confirm('حذف ملف العقد؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 p-1" title="حذف الملف">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('manager.rental-contracts.upload-file', $c) }}"
                          enctype="multipart/form-data" class="flex items-center gap-2">
                        @csrf
                        <label class="flex-1 flex items-center gap-2 cursor-pointer border border-dashed border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-500 hover:border-blue-400 hover:bg-blue-50 transition">
                            <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span id="file-label-{{ $c->id }}">{{ $c->contract_file ? 'استبدال الملف…' : 'رفع ملف العقد…' }}</span>
                            <input type="file" name="contract_file" accept=".pdf,.doc,.docx" class="hidden"
                                   onchange="document.getElementById('file-label-{{ $c->id }}').textContent = this.files[0]?.name || '{{ $c->contract_file ? 'استبدال الملف…' : 'رفع ملف العقد…' }}'">
                        </label>
                        <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium">رفع</button>
                    </form>
                    @error('contract_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX — حد أقصى 10 ميجا</p>
                </div>
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