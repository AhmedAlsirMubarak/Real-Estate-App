<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'serial_number', 'category', 'assigned_to',
        'status', 'purchase_date', 'purchase_price', 'notes',
    ];

    protected $casts = [
        'purchase_date'  => 'date',
        'purchase_price' => 'decimal:2',
    ];

    public function assignedEmployee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function categoryLabel(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return match ($this->category) {
            'laptop'           => $isAr ? 'لابتوب'       : 'Laptop',
            'mobile'           => $isAr ? 'هاتف'         : 'Mobile',
            'office_equipment' => $isAr ? 'معدات مكتبية' : 'Office Equipment',
            default            => $this->category,
        };
    }

    public function statusLabel(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return match ($this->status) {
            'available'    => $isAr ? 'متاح'         : 'Available',
            'assigned'     => $isAr ? 'مُخصص'        : 'Assigned',
            'under_repair' => $isAr ? 'في الصيانة'   : 'Under Repair',
            'retired'      => $isAr ? 'متقاعد'       : 'Retired',
            default        => $this->status,
        };
    }
}
