<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<title>شهادة عدم الاعتراض على التأجير</title>
<style>
    @page { margin: 18mm 15mm 20mm; }
    body  { font-family: dejavusans, sans-serif; font-size: 10pt; color: #1e293b; direction: rtl; margin: 0; padding: 0; }

    /* ── Header ── */
    .hdr        { text-align: center; margin-bottom: 18px; border-bottom: 3px double #1e3a8a; padding-bottom: 12px; }
    .hdr-logo   { font-size: 18pt; font-weight: bold; color: #1e3a8a; letter-spacing: 1px; }
    .hdr-sub    { font-size: 9pt; color: #64748b; margin-top: 2px; }
    .cert-title { font-size: 16pt; font-weight: bold; color: #1e3a8a; text-align: center; margin: 16px 0 4px; }
    .cert-title-en { font-size: 10pt; color: #64748b; text-align: center; margin-bottom: 14px; }
    .ref-line   { text-align: left; font-size: 8.5pt; color: #64748b; margin-bottom: 14px; }

    /* ── Section titles ── */
    .sec        { font-size: 11pt; font-weight: bold; color: #1e3a8a; border-bottom: 2px solid #1e3a8a;
                  padding-bottom: 3px; margin: 16px 0 9px; }

    /* ── Data table ── */
    .dtbl       { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    .dtbl td    { padding: 5px 8px; vertical-align: top; }
    .dlbl       { font-weight: bold; color: #334155; width: 36%; font-size: 9.5pt; }
    .dval       { color: #1e293b; font-size: 9.5pt; border-bottom: 1px dotted #cbd5e1; }

    /* ── Certificate body ── */
    .body-box   { border: 1px solid #bfdbfe; background: #eff6ff; border-radius: 6px;
                  padding: 14px 16px; margin: 16px 0; font-size: 10.5pt; line-height: 1.9; text-align: justify; }
    .highlight  { font-weight: bold; color: #1e3a8a; }

    /* ── Documents list ── */
    .doc-list   { margin: 6px 0 12px 0; padding: 0; list-style: none; }
    .doc-list li{ padding: 3px 0; font-size: 9.5pt; color: #374151; }
    .doc-list li::before { content: "✓  "; color: #15803d; font-weight: bold; }

    /* ── Signature block ── */
    .sig-wrap   { width: 100%; border-collapse: collapse; margin-top: 36px; }
    .sig-box    { width: 45%; vertical-align: top; border: 1px solid #e2e8f0; border-radius: 6px;
                  padding: 10px 14px; text-align: center; }
    .sig-title  { font-weight: bold; font-size: 9.5pt; color: #334155; margin-bottom: 6px; }
    .sig-name   { font-size: 9pt; color: #64748b; margin-top: 4px; }
    .sig-line   { border-bottom: 1px solid #94a3b8; height: 40px; margin: 8px 4px 0; }
    .stamp-box  { width: 45%; vertical-align: top; border: 2px dashed #94a3b8; border-radius: 6px;
                  padding: 14px; text-align: center; min-height: 90px; }
    .stamp-text { font-size: 8.5pt; color: #94a3b8; margin-top: 24px; }

    /* ── Footer ── */
    .footer     { margin-top: 24px; text-align: center; font-size: 7.5pt; color: #9ca3af;
                  border-top: 1px solid #e2e8f0; padding-top: 7px; }
    .spacer     { width: 10%; }
    .badge-blue { background: #dbeafe; color: #1d4ed8; padding: 1px 9px; border-radius: 9px; font-size: 8.5pt; }
</style>
</head>
<body>

{{-- ══ Header ══ --}}
<div class="hdr">
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="width:18%; text-align:right; vertical-align:middle;">
                <img src="{{ public_path('img/logo.png') }}" style="max-height:55px; max-width:110px;">
            </td>
            <td style="text-align:center; vertical-align:middle; padding:0 10px;">
                <div class="hdr-logo">شركة ثروة للعقارات</div>
                <div class="hdr-sub">Tharwa Real Estate Company &mdash; إدارة جمعيات الملاك</div>
            </td>
            <td style="width:18%;"></td>
        </tr>
    </table>
</div>

<p class="cert-title">شهادة عدم الممانعة على التأجير</p>
<p class="cert-title-en">No Objection Certificate for Renting</p>

<table style="width:100%; border-collapse:collapse; margin-bottom:14px; font-size:8.5pt; color:#64748b;">
    <tr>
        <td style="text-align:right;">رقم المرجع: <strong>{{ $refNumber }}</strong></td>
        <td style="text-align:left;">التاريخ: <strong>{{ now()->format('Y/m/d') }}</strong></td>
    </tr>
</table>

{{-- ══ Association Info ══ --}}
<div class="sec">بيانات جمعية الملاك</div>
<table class="dtbl">
    <tr>
        <td class="dlbl">اسم الجمعية:</td>
        <td class="dval">{{ $association->name_ar ?? $association->name_en }}</td>
        <td class="dlbl">اسم العقار:</td>
        <td class="dval">{{ $association->property->name ?? '—' }}</td>
    </tr>
    <tr>
        <td class="dlbl">رمز العقار:</td>
        <td class="dval">{{ $association->property->code ?? '—' }}</td>
        <td class="dlbl">تاريخ التأسيس:</td>
        <td class="dval">{{ $association->established_date?->format('Y/m/d') ?? '—' }}</td>
    </tr>
    @if($association->electricity_account_number || $association->water_account_number)
    <tr>
        <td class="dlbl">حساب الكهرباء:</td>
        <td class="dval">{{ $association->electricity_account_number ?? '—' }}</td>
        <td class="dlbl">حساب الماء:</td>
        <td class="dval">{{ $association->water_account_number ?? '—' }}</td>
    </tr>
    @endif
</table>

{{-- ══ Owners ══ --}}
@php $owners = $association->property->owners ?? collect(); @endphp
@if($owners->count())
<div class="sec">بيانات الملاك</div>
<table class="dtbl">
    @foreach($owners as $owner)
    <tr>
        <td class="dlbl">{{ $owner->user?->name ?? '—' }}
            @if($owner->pivot->is_primary) <span class="badge-blue">مالك رئيسي</span> @endif
        </td>
        <td class="dval">
            {{ $owner->phone ?? $owner->user?->phone ?? '—' }}
            @if($owner->pivot->ownership_percentage)
                &nbsp;&bull;&nbsp; حصة: {{ $owner->pivot->ownership_percentage }}%
            @endif
        </td>
        <td class="dlbl" style="width:20%;">رقم الهوية:</td>
        <td class="dval">{{ $owner->national_id ?? '—' }}</td>
    </tr>
    @endforeach
</table>
@endif

{{-- ══ Lessor Data ══ --}}
<div class="sec">بيانات المؤجر (الطرف الأول)</div>
<table class="dtbl">
    <tr>
        <td class="dlbl">الاسم الكامل:</td>
        <td class="dval">{{ $lessor['lessor_name'] }}</td>
        <td class="dlbl">رقم الهاتف:</td>
        <td class="dval">{{ $lessor['lessor_phone'] }}</td>
    </tr>
    <tr>
        <td class="dlbl">رقم الهوية / الإقامة:</td>
        <td class="dval">{{ $lessor['lessor_id'] }}</td>
        <td class="dlbl"></td>
        <td class="dval"></td>
    </tr>
</table>

{{-- ══ Certificate Body ══ --}}
<div class="body-box">
    نشهد نحن <span class="highlight">{{ $association->name_ar ?? $association->name_en }}</span>،
    جمعية ملاك عقار <span class="highlight">{{ $association->property->name ?? '' }}</span>،
    بأننا لا نعترض على قيام المؤجر
    <span class="highlight">{{ $lessor['lessor_name'] }}</span>
    (رقم هوية: <span class="highlight">{{ $lessor['lessor_id'] }}</span>)
    بتأجير وحدته العقارية الكائنة في المبنى المذكور أعلاه،
    وذلك وفق الأنظمة والقوانين المعمول بها في سلطنة عُمان،
    وبشرط أن يلتزم المستأجر بنظام الجمعية والاشتراطات العامة للمجمع.
    <br><br>
    وقد صدرت هذه الشهادة بناءً على طلب صاحبها لاستخدامها لدى الجهات الرسمية،
    والله ولي التوفيق.
</div>

{{-- ══ Attached Documents ══ --}}
@if(!empty($documentLabels))
<div class="sec">المستندات المرفقة — Attached Documents</div>
<ul class="doc-list">
    @foreach($documentLabels as $label)
    <li>{{ __($label) }}</li>
    @endforeach
</ul>
<p style="font-size:8.5pt; color:#64748b; margin:4px 0 0;">
    * المستندات مرفقة في الصفحات التالية — Documents are appended on the following pages.
</p>
@endif

{{-- ══ Signature Block ══ --}}
<table class="sig-wrap">
    <tr>
        <td class="sig-box">
            <div class="sig-title">توقيع مدير الجمعية</div>
            <div class="sig-line"></div>
            <div class="sig-name">الاسم: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
            <div class="sig-name" style="margin-top:4px;">التاريخ: {{ now()->format('Y/m/d') }}</div>
        </td>
        <td class="spacer"></td>
        <td class="stamp-box">
            <div style="font-size:9pt; font-weight:bold; color:#64748b;">الختم الرسمي</div>
            <div class="stamp-text">Official Stamp</div>
        </td>
    </tr>
</table>

{{-- ══ Footer ══ --}}
<div class="footer">
    شركة ثروة للعقارات &mdash; تم إصدار هذه الشهادة بتاريخ {{ now()->format('Y/m/d H:i') }}
    &mdash; المرجع: {{ $refNumber }}
</div>

</body>
</html>
