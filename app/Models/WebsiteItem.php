<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteItem extends Model
{
    protected $fillable = [
        'section_id', 'title_ar', 'title_en', 'subtitle_ar', 'subtitle_en',
        'body_ar', 'body_en', 'image', 'icon', 'value', 'url',
        'extra', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'extra'     => 'array',
        'is_active' => 'boolean',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(WebsiteSection::class, 'section_id');
    }

    public function title(): string
    {
        return app()->getLocale() === 'ar' ? ($this->title_ar ?? '') : ($this->title_en ?? $this->title_ar ?? '');
    }

    public function body(): string
    {
        return app()->getLocale() === 'ar' ? ($this->body_ar ?? '') : ($this->body_en ?? $this->body_ar ?? '');
    }

    public function imageUrl(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
