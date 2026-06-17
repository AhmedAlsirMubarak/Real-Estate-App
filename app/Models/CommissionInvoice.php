<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionInvoice extends Model
{
    protected $fillable = [
        'property_id',
        'invoice_number',
        'invoice_for',
        'recipient_name',
        'duration_months',
        'monthly_rent',
        'commission_rate',
        'total_rent',
        'commission_amount',
        'invoice_date',
        'notes',
        'file_path',
    ];

    protected $casts = [
        'invoice_date'      => 'date',
        'duration_months'   => 'decimal:2',
        'monthly_rent'      => 'decimal:3',
        'commission_rate'   => 'decimal:2',
        'total_rent'        => 'decimal:3',
        'commission_amount' => 'decimal:3',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
