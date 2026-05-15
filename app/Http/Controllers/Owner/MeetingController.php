<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AssociationMeeting;

class MeetingController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user  = auth()->user();
        $owner = $user->owner;
        abort_unless($owner, 403);

        $directIds = $owner->properties()->pluck('id');
        $sharedIds = $owner->sharedProperties()->pluck('properties.id');
        $propertyIds = $directIds->merge($sharedIds)->unique();

        $meetings = AssociationMeeting::with('association.property')
            ->whereHas('association', fn ($q) => $q->whereIn('property_id', $propertyIds))
            ->latest('scheduled_at')
            ->paginate(15);

        return view('owner.meetings.index', compact('meetings'));
    }

    public function show(AssociationMeeting $meeting)
    {
        /** @var \App\Models\User $user */
        $user  = auth()->user();
        $owner = $user->owner;
        abort_unless($owner, 403);

        $meeting->load('association.property.owners');
        $belongs = $meeting->association->property->owner_id === $owner->id
            || $meeting->association->property->owners->contains('id', $owner->id);
        abort_unless($belongs, 403);

        return view('owner.meetings.show', compact('meeting'));
    }
}
