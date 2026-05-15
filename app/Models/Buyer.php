<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Buyer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'national_id',
        'phone',
        'address',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function saleContracts()
    {
        return $this->hasMany(SaleContract::class);
    }
}
