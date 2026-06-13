<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAttendance extends Model
{
    protected $table = 'employee_attendance';

    protected $fillable = [
        'employee_id', 'date', 'status', 'check_in', 'check_out', 'hours_worked', 'notes',
    ];

    protected $casts = ['date' => 'date'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function statusLabel(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return match ($this->status) {
            'present'  => $isAr ? 'حاضر'    : 'Present',
            'absent'   => $isAr ? 'غائب'    : 'Absent',
            'late'     => $isAr ? 'متأخر'   : 'Late',
            'half_day' => $isAr ? 'نصف يوم' : 'Half Day',
            'holiday'  => $isAr ? 'إجازة'   : 'Holiday',
            default    => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'present'  => 'bg-green-100 text-green-700',
            'absent'   => 'bg-red-100 text-red-700',
            'late'     => 'bg-orange-100 text-orange-700',
            'half_day' => 'bg-blue-100 text-blue-700',
            'holiday'  => 'bg-purple-100 text-purple-700',
            default    => 'bg-gray-100 text-gray-700',
        };
    }
}
