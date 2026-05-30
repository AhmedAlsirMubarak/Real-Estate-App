<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeCommission;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        session()->save();

        $employee = $request->user();
        $year = (int) $request->input('year', now()->year);
        if ($year < 2000 || $year > ((int) now()->year + 2)) {
            $year = (int) now()->year;
        }

        $properties = Property::where('employee_id', $employee->id)
            ->with(['units', 'owner.user'])
            ->get();

        $paymentScope = Payment::whereHas('rentalContract.unit.property', function ($q) use ($employee) {
            $q->where('employee_id', $employee->id);
        });

        $yearPayments = (clone $paymentScope)
            ->where('year', $year)
            ->with(['tenant.user', 'rentalContract.unit.property'])
            ->orderByDesc('month')
            ->get();

        $stats = [
            'total_properties' => $properties->count(),
            'total_units'      => $properties->sum(fn ($property) => $property->units->count()),
            'pending_maintenance' => MaintenanceRequest::whereHas('unit.property', function ($q) use ($employee) {
                $q->where('employee_id', $employee->id);
            })->where('status', 'pending')->count(),
            'pending_payments' => (clone $paymentScope)->where('status', 'pending')->count(),
            'year_total_rent' => (float) $yearPayments->sum('amount'),
            'year_paid_rent' => (float) $yearPayments->where('status', 'paid')->sum('amount'),
            'year_pending_rent' => (float) $yearPayments->where('status', 'pending')->sum('amount'),
            'year_overdue_rent' => (float) $yearPayments->where('status', 'overdue')->sum('amount'),
        ];

        $pendingMaintenance = MaintenanceRequest::whereHas('unit.property', function ($q) use ($employee) {
            $q->where('employee_id', $employee->id);
        })->where('status', 'pending')->with(['tenant.user', 'unit.property'])->take(5)->get();

        $pendingPayments = (clone $paymentScope)
            ->where('status', 'pending')
            ->with(['tenant.user', 'rentalContract.unit.property'])
            ->orderBy('year')
            ->orderBy('month')
            ->take(5)
            ->get();

        $propertyRentSummary = $properties->map(function (Property $property) use ($year) {
            $propertyYearPayments = Payment::query()
                ->whereHas('rentalContract.unit', fn($q) => $q->where('property_id', $property->id))
                ->where('year', $year)
                ->get();

            return [
                'property' => $property,
                'paid' => (float) $propertyYearPayments->where('status', 'paid')->sum('amount'),
                'pending' => (float) $propertyYearPayments->where('status', 'pending')->sum('amount'),
                'overdue' => (float) $propertyYearPayments->where('status', 'overdue')->sum('amount'),
                'count' => $propertyYearPayments->count(),
            ];
        });

        $yearCommissions = EmployeeCommission::query()
            ->where('employee_id', $employee->id)
            ->whereYear('recorded_at', $year)
            ->get();

        $commissionStats = [
            'rent_collection' => (float) $yearCommissions->where('type', 'rent_collection')->sum('commission_amount'),
            'property_sale' => (float) $yearCommissions->where('type', 'property_sale')->sum('commission_amount'),
            'total' => (float) $yearCommissions->sum('commission_amount'),
        ];

        $recentCommissions = EmployeeCommission::query()
            ->where('employee_id', $employee->id)
            ->with(['property', 'payment'])
            ->latest('recorded_at')
            ->take(6)
            ->get();

        // Referral commission (properties this employee referred, filtered by year)
        $referredProperties = Property::where('referral_employee_id', $employee->id)->get();
        $referralPropertyRevenue = collect();
        $referralCommissionTotal = 0;

        if ($referredProperties->isNotEmpty()) {
            $referredIds = $referredProperties->pluck('id');
            $referralPropertyRevenue = DB::table('payments')
                ->join('rental_contracts', 'payments.rental_contract_id', '=', 'rental_contracts.id')
                ->join('units', 'rental_contracts.unit_id', '=', 'units.id')
                ->where('payments.status', 'paid')
                ->where('payments.year', $year)
                ->whereIn('units.property_id', $referredIds)
                ->selectRaw('units.property_id, SUM(payments.amount) as total_paid')
                ->groupBy('units.property_id')
                ->pluck('total_paid', 'property_id');

            $referralCommissionTotal = $referredProperties->sum(
                fn ($p) => ($p->referral_commission_rate ?? 0) / 100 * ($referralPropertyRevenue[$p->id] ?? 0)
            );
        }

        return view('employee.dashboard', compact(
            'stats',
            'properties',
            'pendingMaintenance',
            'pendingPayments',
            'propertyRentSummary',
            'commissionStats',
            'recentCommissions',
            'referredProperties',
            'referralPropertyRevenue',
            'referralCommissionTotal',
            'year'
        ));
    }
}
