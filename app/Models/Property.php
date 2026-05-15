<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'name_ar',
        'name_en',
        'type',
        'purpose',
        'address',
        'address_ar',
        'address_en',
        'city',
        'city_ar',
        'city_en',
        'description',
        'description_ar',
        'description_en',
        'owner_id',
        'employee_id',
        'floors',
        'total_area',
        'bedrooms',
        'bathrooms',
        'status',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function owners()
    {
        return $this->belongsToMany(Owner::class, 'property_owners')
            ->withPivot(['ownership_percentage', 'is_primary', 'since_date', 'notes'])
            ->withTimestamps();
    }

    public function association()
    {
        return $this->hasOne(Association::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function expenses(): MorphMany
    {
        return $this->morphMany(Expense::class, 'expensable');
    }

    public function maintenanceRequests()
    {
        return $this->hasManyThrough(MaintenanceRequest::class, Unit::class);
    }

    public function employeeCommissions()
    {
        return $this->hasMany(EmployeeCommission::class);
    }

    public function isCompanyOwned(): bool
    {
        return $this->owner_id === null;
    }

    public function hasFloors(): bool
    {
        return $this->type === 'apartment_building';
    }

    public function typeLabel(): string
    {
        if (app()->getLocale() === 'en') {
            return match ($this->type) {
                'apartment_building' => 'Apartment Building',
                'villa' => 'Villa',
                'farm' => 'Farm',
                'chalet' => 'Chalet',
                default => $this->type,
            };
        }

        return match ($this->type) {
            'apartment_building' => 'عمارة',
            'villa'              => 'فيلا',
            'farm'               => 'مزرعة',
            'chalet'             => 'شاليه',
            default              => $this->type,
        };
    }

    public function purposeLabel(): string
    {
        if (app()->getLocale() === 'en') {
            return match ($this->purpose) {
                'rent' => 'Rent',
                'sale' => 'Sale',
                'both' => 'Rent & Sale',
                default => $this->purpose,
            };
        }

        return match ($this->purpose) {
            'rent' => 'إيجار',
            'sale' => 'بيع',
            'both' => 'إيجار وبيع',
            default => $this->purpose,
        };
    }

    public function statusLabel(): string
    {
        if (app()->getLocale() === 'en') {
            return match ($this->status) {
                'active' => 'Active',
                'sold' => 'Sold',
                'under_maintenance' => 'Under Maintenance',
                'archived' => 'Archived',
                default => $this->status,
            };
        }

        return match ($this->status) {
            'active'            => 'نشط',
            'sold'              => 'مباع',
            'under_maintenance' => 'قيد الصيانة',
            'archived'          => 'مؤرشف',
            default             => $this->status,
        };
    }

    public function getNameAttribute($value): ?string
    {
        return $this->localizedColumnValue('name', $value);
    }

    public function getAddressAttribute($value): ?string
    {
        return $this->localizedColumnValue('address', $value);
    }

    public function getCityAttribute($value): ?string
    {
        return $this->localizedColumnValue('city', $value);
    }

    public function getDescriptionAttribute($value): ?string
    {
        return $this->localizedColumnValue('description', $value);
    }

    private function localizedColumnValue(string $baseColumn, mixed $fallback): ?string
    {
        $locale = app()->getLocale() === 'en' ? 'en' : 'ar';
        $primary = $this->attributes["{$baseColumn}_{$locale}"] ?? null;
        $secondary = $this->attributes["{$baseColumn}_" . ($locale === 'ar' ? 'en' : 'ar')] ?? null;

        return $primary ?: ($fallback ?: $secondary);
    }
}
