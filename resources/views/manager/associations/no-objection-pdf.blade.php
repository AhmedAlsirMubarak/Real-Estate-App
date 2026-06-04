<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<title>عدم ممانعة من تأجير الوحدة العقارية</title>
<style>
    @page { margin: 20mm 20mm 20mm; }
    body  { font-family: dejavusans, sans-serif; font-size: 11pt; color: #000; direction: rtl; margin: 0; padding: 0; line-height: 1.8; }

    /* Header */
    .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }

    /* Date line */
    .date-line { text-align: right; font-size: 11pt; margin-bottom: 16px; font-weight: bold; }

    /* Addressee */
    .addressee { margin-bottom: 16px; font-size: 11pt; line-height: 2; }

    /* Salutation */
    .salutation { margin-bottom: 14px; font-size: 11pt; }

    /* Subject */
    .subject { font-size: 12pt; font-weight: bold; text-decoration: underline; text-align: center; margin: 18px 0; }

    /* Body */
    .body-text { font-size: 11pt; line-height: 2; margin-bottom: 10px; text-align: justify; }
    .party { margin: 6px 0; font-size: 11pt; line-height: 2; }

    /* Closing */
    .closing { font-size: 12pt; font-weight: bold; margin: 18px 0 14px; }

    /* Attachments */
    .attach-title { font-size: 12pt; font-weight: bold; margin: 14px 0 6px; }
    .attach-list  { margin: 0 20px 14px; padding: 0; list-style: disc; }
    .attach-list li { font-size: 11pt; margin-bottom: 4px; }

    /* Signature */
    .sig-section { margin-top: 20px; }
    .sig-label   { font-size: 12pt; font-weight: bold; margin-bottom: 6px; }
    .sig-img     { height: 60px; }
</style>
</head>
<body>

{{-- ══ Header: Logo + Date ══ --}}
<table class="header-table">
    <tr>
        <td style="width:20%; text-align:right; vertical-align:top;">
            <img src="{{ public_path('img/logo.png') }}" style="max-height:60px; max-width:120px;">
        </td>
        <td style="width:60%; text-align:center; vertical-align:middle;">
            <div style="font-size:14pt; font-weight:bold;">شركة ثروة للعقارات</div>
        </td>
        <td style="width:20%; text-align:left; vertical-align:top; font-size:11pt; font-weight:bold;">
            التاريخ : {{ now()->format('Y/m/d') }}
        </td>
    </tr>
</table>

{{-- ══ Addressee ══ --}}
<div class="addressee">
    <div style="float:left; font-size:11pt;">المحترم،،،</div>
    <div>
        <strong>الفاضل/ مدير بلدية</strong><br>
        <strong>مسقط –بوشر المديرية</strong><br>
        <strong>العامة لبلدية مسقط</strong>
    </div>
    <div style="clear:both;"></div>
</div>

{{-- ══ Salutation ══ --}}
<div class="salutation">
    السلام عليكم ورحمة الله وبركاته تحية طيبة وبعد،،،
</div>

{{-- ══ Subject ══ --}}
<div class="subject">الموضوع/ عدم ممانعة من تأجير الوحدة العقارية</div>

{{-- ══ Body ══ --}}
<div class="body-text">
    بالإشارة إلى الموضوع أعلاه أفيدكم بأنه لا مانع لدينا من تأجير الوحدة العقارية
    رقم ({{ $lessor['unit_number'] ?? '' }}) من المبنى
    المسجل لديكم باسم جمعية الملاك ({{ $association->name_ar ?? $association->name_en }}).
</div>

<div class="party">
    <strong>الطرف الأول المالك:</strong> {{ $lessor['lessor_name'] }}<br>
    <strong>الرقم المدني:</strong> ({{ $lessor['lessor_id'] }})
</div>

<div class="party">
    <strong>إلى الطرف الثاني المستأجر:</strong> {{ $lessor['lessee_name'] }}<br>
    <strong>الرقم المدني:</strong> ({{ $lessor['lessee_id'] }})
</div>

<div class="body-text" style="margin-top:10px;">
    حيث أنه لا توجد لدى الطرف الأول أي التزامات تجاه جمعية الملاك.
</div>

{{-- ══ Closing ══ --}}
<div class="closing">وتفضلوا بقبول فائق الاحترام والتقدير،،</div>

{{-- ══ Attachments ══ --}}
<div class="attach-title">مرفق لكم :</div>
<ul class="attach-list">
    <li>نسخة من البطاقة الشخصية.</li>
    <li>نسخة من شهادة جمعيات الملاك.</li>
    <li>نسخة من بطاقة مدير الجمعية.</li>
</ul>

{{-- ══ Signature ══ --}}
<div class="sig-section">
    <div class="sig-label">مدير جمعية الملاك: {{ $association->name_ar ?? $association->name_en }}</div>
    <div style="font-size:11pt; margin-bottom:8px;">
        <strong>التوقيع :</strong>
        @if(file_exists(public_path('img/sign.png')))
        &nbsp;&nbsp;<img src="{{ public_path('img/sign.png') }}" class="sig-img">
        @else
        <span style="display:inline-block; width:160px; border-bottom:1px solid #000; height:50px; vertical-align:bottom;"></span>
        @endif
    </div>
</div>

</body>
</html>
