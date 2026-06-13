@php
    $dir   = $isAr ? 'rtl' : 'ltr';
    $t     = fn(string $en, string $ar) => $isAr ? $ar : $en;
    $start = $isAr ? 'right' : 'left';
    $end   = $isAr ? 'left'  : 'right';

    $dateFormat  = $isAr ? 'd/m/Y' : 'd M Y';
    $currency    = $isAr ? 'ر.ع' : 'OMR';
    $companyName = $isAr ? 'شركة ثروة للتطوير العقاري' : str_replace('_', ' ', ucwords(config('app.name'), '_'));

    $logoPath   = public_path('img/logo.png');
    $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

    $isDeposit = ($payment->type ?? 'rent') === 'deposit';
    $contract  = $payment->rentalContract;
    $unit      = $contract?->unit;
    $property  = $unit?->property;

    $tenantName  = $tenant->user->name ?? '-';
    $tenantEmail = $tenant->user->email ?? null;
    $tenantPhone = $tenant->user->phone ?? $tenant->phone ?? null;

    $statusColors = [
        'pending' => 'badge-pending',
        'paid'    => 'badge-paid',
        'overdue' => 'badge-overdue',
    ];
    $statusLabels = [
        'pending' => $t('PENDING', 'معلق'),
        'paid'    => $t('PAID',    'مدفوع'),
        'overdue' => $t('OVERDUE', 'متأخر'),
    ];
    $statusClass = $statusColors[$payment->status] ?? 'badge-pending';
    $statusLabel = $statusLabels[$payment->status] ?? strtoupper($payment->status);

    $months = [
        1  => $t('January',   'يناير'),
        2  => $t('February',  'فبراير'),
        3  => $t('March',     'مارس'),
        4  => $t('April',     'أبريل'),
        5  => $t('May',       'مايو'),
        6  => $t('June',      'يونيو'),
        7  => $t('July',      'يوليو'),
        8  => $t('August',    'أغسطس'),
        9  => $t('September', 'سبتمبر'),
        10 => $t('October',   'أكتوبر'),
        11 => $t('November',  'نوفمبر'),
        12 => $t('December',  'ديسمبر'),
    ];
    $monthName = $months[$payment->month] ?? $payment->month;
    $period    = $monthName . ' ' . $payment->year;

    $invoiceNo    = '#INV-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT);
    $invoiceTitle = $isDeposit ? $t('Security Deposit', 'تأمين') : $t('INVOICE', 'فاتورة');
