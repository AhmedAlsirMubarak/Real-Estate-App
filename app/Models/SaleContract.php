<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_number',
        'unit_id',
        'buyer_id',
        'total_price',
        'down_payment',
        'payment_plan',
        'installment_count',
        'installment_amount',
        'contract_date',
        'handover_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'contract_date'      => 'date',
        'handover_date'      => 'date',
        'total_price'        => 'decimal:2',
        'down_payment'       => 'decimal:2',
        'installment_amount' => 'decimal:2',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class)->orderBy('installment_number');
    }

    public function paidInstallments()
    {
        return $this->hasMany(Installment::class)->where('status', 'paid');
    }

    public function employeeCommissions()
    {
        return $this->hasMany(EmployeeCommission::class);
    }

    public function pendingInstallments()
    {
        return $this->hasMany(Installment::class)->where('status', 'pending');
    }

    public function remainingBalance(): float
    {
        $paid = $this->paidInstallments()->sum('amount') + (float) $this->down_payment;
        return (float) $this->total_price - $paid;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft'     => 'مسودة',
            'active'    => 'نشط',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default     => $this->status,
        };
    }
}
