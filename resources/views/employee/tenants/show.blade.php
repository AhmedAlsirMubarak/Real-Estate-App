<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn(string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
        $months = [1=>$tr('يناير','Jan'),2=>$tr('فبراير','Feb'),3=>$tr('مارس','Mar'),4=>$tr('أبريل','Apr'),5=>$tr('مايو','May'),6=>$tr('يونيو','Jun'),7=>$tr('يوليو','Jul'),8=>$tr('أغسطس','Aug'),9=>$tr('سبتمبر','Sep'),10=>$tr('أكتوبر','Oct'),11=>$tr('نوفمبر','Nov'),12=>$tr('ديسمبر','Dec')];
        $statusColors = ['pending'=>'bg-yellow-100 text-yellow-700','paid'=>'bg-green-100 text-green-700','overdue'=>'bg-red-100 text-red-700'];
        $typeLabels = ['rent'=>$tr('إيجار','Rent'),'deposit'=>$tr('تأمين','Deposit'),'maintenance'=>$tr('صيانة','Maintenance')];
    @endphp
    <x-slot name="title">{{ $tenant->user->name ?? $tr('المستأجر', 'Tenant') }}</x-slot>

    <div class="py-4">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('employee.tenants.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">← {{ $tr('قائمة المستأجرين', 'All Tenants') }}</a>
            <h2 class="text-xl font-bold text-gray-800">{{ $tenant->user->name ?? $tr('المستأجر', 'Tenant') }}</h2>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm space-y-1">
                @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Tenant Info --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">{{ $tr('بيانات المستأجر', 'Tenant Details') }}</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('الاسم', 'Name') }}</dt><dd class="font-medium">{{ $tenant->user->name ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('البريد الإلكتروني', 'Email') }}</dt><dd class="font-medium text-sm">{{ $tenant->user->email ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('الهاتف', 'Phone') }}</dt><dd class="font-medium">{{ $tenant->user->phone ?? $tenant->phone ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('الهوية', 'National ID') }}</dt><dd class="font-medium">{{ $tenant->national_id ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('جهة الطوارئ', 'Emergency Contact') }}</dt><dd class="font-medium">{{ $tenant->emergency_contact ?? '-' }}</dd></div>
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

                @if($daysLeft !== null && $daysLeft <= 30)
                <div class="mb-4 px-4 py-3 rounded-lg flex items-center gap-2 {{ $daysLeft <= 0 ? 'bg-red-50 border border-red-200 text-red-700' : ($daysLeft <= 7 ? 'bg-red-50 border border-red-200 text-red-700' : 'bg-orange-50 border border-orange-200 text-orange-700') }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-sm font-medium">
                        @if($daysLeft <= 0)
                            {{ $tr('انتهى العقد', 'Contract has expired') }}
                        @else
                            {{ $tr('ينتهي العقد خلال', 'Contract expires in') }} <strong>{{ $daysLeft }}</strong> {{ $tr('يوم', 'days') }}
                        @endif
                    </p>
                </div>
                @endif

                <dl class="space-y-3">
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('العقار', 'Property') }}</dt><dd class="font-medium">{{ $c->unit->property->name ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('الوحدة', 'Unit') }}</dt><dd class="font-medium">{{ $c->unit->unit_number ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('تاريخ البدء', 'Start Date') }}</dt><dd class="font-medium">{{ $c->start_date?->format('Y/m/d') ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('تاريخ الانتهاء', 'End Date') }}</dt><dd class="font-medium">{{ $c->end_date?->format('Y/m/d') ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('الإيجار الشهري', 'Monthly Rent') }}</dt><dd class="font-bold text-blue-700">{{ number_format($c->monthly_rent) }} {{ $currency }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 text-sm">{{ $tr('مبلغ التأمين', 'Deposit') }}</dt><dd class="font-medium">{{ number_format($c->deposit ?? 0) }} {{ $currency }}</dd></div>
                </dl>
                @else
                <p class="text-gray-400 text-center py-4">{{ $tr('لا يوجد عقد نشط', 'No active contract') }}</p>
                @endif
            </div>
        </div>

        {{-- Generate Invoice Form --}}
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">{{ $tr('إنشاء فاتورة إيجار', 'Generate Rent Invoice') }}</h3>
            <form method="POST" action="{{ route('employee.tenants.payments.generate', $tenant) }}" class="flex flex-wrap gap-3 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('الشهر', 'Month') }}</label>
                    <select name="month" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        @foreach($months as $num => $label)
                            <option value="{{ $num }}" {{ $num == now()->month ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('السنة', 'Year') }}</label>
                    <input type="number" name="year" value="{{ now()->year }}" min="2020" max="2035"
                           class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-24 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ $tr('المبلغ', 'Amount') }} ({{ $currency }})</label>
                    <input type="number" name="amount" step="0.01" min="0"
                           value="{{ $tenant->activeContract?->monthly_rent ?? '' }}"
                           class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-32 focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    {{ $tr('إنشاء', 'Generate') }}
                </button>
            </form>
        </div>

        {{-- Payments Table --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800">{{ $tr('الفواتير والمدفوعات', 'Invoices & Payments') }}</h3>
                <span class="text-sm text-gray-500">{{ $tenant->payments->count() }} {{ $tr('فاتورة', 'invoices') }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('النوع', 'Type') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الفترة', 'Period') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('المبلغ', 'Amount') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الحالة', 'Status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('تاريخ الدفع', 'Paid On') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('إجراءات', 'Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($tenant->payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium text-gray-600">{{ $typeLabels[$payment->type] ?? $payment->type }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ ($months[$payment->month] ?? $payment->month) . ' ' . $payment->year }}</td>
                            <td class="px-4 py-3 font-semibold">{{ number_format($payment->amount, 2) }} {{ $currency }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$payment->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ match($payment->status) { 'paid' => $tr('مدفوع','Paid'), 'pending' => $tr('معلق','Pending'), 'overdue' => $tr('متأخر','Overdue'), default => $payment->status } }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $payment->paid_at?->format('Y/m/d') ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2 flex-wrap">
                                    {{-- Mark paid --}}
                                    @if($payment->status !== 'paid')
                                    <form method="POST" action="{{ route('employee.tenants.payments.mark-paid', [$tenant, $payment]) }}" class="flex items-center gap-1">
                                        @csrf @method('PATCH')
                                        <input type="date" name="paid_at" value="{{ now()->toDateString() }}"
                                               class="border border-gray-200 rounded px-2 py-1 text-xs w-32">
                                        <button type="submit" class="text-xs font-medium text-green-600 hover:text-green-800 border border-green-300 px-2 py-1 rounded-lg hover:bg-green-50 transition">
                                            {{ $tr('تسجيل دفع', 'Mark Paid') }}
                                        </button>
                                    </form>
                                    @endif

                                    {{-- Invoice PDF --}}
                                    <a href="{{ route('employee.tenants.payments.invoice', [$tenant, $payment]) }}" target="_blank"
                                       class="text-xs font-medium text-blue-600 hover:text-blue-800 border border-blue-300 px-2 py-1 rounded-lg hover:bg-blue-50 transition">
                                        {{ $tr('فاتورة', 'Invoice') }}
                                    </a>

                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('employee.tenants.payments.destroy', [$tenant, $payment]) }}"
                                          onsubmit="return confirm('{{ $tr('حذف الفاتورة؟', 'Delete this payment?') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-gray-400 hover:text-red-600 transition">{{ $tr('حذف', 'Del') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">{{ $tr('لا توجد فواتير بعد', 'No invoices yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
