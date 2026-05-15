<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_contract_id',
        'tenant_id',
        'amount',
        'month',
        'year',
        'status',
        'paid_at',
        'confirmed_by',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function rentalContract()
    {
        return $this->belongsTo(RentalContract::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function confirmedByUser()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function employeeCommissions()
    {
        return $this->hasMany(EmployeeCommission::class);
    }

    public function monthName(): string
    {
        $months = app()->getLocale() === 'en'
            ? [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
            ]
            : [
                1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
            ];
        return $months[$this->month] ?? $this->month;
    }

    public function statusLabel(): string
    {
        if (app()->getLocale() === 'en') {
            return match($this->status) {
                'pending' => 'Pending',
                'paid' => 'Paid',
                'overdue' => 'Overdue',
                default => $this->status,
            };
        }

        return match($this->status) {
            'pending' => 'معلق',
            'paid' => 'مدفوع',
            'overdue' => 'متأخر',
            default => $this->status,
        };
    }
}
