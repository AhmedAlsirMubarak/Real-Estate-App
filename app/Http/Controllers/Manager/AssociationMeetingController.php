<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Association;
use App\Models\AssociationMeeting;
use Illuminate\Http\Request;

class AssociationMeetingController extends Controller
{
    public function index()
    {
        $meetings = AssociationMeeting::with('association.property')
            ->latest('scheduled_at')
            ->paginate(15);

        return view('manager.meetings.index', compact('meetings'));
    }

    public function create(Request $request)
    {
        $associations = Association::with('property')->where('status', 'active')->get();
        $selected = $request->query('association');
        return view('manager.meetings.create', compact('associations', 'selected'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'association_id' => 'required|exists:associations,id',
            'title_ar'       => 'required|string|max:255',
            'title_en'       => 'required|string|max:255',
            'scheduled_at'   => 'required|date',
            'location_ar'    => 'nullable|string|max:255',
            'location_en'    => 'nullable|string|max:255',
            'agenda_ar'      => 'nullable|string',
            'agenda_en'      => 'nullable|string',
            'status'         => 'required|in:scheduled,completed,cancelled',
        ]);

        $meeting = AssociationMeeting::create($data);

        return redirect()
            ->route('manager.meetings.show', $meeting)
            ->with('success', __('Created Successfully'));
    }

    public function show(AssociationMeeting $meeting)
    {
        $meeting->load('association.property');
        return view('manager.meetings.show', compact('meeting'));
    }

    public function edit(AssociationMeeting $meeting)
    {
        $associations = Association::with('property')->get();
        return view('manager.meetings.edit', compact('meeting', 'associations'));
    }

    public function update(Request $request, AssociationMeeting $meeting)
    {
        $data = $request->validate([
            'title_ar'     => 'required|string|max:255',
            'title_en'     => 'required|string|max:255',
            'scheduled_at' => 'required|date',
            'location_ar'  => 'nullable|string|max:255',
            'location_en'  => 'nullable|string|max:255',
            'agenda_ar'    => 'nullable|string',
            'agenda_en'    => 'nullable|string',
            'minutes_ar'   => 'nullable|string',
            'minutes_en'   => 'nullable|string',
            'status'       => 'required|in:scheduled,completed,cancelled',
        ]);

        $meeting->update($data);

        return redirect()
            ->route('manager.meetings.show', $meeting)
            ->with('success', __('Updated Successfully'));
    }

    public function destroy(AssociationMeeting $meeting)
    {
        $meeting->delete();
        return redirect()->route('manager.meetings.index')->with('success', __('Deleted Successfully'));
    }
}
