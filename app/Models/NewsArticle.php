<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsArticle extends Model
{
    protected $fillable = [
        'title_ar', 'title_en', 'slug',
        'excerpt_ar', 'excerpt_en',
        'body_ar', 'body_en',
        'image', 'published_at', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active'    => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title_ar ?: $article->title_en);
            }
            if (empty($article->published_at)) {
                $article->published_at = now();
            }
        });
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopePublished($q)
    {
        return $q->where('published_at', '<=', now());
    }

    public function title(): string
    {
        $locale = app()->getLocale();
        return ($locale === 'ar' ? $this->title_ar : $this->title_en) ?: $this->title_ar;
    }

    public function excerpt(): ?string
    {
        $locale = app()->getLocale();
        return ($locale === 'ar' ? $this->excerpt_ar : $this->excerpt_en) ?: $this->excerpt_ar;
    }

    public function body(): ?string
    {
        $locale = app()->getLocale();
        return ($locale === 'ar' ? $this->body_ar : $this->body_en) ?: $this->body_ar;
    }

    public function images()
    {
        return $this->hasMany(NewsArticleImage::class)->orderBy('sort_order')->orderByDesc('is_primary');
    }

    public function primaryImage(): ?NewsArticleImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images->firstWhere('is_primary', true) ?? $this->images->first();
        }
        return $this->images()->where('is_primary', true)->first()
            ?? $this->images()->first();
    }

    public function imageUrl(): ?string
    {
        if ($this->relationLoaded('images') && $this->images->isNotEmpty()) {
            $primary = $this->primaryImage();
            return $primary && $primary->path ? $primary->url() : null;
        }
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
