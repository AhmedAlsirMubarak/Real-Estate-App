<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use App\Models\Property;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [];

        $urls[] = [
            'loc'        => route('home'),
            'lastmod'    => now(),
            'changefreq' => 'daily',
            'priority'   => '1.0',
        ];

        $urls[] = [
            'loc'        => route('properties.index'),
            'lastmod'    => now(),
            'changefreq' => 'daily',
            'priority'   => '0.9',
        ];

        $urls[] = [
            'loc'        => route('news.index'),
            'lastmod'    => now(),
            'changefreq' => 'daily',
            'priority'   => '0.7',
        ];

        Property::where('status', 'active')
            ->where('is_hidden_from_public', false)
            ->select('id', 'updated_at')
            ->orderByDesc('updated_at')
            ->get()
            ->each(function (Property $property) use (&$urls) {
                $urls[] = [
                    'loc'        => route('properties.show', $property),
                    'lastmod'    => $property->updated_at,
                    'changefreq' => 'weekly',
                    'priority'   => '0.8',
                ];
            });

        NewsArticle::where('is_active', true)
            ->select('slug', 'updated_at')
            ->orderByDesc('updated_at')
            ->get()
            ->each(function (NewsArticle $article) use (&$urls) {
                $urls[] = [
                    'loc'        => route('news.show', $article),
                    'lastmod'    => $article->updated_at,
                    'changefreq' => 'monthly',
                    'priority'   => '0.6',
                ];
            });

        return response()
            ->view('sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }
}
