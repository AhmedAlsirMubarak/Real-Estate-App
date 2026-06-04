<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class PropertyImage extends Model
{
    protected $fillable = ['property_id', 'path', 'is_primary', 'sort_order'];

    protected $casts = ['is_primary' => 'boolean'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function url(): string
    {
        if (blank($this->path)) return '';
        return asset('storage/' . $this->path);
    }
}
