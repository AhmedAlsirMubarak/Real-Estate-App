<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PropertyOwnerController extends Controller
{
    public function index(Property $property)
    {
        $property->load('owners.user');
        $availableOwners = Owner::with('user')
            ->whereNotIn('id', $property->owners->pluck('id'))
            ->get();
        return view('manager.properties.owners', compact('property', 'availableOwners'));
    }

    public function store(Request $request, Property $property)
    {
        $data = $request->validate([
            'owner_id'             => 'required|exists:owners,id',
            'ownership_percentage' => 'required|numeric|min:0.01|max:100',
            'is_primary'           => 'nullable|boolean',
            'since_date'           => 'nullable|date',
            'notes'                => 'nullable|string',
        ]);

        $currentTotal = $property->owners()->sum('ownership_percentage');
        if ($currentTotal + $data['ownership_percentage'] > 100.01) {
            throw ValidationException::withMessages([
                'ownership_percentage' => __('Operation Failed') . " — total > 100%",
            ]);
        }

        if ($request->boolean('is_primary')) {
            $property->owners()->newPivotStatement()->where('property_id', $property->id)->update(['is_primary' => false]);
        }

        $property->owners()->attach($data['owner_id'], [
            'ownership_percentage' => $data['ownership_percentage'],
            'is_primary'           => $request->boolean('is_primary'),
            'since_date'           => $data['since_date'] ?? null,
            'notes'                => $data['notes'] ?? null,
        ]);

        if ($request->boolean('is_primary')) {
            $property->update(['owner_id' => $data['owner_id']]);
        }

        return back()->with('success', __('Operation Successful'));
    }

    public function update(Request $request, Property $property, Owner $owner)
    {
        $data = $request->validate([
            'ownership_percentage' => 'required|numeric|min:0.01|max:100',
            'is_primary'           => 'nullable|boolean',
            'since_date'           => 'nullable|date',
            'notes'                => 'nullable|string',
        ]);

        $currentTotal = $property->owners()
            ->where('owners.id', '!=', $owner->id)
            ->sum('ownership_percentage');

        if ($currentTotal + $data['ownership_percentage'] > 100.01) {
            throw ValidationException::withMessages([
                'ownership_percentage' => __('Operation Failed') . " — total > 100%",
            ]);
        }

        if ($request->boolean('is_primary')) {
            $property->owners()->newPivotStatement()->where('property_id', $property->id)->update(['is_primary' => false]);
            $property->update(['owner_id' => $owner->id]);
        }

        $property->owners()->updateExistingPivot($owner->id, [
            'ownership_percentage' => $data['ownership_percentage'],
            'is_primary'           => $request->boolean('is_primary'),
            'since_date'           => $data['since_date'] ?? null,
            'notes'                => $data['notes'] ?? null,
        ]);

        return back()->with('success', __('Updated Successfully'));
    }

    public function destroy(Property $property, Owner $owner)
    {
        $property->owners()->detach($owner->id);

        if ($property->owner_id === $owner->id) {
            $newPrimary = $property->owners()->wherePivot('is_primary', true)->first()
                ?? $property->owners()->first();
            $property->update(['owner_id' => $newPrimary?->id]);
        }

        return back()->with('success', __('Deleted Successfully'));
    }
}
