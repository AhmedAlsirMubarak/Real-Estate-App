<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use App\Models\Property;
use App\Models\WebsiteSection;

class HomeController extends Controller
{
    public function index()
    {
        $sections = WebsiteSection::where('page', 'home')
            ->where('is_active', true)
            ->with(['activeItems'])
            ->orderBy('sort_order')
            ->get()
            ->keyBy('key');

        $footer = WebsiteSection::where('page', 'global')->where('key', 'footer')->first();

        $featured = Property::with([
                'images'  => fn($q) => $q->orderByDesc('is_primary')->orderBy('sort_order')->limit(3),
                'units'   => fn($q) => $q->select('id','property_id','status','listing_type','rent_price','sale_price','bedrooms','bathrooms','area'),
            ])
            ->withCount('units')
            ->where('status', 'active')
            ->latest()
            ->take(12)
            ->get();

        // Property counts per type for the types section
        $typeCounts = Property::where('status', 'active')
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        // Cities for location tabs — get both AR and EN names
        $cities = Property::where('status', 'active')
            ->whereNotNull('city')
            ->select('city', 'city_ar', 'city_en')
            ->distinct()
            ->orderBy('city')
            ->get()
            ->unique('city')
            ->take(6)
            ->values();

        // Properties grouped by city for tab switching (keyed by city AR)
        $propertiesByCity = collect();
        foreach ($cities as $cityObj) {
            $propertiesByCity[$cityObj->city] = Property::with([
                    'images'  => fn($q) => $q->orderByDesc('is_primary')->orderBy('sort_order')->limit(3),
                    'units'   => fn($q) => $q->select('id','property_id','status','listing_type','rent_price','sale_price','bedrooms','bathrooms','area'),
                ])
                ->withCount('units')
                ->where('status', 'active')
                ->where('city', $cityObj->city)
                ->latest()
                ->take(8)
                ->get();
        }

        // Showcase properties grid — load up to 12 for the load-more grid
        $showcaseProperties = Property::with([
                'images' => fn($q) => $q->orderByDesc('is_primary')->orderBy('sort_order')->limit(3),
                'units'  => fn($q) => $q->select('id','property_id','status','listing_type','rent_price','sale_price','bedrooms','bathrooms','area'),
            ])
            ->withCount('units')
            ->where('status', 'active')
            ->latest()
            ->take(12)
            ->get();

        $latestNews = NewsArticle::active()->published()
            ->with(['images' => fn($q) => $q->orderByDesc('is_primary')->orderBy('sort_order')])
            ->orderByDesc('published_at')
            ->take(4)
            ->get();

        return view('welcome', compact('sections', 'footer', 'featured', 'typeCounts', 'cities', 'propertiesByCity', 'showcaseProperties', 'latestNews'));
    }
}
