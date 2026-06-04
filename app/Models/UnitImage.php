<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class UnitImage extends Model
{
    protected $fillable = ['unit_id', 'path', 'is_primary', 'sort_order'];

    protected $casts = ['is_primary' => 'boolean'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function url(): string
    {
        if (blank($this->path)) return '';
        return asset('storage/' . $this->path);
    }
}
