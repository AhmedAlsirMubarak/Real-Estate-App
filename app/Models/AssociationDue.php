<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssociationDue extends Model
{
    use HasFactory;

    protected $fillable = [
        'association_id',
        'owner_id',
        'period_month',
        'period_year',
        'amount',
        'due_date',
        'paid_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at'  => 'date',
        'amount'   => 'decimal:2',
    ];

    public function association()
    {
        return $this->belongsTo(Association::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function statusLabel(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return match ($this->status) {
            'pending' => $isAr ? 'معلّق'  : 'Pending',
            'paid'    => $isAr ? 'مدفوع'  : 'Paid',
            'overdue' => $isAr ? 'متأخر'  : 'Overdue',
            'waived'  => $isAr ? 'معفى'   : 'Waived',
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
