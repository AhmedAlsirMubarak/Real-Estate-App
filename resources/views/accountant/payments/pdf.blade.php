<!DOCTYPE html>
@php
    $isAr = app()->getLocale() === 'ar';
    $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    $dir = $isAr ? 'rtl' : 'ltr';
    $align = $isAr ? 'right' : 'left';
    $currency = $isAr ? 'ر.س' : 'SAR';
    $months = $isAr
        ? ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر']
        : ['January','February','March','April','May','June','July','August','September','October','November','December'];
@endphp
<html lang="{{ $isAr ? 'ar' : 'en' }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <title>{{ $tr('تقرير المدفوعات', 'Payments Report') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; direction: {{ $dir }}; font-size: 12px; color: #1a1a1a; background: #fff; }
        .header { background: #1e3a8a; color: white; padding: 20px 30px; margin-bottom: 24px; }
        .header h1 { font-size: 22px; font-weight: bold; margin-bottom: 4px; }
        .header p { font-size: 13px; opacity: 0.85; }
        .container { padding: 0 30px 30px; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; }
        .stat-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; text-align: center; }
        .stat-card .label { font-size: 11px; color: #64748b; margin-bottom: 6px; }
        .stat-card .value { font-size: 18px; font-weight: bold; color: #1e3a8a; }
        .section-title { font-size: 15px; font-weight: bold; color: #1e3a8a; border-bottom: 2px solid #1e3a8a; padding-bottom: 6px; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th { background: #1e3a8a; color: white; padding: 9px 12px; text-align: {{ $align }}; font-size: 11px; }
        td { padding: 8px 12px; border-bottom: 1px solid #f1f5f9; font-size: 11px; }
        tr:nth-child(even) td { background: #f8fafc; }
        .badge { padding: 2px 8px; border-radius: 12px; font-size: 10px; display: inline-block; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-green  { background: #dcfce7; color: #166534; }
        .badge-red    { background: #fee2e2; color: #991b1b; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tr('شركة ثروة للعقارات — تقرير المدفوعات', 'Tharwa Real Estate — Payments Report') }}</h1>
        <p>
            {{ $tr('السنة', 'Year') }}: {{ $year }}
            @if($month)
            | {{ $tr('الشهر', 'Month') }}: {{ $months[$month-1] ?? $month }}
            @endif
            @if($status && $status !== 'all')
            | {{ $tr('الحالة', 'Status') }}: {{ ($isAr ? ['pending' => 'معلق', 'paid' => 'مدفوع', 'overdue' => 'متأخر'] : ['pending' => 'Pending', 'paid' => 'Paid', 'overdue' => 'Overdue'])[$status] ?? $status }}
            @endif
            | {{ $tr('تاريخ الإنشاء', 'Generated At') }}: {{ now()->format('Y/m/d H:i') }}
        </p>
    </div>

    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">{{ $tr('إجمالي الإيجارات', 'Total Rents') }}</div>
                <div class="value">{{ $totals['count'] }}</div>
            </div>
            <div class="stat-card">
                <div class="label">{{ $tr('المدفوعات', 'Paid') }} ({{ $currency }})</div>
                <div class="value" style="color:#166534">{{ number_format($totals['paid']) }}</div>
            </div>
            <div class="stat-card">
                <div class="label">{{ $tr('المعلقة', 'Pending') }} ({{ $currency }})</div>
                <div class="value" style="color:#854d0e">{{ number_format($totals['pending']) }}</div>
            </div>
            <div class="stat-card">
                <div class="label">{{ $tr('المتأخرة', 'Overdue') }} ({{ $currency }})</div>
                <div class="value" style="color:#991b1b">{{ number_format($totals['overdue']) }}</div>
            </div>
        </div>

        <div class="section-title">{{ $tr('تفاصيل المدفوعات', 'Payment Details') }}</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ $tr('المستأجر', 'Tenant') }}</th>
                    <th>{{ $tr('المبنى / الوحدة', 'Property / Unit') }}</th>
                    <th>{{ $tr('المبلغ', 'Amount') }} ({{ $currency }})</th>
                    <th>{{ $tr('الشهر / السنة', 'Month / Year') }}</th>
                    <th>{{ $tr('الحالة', 'Status') }}</th>
                    <th>{{ $tr('تاريخ الدفع', 'Payment Date') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $i => $pay)
                @php
                    $pc=['pending'=>'badge-yellow','paid'=>'badge-green','overdue'=>'badge-red'];
                    $pl = $isAr
                        ? ['pending' => 'معلق', 'paid' => 'مدفوع', 'overdue' => 'متأخر']
                        : ['pending' => 'Pending', 'paid' => 'Paid', 'overdue' => 'Overdue'];
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $pay->tenant->user->name ?? '-' }}</td>
                    <td>{{ $pay->rentalContract->unit->property->name ?? '-' }} / {{ $pay->rentalContract->unit->unit_number ?? '-' }}</td>
                    <td>{{ number_format($pay->amount) }}</td>
                    <td>{{ $months[($pay->month)-1] ?? $pay->month }} {{ $pay->year }}</td>
                    <td><span class="badge {{ $pc[$pay->status] ?? 'badge-gray' }}">{{ $pl[$pay->status] ?? $pay->status }}</span></td>
                    <td>{{ $pay->paid_at ? $pay->paid_at->format('Y/m/d') : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center; color:#94a3b8; padding:16px;">{{ $tr('لا توجد مدفوعات', 'No payments found') }}</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            {{ $tr('شركة ثروة للعقارات', 'Tharwa Real Estate') }} &mdash; {{ $tr('تم إنشاء هذا التقرير في', 'This report was generated at') }} {{ now()->format('Y/m/d H:i') }}
        </div>
    </div>
</body>
</html>
