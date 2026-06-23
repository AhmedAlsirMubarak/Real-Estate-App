<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;

class PropertyController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user  = auth()->user();
        $owner = $user->owner;
        abort_unless($owner, 403);

        $directIds = $owner->properties()->pluck('id');
        $sharedIds = $owner->sharedProperties()->pluck('properties.id');
        $allIds = $directIds->merge($sharedIds)->unique()->values();

        $properties = Property::with(['units', 'employee', 'owners' => fn ($q) => $q->where('owners.id', $owner->id)])
            ->whereIn('id', $allIds)
            ->get();

        return view('owner.properties.index', compact('properties', 'owner'));
    }

    public function show(Property $property)
    {
        /** @var \App\Models\User $user */
        $user  = auth()->user();
        $owner = $user->owner;
        abort_unless($owner, 403);

        $isDirectOwner = $property->owner_id === $owner->id;
        $isSharedOwner = $property->owners()->where('owners.id', $owner->id)->exists();
        abort_unless($isDirectOwner || $isSharedOwner, 403);

        $property->load(['units', 'owners.user', 'associations', 'employee']);

        return view('owner.properties.show', compact('property', 'owner'));
    }
}
