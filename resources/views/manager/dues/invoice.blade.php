@php
    $isAr  = app()->getLocale() === 'ar';
    $dir   = $isAr ? 'rtl' : 'ltr';
    $t     = fn(string $en, string $ar) => $isAr ? $ar : $en;
    $start = $isAr ? 'right' : 'left';
    $end   = $isAr ? 'left'  : 'right';

    $statusLabels = [
        'pending' => $t('PENDING', 'معلق'),
        'paid'    => $t('PAID',    'مدفوع'),
        'overdue' => $t('OVERDUE', 'متأخر'),
        'waived'  => $t('WAIVED',  'معفو عنه'),
    ];
    $statusLabel = $statusLabels[$due->status] ?? strtoupper($due->status);
    $dateFormat  = $isAr ? 'd/m/Y' : 'd M Y';

    $logoPath   = public_path('img/logo.png');
    $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
    $companyName = $isAr ? 'شركة ثروة للتطوير العقاري' : str_replace('_', ' ', ucwords(config('app.name'), '_'));
@endphp
<!DOCTYPE html>
<html lang="{{ $isAr ? 'ar' : 'en' }}" dir="{{ $dir }}">
<head>
<meta charset="UTF-8">
<title>{{ $t('Invoice', 'فاتورة') }} #INV-{{ str_pad($due->id, 6, '0', STR_PAD_LEFT) }}</title>
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

    /* ── Header bar ── */
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
    .badge-waived  { background: #f1f5f9; color: #475569; border-color: #cbd5e1; }

    /* ── Body wrapper ── */
    .body { padding: 28px 36px; }

    /* ── Meta section ── */
    .meta-tbl { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    .meta-tbl td { vertical-align: top; padding: 0; }
    .meta-gap  { width: 14px; }

    .meta-box { border: 1px solid #e2e8f0; border-radius: 4px; padding: 14px 16px; background: #f8fafc; }
    .meta-box-title {
        font-size: 8.5px; font-weight: bold; color: #94a3b8; text-transform: uppercase;
        letter-spacing: .6px; border-bottom: 1px solid #e8edf3; padding-bottom: 6px; margin-bottom: 10px;
    }
    .meta-line { font-size: 11px; color: #475569; margin-bottom: 5px; }
    .meta-line b { color: #0f172a; }
    .meta-name { font-size: 14px; font-weight: bold; color: #0f172a; margin-bottom: 6px; }

    /* ── Divider ── */
    .divider { height: 1px; background: #e2e8f0; margin-bottom: 20px; }

    /* ── Items table ── */
    .items { width: 100%; border-collapse: collapse; margin-bottom: 22px; border-radius: 4px; overflow: hidden; }
    .items thead tr { background: #1e3a5f; }
    .items thead th {
        color: #fff; font-size: 9.5px; font-weight: bold; text-transform: uppercase;
        letter-spacing: .5px; padding: 11px 14px; text-align: {{ $start }}; border: none;
    }
    .items thead th.col-end { text-align: {{ $end }}; }
    .items tbody tr { border-bottom: 1px solid #f1f5f9; }
    .items tbody tr:last-child { border-bottom: none; }
    .items tbody td {
        padding: 14px 14px; font-size: 11px; color: #334155; border: none;
        text-align: {{ $start }};
    }
    .items tbody td.col-end { text-align: {{ $end }}; font-weight: bold; color: #0f172a; font-size: 12px; }
    .item-title { font-weight: bold; color: #0f172a; font-size: 12px; }
    .item-sub   { font-size: 9.5px; color: #64748b; margin-top: 3px; }
    .unit-lbl   { font-size: 9px; color: #94a3b8; }

    /* ── Totals ── */
    .totals-wrap { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    .totals-wrap td { vertical-align: top; padding: 0; }

    .totals-box  { width: 44%; }
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
            {{-- Logo + Company (appears on RIGHT in RTL, LEFT in LTR) --}}
            <td style="width:58px;">
                @if($logoBase64)
                    <img src="data:image/png;base64,{{ $logoBase64 }}" class="hdr-logo">
                @endif
            </td>
            <td style="padding-{{ $start }}: 12px;">
                <div class="hdr-co-name">{{ $companyName }}</div>
                <div class="hdr-co-sub">{{ $t('Real Estate & Property Management', 'إدارة العقارات والممتلكات') }}</div>
            </td>
            {{-- Invoice word (appears on LEFT in RTL, RIGHT in LTR) --}}
            <td style="width:160px;">
                <div class="hdr-word">{{ $t('INVOICE', 'فاتورة') }}</div>
                <div class="hdr-num">#INV-{{ str_pad($due->id, 6, '0', STR_PAD_LEFT) }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ══ STATUS STRIP ══ --}}
<div class="status-strip">
    <table class="status-strip-tbl">
        <tr>
            <td style="text-align: {{ $start }};">
                <span class="badge badge-{{ $due->status }}">{{ $statusLabel }}</span>
                @if($due->paid_at)
                    <span style="font-size:10px; color:#64748b; margin-{{ $start }}: 10px;">
                        {{ $t('Paid on:', 'تاريخ الدفع:') }} {{ $due->paid_at->format($dateFormat) }}
                    </span>
                @endif
            </td>
            <td style="text-align: {{ $end }}; font-size:10px; color:#64748b;">
                {{ $t('Issue Date:', 'تاريخ الإصدار:') }} {{ now()->format($dateFormat) }}
                &nbsp;&bull;&nbsp;
                {{ $t('Due Date:', 'تاريخ الاستحقاق:') }} <b style="color:#0f172a;">{{ $due->due_date->format($dateFormat) }}</b>
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
                <div class="meta-name">{{ $ownerName }}</div>
                <div class="meta-line">{{ $propertyName }}</div>
                @if($ownerPhone)
                    <div class="meta-line" style="color:#64748b;">{{ $ownerPhone }}</div>
                @endif
            </div>
        </td>
        <td class="meta-gap"></td>
        <td style="width:48%;">
            <div class="meta-box">
                <div class="meta-box-title">{{ $t('Invoice Details', 'تفاصيل الفاتورة') }}</div>
                <div class="meta-line">{{ $t('Invoice No.:', 'رقم الفاتورة:') }} <b>#INV-{{ str_pad($due->id, 6, '0', STR_PAD_LEFT) }}</b></div>
                <div class="meta-line">{{ $t('Issue Date:', 'تاريخ الإصدار:') }} {{ now()->format($dateFormat) }}</div>
                <div class="meta-line">{{ $t('Due Date:', 'تاريخ الاستحقاق:') }} <b>{{ $due->due_date->format($dateFormat) }}</b></div>
            </div>
        </td>
    </tr>
</table>

{{-- ══ ITEMS ══ --}}
<table class="items">
    <thead>
        <tr>
            <th style="width:44%;">{{ $t('Description', 'الوصف') }}</th>
            <th style="width:22%;">{{ $t('Period', 'الفترة') }}</th>
            <th style="width:12%;">{{ $t('Units', 'الوحدات') }}</th>
            <th class="col-end" style="width:22%;">{{ $t('Amount', 'المبلغ') }}</th>
        </tr>
    </thead>
    <tbody>
        @php
            $feeLabel = ($feeFrequency ?? 'monthly') === 'yearly' ? $t('Yearly Fee', 'رسوم سنوية') : $t('Monthly Fee', 'رسوم شهرية');
            $hasPerUnitFees = count($unitNumbers) > 1 && !empty($unitFees);
            $uniqueFeeCount = $hasPerUnitFees ? count(array_unique(array_map(fn($u) => $unitFees[$u] ?? $defaultFee, $unitNumbers))) : 1;
            $showBreakdown  = $hasPerUnitFees && $uniqueFeeCount > 1;
        @endphp
        @if($showBreakdown)
            @foreach($unitNumbers as $uNum)
                @php $uFee = (float) ($unitFees[$uNum] ?? $defaultFee); @endphp
                <tr>
                    <td>
                        <div class="item-title">{{ $t("Owners' Association Dues", 'اشتراكات جمعية الملاك') }}</div>
                        @if($associationName)
                            <div class="item-sub">{{ $associationName }}</div>
                        @endif
                        <div class="item-sub">{{ $feeLabel }}</div>
                    </td>
                    <td>{{ $due->periodLabel() }}</td>
                    <td>
                        {{ $uNum }}
                        <span class="unit-lbl">{{ $t('unit', 'وحدة') }}</span>
                    </td>
                    <td class="col-end">{{ number_format($uFee, 2) }} {{ $currency }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td>
                    <div class="item-title">{{ $t("Owners' Association Dues", 'اشتراكات جمعية الملاك') }}</div>
                    @if($associationName)
                        <div class="item-sub">{{ $associationName }}</div>
                    @endif
                    <div class="item-sub">{{ $feeLabel }}</div>
                </td>
                <td>{{ $due->periodLabel() }}</td>
                <td>
                    {{ $unitCount }}
                    <span class="unit-lbl">{{ $t($unitCount === 1 ? 'unit' : 'units', $unitCount === 1 ? 'وحدة' : 'وحدات') }}</span>
                </td>
                <td class="col-end">{{ number_format($due->amount, 2) }} {{ $currency }}</td>
            </tr>
        @endif
    </tbody>
</table>

{{-- ══ TOTALS ══ --}}
<table class="totals-wrap">
    <tr>
        @if(!$isAr)<td style="width:56%;"></td>@endif
        <td class="totals-box">
            <table class="totals-inner">
                <tr>
                    <td class="lbl">{{ $t('Subtotal', 'المجموع الفرعي') }}</td>
                    <td class="val">{{ number_format($due->amount, 2) }} {{ $currency }}</td>
                </tr>
                <tr>
                    <td class="lbl">{{ $t('Tax', 'الضريبة') }}</td>
                    <td class="val" style="color:#94a3b8; font-weight:normal;">{{ $t('N/A', 'لا ينطبق') }}</td>
                </tr>
                <tr class="total-row">
                    <td class="lbl">{{ $t('Total Due', 'الإجمالي المستحق') }}</td>
                    <td class="val">{{ number_format($due->amount, 2) }} {{ $currency }}</td>
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
        @if($isAr)
            يرجى سداد مبلغ <b>{{ number_format($due->amount, 2) }} {{ $currency }}</b>
            قبل تاريخ الاستحقاق <b>{{ $due->due_date->format($dateFormat) }}</b>
            لتجنب أي رسوم تأخير. للاستفسار، يرجى التواصل مع مكتب إدارة الجمعية.
        @else
            Please arrange payment of <b>{{ number_format($due->amount, 2) }} {{ $currency }}</b>
            before the due date of <b>{{ $due->due_date->format($dateFormat) }}</b>
            to avoid any late charges. For inquiries, please contact the association management office.
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
