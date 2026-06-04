<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoObjectionSaleCertificate extends Model
{
    protected $fillable = [
        'association_id',
        'generated_by',
        'ref_number',
        'seller_name',
        'seller_id',
        'unit_number',
        'buyer_name',
        'buyer_phone',
        'buyer_id',
        'file_path',
    ];

    public function association()
    {
        return $this->belongsTo(Association::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
