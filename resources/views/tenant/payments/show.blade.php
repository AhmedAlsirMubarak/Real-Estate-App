<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
        $months = $isAr
            ? ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر']
            : ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $displayName = function (?string $name, string $fallback = 'User') use ($isAr) {
            if ($name === null || $name === '') return $fallback;
            if ($isAr || ! preg_match('/\p{Arabic}/u', $name)) return $name;
            return $fallback;
        };
    @endphp
    <x-slot name="title">{{ $tr('إيصال الدفع', 'Payment Receipt') }}</x-slot>
    <div class="py-4 max-w-lg mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('tenant.payments.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('إيصال الدفع', 'Payment Receipt') }}</h2>
        </div>

        @php
            $pc=['pending'=>'bg-yellow-100 text-yellow-700','paid'=>'bg-green-100 text-green-700','overdue'=>'bg-red-100 text-red-700'];
            $pl = $isAr
                ? ['pending' => 'معلق', 'paid' => 'مدفوع', 'overdue' => 'متأخر']
                : ['pending' => 'Pending', 'paid' => 'Paid', 'overdue' => 'Overdue'];
        @endphp

        <div class="bg-white rounded-xl shadow overflow-hidden">
            {{-- Receipt Header --}}
            <div class="bg-blue-900 text-white p-6 text-center">
                <h3 class="text-2xl font-bold text-yellow-400 mb-1">{{ $tr('شركة ثروة للتطوير العقاري', 'Tharwa Real Estate') }}</h3>
                <p class="text-blue-200 text-sm">{{ $tr('إيصال دفع إيجار', 'Rent Payment Receipt') }}</p>
            </div>

            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <span class="text-gray-500 text-sm">{{ $tr('رقم الإيصال', 'Receipt Number') }}</span>
                    <span class="font-bold text-gray-800">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>

                <div class="space-y-4 border-t border-b border-gray-100 py-4 mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ $tr('المستأجر', 'Tenant') }}</span>
                        <span class="font-medium">{{ $displayName($payment->tenant->user->name ?? null, $tr('مستأجر', 'Tenant')) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ $tr('العقار', 'Property') }}</span>
                        <span class="font-medium">{{ $payment->tenant->activeContract->unit->property->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ $tr('رقم الوحدة', 'Unit Number') }}</span>
                        <span class="font-medium">{{ $payment->tenant->activeContract->unit->unit_number ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ $tr('الشهر', 'Month') }}</span>
                        <span class="font-medium">{{ $months[$payment->month] ?? $payment->month }} {{ $payment->year }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ $tr('الحالة', 'Status') }}</span>
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $pc[$payment->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $pl[$payment->status] ?? $payment->status }}</span>
                    </div>
                    @if($payment->paid_at)
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ $tr('تاريخ الدفع', 'Payment Date') }}</span>
                        <span class="font-medium">{{ $payment->paid_at->format('Y/m/d') }}</span>
                    </div>
                    @endif
                    @if($payment->confirmedBy)
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ $tr('تأكيد بواسطة', 'Confirmed By') }}</span>
                        <span class="font-medium">{{ $displayName($payment->confirmedBy->name ?? null, $tr('مستخدم', 'User')) }}</span>
                    </div>
                    @endif
                </div>

                <div class="bg-blue-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-gray-500 mb-1">{{ $tr('المبلغ الإجمالي', 'Total Amount') }}</p>
                    <p class="text-3xl font-bold text-blue-700">{{ number_format($payment->amount) }} <span class="text-lg">{{ $currency }}</span></p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
