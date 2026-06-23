<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
        $appName = str_replace('_', ' ', ucwords(config('app.name'), '_'));

        $monthNames = $isAr
            ? ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر']
            : ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    @endphp
    <x-slot name="title">{{ $tr('الرسوم والاشتراكات', 'Dues & Subscriptions') }}</x-slot>

    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('الرسوم والاشتراكات', 'Dues & Subscriptions') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $tr('رسوم جمعيات الملاك — الفواتير والمتابعة', 'Owners\' association dues — invoices & follow-up') }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5 grid grid-cols-1 md:grid-cols-5 gap-3">
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ $tr('كل الحالات', 'All Statuses') }}</option>
            <option value="pending"  @selected(request('status')==='pending') >{{ $tr('معلّق', 'Pending') }}</option>
            <option value="paid"     @selected(request('status')==='paid')    >{{ $tr('مدفوع', 'Paid') }}</option>
            <option value="overdue"  @selected(request('status')==='overdue') >{{ $tr('متأخر', 'Overdue') }}</option>
        </select>
        <select name="month" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ $tr('كل الأشهر', 'All Months') }}</option>
            @for($m = 1; $m <= 12; $m++)
            <option value="{{ $m }}" @selected(request('month') == $m)>{{ $monthNames[$m] }}</option>
            @endfor
        </select>
        <input type="number" name="year" value="{{ request('year') }}"
               placeholder="{{ $tr('السنة', 'Year') }}"
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
        <div class="flex gap-2">
            <button class="bg-blue-900 text-white px-4 py-2 rounded-lg text-sm flex-1">{{ $tr('بحث', 'Search') }}</button>
            <a href="{{ route('manager.dues.index') }}" class="text-gray-500 text-sm px-3 py-2 hover:text-gray-700">{{ $tr('إعادة', 'Reset') }}</a>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 {{ $isAr ? 'text-right' : 'text-left' }}">{{ $tr('العقار', 'Property') }}</th>
                        <th class="px-4 py-3 {{ $isAr ? 'text-right' : 'text-left' }}">{{ $tr('المالك', 'Owner') }}</th>
                        <th class="px-4 py-3 {{ $isAr ? 'text-right' : 'text-left' }}">{{ $tr('الفترة', 'Period') }}</th>
                        <th class="px-4 py-3 {{ $isAr ? 'text-right' : 'text-left' }}">{{ $tr('المبلغ', 'Amount') }}</th>
                        <th class="px-4 py-3 {{ $isAr ? 'text-right' : 'text-left' }}">{{ $tr('تاريخ الاستحقاق', 'Due Date') }}</th>
                        <th class="px-4 py-3 {{ $isAr ? 'text-right' : 'text-left' }}">{{ $tr('الحالة', 'Status') }}</th>
                        <th class="px-4 py-3 {{ $isAr ? 'text-right' : 'text-left' }}">{{ $tr('إجراءات', 'Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($dues as $due)
                    @php
                        $propertyName = $due->association?->property?->name ?? '—';
                        $ownerName    = $due->owner?->user?->name ?? '—';
                        $ownerPhone   = $due->owner?->phone ?? $due->owner?->user?->phone ?? null;

                        // Professional WhatsApp message
                        $invoiceNo   = '#INV-' . str_pad($due->id, 6, '0', STR_PAD_LEFT);
                        $amountFmt   = number_format($due->amount, 2) . ' ' . $currency;
                        $dueDateFmt  = $due->due_date->format('d M Y');
                        $periodLabel = $due->periodLabel();

                        if ($isAr) {
                            $waMsg = "السيد/ة {$ownerName}،\n\n"
                                   . "تحية طيبة،\n\n"
                                   . "📋 *إشعار فاتورة — رسوم جمعية الملاك*\n"
                                   . "──────────────────\n"
                                   . "• رقم الفاتورة: {$invoiceNo}\n"
                                   . "• العقار: {$propertyName}\n"
                                   . "• الفترة: {$periodLabel}\n"
                                   . "• المبلغ المستحق: *{$amountFmt}*\n"
                                   . "• تاريخ الاستحقاق: {$dueDateFmt}\n"
                                   . "──────────────────\n\n"
                                   . "يُرجى سداد المبلغ قبل تاريخ الاستحقاق لتجنب أي رسوم إضافية.\n\n"
                                   . "لأي استفسار لا تترددوا في التواصل معنا.\n\n"
                                   . "مع تحياتنا،\n{$appName}";
                        } else {
                            $waMsg = "Dear {$ownerName},\n\n"
                                   . "Greetings from {$appName},\n\n"
                                   . "📋 *Invoice Notice — Owners' Association Dues*\n"
                                   . "──────────────────\n"
                                   . "• Invoice No.: {$invoiceNo}\n"
                                   . "• Property: {$propertyName}\n"
                                   . "• Period: {$periodLabel}\n"
                                   . "• Amount Due: *{$amountFmt}*\n"
                                   . "• Due Date: {$dueDateFmt}\n"
                                   . "──────────────────\n\n"
                                   . "Please arrange payment before the due date to avoid any late charges.\n\n"
                                   . "For any inquiries, feel free to contact us.\n\n"
                                   . "Kind regards,\n{$appName}";
                        }
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $propertyName }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $ownerName }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $due->periodLabel() }}</td>
                        <td class="px-4 py-3 font-semibold text-blue-900">{{ number_format($due->amount, 2) }} {{ $currency }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $due->due_date->format('Y/m/d') }}</td>
                        <td class="px-4 py-3">
                            <span @class(['text-xs px-2.5 py-0.5 rounded-full font-medium',
                                'bg-green-50 text-green-700'   => $due->status === 'paid',
                                'bg-red-50 text-red-700'       => $due->status === 'overdue',
                                'bg-gray-100 text-gray-600'    => $due->status === 'waived',
                                'bg-yellow-50 text-yellow-700' => !in_array($due->status, ['paid','overdue','waived']),
                            ])>
                                {{ $due->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap items-center gap-1.5">
                                {{-- Invoice PDF --}}
                                <a href="{{ route('manager.dues.invoice', $due) }}" target="_blank"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                    </svg>
                                    {{ $tr('فاتورة PDF', 'PDF Invoice') }}
                                </a>

                                {{-- Mark as Paid --}}
                                @if($due->status !== 'paid')
                                <form method="POST" action="{{ route('manager.dues.paid', $due) }}">
                                    @csrf @method('PATCH')
                                    <button class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-green-50 text-green-700 hover:bg-green-100 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        {{ $tr('تأكيد الدفع', 'Mark Paid') }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('manager.dues.waived', $due) }}">
                                    @csrf @method('PATCH')
                                    <button class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                                        {{ $tr('إعفاء', 'Waive') }}
                                    </button>
                                </form>
                                @endif

                                {{-- WhatsApp --}}
                                <x-whatsapp-button size="sm" :phone="$ownerPhone" :message="$waMsg" />
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-gray-400">
                            {{ $tr('لا توجد رسوم مسجلة', 'No dues found') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">{{ $dues->links() }}</div>
    </div>

    @if(session('success'))
    <div class="fixed bottom-4 {{ $isAr ? 'right-4' : 'left-4' }} bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm z-50">
        {{ session('success') }}
    </div>
    @endif
</x-app-layout>
