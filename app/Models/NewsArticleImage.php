<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsArticleImage extends Model
{
    protected $fillable = ['news_article_id', 'path', 'is_primary', 'sort_order'];

    protected $casts = ['is_primary' => 'boolean'];

    public function article()
    {
        return $this->belongsTo(NewsArticle::class, 'news_article_id');
    }

    public function url(): string
    {
        if (blank($this->path)) return '';
        return asset('storage/' . $this->path);
    }
}
