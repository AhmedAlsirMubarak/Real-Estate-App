<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expensable_type',
        'expensable_id',
        'scope',
        'category',
        'title',
        'title_ar',
        'title_en',
        'amount',
        'expense_date',
        'description',
        'description_ar',
        'description_en',
        'paid_by',
        'receipt_path',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function expensable(): MorphTo
    {
        return $this->morphTo();
    }

    public function invoices()
    {
        return $this->hasMany(ExpenseInvoice::class);
    }

    public function paidByUser()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function scopeForCompany($query)
    {
        return $query->where('scope', 'company');
    }

    public function scopeForProperty($query, ?int $propertyId = null)
    {
        $query->where('scope', 'property');
        if ($propertyId) {
            $query->where('expensable_type', Property::class)
                  ->where('expensable_id', $propertyId);
        }
        return $query;
    }

    public function categoryLabel(): string
    {
        return match ($this->category) {
            'utilities'   => 'مرافق',
            'maintenance' => 'صيانة',
            'salaries'    => 'رواتب',
            'marketing'   => 'تسويق',
            'taxes'       => 'ضرائب',
            'supplies'    => 'مستلزمات',
            'insurance'   => 'تأمين',
            'legal'       => 'قانوني',
            'other'       => 'أخرى',
            default       => $this->category,
        };
    }

    public function scopeLabel(): string
    {
        return match ($this->scope) {
            'company'  => 'الشركة',
            'property' => 'عقار',
            default    => $this->scope,
        };
    }

    public function getTitleAttribute($value): ?string
    {
        return $this->localizedColumnValue('title', $value);
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
