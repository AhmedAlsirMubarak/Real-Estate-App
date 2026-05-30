<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceRequestImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'priority'    => 'required|in:low,medium,high,urgent',
            'images'      => 'nullable|array|max:10',
            'images.*'    => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $maintenanceRequest = MaintenanceRequest::create([
            'tenant_id'   => $tenant->id,
            'unit_id'     => $activeContract->unit_id,
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'priority'    => $validated['priority'],
            'status'      => 'pending',
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if (! $image->isValid()) {
                    continue;
                }
                $ext      = strtolower($image->getClientOriginalExtension() ?: 'jpg');
                $filename = sha1(uniqid('', true) . microtime()) . '.' . $ext;
                try {
                    $stored = Storage::disk('public')->putFileAs('maintenance-images', $image, $filename);
                } catch (\Throwable) {
                    continue;
                }
                if (!$stored) {
                    continue;
                }
                MaintenanceRequestImage::create([
                    'maintenance_request_id' => $maintenanceRequest->id,
                    'path'                   => $stored,
                ]);
            }
        }

        return redirect()->route('tenant.maintenance.index')
            ->with('success', 'تم تقديم طلب الصيانة بنجاح');
    }

    public function show(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $tenant = $request->user()->tenant;

        if ($maintenanceRequest->tenant_id !== $tenant->id) {
            abort(403);
        }

        $maintenanceRequest->load(['unit.property', 'assignedEmployee', 'images']);
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
