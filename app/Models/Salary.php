<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'period_month',
        'period_year',
        'base_salary',
        'housing_allowance',
        'transport_allowance',
        'food_allowance',
        'other_allowances',
        'bonuses',
        'deductions',
        'net_paid',
        'paid_at',
        'status',
        'notes',
        'paid_by',
    ];

    protected $casts = [
        'paid_at'             => 'date',
        'base_salary'         => 'decimal:2',
        'housing_allowance'   => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'food_allowance'      => 'decimal:2',
        'other_allowances'    => 'decimal:2',
        'bonuses'             => 'decimal:2',
        'deductions'          => 'decimal:2',
        'net_paid'            => 'decimal:2',
    ];

    public function totalAllowances(): float
    {
        return (float) $this->housing_allowance
             + (float) $this->transport_allowance
             + (float) $this->food_allowance
             + (float) $this->other_allowances;
    }

    public static function calcNet(array $data): float
    {
        return (float) ($data['base_salary'] ?? 0)
             + (float) ($data['housing_allowance'] ?? 0)
             + (float) ($data['transport_allowance'] ?? 0)
             + (float) ($data['food_allowance'] ?? 0)
             + (float) ($data['other_allowances'] ?? 0)
             + (float) ($data['bonuses'] ?? 0)
             - (float) ($data['deductions'] ?? 0);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function statusLabel(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return match ($this->status) {
            'draft'   => $isAr ? 'مسودة'   : 'Draft',
            'pending' => $isAr ? 'بانتظار الدفع' : 'Pending',
            'paid'    => $isAr ? 'مدفوع'   : 'Paid',
            default   => $this->status,
        };
    }

    public function periodLabel(): string
    {
        $monthNames = app()->getLocale() === 'ar'
            ? ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر']
            : ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        return ($monthNames[$this->period_month] ?? '') . ' ' . $this->period_year;
    }
}
