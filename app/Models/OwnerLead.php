<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnerLead extends Model
{
    protected $fillable = [
        'created_by',
        'name', 'mobile', 'email', 'location',
        'property_type', 'purpose',
        'min_budget', 'max_budget', 'bedrooms',
        'notes', 'status', 'source',
    ];

    public static array $propertyTypes = [
        'any'                => ['ar' => 'أي نوع',    'en' => 'Any'],
        'apartment_building' => ['ar' => 'شقة',        'en' => 'Apartment'],
        'villa'              => ['ar' => 'فيلا',       'en' => 'Villa'],
        'farm'               => ['ar' => 'مزرعة',      'en' => 'Farm'],
        'chalet'             => ['ar' => 'شاليه',      'en' => 'Chalet'],
        'office'             => ['ar' => 'مكتب',       'en' => 'Office'],
        'shop'               => ['ar' => 'محل',        'en' => 'Shop'],
    ];

    public static array $purposes = [
        'rent' => ['ar' => 'إيجار',        'en' => 'Rent'],
        'sale' => ['ar' => 'شراء',          'en' => 'Buy'],
        'both' => ['ar' => 'إيجار أو شراء', 'en' => 'Rent or Buy'],
    ];

    public static array $sources = [
        'open_market' => ['ar' => 'السوق المفتوح',  'en' => 'Open Market'],
        'instagram'   => ['ar' => 'إنستجرام',        'en' => 'Instagram'],
        'facebook'    => ['ar' => 'فيس بوك',         'en' => 'Facebook'],
        'tiktok'      => ['ar' => 'تيك توك',         'en' => 'TikTok'],
        'dubizzle'    => ['ar' => 'دوبيزل',           'en' => 'Dubizzle'],
        'website'     => ['ar' => 'الويبسايت',        'en' => 'Website'],
        'billboard'   => ['ar' => 'لوحة إعلانية',    'en' => 'Billboard'],
        'referral'    => ['ar' => 'عميل (إحالة)',    'en' => 'Client Referral'],
        'other'       => ['ar' => 'أخرى',             'en' => 'Other'],
    ];

    public static array $statuses = [
        'new'        => ['ar' => 'جديد',         'en' => 'New',        'color' => 'bg-blue-100 text-blue-700'],
        'contacted'  => ['ar' => 'تم التواصل',   'en' => 'Contacted',  'color' => 'bg-yellow-100 text-yellow-700'],
        'interested' => ['ar' => 'مهتم',          'en' => 'Interested', 'color' => 'bg-emerald-100 text-emerald-700'],
        'closed'     => ['ar' => 'مغلق',          'en' => 'Closed',     'color' => 'bg-gray-100 text-gray-600'],
        'done'       => ['ar' => 'تم',            'en' => 'Done',       'color' => 'bg-green-100 text-green-700'],
    ];

    public static array $locations = [
        'بوشر'               => 'Bowsher',
        'مرتفعات بوشر'       => 'Bowsher Heights',
        'الانصب'             => 'Al Ansab',
        'العذيبة الشمالية'   => 'Al Azaiba North',
        'العذيبة الجنوبية'   => 'Al Azaiba South',
        'الخوض السادسة'      => 'Al Khoud 6',
        'الخوض السابعة'      => 'Al Khoud 7',
        'الخوض الرابعة'      => 'Al Khoud 4',
        'الخوض الكوثر'       => 'Al Khoud Al Kawthar',
        'الموالح الجنوبية'   => 'Al Mawaleh South',
        'الموالح الشمالية'   => 'Al Mawaleh North',
        'الموج'              => 'Al Mouj',
        'مسقط هيلز'          => 'Muscat Hills',
        'القرم'              => 'Al Qurum',
        'الخوير'             => 'Al Khuwair',
        'مدينة الاعلام'      => 'Media City',
        'مدينة السلطان قابوس' => 'Madinat Sultan Qaboos',
        'مدينة السلطان هيثم'  => 'Madinat Sultan Haitham',
        'الغبرة الشمالية'    => 'Al Ghubra North',
        'الغبرة الجنوبية'    => 'Al Ghubra South',
        'السيب'              => 'Al Seeb',
        'المعبيلة'           => 'Al Mabelah',
    ];

    public function locationLabel(string $locale = 'ar'): string
    {
        if (!$this->location) return '—';
        if ($locale === 'en') {
            return self::$locations[$this->location] ?? $this->location;
        }
        return $this->location;
    }

    public function statusLabel(string $locale = 'ar'): string
    {
        return self::$statuses[$this->status][$locale] ?? $this->status;
    }

    public function statusColor(): string
    {
        return self::$statuses[$this->status]['color'] ?? 'bg-gray-100 text-gray-600';
    }

    public function purposeLabel(string $locale = 'ar'): string
    {
        return self::$purposes[$this->purpose][$locale] ?? $this->purpose;
    }

    public function typeLabel(string $locale = 'ar'): string
    {
        return self::$propertyTypes[$this->property_type][$locale] ?? $this->property_type;
    }

    public function whatsappUrl(string $message = ''): ?string
    {
        if (!$this->mobile) return null;
        $phone = preg_replace('/[^0-9]/', '', $this->mobile);
        if (str_starts_with($phone, '0')) {
            $phone = '966' . substr($phone, 1);
        }
        return 'https://api.whatsapp.com/send?phone=' . $phone . ($message ? '&text=' . rawurlencode($message) : '');
    }
}
