<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        if (!$this->path) return '';
        return Storage::disk('public')->url($this->path);
    }
}
