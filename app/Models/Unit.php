<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'unit_number',
        'floor',
        'type',
        'area',
        'bedrooms',
        'bathrooms',
        'listing_type',
        'rent_price',
        'sale_price',
        'status',
        'notes',
    ];

    protected $casts = [
        'rent_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'area'       => 'decimal:2',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function rentalContracts()
    {
        return $this->hasMany(RentalContract::class);
    }

    public function activeRentalContract()
    {
        return $this->hasOne(RentalContract::class)->where('status', 'active');
    }

    public function saleContracts()
    {
        return $this->hasMany(SaleContract::class);
    }

    public function activeSaleContract()
    {
        return $this->hasOne(SaleContract::class)->whereIn('status', ['active', 'completed'])->latest();
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function isRented(): bool
    {
        return $this->status === 'rented';
    }

    public function isSold(): bool
    {
        return $this->status === 'sold';
    }

    public function statusLabel(): string
    {
        if (app()->getLocale() === 'en') {
            return match ($this->status) {
                'available' => 'Available',
                'rented' => 'Rented',
                'sold' => 'Sold',
                'reserved' => 'Reserved',
                'maintenance' => 'Maintenance',
                default => $this->status,
            };
        }

        return match ($this->status) {
            'available'   => 'متاح',
            'rented'      => 'مؤجر',
            'sold'        => 'مباع',
            'reserved'    => 'محجوز',
            'maintenance' => 'صيانة',
            default       => $this->status,
        };
    }

    public function typeLabel(): string
    {
        if (app()->getLocale() === 'en') {
            return match ($this->type) {
                'apartment' => 'Apartment',
                'shop' => 'Shop',
                'office' => 'Office',
                'studio' => 'Studio',
                'villa_unit' => 'Villa',
                'farm_unit' => 'Farm',
                'chalet_unit' => 'Chalet',
                default => $this->type,
            };
        }

        return match ($this->type) {
            'apartment'   => 'شقة',
            'shop'        => 'محل تجاري',
            'office'      => 'مكتب',
            'studio'      => 'استوديو',
            'villa_unit'  => 'فيلا',
            'farm_unit'   => 'مزرعة',
            'chalet_unit' => 'شاليه',
            default       => $this->type,
        };
    }
}
