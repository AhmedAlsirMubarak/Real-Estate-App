<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Installment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_contract_id',
        'installment_number',
        'amount',
        'due_date',
        'status',
        'paid_at',
        'confirmed_by',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at'  => 'datetime',
        'amount'   => 'decimal:2',
    ];

    public function saleContract()
    {
        return $this->belongsTo(SaleContract::class);
    }

    public function confirmedByUser()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'معلق',
            'paid'    => 'مدفوع',
            'overdue' => 'متأخر',
            default   => $this->status,
        };
    }
}
