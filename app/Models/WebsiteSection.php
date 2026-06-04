<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebsiteSection extends Model
{
    protected $fillable = [
        'page', 'key', 'title_ar', 'title_en', 'subtitle_ar', 'subtitle_en',
        'body_ar', 'body_en', 'image', 'button_text_ar', 'button_text_en',
        'button_url', 'extra', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'extra'     => 'array',
        'is_active' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(WebsiteItem::class, 'section_id')->orderBy('sort_order');
    }

    public function activeItems(): HasMany
    {
        return $this->hasMany(WebsiteItem::class, 'section_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function title(): string
    {
        return app()->getLocale() === 'ar' ? ($this->title_ar ?? '') : ($this->title_en ?? $this->title_ar ?? '');
    }

    public function subtitle(): string
    {
        return app()->getLocale() === 'ar' ? ($this->subtitle_ar ?? '') : ($this->subtitle_en ?? $this->subtitle_ar ?? '');
    }

    public function body(): string
    {
        return app()->getLocale() === 'ar' ? ($this->body_ar ?? '') : ($this->body_en ?? $this->body_ar ?? '');
    }

    public function buttonText(): string
    {
        return app()->getLocale() === 'ar' ? ($this->button_text_ar ?? '') : ($this->button_text_en ?? $this->button_text_ar ?? '');
    }

    public function imageUrl(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public static function forPage(string $page): \Illuminate\Support\Collection
    {
        return static::where('page', $page)
            ->where('is_active', true)
            ->with('activeItems')
            ->orderBy('sort_order')
            ->get()
            ->keyBy('key');
    }
}
