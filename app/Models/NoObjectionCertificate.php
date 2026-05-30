<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoObjectionCertificate extends Model
{
    protected $fillable = [
        'association_id',
        'generated_by',
        'ref_number',
        'lessor_name',
        'lessor_phone',
        'lessor_id',
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
