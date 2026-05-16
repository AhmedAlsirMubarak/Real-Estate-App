<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name', 'mobile', 'email', 'location',
        'property_type', 'purpose',
        'min_budget', 'max_budget', 'bedrooms',
        'notes', 'status',
    ];

    public static array $propertyTypes = [
        'any'                => ['ar' => 'أي نوع',    'en' => 'Any'],
        'apartment_building' => ['ar' => 'شقة',        'en' => 'Apartment'],
        'villa'              => ['ar' => 'فيلا',       'en' => 'Villa'],
        'farm'               => ['ar' => 'مزرعة',      'en' => 'Farm'],
        'chalet'             => ['ar' => 'شاليه',      'en' => 'Chalet'],
    ];

    public static array $purposes = [
        'rent' => ['ar' => 'إيجار', 'en' => 'Rent'],
        'sale' => ['ar' => 'شراء',  'en' => 'Buy'],
        'both' => ['ar' => 'إيجار أو شراء', 'en' => 'Rent or Buy'],
    ];

    public static array $statuses = [
        'new'        => ['ar' => 'جديد',         'en' => 'New',        'color' => 'bg-blue-100 text-blue-700'],
        'contacted'  => ['ar' => 'تم التواصل',   'en' => 'Contacted',  'color' => 'bg-yellow-100 text-yellow-700'],
        'interested' => ['ar' => 'مهتم',          'en' => 'Interested', 'color' => 'bg-green-100 text-green-700'],
        'closed'     => ['ar' => 'مغلق',          'en' => 'Closed',     'color' => 'bg-gray-100 text-gray-600'],
    ];

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
