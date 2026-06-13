<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn(string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
    @endphp
    <x-slot name="title">{{ $tenant->user->name ?? $tr('المستأجر', 'Tenant') }}</x-slot>
    <div class="py-4">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('manager.tenants.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ $tenant->user->name ?? $tr('المستأجر', 'Tenant') }}</h2>
            <a href="{{ route('manager.tenants.edit', $tenant) }}" class="mr-auto bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">{{ $tr('تعديل', 'Edit') }}</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Tenant Info --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">{{ $tr('بيانات المستأجر', 'Tenant Details') }}</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('الاسم', 'Name') }}</dt><dd class="font-medium">{{ $tenant->user->name ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('البريد الإلكتروني', 'Email') }}</dt><dd class="font-medium">{{ $tenant->user->email ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('الهاتف', 'Phone') }}</dt><dd class="font-medium">{{ $tenant->user->phone ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('الهوية', 'ID') }}</dt><dd class="font-medium">{{ $tenant->national_id ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('طوارئ', 'Emergency Contact') }}</dt><dd class="font-medium">{{ $tenant->emergency_contact ?? '-' }}</dd></div>
                </dl>
            </div>

            {{-- Contract Info --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">{{ $tr('بيانات العقد النشط', 'Active Contract') }}</h3>
                @if($tenant->activeContract)
                @php
                    $c = $tenant->activeContract;
                    $daysLeft = $c->end_date ? (int) now()->diffInDays($c->end_date, false) : null;
                @endphp
                <dl class="space-y-3">
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('العقار', 'Property') }}</dt><dd class="font-medium">{{ $c->unit->property->name ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('الوحدة', 'Unit') }}</dt><dd class="font-medium">{{ $c->unit->unit_number ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('تاريخ البدء', 'Start Date') }}</dt><dd class="font-medium">{{ $c->start_date ? $c->start_date->format('Y/m/d') : '-' }}</dd></div>
                    <div class="flex justify-between items-center">
                        <dt class="text-gray-500 text-sm">{{ $tr('تاريخ الانتهاء', 'End Date') }}</dt>
                        <dd class="font-medium flex items-center gap-2">
                            {{ $c->end_date ? $c->end_date->format('Y/m/d') : '-' }}
                            @if($daysLeft !== null && $daysLeft >= 0 && $daysLeft <= 30)
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $daysLeft <= 7 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $daysLeft }} {{ $tr('يوم', 'days') }}
                                </span>
                            @elseif($daysLeft !== null && $daysLeft < 0)
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">{{ $tr('منتهي', 'Expired') }}</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('الإيجار الشهري', 'Monthly Rent') }}</dt><dd class="font-bold text-blue-700">{{ number_format($c->monthly_rent) }} {{ $currency }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('مبلغ التأمين', 'Deposit') }}</dt><dd class="font-medium">{{ number_format($c->deposit ?? 0) }} {{ $currency }}</dd></div>
                    @if($c->electricity_account_number)
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm flex items-center gap-1"><svg class="w-3.5 h-3.5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>{{ $tr('رقم حساب الكهرباء', 'Electricity Acc.') }}</dt><dd class="font-medium font-mono">{{ $c->electricity_account_number }}</dd></div>
                    @endif
                    @if($c->water_account_number)
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm flex items-center gap-1"><svg class="w-3.5 h-3.5 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c-5.33 4.55-8 8.48-8 11.8C4 17.78 7.58 22 12 22s8-4.22 8-8.2C20 10.48 17.33 6.55 12 2z"/></svg>{{ $tr('رقم حساب الماء', 'Water Acc.') }}</dt><dd class="font-medium font-mono">{{ $c->water_account_number }}</dd></div>
                    @endif
                </dl>

                {{-- Contract file section --}}
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">{{ $tr('ملف العقد', 'Contract File') }}</p>
                    @if($c->contract_file)
                        <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg mb-3">
                            <svg class="w-8 h-8 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/></svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ basename($c->contract_file) }}</p>
                                <p class="text-xs text-gray-500">{{ $tr('ملف العقد محفوظ', 'Contract file saved') }}</p>
                            </div>
                            <a href="{{ asset('storage/' . $c->contract_file) }}" target="_blank"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                {{ $tr('تحميل', 'Download') }}
                            </a>
                            <form method="POST" action="{{ route('manager.rental-contracts.delete-file', $c) }}"
                                  onsubmit="return confirm('{{ $tr('حذف ملف العقد؟', 'Delete contract file?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 p-1" title="{{ $tr('حذف الملف', 'Delete file') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('manager.rental-contracts.upload-file', $c) }}"
                          enctype="multipart/form-data" class="flex items-center gap-2">
                        @csrf
                        @php $uploadLabel = $c->contract_file ? $tr('استبدال الملف…', 'Replace file…') : $tr('رفع ملف العقد…', 'Upload contract file…'); @endphp
                        <label class="flex-1 flex items-center gap-2 cursor-pointer border border-dashed border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-500 hover:border-blue-400 hover:bg-blue-50 transition">
                            <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span id="file-label-{{ $c->id }}">{{ $uploadLabel }}</span>
                            <input type="file" name="contract_file" accept=".pdf,.doc,.docx" class="hidden"
                                   onchange="document.getElementById('file-label-{{ $c->id }}').textContent = this.files[0]?.name || '{{ $uploadLabel }}'">
                        </label>
                        <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium">{{ $tr('رفع', 'Upload') }}</button>
                    </form>
                    @error('contract_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX — {{ $tr('حد أقصى 10 ميجا', 'max 10 MB') }}</p>
                </div>
                @else
                <p class="text-gray-400 text-center py-4">{{ $tr('لا يوجد عقد نشط', 'No active contract') }}</p>
                @endif
            </div>
        </div>

        {{-- Maintenance Requests --}}
        <div class="bg-white rounded-xl shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-100"><h3 class="font-bold text-gray-800">{{ $tr('طلبات الصيانة', 'Maintenance Requests') }}</h3></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('العنوان', 'Title') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الأولوية', 'Priority') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الحالة', 'Status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('التاريخ', 'Date') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($tenant->maintenanceRequests ?? [] as $req)
                        @php
                            $pc=['low'=>'bg-gray-100 text-gray-700','medium'=>'bg-blue-100 text-blue-700','high'=>'bg-orange-100 text-orange-700','urgent'=>'bg-red-100 text-red-700'];
                            $pl=['low'=> $tr('منخفضة','Low'), 'medium'=> $tr('متوسطة','Medium'), 'high'=> $tr('عالية','High'), 'urgent'=> $tr('عاجل','Urgent')];
                            $sc=['pending'=>'bg-yellow-100 text-yellow-700','in_progress'=>'bg-blue-100 text-blue-700','completed'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'];
                            $sl=['pending'=> $tr('معلق','Pending'), 'in_progress'=> $tr('جاري','In Progress'), 'completed'=> $tr('مكتمل','Completed'), 'rejected'=> $tr('مرفوض','Rejected')];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $req->title }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $pc[$req->priority] ?? 'bg-gray-100 text-gray-700' }}">{{ $pl[$req->priority] ?? $req->priority }}</span></td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $sc[$req->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $sl[$req->status] ?? $req->status }}</span></td>
                            <td class="px-4 py-3 text-gray-600">{{ $req->created_at->format('Y/m/d') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">{{ $tr('لا توجد طلبات صيانة', 'No maintenance requests') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Payments --}}
        <div id="payments" class="bg-white rounded-xl shadow">
            @php
                $lastRent   = $tenant->payments->where('type', 'rent')->sortByDesc(fn($p) => $p->year * 100 + $p->month)->first();
                $nextMonth  = $lastRent ? ($lastRent->month == 12 ? 1  : $lastRent->month + 1) : now()->month;
                $nextYear   = $lastRent ? ($lastRent->month == 12 ? $lastRent->year + 1 : $lastRent->year) : now()->year;
                $defaultAmt = $tenant->activeContract?->monthly_rent ?? 0;
            @endphp
            <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                <h3 class="font-bold text-gray-800">{{ $tr('سجل المدفوعات', 'Payment Records') }}</h3>
                @if($tenant->activeContract ?? $tenant->rentalContracts->first())
                <form method="POST" action="{{ route('manager.tenants.payments.generate', $tenant) }}"
                      class="flex flex-wrap items-end gap-2">
                    @csrf
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ $tr('الشهر', 'Month') }}</label>
                        <select name="month" class="border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                            @foreach(range(1,12) as $m)
                                <option value="{{ $m }}" {{ $m == $nextMonth ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ $tr('السنة', 'Year') }}</label>
                        <select name="year" class="border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                            @foreach(range(now()->year - 1, now()->year + 3) as $y)
                                <option value="{{ $y }}" {{ $y == $nextYear ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ $tr('المبلغ', 'Amount') }} ({{ $currency }})</label>
                        <input type="number" name="amount" value="{{ number_format((float)$defaultAmt, 2, '.', '') }}" min="0" step="0.01"
                               class="border border-gray-300 rounded-lg px-2 py-1.5 text-sm w-28 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ $tr('توليد فاتورة شهرية', 'Generate Invoice') }}
                    </button>
                </form>
                @endif
            </div>
            @error('payment')
                <div class="px-6 py-2 bg-red-50 text-red-600 text-sm border-b border-red-100">{{ $message }}</div>
            @enderror
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('النوع', 'Type') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الشهر / السنة', 'Month / Year') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('المبلغ', 'Amount') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الحالة', 'Status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('تاريخ الدفع', 'Paid Date') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الفاتورة', 'Invoice') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($tenant->payments ?? [] as $pay)
                        @php
                            $pc2=['pending'=>'bg-yellow-100 text-yellow-700','paid'=>'bg-green-100 text-green-700','overdue'=>'bg-red-100 text-red-700'];
                            $pl2=['pending'=> $tr('معلق','Pending'), 'paid'=> $tr('مدفوع','Paid'), 'overdue'=> $tr('متأخر','Overdue')];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                @if(($pay->type ?? 'rent') === 'deposit')
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">{{ $tr('تأمين', 'Deposit') }}</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $tr('إيجار', 'Rent') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium">{{ $pay->month }}/{{ $pay->year }}</td>
                            <td class="px-4 py-3">{{ number_format((float)$pay->amount, 2) }} {{ $currency }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $pc2[$pay->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $pl2[$pay->status] ?? $pay->status }}</span></td>
                            <td class="px-4 py-3 text-gray-600">{{ $pay->paid_at ? $pay->paid_at->format('Y/m/d') : '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('manager.tenants.payments.invoice', [$tenant, $pay]) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        {{ $tr('فاتورة', 'Invoice') }}
                                    </a>
                                    <form method="POST" action="{{ route('manager.tenants.payments.destroy', [$tenant, $pay]) }}"
                                          onsubmit="return confirm('{{ $tr('هل أنت متأكد من حذف هذه الفاتورة؟', 'Are you sure you want to delete this invoice?') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white px-2.5 py-1.5 rounded-lg text-xs font-medium transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            {{ $tr('حذف', 'Delete') }}
                                        </button>
                                    </form>
                                    @if($pay->status !== 'paid')
                                    <form method="POST" action="{{ route('manager.tenants.payments.mark-paid', [$tenant, $pay]) }}"
                                          class="flex items-center gap-1">
                                        @csrf @method('PATCH')
                                        <input type="date" name="paid_at" value="{{ now()->format('Y-m-d') }}"
                                               class="border border-gray-300 rounded-lg px-2 py-1 text-xs focus:ring-2 focus:ring-green-500">
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            {{ $tr('تم الدفع', 'Mark Paid') }}
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">{{ $tr('لا توجد مدفوعات', 'No payment records') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
