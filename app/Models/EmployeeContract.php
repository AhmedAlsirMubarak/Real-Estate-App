<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'title', 'type', 'start_date', 'end_date',
        'value', 'status', 'document_path', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'value'      => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function statusLabel(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return match ($this->status) {
            'draft'      => $isAr ? 'مسودة'   : 'Draft',
            'active'     => $isAr ? 'نشط'      : 'Active',
            'expired'    => $isAr ? 'منتهي'    : 'Expired',
            'terminated' => $isAr ? 'مُنهى'    : 'Terminated',
            default      => $this->status,
        };
    }

    public function typeLabel(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return match ($this->type) {
            'employment' => $isAr ? 'توظيف'    : 'Employment',
            'service'    => $isAr ? 'خدمة'     : 'Service',
            'freelance'  => $isAr ? 'مستقل'    : 'Freelance',
            'supplier'   => $isAr ? 'مورّد'    : 'Supplier',
            'other'      => $isAr ? 'أخرى'     : 'Other',
            default      => $this->type,
        };
    }
}
