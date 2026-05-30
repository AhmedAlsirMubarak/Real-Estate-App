<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\CompanyAsset;
use App\Models\User;
use Illuminate\Http\Request;

class CompanyAssetController extends Controller
{
    public function index(Request $request)
    {
        $query = CompanyAsset::with('assignedEmployee');

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($assigned = $request->query('assigned_to')) {
            $query->where('assigned_to', $assigned);
        }

        $assets    = $query->latest()->paginate(20)->withQueryString();
        $employees = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['employee', 'accountant', 'manager']))->orderBy('name')->get();

        return view('manager.assets.index', compact('assets', 'employees'));
    }

    public function create()
    {
        $employees = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['employee', 'accountant', 'manager']))->orderBy('name')->get();
        return view('manager.assets.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'serial_number'  => 'nullable|string|max:255',
            'category'       => 'required|in:laptop,mobile,office_equipment',
            'assigned_to'    => 'nullable|exists:users,id',
            'status'         => 'required|in:available,assigned,under_repair,retired',
            'purchase_date'  => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
        ]);

        if (! $data['assigned_to']) {
            $data['status'] = 'available';
        }

        CompanyAsset::create($data);

        return redirect()->route('manager.assets.index')->with('success', __('Created Successfully'));
    }

    public function edit(CompanyAsset $asset)
    {
        $employees = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['employee', 'accountant', 'manager']))->orderBy('name')->get();
        return view('manager.assets.edit', compact('asset', 'employees'));
    }

    public function update(Request $request, CompanyAsset $asset)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'serial_number'  => 'nullable|string|max:255',
            'category'       => 'required|in:laptop,mobile,office_equipment',
            'assigned_to'    => 'nullable|exists:users,id',
            'status'         => 'required|in:available,assigned,under_repair,retired',
            'purchase_date'  => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
        ]);

        if (! $data['assigned_to']) {
            $data['status'] = 'available';
        }

        $asset->update($data);

        return redirect()->route('manager.assets.index')->with('success', __('Updated Successfully'));
    }

    public function destroy(CompanyAsset $asset)
    {
        $asset->delete();
        return back()->with('success', __('Deleted Successfully'));
    }
}
