<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            abort(403, 'لا يوجد ملف مستأجر مرتبط بهذا الحساب');
        }

        $activeContract = $tenant->activeContract()->with('unit.property')->first();

        $stats = [
            'pending_maintenance' => $tenant->maintenanceRequests()->where('status', 'pending')->count(),
            'pending_payments' => $tenant->payments()->where('status', 'pending')->count(),
            'overdue_payments' => $tenant->payments()->where('status', 'overdue')->count(),
        ];

        $recentMaintenance = $tenant->maintenanceRequests()
            ->with('unit.property')
            ->latest()
            ->take(5)
            ->get();

        $recentPayments = $tenant->payments()
            ->with('rentalContract.unit.property')
            ->latest()
            ->take(5)
            ->get();

        return view('tenant.dashboard', compact('tenant', 'activeContract', 'stats', 'recentMaintenance', 'recentPayments'));
    }
}
