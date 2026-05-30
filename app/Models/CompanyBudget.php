<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyBudget extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'category', 'period_year', 'period_month',
        'allocated_amount', 'spent_amount', 'status', 'notes',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'spent_amount'     => 'decimal:2',
    ];

    public function remaining(): float
    {
        return (float) $this->allocated_amount - (float) $this->spent_amount;
    }

    public function usagePercent(): int
    {
        if ((float) $this->allocated_amount <= 0) return 0;
        return min(100, (int) round(((float) $this->spent_amount / (float) $this->allocated_amount) * 100));
    }

    public function periodLabel(): string
    {
        $isAr = app()->getLocale() === 'ar';
        if (! $this->period_month) {
            return $isAr ? "سنوي {$this->period_year}" : "Annual {$this->period_year}";
        }
        $months = $isAr
            ? ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر']
            : ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return ($months[$this->period_month] ?? '') . ' ' . $this->period_year;
    }

    public function categoryLabel(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return match ($this->category) {
            'hr'          => $isAr ? 'الموارد البشرية' : 'HR',
            'operations'  => $isAr ? 'العمليات'        : 'Operations',
            'it'          => $isAr ? 'تقنية المعلومات' : 'IT',
            'marketing'   => $isAr ? 'التسويق'         : 'Marketing',
            'maintenance' => $isAr ? 'الصيانة'         : 'Maintenance',
            'other'       => $isAr ? 'أخرى'            : 'Other',
            default       => $this->category,
        };
    }

    public function statusLabel(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return match ($this->status) {
            'draft'    => $isAr ? 'مسودة'   : 'Draft',
            'approved' => $isAr ? 'معتمد'   : 'Approved',
            'closed'   => $isAr ? 'مغلق'    : 'Closed',
            default    => $this->status,
        };
    }
}
