<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $tenant = $request->user()->tenant;

        $requests = $tenant->maintenanceRequests()
            ->with('unit.property')
            ->latest()
            ->paginate(10);

        return view('tenant.maintenance.index', compact('requests'));
    }

    public function create(Request $request)
    {
        $tenant = $request->user()->tenant;
        $activeContract = $tenant->activeContract()->with('unit.property')->first();

        if (!$activeContract) {
            return redirect()->route('tenant.dashboard')
                ->with('error', 'لا يوجد عقد إيجار نشط');
        }

        return view('tenant.maintenance.create', compact('activeContract'));
    }

    public function store(Request $request)
    {
        $tenant = $request->user()->tenant;
        $activeContract = $tenant->activeContract()->first();

        if (!$activeContract) {
            return redirect()->route('tenant.dashboard')
                ->with('error', 'لا يوجد عقد إيجار نشط');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        MaintenanceRequest::create([
            'tenant_id' => $tenant->id,
            'unit_id' => $activeContract->unit_id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'pending',
        ]);

        return redirect()->route('tenant.maintenance.index')
            ->with('success', 'تم تقديم طلب الصيانة بنجاح');
    }

    public function show(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $tenant = $request->user()->tenant;

        if ($maintenanceRequest->tenant_id !== $tenant->id) {
            abort(403);
        }

        $maintenanceRequest->load(['unit.property', 'assignedEmployee']);
        return view('tenant.maintenance.show', compact('maintenanceRequest'));
    }

    public function destroy(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $tenant = $request->user()->tenant;

        if ($maintenanceRequest->tenant_id !== $tenant->id) {
            abort(403);
        }

        if ($maintenanceRequest->status !== 'pending') {
            return redirect()->route('tenant.maintenance.index')
                ->with('error', 'لا يمكن حذف طلب تجاوز مرحلة الانتظار');
        }

        $maintenanceRequest->delete();

        return redirect()->route('tenant.maintenance.index')
            ->with('success', 'تم حذف طلب الصيانة بنجاح');
    }
}
