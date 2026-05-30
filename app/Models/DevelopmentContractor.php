<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevelopmentContractor extends Model
{
    protected $fillable = ['development_project_id', 'name', 'scope_of_work', 'contract_value'];

    protected $casts = ['contract_value' => 'decimal:2'];

    public function project()
    {
        return $this->belongsTo(DevelopmentProject::class, 'development_project_id');
    }

    public function payments()
    {
        return $this->hasMany(DevelopmentContractorPayment::class);
    }

    public function totalPaid(): float
    {
        return (float) $this->payments->sum('amount');
    }

    public function remaining(): float
    {
        return max(0, (float) $this->contract_value - $this->totalPaid());
    }

    public function paidPercent(): int
    {
        if ((float) $this->contract_value <= 0) return 0;
        return min(100, (int) round(($this->totalPaid() / (float) $this->contract_value) * 100));
    }
}
