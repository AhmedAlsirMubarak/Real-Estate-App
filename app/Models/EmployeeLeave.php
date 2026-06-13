<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLeave extends Model
{
    protected $fillable = [
        'employee_id', 'type', 'start_date', 'end_date', 'days',
        'reason', 'status', 'approved_by', 'approved_at', 'notes',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'approved_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function typeLabel(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return match ($this->type) {
            'annual'    => $isAr ? 'سنوية'   : 'Annual',
            'sick'      => $isAr ? 'مرضية'   : 'Sick',
            'unpaid'    => $isAr ? 'بدون راتب' : 'Unpaid',
            'emergency' => $isAr ? 'طارئة'   : 'Emergency',
            default     => $this->type,
        };
    }

    public function statusLabel(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return match ($this->status) {
            'pending'  => $isAr ? 'قيد المراجعة' : 'Pending',
            'approved' => $isAr ? 'موافق عليها'  : 'Approved',
            'rejected' => $isAr ? 'مرفوضة'       : 'Rejected',
            default    => $this->status,
        };
    }
}
