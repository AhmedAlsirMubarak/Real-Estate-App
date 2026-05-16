<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<meta charset="UTF-8">
<title>Invoice #INV-{{ str_pad($due->id, 6, '0', STR_PAD_LEFT) }}</title>
<style>
    * { margin: 0; padding: 0; }

    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        color: #1e293b;
        background: #ffffff;
    }

    .page { padding: 38px 46px; }

    /* ── Header ── */
    .header-table { width: 100%; border-collapse: collapse; }
    .header-table td { vertical-align: middle; padding: 0; }
    .company-name { font-size: 19px; font-weight: bold; color: #1e3a5f; }
    .company-sub  { font-size: 10px; color: #64748b; margin-top: 3px; }
    .invoice-word { font-size: 28px; font-weight: bold; color: #1e3a5f; text-align: right; }
    .invoice-num  { font-size: 11px; color: #64748b; text-align: right; margin-top: 5px; }

    /* ── Accent bars ── */
    .bar-thick { height: 3px; background: #1e3a5f; margin-top: 12px; }
    .bar-thin  { height: 1px; background: #e2e8f0; margin-bottom: 20px; }

    /* ── Meta cards (3 columns via table) ── */
    .meta-outer { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
    .meta-outer td { vertical-align: top; padding: 0; }
    .meta-gap { width: 10px; }

    .meta-card { padding: 12px 14px; background: #f8fafc; border: 1px solid #dde3ec; }
    .meta-label {
        font-size: 9px; font-weight: bold; color: #94a3b8;
        text-transform: uppercase;
        border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 8px;
    }
    .meta-val { font-size: 12px; color: #334155; line-height: 1.8; }
    .meta-val b { color: #0f172a; }

    /* ── Status badges ── */
    .badge { padding: 2px 10px; font-size: 10px; font-weight: bold; border: 1px solid; }
    .badge-pending { background: #fef9c3; color: #92400e; border-color: #fde68a; }
    .badge-paid    { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
    .badge-overdue { background: #fee2e2; color: #991b1b; border-color: #fecaca; }
    .badge-waived  { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }

    /* ── Items table ── */
    .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .items-table thead tr { background: #1e3a5f; }
    .items-table thead th {
        color: #ffffff; font-size: 10px; font-weight: bold;
        text-transform: uppercase; padding: 10px 14px; text-align: left; border: none;
    }
    .items-table thead th.r { text-align: right; }
    .items-table tbody tr { border-bottom: 1px solid #e2e8f0; }
    .items-table tbody td { padding: 13px 14px; font-size: 12px; color: #334155; border: none; }
    .items-table tbody td.r { text-align: right; font-weight: bold; color: #0f172a; }
    .desc-title { font-weight: bold; color: #0f172a; }
    .desc-sub   { font-size: 10px; color: #64748b; margin-top: 2px; }

    /* ── Totals ── */
    .totals-wrap { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
    .totals-wrap td { padding: 0; vertical-align: top; }
    .totals-spacer { width: 54%; }

    .totals-box { width: 46%; }
    .totals-inner { width: 100%; border-collapse: collapse; border: 1px solid #e2e8f0; }
    .totals-inner td { padding: 9px 15px; font-size: 12px; border-bottom: 1px solid #e2e8f0; }
    .totals-inner td.lbl { color: #64748b; }
    .totals-inner td.val { text-align: right; font-weight: bold; color: #1e293b; }
    .totals-inner tr.total-row td { background: #1e3a5f; color: #ffffff; font-size: 14px; font-weight: bold; border-bottom: none; }
    .totals-inner tr.total-row td.val { color: #ffffff; }

    /* ── Notes ── */
    .notes { border: 1px solid #e2e8f0; border-left: 3px solid #3b82f6; padding: 12px 15px; margin-bottom: 26px; background: #f8fafc; }
    .notes-title { font-size: 9px; font-weight: bold; text-transform: uppercase; color: #94a3b8; margin-bottom: 6px; }
    .notes-text  { font-size: 11px; color: #475569; line-height: 1.8; }

    /* ── Footer ── */
    .footer-bar   { height: 2px; background: #1e3a5f; margin-bottom: 12px; }
    .footer-table { width: 100%; border-collapse: collapse; }
    .footer-table td { font-size: 10px; color: #94a3b8; padding: 0; vertical-align: middle; }
    .footer-left  { font-size: 12px; font-weight: bold; color: #1e3a5f; }
    .footer-right { text-align: right; }

    /* ── Print button ── */
    /* .print-wrap { text-align: center; padding: 28px 0 4px; }
    .print-btn {
        background: #1e3a5f; color: #ffffff;
        padding: 10px 32px; border: none;
        font-size: 13px; font-weight: bold; cursor: pointer;
        font-family: Arial, sans-serif;
    } */

    @media print { .no-print { display: none !important; } }
</style>
</head>
<body>
<div class="page">

{{-- ══ HEADER ══ --}}
<table class="header-table">
    <tr>
        <td style="width:68px;">
            @php $logoPath = public_path('img/logo.png'); @endphp
            @if(file_exists($logoPath))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}"
                     style="width:56px; height:auto;" alt="Logo">
            @endif
        </td>
        <td style="padding-left:14px;">
            <div class="company-name">{{ str_replace('_', ' ', ucwords(config('app.name'), '_')) }}</div>
            <div class="company-sub">Real Estate &amp; Property Management</div>
        </td>
        <td style="width:160px; text-align:right;">
            <div class="invoice-word">INVOICE</div>
            <div class="invoice-num">#INV-{{ str_pad($due->id, 6, '0', STR_PAD_LEFT) }}</div>
        </td>
    </tr>
</table>

<div class="bar-thick"></div>
<div class="bar-thin"></div>

{{-- ══ META ══ --}}
<table class="meta-outer">
    <tr>
        <td style="width:33%;">
            <div class="meta-card">
                <div class="meta-label">Billed To</div>
                <div class="meta-val">
                    <b>{{ $ownerName }}</b><br>
                    {{ $propertyName }}<br>
                    @if($ownerPhone){{ $ownerPhone }}@endif
                </div>
            </div>
        </td>
        <td class="meta-gap"></td>
        <td style="width:34%;">
            <div class="meta-card">
                <div class="meta-label">Invoice Details</div>
                <div class="meta-val">
                    Invoice No.: <b>#INV-{{ str_pad($due->id, 6, '0', STR_PAD_LEFT) }}</b><br>
                    Issue Date: {{ now()->format('d M Y') }}<br>
                    Due Date: {{ $due->due_date->format('d M Y') }}
                </div>
            </div>
        </td>
        <td class="meta-gap"></td>
        <td style="width:32%;">
            <div class="meta-card">
                <div class="meta-label">Status</div>
                <div class="meta-val" style="padding-top:3px;">
                    <span class="badge badge-{{ $due->status }}">{{ strtoupper($due->status) }}</span>
                    @if($due->paid_at)
                        <br><span style="font-size:10px; color:#64748b; display:block; margin-top:5px;">
                            Paid on: {{ $due->paid_at->format('d M Y') }}
                        </span>
                    @endif
                </div>
            </div>
        </td>
    </tr>
</table>

{{-- ══ ITEMS ══ --}}
<table class="items-table">
    <thead>
        <tr>
            <th style="width:42%;">Description</th>
            <th style="width:20%;">Period</th>
            <th style="width:14%;">Units</th>
            <th class="r" style="width:24%;">Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <div class="desc-title">Owners' Association Dues</div>
                @if($associationName)
                    <div class="desc-sub">{{ $associationName }}</div>
                @endif
            </td>
            <td>{{ $due->periodLabel() }}</td>
            <td>
                {{ $unitCount }}
                <span style="font-size:10px; color:#94a3b8;">{{ $unitCount === 1 ? 'unit' : 'units' }}</span>
            </td>
            <td class="r">{{ number_format($due->amount, 2) }} {{ $currency }}</td>
        </tr>
    </tbody>
</table>

{{-- ══ TOTALS ══ --}}
<table class="totals-wrap">
    <tr>
        <td class="totals-spacer"></td>
        <td class="totals-box">
            <table class="totals-inner">
                <tr>
                    <td class="lbl">Subtotal</td>
                    <td class="val">{{ number_format($due->amount, 2) }} {{ $currency }}</td>
                </tr>
                <tr>
                    <td class="lbl">Tax</td>
                    <td class="val">N/A</td>
                </tr>
                <tr class="total-row">
                    <td class="lbl">Total Due</td>
                    <td class="val">{{ number_format($due->amount, 2) }} {{ $currency }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- ══ NOTES ══ --}}
<div class="notes">
    <div class="notes-title">Payment Terms &amp; Notes</div>
    <div class="notes-text">
        Please arrange payment of <b>{{ number_format($due->amount, 2) }} {{ $currency }}</b>
        before the due date of <b>{{ $due->due_date->format('d M Y') }}</b>
        to avoid any late charges. For inquiries, please contact the association management office.
    </div>
</div>

{{-- ══ FOOTER ══ --}}
<div class="footer-bar"></div>
<table class="footer-table">
    <tr>
        <td class="footer-left">Thank you for your cooperation</td>
        <td class="footer-right">
            Official authorized document
            &nbsp;&middot;&nbsp;
            {{ str_replace('_', ' ', ucwords(config('app.name'), '_')) }}
        </td>
    </tr>
</table>

</div>

{{-- <div class="no-print print-wrap">
    <button class="print-btn" onclick="window.print()">Print / Save as PDF</button>
</div> --}}

</body>
</html>
