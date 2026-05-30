<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Unit;
use App\Models\UnitImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UnitController extends Controller
{
    public function create(Property $property)
    {
        return view('manager.units.create', compact('property'));
    }

    public function store(Request $request, Property $property)
    {
        $validated = $this->validated($request, $property);
        $validated['property_id'] = $property->id;

        Unit::create($validated);

        return redirect()->route('manager.properties.show', $property)
            ->with('success', 'تم إضافة الوحدة بنجاح');
    }

    public function storeImage(Request $request, Property $property, Unit $unit)
    {
        $request->validate(['images' => 'required', 'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048']);

        $isFirst = $unit->images()->count() === 0;
        foreach ($request->file('images') as $i => $file) {
            $path = $this->storeUploadedFile($file, 'units/' . $unit->id);
            if (!$path) continue;
            $unit->images()->create([
                'path'       => $path,
                'is_primary' => $isFirst && $i === 0,
                'sort_order' => $unit->images()->count(),
            ]);
        }

        return back()->with('success', 'تم رفع الصور بنجاح');
    }

    public function destroyImage(Property $property, Unit $unit, UnitImage $image)
    {
        Storage::disk('public')->delete($image->path);
        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $next = $unit->images()->first();
            $next?->update(['is_primary' => true]);
        }

        return back()->with('success', 'تم حذف الصورة');
    }

    public function setPrimaryImage(Property $property, Unit $unit, UnitImage $image)
    {
        $unit->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'تم تعيين الصورة الرئيسية');
    }

    public function edit(Property $property, Unit $unit)
    {
        $unit->load('images');
        return view('manager.units.edit', compact('property', 'unit'));
    }

    public function update(Request $request, Property $property, Unit $unit)
    {
        $validated = $this->validated($request, $property, updating: true);
        $unit->update($validated);

        return redirect()->route('manager.properties.show', $property)
            ->with('success', 'تم تحديث الوحدة بنجاح');
    }

    public function destroy(Property $property, Unit $unit)
    {
        $unit->delete();
        return redirect()->route('manager.properties.show', $property)
            ->with('success', 'تم حذف الوحدة بنجاح');
    }

    private function validated(Request $request, Property $property, bool $updating = false): array
    {
        $rules = [
            'unit_number'  => 'nullable|string|max:50',
            'floor'        => 'nullable|integer|min:0',
            'type'         => 'required|in:apartment,shop,office,studio,villa_unit,farm_unit,chalet_unit',
            'area'         => 'nullable|numeric|min:0',
            'bedrooms'     => 'nullable|integer|min:0',
            'bathrooms'    => 'nullable|integer|min:0',
            'listing_type' => 'required|in:rent,sale,both',
            'rent_price'   => 'nullable|numeric|min:0|required_if:listing_type,rent,both',
            'sale_price'   => 'nullable|numeric|min:0|required_if:listing_type,sale,both',
            'notes'        => 'nullable|string',
        ];

        if ($updating) {
            $rules['status'] = 'required|in:available,rented,sold,reserved,maintenance';
        }

        return $request->validate($rules);
    }

    private function storeUploadedFile(\Illuminate\Http\UploadedFile $file, string $directory): string|false
    {
        if (! $file->isValid()) {
            return false;
        }
        $ext      = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = sha1(uniqid('', true) . microtime()) . '.' . $ext;
        try {
            $stored = Storage::disk('public')->putFileAs($directory, $file, $filename);
        } catch (\Throwable) {
            return false;
        }
        return $stored ?: false;
    }
}
