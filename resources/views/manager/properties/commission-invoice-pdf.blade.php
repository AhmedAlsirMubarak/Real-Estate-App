<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, sans-serif; direction: rtl; color: #1e293b; font-size: 13px; }

.header { background: #0f2444; color: #fff; padding: 0; }
.header-inner { display: table; width: 100%; }
.header-logo  { display: table-cell; vertical-align: middle; width: 160px; padding: 20px 28px; background: rgba(255,255,255,0.07); border-left: 1px solid rgba(255,255,255,0.12); text-align: center; }
.header-logo img { max-width: 110px; max-height: 70px; }
.header-text  { display: table-cell; vertical-align: middle; padding: 28px 32px; }
.header h1 { font-size: 24px; font-weight: bold; margin-bottom: 5px; letter-spacing: 0.5px; }
.header p  { font-size: 12px; opacity: .6; letter-spacing: 1px; text-transform: uppercase; }
.header-divider { height: 4px; background: linear-gradient(to left, #c9a84c, #e8c96b, #c9a84c); }

.meta-bar { display: table; width: 100%; padding: 16px 32px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
.meta-bar .col { display: table-cell; width: 33%; }
.meta-label { font-size: 10px; color: #94a3b8; margin-bottom: 2px; }
.meta-val   { font-size: 13px; font-weight: bold; color: #0f2444; }

.body { padding: 28px 32px; }

.section-title { font-size: 11px; font-weight: bold; color: #64748b; text-transform: uppercase;
                 letter-spacing: 1px; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; margin-bottom: 14px; }

.info-grid { display: table; width: 100%; margin-bottom: 24px; }
.info-row  { display: table-row; }
.info-lbl  { display: table-cell; width: 40%; color: #64748b; padding: 5px 0; font-size: 12px; }
.info-val  { display: table-cell; color: #1e293b; padding: 5px 0; font-size: 12px; font-weight: 600; }

.calc-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
.calc-table th { background: #0f2444; color: #fff; padding: 10px 14px; font-size: 12px; text-align: right; }
.calc-table td { padding: 10px 14px; border-bottom: 1px solid #e2e8f0; font-size: 12px; }
.calc-table tr:last-child td { border-bottom: none; }
.calc-table .highlight { background: #f0f9ff; font-weight: bold; }

.total-box { background: #0f2444; color: #fff; padding: 18px 24px; border-radius: 8px; margin-bottom: 24px; }
.total-box .lbl { font-size: 12px; opacity: .75; margin-bottom: 4px; }
.total-box .amount { font-size: 26px; font-weight: bold; letter-spacing: 1px; }
.total-box .sub { font-size: 11px; opacity: .7; margin-top: 4px; }

.notes-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 14px; margin-bottom: 24px; }
.notes-box p { font-size: 12px; color: #92400e; }

.footer { text-align: center; padding: 18px 32px; border-top: 1px solid #e2e8f0; color: #94a3b8; font-size: 11px; }

.badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
.badge-owner  { background: #dbeafe; color: #1e40af; }
.badge-client { background: #dcfce7; color: #166534; }
</style>
</head>
<body>

@php
    $logoPath = public_path('img/logo.png');
    $logoSrc  = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;
@endphp
<div class="header">
    <div class="header-inner">
        <div class="header-text">
            <h1>فاتورة عمولة أعمال</h1>
            <p>Business Commission Invoice</p>
        </div>
        @if($logoSrc)
        <div class="header-logo">
            <img src="{{ $logoSrc }}">
        </div>
        @endif
    </div>
    <div class="header-divider"></div>
</div>

<div class="meta-bar">
    <div class="col">
        <div class="meta-label">رقم الفاتورة</div>
        <div class="meta-val">{{ $invoiceNumber }}</div>
    </div>
    <div class="col">
        <div class="meta-label">تاريخ الإصدار</div>
        <div class="meta-val">{{ \Carbon\Carbon::parse($invoiceDate)->format('Y/m/d') }}</div>
    </div>
    <div class="col">
        <div class="meta-label">الفاتورة موجهة إلى</div>
        <div class="meta-val">
            <span class="badge {{ $invoiceFor === 'owner' ? 'badge-owner' : 'badge-client' }}">
                {{ $invoiceFor === 'owner' ? 'المالك' : 'العميل' }}
            </span>
        </div>
    </div>
</div>

<div class="body">

    {{-- Property & Recipient --}}
    <div class="section-title">معلومات العقار والمستلم</div>
    <div class="info-grid">
        <div class="info-row">
            <div class="info-lbl">اسم العقار</div>
            <div class="info-val">{{ $property->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-lbl">كود العقار</div>
            <div class="info-val">{{ $property->code }}</div>
        </div>
        <div class="info-row">
            <div class="info-lbl">العنوان</div>
            <div class="info-val">{{ $property->address }}{{ $property->city ? ' — ' . $property->city : '' }}</div>
        </div>
        <div class="info-row">
            <div class="info-lbl">اسم {{ $invoiceFor === 'owner' ? 'المالك' : 'العميل' }}</div>
            <div class="info-val">{{ $recipientName }}</div>
        </div>
    </div>

    {{-- Calculation --}}
    <div class="section-title">تفاصيل الحساب</div>
    <table class="calc-table">
        <thead>
            <tr>
                <th>البيان</th>
                <th style="text-align:left">القيمة</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>قيمة الإيجار الشهري</td>
                <td style="text-align:left">{{ number_format($monthlyRent, 3) }} ر.ع</td>
            </tr>
            <tr>
                <td>مدة العقد</td>
                <td style="text-align:left">{{ $durationMonths }} شهر</td>
            </tr>
            <tr class="highlight">
                <td>إجمالي قيمة الإيجار (الشهري × المدة)</td>
                <td style="text-align:left">{{ number_format($totalRent, 3) }} ر.ع</td>
            </tr>
            <tr>
                <td>نسبة العمولة</td>
                <td style="text-align:left">{{ $commissionRate }}%</td>
            </tr>
        </tbody>
    </table>

    {{-- Total --}}
    <div class="total-box">
        <div class="lbl">إجمالي العمولة المستحقة</div>
        <div class="amount">{{ number_format($commissionAmount, 3) }} ر.ع</div>
        <div class="sub">= {{ $totalRent }} × {{ $commissionRate }}% = {{ number_format($commissionAmount, 3) }} ر.ع</div>
    </div>

    {{-- Notes --}}
    @if($notes)
    <div class="notes-box">
        <p><strong>ملاحظات:</strong> {{ $notes }}</p>
    </div>
    @endif

</div>

<div class="footer">
    تم إصدار هذه الفاتورة بتاريخ {{ \Carbon\Carbon::parse($invoiceDate)->format('Y/m/d') }} — {{ $invoiceNumber }}
</div>

</body>
</html>
