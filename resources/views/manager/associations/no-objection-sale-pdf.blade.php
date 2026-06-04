<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<title>عدم ممانعة لنقل وحدة عقارية</title>
<style>
    @page { margin: 18mm 20mm 18mm; }
    body  { font-family: dejavusans, sans-serif; font-size: 11pt; color: #000; direction: rtl; margin: 0; padding: 0; line-height: 1.8; }

    .header-table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    .addressee    { margin-bottom: 12px; font-size: 11pt; line-height: 1.9; }
    .salutation   { margin-bottom: 12px; font-size: 11pt; }
    .subject      { font-size: 12pt; font-weight: bold; text-decoration: underline; text-align: center; margin: 16px 0; }
    .body-text    { font-size: 11pt; line-height: 1.9; margin-bottom: 8px; text-align: justify; }
    .party        { margin: 6px 0; font-size: 11pt; line-height: 1.9; }
    .closing      { font-size: 12pt; font-weight: bold; margin: 14px 0 12px; }
    .attach-title { font-size: 12pt; font-weight: bold; margin: 10px 0 5px; }
    .attach-list  { margin: 0 20px 12px; padding: 0; list-style: disc; }
    .attach-list li { font-size: 11pt; margin-bottom: 4px; }
    .sig-section  { margin-top: 16px; }
    .sig-img      { height: 58px; vertical-align: middle; }
</style>
</head>
<body>

{{-- ══ Header ══ --}}
<table class="header-table">
    <tr>
        <td style="width:20%; text-align:right; vertical-align:middle;">
            <img src="{{ public_path('img/logo.png') }}" style="max-height:58px; max-width:115px;">
        </td>
        <td style="width:60%; text-align:center; vertical-align:middle;">
            <div style="font-size:14pt; font-weight:bold;">شركة ثروة للعقارات</div>
        </td>
        <td style="width:20%; text-align:left; vertical-align:middle; font-size:11pt; font-weight:bold;">
            التاريخ : {{ now()->format('Y/m/d') }}
        </td>
    </tr>
</table>

{{-- ══ Addressee ══ --}}
<div class="addressee">
    <div style="float:left; font-size:11pt;">المحترم،،،</div>
    <div><strong>الفاضل / مدير عام وزارة الإسكان والتخطيط العمراني</strong></div>
    <div style="clear:both;"></div>
</div>

{{-- ══ Salutation ══ --}}
<div class="salutation">
    السلام عليكم ورحمة الله وبركاته،،<br>
    تحية طيبة وبعد،،،
</div>

{{-- ══ Subject ══ --}}
<div class="subject">الموضوع / عدم ممانعة لنقل وحدة عقارية</div>

{{-- ══ Body ══ --}}
<div class="body-text">
    بالإشارة إلى الموضوع أعلاه، نفيدكم بأنه <strong>لا مانع لدينا من نقل ملكية الوحدة العقارية رقم
    ({{ $seller['unit_number'] ?? '' }})</strong>، الكائنة ضمن المبنى المسجل لديكم باسم
    <strong>جمعية الملاك ({{ $association->name_ar ?? $association->name_en }})</strong>.
</div>

<div class="body-text">وذلك من:</div>

<div class="party">
    <strong>الطرف الأول (المالك):</strong><br>
    الاسم : {{ $seller['seller_name'] }}<br>
    الرقم المدني : {{ $seller['seller_id'] }}
</div>

<div class="body-text" style="margin-top:5px; margin-bottom:5px;">إلى :</div>

<div class="party">
    <strong>الطرف الثاني (المشتري):</strong><br>
    الاسم: {{ $buyer['buyer_name'] }}<br>
    الرقم المدني : {{ $buyer['buyer_id'] }}
</div>

<div class="body-text" style="margin-top:10px;">
    كما نؤكد بأنه لا توجد أي التزامات أو مستحقات مالية مترتبة على الطرف الأول تجاه جمعية الملاك
    حتى تاريخ إعداد هذا الخطاب.
</div>

{{-- ══ Closing ══ --}}
<div class="closing">وتفضلوا بقبول فائق الاحترام والتقدير،،</div>

{{-- ══ Attachments ══ --}}
<div class="attach-title">المرفقات:</div>
<ul class="attach-list">
    <li>نسخة من البطاقة الشخصية.</li>
    <li>نسخة من بطاقة مدير الجمعية.</li>
    <li>نسخة من شهادة جمعية الملاك.</li>
</ul>

{{-- ══ Signature — inline ══ --}}
<div class="sig-section">
    <span style="font-size:12pt; font-weight:bold;">مدير جمعية الملاك</span>
    &nbsp;&nbsp;&nbsp;
    <strong style="font-size:11pt;">التوقيع :</strong>
    @if(file_exists(public_path('img/sign.png')))
        &nbsp;<img src="{{ public_path('img/sign.png') }}" class="sig-img">
    @else
        <span style="display:inline-block; width:150px; border-bottom:1px solid #000; height:48px; vertical-align:bottom;"></span>
    @endif
</div>

</body>
</html>
