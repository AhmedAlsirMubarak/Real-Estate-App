<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'created_by',
        'national_id',
        'phone',
        'emergency_contact',
        'referral_employee_id',
        'referral_commission_rate',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referralEmployee()
    {
        return $this->belongsTo(User::class, 'referral_employee_id');
    }

    public function rentalContracts()
    {
        return $this->hasMany(RentalContract::class);
    }

    public function activeContract()
    {
        return $this->hasOne(RentalContract::class)->where('status', 'active')->latest();
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }
}
