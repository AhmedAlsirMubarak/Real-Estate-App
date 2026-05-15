<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Association;
use App\Models\Property;
use Illuminate\Http\Request;

class AssociationController extends Controller
{
    public function index(Request $request)
    {
        $associations = Association::with(['property.owners', 'dues'])
            ->latest()
            ->paginate(15);

        return view('manager.associations.index', compact('associations'));
    }

    public function create()
    {
        $properties = Property::whereDoesntHave('association')->orderBy('name_ar')->get();
        return view('manager.associations.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'property_id'          => 'required|exists:properties,id|unique:associations,property_id',
            'name_ar'              => 'required|string|max:255',
            'name_en'              => 'required|string|max:255',
            'established_date'     => 'nullable|date',
            'monthly_fee_per_unit' => 'required|numeric|min:0',
            'description_ar'       => 'nullable|string',
            'description_en'       => 'nullable|string',
            'status'               => 'required|in:active,inactive',
        ]);

        $association = Association::create($data);

        return redirect()
            ->route('manager.associations.show', $association)
            ->with('success', __('Created Successfully'));
    }

    public function show(Association $association)
    {
        $association->load([
            'property.owners',
            'property.units',
            'dues' => fn ($q) => $q->latest('due_date')->limit(20),
            'dues.owner.user',
            'meetings' => fn ($q) => $q->latest('scheduled_at')->limit(10),
        ]);

        return view('manager.associations.show', compact('association'));
    }

    public function edit(Association $association)
    {
        return view('manager.associations.edit', compact('association'));
    }

    public function update(Request $request, Association $association)
    {
        $data = $request->validate([
            'name_ar'              => 'required|string|max:255',
            'name_en'              => 'required|string|max:255',
            'established_date'     => 'nullable|date',
            'monthly_fee_per_unit' => 'required|numeric|min:0',
            'description_ar'       => 'nullable|string',
            'description_en'       => 'nullable|string',
            'status'               => 'required|in:active,inactive',
        ]);

        $association->update($data);

        return redirect()
            ->route('manager.associations.show', $association)
            ->with('success', __('Updated Successfully'));
    }

    public function destroy(Association $association)
    {
        $association->delete();
        return redirect()->route('manager.associations.index')->with('success', __('Deleted Successfully'));
    }
}