@endphp
<!DOCTYPE html>
<html lang="{{ $isAr ? 'ar' : 'en' }}" dir="{{ $dir }}">
<head>
<meta charset="UTF-8">
<title>{{ $t('Invoice', 'فاتورة') }} {{ $invoiceNo }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 11px;
        color: #1e293b;
        background: #fff;
        direction: {{ $dir }};
    }

    .page { padding: 0; }

    /* ── Header ── */
    .hdr { background: #1e3a5f; color: #fff; padding: 22px 36px; }
    .hdr-tbl { width: 100%; border-collapse: collapse; }
    .hdr-tbl td { vertical-align: middle; padding: 0; }
    .hdr-logo { width: 52px; height: auto; background: #fff; border-radius: 5px; padding: 3px; }
    .hdr-co-name { font-size: 13px; font-weight: bold; color: #fff; }
    .hdr-co-sub  { font-size: 9px; color: #93c5fd; margin-top: 2px; }
    .hdr-word    { font-size: 30px; font-weight: bold; color: #fff; text-align: {{ $end }}; letter-spacing: 1px; }
    .hdr-num     { font-size: 10px; color: #93c5fd; text-align: {{ $end }}; margin-top: 4px; }

    /* ── Status strip ── */
    .status-strip { background: #f1f5f9; border-bottom: 1px solid #e2e8f0; padding: 8px 36px; }
    .status-strip-tbl { width: 100%; border-collapse: collapse; }
    .badge { display: inline-block; padding: 3px 12px; font-size: 10px; font-weight: bold; border-radius: 3px; border: 1px solid; }
    .badge-pending { background: #fef9c3; color: #92400e; border-color: #fde68a; }
    .badge-paid    { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
    .badge-overdue { background: #fee2e2; color: #991b1b; border-color: #fecaca; }

    /* ── Body ── */
    .body { padding: 28px 36px; }

    /* ── Meta boxes ── */
    .meta-tbl { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    .meta-tbl td { vertical-align: top; padding: 0; }
    .meta-gap  { width: 14px; }
    .meta-box { border: 1px solid #e2e8f0; border-radius: 4px; padding: 14px 16px; background: #f8fafc; }
    .meta-box-title {
        font-size: 8.5px; font-weight: bold; color: #94a3b8; text-transform: uppercase;
        letter-spacing: .6px; border-bottom: 1px solid #e8edf3; padding-bottom: 6px; margin-bottom: 10px;
    }
    .meta-name { font-size: 14px; font-weight: bold; color: #0f172a; margin-bottom: 6px; }
    .meta-line { font-size: 11px; color: #475569; margin-bottom: 5px; }
    .meta-line b { color: #0f172a; }

    /* ── Items table ── */
    .items { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
    .items thead tr { background: #1e3a5f; }
    .items thead th {
        color: #fff; font-size: 9.5px; font-weight: bold; text-transform: uppercase;
        letter-spacing: .5px; padding: 11px 14px; text-align: {{ $start }}; border: none;
    }
    .items thead th.col-end { text-align: {{ $end }}; }
    .items tbody tr { border-bottom: 1px solid #f1f5f9; }
    .items tbody td { padding: 14px; font-size: 11px; color: #334155; border: none; text-align: {{ $start }}; }
    .items tbody td.col-end { text-align: {{ $end }}; font-weight: bold; color: #0f172a; font-size: 12px; }
    .item-title { font-weight: bold; color: #0f172a; font-size: 12px; }
    .item-sub   { font-size: 9.5px; color: #64748b; margin-top: 3px; }

    /* ── Totals ── */
    .totals-wrap { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    .totals-wrap td { vertical-align: top; padding: 0; }
    .totals-box { width: 44%; }
    .totals-inner { width: 100%; border-collapse: collapse; border: 1px solid #e2e8f0; border-radius: 4px; }
    .totals-inner td { padding: 10px 16px; font-size: 11px; border-bottom: 1px solid #f1f5f9; }
    .totals-inner td.lbl { color: #64748b; text-align: {{ $start }}; white-space: nowrap; }
    .totals-inner td.val { text-align: {{ $end }}; font-weight: bold; color: #1e293b; }
    .total-row td { background: #1e3a5f !important; color: #fff !important; font-size: 13px; font-weight: bold; border-bottom: none !important; white-space: nowrap; }
    .total-row td.val { color: #fff !important; }

    /* ── Notes ── */
    .notes {
        border: 1px solid #e2e8f0; border-radius: 4px;
        border-{{ $start }}: 3px solid #3b82f6;
        padding: 13px 16px; margin-bottom: 28px; background: #f8fafc;
    }
    .notes-title { font-size: 8.5px; font-weight: bold; text-transform: uppercase; color: #94a3b8; letter-spacing: .5px; margin-bottom: 7px; }
    .notes-text  { font-size: 11px; color: #475569; line-height: 1.9; }

    /* ── Footer ── */
    .footer-bar { height: 2px; background: #1e3a5f; margin-bottom: 10px; }
    .footer-tbl { width: 100%; border-collapse: collapse; }
    .footer-tbl td { font-size: 9.5px; color: #94a3b8; padding: 0; vertical-align: middle; }
    .footer-thanks { font-size: 11px; font-weight: bold; color: #1e3a5f; text-align: {{ $start }}; }
    .footer-right  { text-align: {{ $end }}; }
</style>
</head>
<body>
<div class="page">

{{-- ══ HEADER ══ --}}
<div class="hdr">
    <table class="hdr-tbl">
        <tr>
            <td style="width:58px;">
                @if($logoBase64)
                    <img src="data:image/png;base64,{{ $logoBase64 }}" class="hdr-logo">
                @endif
            </td>
            <td style="padding-{{ $start }}: 12px;">
                <div class="hdr-co-name">{{ $companyName }}</div>
                <div class="hdr-co-sub">{{ $t('Real Estate & Property Management', 'إدارة العقارات والممتلكات') }}</div>
            </td>
            <td style="width:160px;">
                <div class="hdr-word">{{ $invoiceTitle }}</div>
                <div class="hdr-num">{{ $invoiceNo }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ══ STATUS STRIP ══ --}}
<div class="status-strip">
    <table class="status-strip-tbl">
        <tr>
            <td style="text-align: {{ $start }};">
                <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                @if($payment->paid_at)
                    <span style="font-size:10px; color:#64748b; margin-{{ $start }}: 10px;">
                        {{ $t('Paid on:', 'تاريخ الدفع:') }} {{ $payment->paid_at->format($dateFormat) }}
                    </span>
                @endif
            </td>
            <td style="text-align: {{ $end }}; font-size:10px; color:#64748b;">
                {{ $t('Issue Date:', 'تاريخ الإصدار:') }} {{ now()->format($dateFormat) }}
                &nbsp;&bull;&nbsp;
                {{ $t('Period:', 'الفترة:') }} <b style="color:#0f172a;">{{ $period }}</b>
            </td>
        </tr>
    </table>
</div>

<div class="body">

{{-- ══ META ══ --}}
<table class="meta-tbl">
    <tr>
        <td style="width:48%;">
            <div class="meta-box">
                <div class="meta-box-title">{{ $t('Billed To', 'فاتورة إلى') }}</div>
                <div class="meta-name">{{ $tenantName }}</div>
                @if($tenantEmail)
                    <div class="meta-line">{{ $tenantEmail }}</div>
                @endif
                @if($tenantPhone)
                    <div class="meta-line" style="color:#64748b;">{{ $tenantPhone }}</div>
                @endif
                @if($tenant->national_id)
                    <div class="meta-line">{{ $t('ID:', 'الهوية:') }} <b>{{ $tenant->national_id }}</b></div>
                @endif
            </div>
        </td>
        <td class="meta-gap"></td>
        <td style="width:48%;">
            <div class="meta-box">
                <div class="meta-box-title">{{ $t('Invoice Details', 'تفاصيل الفاتورة') }}</div>
                <div class="meta-line">{{ $t('Invoice No.:', 'رقم الفاتورة:') }} <b>{{ $invoiceNo }}</b></div>
                <div class="meta-line">{{ $t('Issue Date:', 'تاريخ الإصدار:') }} <b>{{ now()->format($dateFormat) }}</b></div>
                <div class="meta-line">{{ $t('Billing Period:', 'فترة الفوترة:') }} <b>{{ $period }}</b></div>
                @if($property)
                    <div class="meta-line">{{ $t('Property:', 'العقار:') }} <b>{{ $property->name }}</b></div>
                @endif
                @if($unit)
                    <div class="meta-line">{{ $t('Unit:', 'الوحدة:') }} <b>{{ $unit->unit_number ?? '-' }}</b></div>
                @endif
            </div>
        </td>
    </tr>
</table>

{{-- ══ ITEMS ══ --}}
<table class="items">
    <thead>
        <tr>
            <th style="width:50%;">{{ $t('Description', 'الوصف') }}</th>
            <th style="width:25%;">{{ $t('Period', 'الفترة') }}</th>
            <th class="col-end" style="width:25%;">{{ $t('Amount', 'المبلغ') }}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                @if($isDeposit)
                    <div class="item-title">{{ $t('Security Deposit', 'مبلغ التأمين') }}</div>
                @else
                    <div class="item-title">{{ $t('Monthly Rent', 'الإيجار الشهري') }}</div>
                @endif
                @if($property)
                    <div class="item-sub">{{ $property->name }}{{ $unit ? ' — ' . $t('Unit', 'وحدة') . ' ' . $unit->unit_number : '' }}</div>
                @endif
            </td>
            <td>{{ $isDeposit ? $t('One-time', 'دفعة واحدة') : $period }}</td>
            <td class="col-end">{{ number_format($payment->amount, 2) }} {{ $currency }}</td>
        </tr>
    </tbody>
</table>

{{-- ══ TOTALS ══ --}}
<table class="totals-wrap">
    <tr>
        @if(!$isAr)<td style="width:56%;"></td>@endif
        <td class="totals-box">
            <table class="totals-inner">
                <tr>
                    <td class="lbl">{{ $isDeposit ? $t('Deposit Amount', 'مبلغ التأمين') : $t('Rent Amount', 'مبلغ الإيجار') }}</td>
                    <td class="val">{{ number_format($payment->amount, 2) }} {{ $currency }}</td>
                </tr>
                <tr>
                    <td class="lbl">{{ $t('Tax', 'الضريبة') }}</td>
                    <td class="val" style="color:#94a3b8; font-weight:normal;">{{ $t('N/A', 'لا ينطبق') }}</td>
                </tr>
                <tr class="total-row">
                    <td class="lbl">{{ $t('Total Due', 'الإجمالي المستحق') }}</td>
                    <td class="val">{{ number_format($payment->amount, 2) }} {{ $currency }}</td>
                </tr>
            </table>
        </td>
        @if($isAr)<td style="width:56%;"></td>@endif
    </tr>
</table>

{{-- ══ NOTES ══ --}}
<div class="notes">
    <div class="notes-title">{{ $t('Payment Terms & Notes', 'شروط الدفع والملاحظات') }}</div>
    <div class="notes-text">
        @if($payment->status === 'paid')
            @if($isAr)
                تم استلام مبلغ <b>{{ number_format($payment->amount, 2) }} {{ $currency }}</b>
                بتاريخ <b>{{ $payment->paid_at?->format($dateFormat) }}</b>. شكراً لالتزامكم بالسداد في الموعد المحدد.
            @else
                Payment of <b>{{ number_format($payment->amount, 2) }} {{ $currency }}</b>
                received on <b>{{ $payment->paid_at?->format($dateFormat) }}</b>. Thank you for your prompt payment.
            @endif
        @elseif($isDeposit)
            @if($isAr)
                يرجى سداد مبلغ التأمين <b>{{ number_format($payment->amount, 2) }} {{ $currency }}</b>
                عند توقيع العقد. مبلغ التأمين قابل للاسترداد عند انتهاء مدة الإيجار وفقاً لشروط العقد.
            @else
                Please arrange payment of the security deposit <b>{{ number_format($payment->amount, 2) }} {{ $currency }}</b>
                upon signing the contract. The deposit is refundable at the end of the tenancy subject to contract terms.
            @endif
        @else
            @if($isAr)
                يرجى سداد مبلغ <b>{{ number_format($payment->amount, 2) }} {{ $currency }}</b>
                الخاص بشهر <b>{{ $period }}</b> في أقرب وقت ممكن لتجنب أي غرامات تأخير.
                للاستفسار يرجى التواصل مع مكتب الإدارة.
            @else
                Please arrange payment of <b>{{ number_format($payment->amount, 2) }} {{ $currency }}</b>
                for the period <b>{{ $period }}</b> at your earliest convenience to avoid late charges.
                For inquiries, please contact the management office.
            @endif
        @endif
        @if($payment->notes)
            <br><br><b>{{ $t('Notes:', 'ملاحظات:') }}</b> {{ $payment->notes }}
        @endif
    </div>
</div>

{{-- ══ FOOTER ══ --}}
<div class="footer-bar"></div>
<table class="footer-tbl">
    <tr>
        <td class="footer-thanks">{{ $t('Thank you for your cooperation', 'شكراً لتعاونكم') }}</td>
        <td class="footer-right">
            {{ $t('Official authorized document', 'وثيقة رسمية معتمدة') }}
            &nbsp;&middot;&nbsp;
            {{ $companyName }}
        </td>
    </tr>
</table>

</div>
</div>
</body>
</html>
