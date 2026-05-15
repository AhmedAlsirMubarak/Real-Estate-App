<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.س' : 'SAR';
        $months = $isAr
            ? ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر']
            : ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    @endphp
    <x-slot name="title">{{ $tr('إشعارات الدفع', 'Payment Notices') }}</x-slot>
    <div class="py-4">
        <h2 class="text-xl font-bold text-gray-800 mb-6">{{ $tr('إشعارات الدفع', 'Payment Notices') }}</h2>
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الشهر / السنة', 'Month / Year') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('المبلغ', 'Amount') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الحالة', 'Status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('تاريخ الدفع', 'Payment Date') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('إجراء', 'Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($payments as $pay)
                        @php
                            $pc=['pending'=>'bg-yellow-100 text-yellow-700','paid'=>'bg-green-100 text-green-700','overdue'=>'bg-red-100 text-red-700'];
                            $pl = $isAr
                                ? ['pending' => 'معلق', 'paid' => 'مدفوع', 'overdue' => 'متأخر']
                                : ['pending' => 'Pending', 'paid' => 'Paid', 'overdue' => 'Overdue'];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $months[$pay->month] ?? $pay->month }} {{ $pay->year }}</td>
                            <td class="px-4 py-3 font-medium">{{ number_format($pay->amount) }} {{ $currency }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $pc[$pay->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $pl[$pay->status] ?? $pay->status }}</span></td>
                            <td class="px-4 py-3 text-gray-600">{{ $pay->paid_at ? $pay->paid_at->format('Y/m/d') : '-' }}</td>
                            <td class="px-4 py-3"><a href="{{ route('tenant.payments.show', $pay) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">{{ $tr('عرض الإيصال', 'View Receipt') }}</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">{{ $tr('لا توجد مدفوعات', 'No payments found') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $payments->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
