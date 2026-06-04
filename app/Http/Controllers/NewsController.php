<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;

class NewsController extends Controller
{
    public function index()
    {
        $articles = NewsArticle::active()->published()
            ->with(['images' => fn($q) => $q->orderByDesc('is_primary')->orderBy('sort_order')])
            ->orderByDesc('published_at')
            ->paginate(9);

        return view('news.index', compact('articles'));
    }

    public function show(NewsArticle $article)
    {
        abort_if(! $article->is_active, 404);
        $article->load('images');

        $related = NewsArticle::active()->published()
            ->with(['images' => fn($q) => $q->orderByDesc('is_primary')->orderBy('sort_order')])
            ->where('id', '!=', $article->id)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return view('news.show', compact('article', 'related'));
    }
}
