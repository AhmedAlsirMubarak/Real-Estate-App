<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'name_ar',
        'name_en',
        'email',
        'password',
        'phone',
        'is_blocked',
        'blocked_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_blocked' => 'boolean',
            'blocked_at' => 'datetime',
        ];
    }

    public function managedProperties()
    {
        return $this->hasMany(Property::class, 'employee_id');
    }

    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }

    public function owner()
    {
        return $this->hasOne(Owner::class);
    }

    public function buyer()
    {
        return $this->hasOne(Buyer::class);
    }

    public function assignedMaintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'assigned_employee_id');
    }

    public function employeeCommissions()
    {
        return $this->hasMany(EmployeeCommission::class, 'employee_id');
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class, 'employee_id');
    }

    public function getNameAttribute($value): ?string
    {
        $locale = app()->getLocale() === 'en' ? 'en' : 'ar';
        $primary = $this->attributes["name_{$locale}"] ?? null;
        $secondary = $this->attributes['name_' . ($locale === 'ar' ? 'en' : 'ar')] ?? null;

        return $primary ?: ($value ?: $secondary);
    }
}
