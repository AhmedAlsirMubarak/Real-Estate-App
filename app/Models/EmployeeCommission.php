<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'property_id',
        'payment_id',
        'sale_contract_id',
        'type',
        'base_amount',
        'rate',
        'commission_amount',
        'notes',
        'recorded_at',
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function saleContract()
    {
        return $this->belongsTo(SaleContract::class);
    }

    public function typeLabel(): string
    {
        if (app()->getLocale() === 'en') {
            return match ($this->type) {
                'rent_collection' => 'Rent Collection',
                'property_sale' => 'Property Sale',
                default => $this->type,
            };
        }

        return match ($this->type) {
            'rent_collection' => 'تحصيل إيجار',
            'property_sale' => 'بيع عقار',
            default => $this->type,
        };
    }
}
