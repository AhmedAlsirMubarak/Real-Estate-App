<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Association extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'created_by',
        'name_ar',
        'name_en',
        'established_date',
        'monthly_fee_per_unit',
        'description_ar',
        'description_en',
        'status',
        'electricity_account_number',
        'water_account_number',
        'no_objection_certificate_path',
        'sketch_path',
        'association_certificate_path',
        'personal_id_path',
        'manager_id_path',
    ];

    protected $casts = [
        'established_date'     => 'date',
        'monthly_fee_per_unit' => 'decimal:2',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function dues()
    {
        return $this->hasMany(AssociationDue::class);
    }

    public function meetings()
    {
        return $this->hasMany(AssociationMeeting::class);
    }

    public function noObjectionCertificates()
    {
        return $this->hasMany(NoObjectionCertificate::class)->latest();
    }

    public function noObjectionSaleCertificates()
    {
        return $this->hasMany(NoObjectionSaleCertificate::class)->latest();
    }

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale() === 'en' ? 'en' : 'ar';
        return $this->attributes["name_{$locale}"] ?: ($this->attributes['name_ar'] ?? '');
    }

    public function getDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale() === 'en' ? 'en' : 'ar';
        return $this->attributes["description_{$locale}"] ?? null;
    }
}
