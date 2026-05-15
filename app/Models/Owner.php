<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Owner extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'national_id',
        'phone',
        'bank_account',
        'commission_rate',
        'notes',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function sharedProperties()
    {
        return $this->belongsToMany(Property::class, 'property_owners')
            ->withPivot(['ownership_percentage', 'is_primary', 'since_date', 'notes'])
            ->withTimestamps();
    }

    public function dues()
    {
        return $this->hasMany(AssociationDue::class);
    }
}
