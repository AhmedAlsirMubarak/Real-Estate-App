<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $employee = $request->user();
        $status = $request->input('status', 'pending');

        $requests = MaintenanceRequest::whereHas('unit.property', function ($q) use ($employee) {
            $q->where('employee_id', $employee->id);
        })->when($status !== 'all', fn($q) => $q->where('status', $status))
          ->with(['tenant.user', 'unit.property'])
          ->latest()
          ->paginate(15);

        return view('employee.maintenance.index', compact('requests', 'status'));
    }

    public function show(MaintenanceRequest $maintenanceRequest)
    {
        $this->ensureEmployeeCanManageRequest(auth()->id(), $maintenanceRequest);
        $maintenanceRequest->load(['tenant.user', 'unit.property', 'assignedEmployee']);
        return view('employee.maintenance.show', compact('maintenanceRequest'));
    }

    public function updateStatus(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $this->ensureEmployeeCanManageRequest($request->user()->id, $maintenanceRequest);
        $validated = $request->validate([
            'status' => 'required|in:in_progress,completed,rejected',
            'employee_notes' => 'nullable|string',
            'required_tools' => 'nullable|string|max:2000',
            'requires_external_worker' => 'nullable|boolean',
            'external_worker_name' => 'nullable|string|max:255|required_if:requires_external_worker,1',
            'external_worker_cost' => 'nullable|numeric|min:0|required_if:requires_external_worker,1',
        ]);

        $requiresExternalWorker = (bool) ($validated['requires_external_worker'] ?? false);

        $maintenanceRequest->update([
            'status' => $validated['status'],
            'employee_notes' => $validated['employee_notes'] ?? null,
            'required_tools' => $validated['required_tools'] ?? null,
            'requires_external_worker' => $requiresExternalWorker,
            'external_worker_name' => $requiresExternalWorker ? ($validated['external_worker_name'] ?? null) : null,
            'external_worker_cost' => $requiresExternalWorker ? ($validated['external_worker_cost'] ?? null) : null,
            'assigned_employee_id' => $request->user()->id,
            'resolved_at' => in_array($validated['status'], ['completed', 'rejected']) ? now() : null,
        ]);

        return redirect()->route('employee.maintenance.show', $maintenanceRequest)
            ->with(
                'success',
                app()->getLocale() === 'en'
                    ? 'Maintenance request updated successfully.'
                    : 'تم تحديث طلب الصيانة بنجاح'
            );
    }

    private function ensureEmployeeCanManageRequest(int $employeeId, MaintenanceRequest $maintenanceRequest): void
    {
        $maintenanceRequest->loadMissing('unit.property');
        $property = $maintenanceRequest->unit?->property;

        abort_if(! $property || (int) $property->employee_id !== $employeeId, 403);
    }
}
