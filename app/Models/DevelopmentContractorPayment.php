<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevelopmentContractorPayment extends Model
{
    protected $fillable = ['development_contractor_id', 'amount', 'description', 'paid_at'];

    protected $casts = ['paid_at' => 'date', 'amount' => 'decimal:2'];

    public function contractor()
    {
        return $this->belongsTo(DevelopmentContractor::class, 'development_contractor_id');
    }
}
