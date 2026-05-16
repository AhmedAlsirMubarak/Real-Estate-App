<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
        $displayName = function (?string $name, string $fallback = 'Buyer') use ($isAr) {
            if ($name === null || $name === '') return $fallback;
            if ($isAr || ! preg_match('/\p{Arabic}/u', $name)) return $name;
            return $fallback;
        };
    @endphp
    <x-slot name="title">{{ $tr('بوابة المشتري', 'Buyer Portal') }}</x-slot>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ $tr('مرحباً،', 'Welcome,') }} {{ $displayName(auth()->user()->name ?? null, $tr('مشتري', 'Buyer')) }}</h2>
        <p class="text-gray-500 mt-1">{{ $tr('بوابة المشتري — متابعة عقودك وأقساطك', 'Buyer portal — track your contracts and installments') }}</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $tr('أقساط مدفوعة', 'Paid Installments') }}</p>
                <p class="text-2xl font-bold text-green-700">{{ $stats['paid_installments'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $tr('من', 'Out of') }} {{ $stats['total_installments'] }} {{ $tr('قسط', 'installments') }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $tr('أقساط معلقة', 'Pending Installments') }}</p>
                <p class="text-2xl font-bold text-yellow-700">{{ $stats['pending_installments'] }}</p>
                @if($stats['overdue_installments'] > 0)
                <p class="text-xs text-red-500 mt-0.5">{{ $stats['overdue_installments'] }} {{ $tr('متأخرة', 'overdue') }}</p>
                @endif
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1"/></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $tr('إجمالي المدفوع', 'Total Paid') }}</p>
                <p class="text-xl font-bold text-blue-800">{{ number_format($stats['total_paid']) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $currency }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $tr('المتبقي', 'Remaining') }}</p>
                <p class="text-xl font-bold text-red-700">{{ number_format($stats['total_remaining']) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $currency }}</p>
            </div>
        </div>
    </div>

    {{-- Active Contract Banner --}}
    @if($activeContract)
    @php
        $unit     = $activeContract->unit;
        $property = $unit->property;
        $paid     = $activeContract->paidInstallments->count();
        $total    = $activeContract->installments->count();
        $progress = $total > 0 ? round(($paid / $total) * 100) : 0;
    @endphp
    <div class="bg-gradient-to-l from-blue-900 to-blue-700 text-white rounded-xl shadow p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-blue-200 text-xs mb-1">{{ $tr('عقد البيع النشط', 'Active Sale Contract') }} — {{ $activeContract->contract_number }}</p>
                <h3 class="text-xl font-bold">{{ $property->name ?? '—' }}</h3>
                <p class="text-blue-200 text-sm mt-1">
                    {{ $unit->unit_number ? $tr('وحدة', 'Unit') . ' ' . $unit->unit_number : $unit->typeLabel() ?? '' }}
                    · {{ $property->address ?? '' }}
                </p>
            </div>
            <div class="text-left md:text-right flex-shrink-0">
                <p class="text-blue-200 text-xs">{{ $tr('سعر البيع الإجمالي', 'Total Sale Price') }}</p>
                <p class="text-2xl font-bold text-yellow-300">{{ number_format($activeContract->total_price) }} {{ $currency }}</p>
                <p class="text-xs text-blue-300 mt-0.5">{{ $tr('دفعة أولى', 'Down Payment') }}: {{ number_format($activeContract->down_payment) }} {{ $currency }}</p>
            </div>
        </div>

        <div class="mt-4">
            <div class="flex justify-between text-xs text-blue-200 mb-1">
                <span>{{ $tr('تقدم السداد', 'Payment Progress') }}</span>
                <span>{{ $paid }} / {{ $total }} {{ $tr('قسط', 'installments') }} ({{ $progress }}%)</span>
            </div>
            <div class="w-full bg-blue-800 rounded-full h-2">
                <div class="bg-yellow-400 h-2 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 text-sm">
            <div>
                <p class="text-blue-300 text-xs mb-0.5">{{ $tr('تاريخ العقد', 'Contract Date') }}</p>
                <p class="font-semibold">{{ $activeContract->contract_date?->format('Y/m/d') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-blue-300 text-xs mb-0.5">{{ $tr('قيمة القسط', 'Installment Amount') }}</p>
                <p class="font-semibold">{{ number_format($activeContract->installment_amount) }} {{ $currency }}</p>
            </div>
            <div>
                <p class="text-blue-300 text-xs mb-0.5">{{ $tr('عدد الأقساط', 'Installments Count') }}</p>
                <p class="font-semibold">{{ $activeContract->installment_count }}</p>
            </div>
            <div>
                <p class="text-blue-300 text-xs mb-0.5">{{ $tr('حالة العقد', 'Contract Status') }}</p>
                <p class="font-semibold text-green-300">{{ $tr('نشط', 'Active') }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Next Installment Alert --}}
    @if($nextInstallment)
    @php $isOverdue = $nextInstallment->due_date->isPast(); @endphp
    <div class="mb-6 border rounded-xl p-4 flex items-center gap-4
        {{ $isOverdue ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200' }}">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
            {{ $isOverdue ? 'bg-red-100' : 'bg-yellow-100' }}">
            <svg class="w-5 h-5 {{ $isOverdue ? 'text-red-600' : 'text-yellow-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="font-semibold text-sm {{ $isOverdue ? 'text-red-800' : 'text-yellow-800' }}">
                {{ $isOverdue ? $tr('قسط متأخر!', 'Overdue installment!') : $tr('القسط القادم', 'Next installment') }}
            </p>
            <p class="text-xs {{ $isOverdue ? 'text-red-600' : 'text-yellow-700' }} mt-0.5">
                {{ $tr('القسط رقم', 'Installment #') }} {{ $nextInstallment->installment_number }}
                · {{ number_format($nextInstallment->amount) }} {{ $currency }}
                · {{ $tr('استحق في', 'Due on') }} {{ $nextInstallment->due_date->format('Y/m/d') }}
            </p>
        </div>
    </div>
    @endif

    {{-- Recent Installments --}}
    <div class="bg-white rounded-xl shadow">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">{{ $tr('جدول الأقساط', 'Installment Schedule') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs">
                    <tr>
                        <th class="px-4 py-3 text-right">{{ $tr('رقم القسط', 'Installment #') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('العقار', 'Property') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('المبلغ', 'Amount') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('تاريخ الاستحقاق', 'Due Date') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('تاريخ الدفع', 'Paid Date') }}</th>
                        <th class="px-4 py-3 text-right">{{ $tr('الحالة', 'Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentInstallments as $inst)
                    @php
                        $sc = ['paid'=>'bg-green-100 text-green-700','pending'=>'bg-yellow-100 text-yellow-700','overdue'=>'bg-red-100 text-red-700'];
                        $sl = $isAr
                            ? ['paid' => 'مدفوع', 'pending' => 'معلق', 'overdue' => 'متأخر']
                            : ['paid' => 'Paid', 'pending' => 'Pending', 'overdue' => 'Overdue'];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-700">{{ $inst->installment_number }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $inst->saleContract->unit->property->name ?? '—' }}</td>
                        <td class="px-4 py-3 font-semibold">{{ number_format($inst->amount) }} {{ $currency }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $inst->due_date->format('Y/m/d') }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $inst->paid_at ? $inst->paid_at->format('Y/m/d') : '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $sc[$inst->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $sl[$inst->status] ?? $inst->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-8 text-center text-gray-400">{{ $tr('لا توجد أقساط', 'No installments found') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
