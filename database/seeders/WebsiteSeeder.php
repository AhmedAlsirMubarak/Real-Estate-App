<?php

namespace Database\Seeders;

use App\Models\WebsiteSection;
use App\Models\WebsiteItem;
use Illuminate\Database\Seeder;

class WebsiteSeeder extends Seeder
{
    public function run(): void
    {
        // ── HOME / HERO ──────────────────────────────────────────────────────
        WebsiteSection::updateOrCreate(['page' => 'home', 'key' => 'hero'], [
            'title_ar'      => 'إدارة عقارية ذكية ومتكاملة',
            'title_en'      => 'Smart & Integrated Property Management',
            'subtitle_ar'   => 'الرائد في إدارة العقارات',
            'subtitle_en'   => 'Leading Real Estate Management',
            'body_ar'       => 'نقدم حلولاً متطورة لإدارة العقارات والمباني السكنية والتجارية. من المستأجر إلى الإدارة، كل شيء في منصة واحدة قوية.',
            'body_en'       => 'We provide advanced solutions for managing residential and commercial properties. From tenants to administration — everything in one powerful platform.',
            'button_text_ar' => 'ابدأ الآن',
            'button_text_en' => 'Get Started',
            'button_url'    => '/login',
            'extra'         => [
                'btn2_text_ar' => 'تعرف علينا',
                'btn2_text_en' => 'Learn More',
                'btn2_url'     => '#about',
                'badge_ar'     => 'الرائد في إدارة العقارات',
                'badge_en'     => 'Leading Real Estate Management',
            ],
            'sort_order'    => 1,
        ]);

        // ── HOME / FEATURED PROPERTIES ──────────────────────────────────────
        WebsiteSection::updateOrCreate(['page' => 'home', 'key' => 'featured_properties'], [
            'title_ar'       => 'اعثر على أفضل العقارات المتاحة',
            'title_en'       => 'Find The Best Available Properties',
            'subtitle_ar'    => 'محفظتنا العقارية',
            'subtitle_en'    => 'Our Portfolio',
            'body_ar'        => 'محفظة عقارية متنوعة في أفضل المواقع',
            'body_en'        => 'A diverse property portfolio in the best locations',
            'button_text_ar' => 'عرض جميع العقارات',
            'button_text_en' => 'View All Properties',
            'button_url'     => '/properties',
            'sort_order'     => 35,
        ]);

        // ── HOME / STATS ─────────────────────────────────────────────────────
        $stats = WebsiteSection::updateOrCreate(['page' => 'home', 'key' => 'stats'], [
            'sort_order' => 3,
        ]);
        $this->seedItems($stats, [
            ['value' => '50+',  'title_ar' => 'مبنى تحت إدارتنا',    'title_en' => 'Buildings Under Management', 'icon' => 'building',  'sort_order' => 1],
            ['value' => '500+', 'title_ar' => 'وحدة مؤجرة',          'title_en' => 'Units Leased',               'icon' => 'key',        'sort_order' => 2],
            ['value' => '200+', 'title_ar' => 'مستأجر سعيد',         'title_en' => 'Happy Tenants',             'icon' => 'users',      'sort_order' => 3],
            ['value' => '98%',  'title_ar' => 'نسبة رضا العملاء',    'title_en' => 'Client Satisfaction Rate',  'icon' => 'star',       'sort_order' => 4],
        ]);

        // ── HOME / SERVICES ──────────────────────────────────────────────────
        WebsiteSection::updateOrCreate(['page' => 'home', 'key' => 'services'], [
            'title_ar'    => 'اكتشف أحدث العقارات المضافة',
            'title_en'    => 'Explore New Added Properties',
            'subtitle_ar' => 'في شركة ثروة للعقارات',
            'subtitle_en' => 'In Tharwa Real Estate Agency',
            'body_ar'     => 'نفخر بتقديم أفضل العقارات السكنية والتجارية في أرقى المواقع. اكتشف مجموعتنا المتنوعة من العقارات المميزة.',
            'body_en'     => 'We are proud to offer the finest residential and commercial properties in premium locations. Explore our diverse collection of featured properties.',
            'extra'       => ['showcase_property_id' => null],
            'sort_order'  => 2,
        ]);

        // ── HOME / PROPERTY TYPES ────────────────────────────────────────────
        $types = WebsiteSection::updateOrCreate(['page' => 'home', 'key' => 'property_types'], [
            'title_ar'    => 'تصفح حسب نوع العقار',
            'title_en'    => 'Browse by Property Type',
            'subtitle_ar' => 'أنواع العقارات',
            'subtitle_en' => 'Property Types',
            'body_ar'     => 'اختر نوع العقار الذي يناسبك واستعرض أفضل الخيارات المتاحة',
            'body_en'     => 'Choose the property type that suits you and explore the best available options',
            'sort_order'  => 4,
        ]);
        $this->seedItems($types, [
            ['icon' => 'apartment', 'title_ar' => 'شقق',      'title_en' => 'Apartments',  'value' => 'apartment', 'url' => '/properties?type=apartment', 'sort_order' => 1],
            ['icon' => 'villa',     'title_ar' => 'فيلل',     'title_en' => 'Villas',      'value' => 'villa',     'url' => '/properties?type=villa',     'sort_order' => 2],
            ['icon' => 'office',    'title_ar' => 'مكاتب',    'title_en' => 'Offices',     'value' => 'office',    'url' => '/properties?type=office',    'sort_order' => 3],
            ['icon' => 'shop',      'title_ar' => 'محلات',    'title_en' => 'Shops',       'value' => 'shop',      'url' => '/properties?type=shop',      'sort_order' => 4],
            ['icon' => 'studio',    'title_ar' => 'استوديو',  'title_en' => 'Studios',     'value' => 'studio',    'url' => '/properties?type=studio',    'sort_order' => 5],
            ['icon' => 'land',      'title_ar' => 'أراضي',    'title_en' => 'Land',        'value' => 'land',      'url' => '/properties?type=land',      'sort_order' => 6],
        ]);

        // ── HOME / ABOUT ─────────────────────────────────────────────────────
        $about = WebsiteSection::updateOrCreate(['page' => 'home', 'key' => 'about'], [
            'title_ar'      => 'شركة ثروة للعقارات',
            'title_en'      => 'Tharwa Real Estate Co.',
            'subtitle_ar'   => 'من نحن',
            'subtitle_en'   => 'About Us',
            'body_ar'       => 'شركة ثروة للعقارات رائدة في قطاع إدارة العقارات في المنطقة. نقدم خدمات إدارية متكاملة للمباني السكنية والتجارية بأعلى معايير الجودة والاحترافية. يجمعنا شغف حقيقي بتطوير قطاع العقارات وتسهيل التعاملات بين ملاك العقارات والمستأجرين من خلال منصتنا الرقمية المتطورة.',
            'body_en'       => 'Tharwa Real Estate is a leader in property management in the region. We provide comprehensive management services for residential and commercial buildings with the highest standards of quality and professionalism. We share a genuine passion for advancing the real estate sector and simplifying dealings between property owners and tenants through our advanced digital platform.',
            'button_text_ar' => 'تواصل معنا',
            'button_text_en' => 'Contact Us',
            'button_url'    => '#contact',
            'extra'         => ['badge_ar' => 'معدل إشغال 97%', 'badge_en' => '97% Occupancy Rate'],
            'sort_order'    => 5,
        ]);
        $this->seedItems($about, [
            ['icon' => 'check', 'title_ar' => 'خبرة تزيد عن 10 سنوات',   'title_en' => 'Over 10 years of experience',       'sort_order' => 1],
            ['icon' => 'check', 'title_ar' => 'فريق متخصص ومحترف',        'title_en' => 'Specialized professional team',       'sort_order' => 2],
            ['icon' => 'check', 'title_ar' => 'دعم فني على مدار الساعة',  'title_en' => '24/7 technical support',              'sort_order' => 3],
            ['icon' => 'check', 'title_ar' => 'أسعار تنافسية وشفافة',    'title_en' => 'Competitive & transparent pricing',    'sort_order' => 4],
        ]);

        // ── HOME / CTA ───────────────────────────────────────────────────────
        WebsiteSection::updateOrCreate(['page' => 'home', 'key' => 'cta'], [
            'title_ar'      => 'هل تريد تأجير أو بيع عقارك؟',
            'title_en'      => 'Want to Rent or Sell Your Property?',
            'body_ar'       => 'نحن هنا لمساعدتك في الحصول على أفضل العروض والإدارة الاحترافية لعقارك. تواصل مع فريقنا اليوم.',
            'body_en'       => 'We are here to help you get the best deals and professional management for your property. Contact our team today.',
            'button_text_ar' => 'تواصل معنا',
            'button_text_en' => 'Contact Us Now',
            'button_url'    => '#contact',
            'sort_order'    => 6,
        ]);

        // ── HOME / TESTIMONIALS ──────────────────────────────────────────────
        $testimonials = WebsiteSection::updateOrCreate(['page' => 'home', 'key' => 'testimonials'], [
            'title_ar'    => 'ماذا يقول عملاؤنا',
            'title_en'    => 'What Our Clients Say',
            'subtitle_ar' => 'آراء العملاء',
            'subtitle_en' => 'Testimonials',
            'sort_order'  => 7,
        ]);
        $this->seedItems($testimonials, [
            ['title_ar' => 'أحمد الراشدي',  'title_en' => 'Ahmed Al-Rashdi',   'subtitle_ar' => 'مالك عقار',       'subtitle_en' => 'Property Owner',   'body_ar' => 'خدمة ممتازة واحترافية عالية. أنصح بالتعامل مع شركة ثروة لكل من يريد إدارة عقاراته بكفاءة.',         'body_en' => 'Excellent service and high professionalism. I recommend Tharwa to anyone who wants to manage their properties efficiently.', 'sort_order' => 1],
            ['title_ar' => 'سارة المنصوري', 'title_en' => 'Sarah Al-Mansouri', 'subtitle_ar' => 'مستأجرة',         'subtitle_en' => 'Tenant',            'body_ar' => 'تجربة رائعة مع المنصة. سهولة في دفع الإيجار وتقديم طلبات الصيانة. فريق متجاوب ومحترف.',           'body_en' => 'Wonderful experience with the platform. Easy to pay rent and submit maintenance requests. Responsive and professional team.', 'sort_order' => 2],
            ['title_ar' => 'خالد العبري',   'title_en' => 'Khalid Al-Abri',   'subtitle_ar' => 'مستثمر عقاري',  'subtitle_en' => 'Real Estate Investor','body_ar' => 'استثمرت في عدة عقارات وكانت إدارة ثروة لها بالغة الأثر في تحقيق عوائد ممتازة. شكراً للفريق.',  'body_en' => 'I invested in several properties and Tharwa\'s management significantly contributed to achieving excellent returns.', 'sort_order' => 3],
        ]);

        // ── HOME / PARTNERS ──────────────────────────────────────────────────
        WebsiteSection::updateOrCreate(['page' => 'home', 'key' => 'partners'], [
            'title_ar'    => 'شركاؤنا',
            'title_en'    => 'Our Partners',
            'subtitle_ar' => 'نفخر بشراكاتنا مع أبرز المؤسسات',
            'subtitle_en' => 'We are proud of our partnerships with leading institutions',
            'sort_order'  => 8,
        ]);

        // ── HOME / CONTACT ───────────────────────────────────────────────────
        $contact = WebsiteSection::updateOrCreate(['page' => 'home', 'key' => 'contact'], [
            'title_ar'    => 'نسعد بخدمتك',
            'title_en'    => "We're Happy to Help",
            'subtitle_ar' => 'تواصل معنا',
            'subtitle_en' => 'Contact Us',
            'body_ar'     => 'هل لديك سؤال أو تحتاج إلى مزيد من المعلومات؟ تواصل معنا وسنرد عليك في أقرب وقت',
            'body_en'     => 'Have a question or need more information? Contact us and we\'ll reply as soon as possible',
            'sort_order'  => 9,
        ]);
        $this->seedItems($contact, [
            ['icon' => 'location', 'title_ar' => 'العنوان',     'title_en' => 'Address',       'body_ar' => 'مسقط، سلطنة عُمان',        'body_en' => 'Muscat, Sultanate of Oman',    'sort_order' => 1],
            ['icon' => 'phone',    'title_ar' => 'الهاتف',      'title_en' => 'Phone',         'body_ar' => '+968 24 000 000',           'body_en' => '+968 24 000 000',              'sort_order' => 2],
            ['icon' => 'email',    'title_ar' => 'البريد',      'title_en' => 'Email',         'body_ar' => 'info@tharwa.com',           'body_en' => 'info@tharwa.com',              'sort_order' => 3],
            ['icon' => 'clock',    'title_ar' => 'أوقات العمل', 'title_en' => 'Working Hours', 'body_ar' => 'الأحد - الخميس، 8ص - 5م', 'body_en' => 'Sun–Thu, 8AM–5PM',            'sort_order' => 4],
        ]);

        // ── GLOBAL / FOOTER ──────────────────────────────────────────────────
        WebsiteSection::updateOrCreate(['page' => 'global', 'key' => 'footer'], [
            'title_ar' => 'شركة ثروة للعقارات',
            'title_en' => 'Tharwa Real Estate',
            'body_ar'  => 'رائدون في إدارة العقارات السكنية والتجارية بأعلى معايير الجودة والاحترافية.',
            'body_en'  => 'Leaders in managing residential and commercial properties with the highest standards of quality.',
            'extra'    => [
                'whatsapp' => '',
                'twitter'  => '',
                'instagram'=> '',
                'facebook' => '',
                'linkedin' => '',
            ],
            'sort_order' => 1,
        ]);
    }

    private function seedItems(WebsiteSection $section, array $items): void
    {
        if ($section->items()->count() > 0) return;
        foreach ($items as $item) {
            WebsiteItem::create(array_merge(['section_id' => $section->id], $item));
        }
    }
}
