<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\WebsiteSection;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with(['units' => fn($q) => $q->where('status', 'available')])
            ->where('status', 'active')
            ->where('is_hidden_from_public', false);

        // Purpose filter
        if ($request->filled('purpose') && in_array($request->purpose, ['rent', 'sale', 'both'])) {
            $query->where(function ($q) use ($request) {
                $q->where('purpose', $request->purpose)
                  ->orWhere('purpose', 'both');
            });
        }

        // Type filter
        if ($request->filled('type') && in_array($request->type, ['apartment_building', 'villa', 'farm', 'chalet', 'flat', 'land'])) {
            $query->where('type', $request->type);
        }

        // City filter
        if ($request->filled('city')) {
            $query->where(function ($q) use ($request) {
                $q->where('city', 'like', '%' . $request->city . '%')
                    ->orWhere('city_ar', 'like', '%' . $request->city . '%')
                    ->orWhere('city_en', 'like', '%' . $request->city . '%');
            });
        }

        // Bedrooms filter (at unit level — property must have at least one unit matching)
        if ($request->filled('bedrooms') && is_numeric($request->bedrooms)) {
            $query->whereHas('units', fn($q) => $q->where('bedrooms', '>=', (int)$request->bedrooms));
        }

        // Min/Max price on units
        if ($request->filled('min_price') && is_numeric($request->min_price)) {
            $minPrice = (float)$request->min_price;
            $query->whereHas('units', function ($q) use ($minPrice) {
                $q->where(function ($sq) use ($minPrice) {
                    $sq->whereNotNull('rent_price')->where('rent_price', '>=', $minPrice)
                       ->orWhereNotNull('sale_price')->where('sale_price', '>=', $minPrice);
                });
            });
        }

        if ($request->filled('max_price') && is_numeric($request->max_price)) {
            $maxPrice = (float)$request->max_price;
            $query->whereHas('units', function ($q) use ($maxPrice) {
                $q->where(function ($sq) use ($maxPrice) {
                    $sq->whereNotNull('rent_price')->where('rent_price', '<=', $maxPrice)
                       ->orWhereNotNull('sale_price')->where('sale_price', '<=', $maxPrice);
                });
            });
        }

        // Min area
        if ($request->filled('min_area') && is_numeric($request->min_area)) {
            $query->where(function ($q) use ($request) {
                $q->where('total_area', '>=', (float)$request->min_area)
                  ->orWhereHas('units', fn($sq) => $sq->where('area', '>=', (float)$request->min_area));
            });
        }

        // Sort
        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'oldest'    => $query->oldest(),
            'area_asc'  => $query->orderBy('total_area', 'asc'),
            'area_desc' => $query->orderBy('total_area', 'desc'),
            default     => $query->latest(),
        };

        $properties = $query->with(['images' => fn($q) => $q->orderByDesc('is_primary')->orderBy('sort_order')])->withCount([
            'units',
            'units as available_units_count' => fn($q) => $q->where('status', 'available'),
        ])->paginate(12)->withQueryString();

        // Distinct cities for the filter dropdown
        $cityColumn = app()->getLocale() === 'en' ? 'city_en' : 'city_ar';
        $fallbackColumn = app()->getLocale() === 'en' ? 'city_ar' : 'city_en';

        $cities = Property::where('status', 'active')
            ->where('is_hidden_from_public', false)
            ->where(function ($q) use ($cityColumn, $fallbackColumn) {
                $q->whereNotNull($cityColumn)->orWhereNotNull($fallbackColumn)->orWhereNotNull('city');
            })
            ->get([$cityColumn, $fallbackColumn, 'city'])
            ->map(fn($property) => $property->{$cityColumn} ?: ($property->{$fallbackColumn} ?: $property->city))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $contactPhone = WebsiteSection::where('page', 'home')->where('key', 'contact')
            ->with(['activeItems' => fn($q) => $q->where('icon', 'phone')])
            ->first()?->activeItems?->first()?->body_ar ?? '';

        return view('properties.index', compact('properties', 'cities', 'contactPhone'));
    }

    public function show(Property $property)
    {
        abort_if($property->status !== 'active' || $property->is_hidden_from_public, 404);

        $property->load(['images' => fn($q) => $q->orderByDesc('is_primary')->orderBy('sort_order')]);
        $property->loadCount([
            'units',
            'units as available_units_count' => fn($q) => $q->where('status', 'available'),
        ]);

        $units = $property->units()
            ->orderBy('floor')
            ->orderBy('unit_number')
            ->get();

        $availableUnits = $units->where('status', 'available');
        $minRentPrice   = $availableUnits->whereNotNull('rent_price')->min('rent_price');
        $maxRentPrice   = $availableUnits->whereNotNull('rent_price')->max('rent_price');
        $minSalePrice   = $availableUnits->whereNotNull('sale_price')->min('sale_price');
        $maxSalePrice   = $availableUnits->whereNotNull('sale_price')->max('sale_price');

        // Similar properties — same type, excluding current, max 4
        $similar = Property::where('status', 'active')
            ->where('is_hidden_from_public', false)
            ->where('id', '!=', $property->id)
            ->where('type', $property->type)
            ->with(['images' => fn($q) => $q->orderByDesc('is_primary')->limit(1)])
            ->withCount('units')
            ->with(['units' => fn($q) => $q->select('id','property_id','listing_type','rent_price','sale_price','bedrooms','bathrooms','area')])
            ->latest()
            ->take(4)
            ->get();

        // Contact info from website CMS
        $contactSection = WebsiteSection::where('page', 'home')->where('key', 'contact')
            ->with('activeItems')
            ->first();
        $contactPhone = $contactSection?->activeItems?->firstWhere('icon', 'phone')?->body_ar ?? '';
        $contactEmail = $contactSection?->activeItems?->firstWhere('icon', 'email')?->body_ar ?? '';
        $waNum        = preg_replace('/\D/', '', $contactPhone);

        return view('properties.show', compact(
            'property', 'units', 'availableUnits',
            'minRentPrice', 'maxRentPrice', 'minSalePrice', 'maxSalePrice',
            'similar', 'contactPhone', 'contactEmail', 'waNum'
        ));
    }
}
