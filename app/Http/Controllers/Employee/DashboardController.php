<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLeave;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RentalContract;
use App\Models\Tenant;
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

        // Referral commission — properties this employee referred
        $referredProperties = Property::where('referral_employee_id', $employee->id)->get();
        $referralPropertyRevenue = collect();
        $propertyReferralCommissionTotal = 0;

        if ($referredProperties->isNotEmpty()) {
            $referredPropertyIds = $referredProperties->pluck('id');
            $referralPropertyRevenue = DB::table('payments')
                ->join('rental_contracts', 'payments.rental_contract_id', '=', 'rental_contracts.id')
                ->join('units', 'rental_contracts.unit_id', '=', 'units.id')
                ->where('payments.status', 'paid')
                ->where('payments.year', $year)
                ->whereIn('units.property_id', $referredPropertyIds)
                ->selectRaw('units.property_id, SUM(payments.amount) as total_paid')
                ->groupBy('units.property_id')
                ->pluck('total_paid', 'property_id');

            $propertyReferralCommissionTotal = $referredProperties->sum(
                fn ($p) => ($p->referral_commission_rate ?? 0) / 100 * ($referralPropertyRevenue[$p->id] ?? 0)
            );
        }

        // Referral commission — tenants this employee referred
        $referredTenants = Tenant::where('referral_employee_id', $employee->id)
            ->with('user')
            ->get();
        $referralTenantRevenue = collect();
        $tenantReferralCommissionTotal = 0;

        if ($referredTenants->isNotEmpty()) {
            $referredTenantIds = $referredTenants->pluck('id');
            $referralTenantRevenue = DB::table('payments')
                ->where('status', 'paid')
                ->where('year', $year)
                ->whereIn('tenant_id', $referredTenantIds)
                ->selectRaw('tenant_id, SUM(amount) as total_paid')
                ->groupBy('tenant_id')
                ->pluck('total_paid', 'tenant_id');

            $tenantReferralCommissionTotal = $referredTenants->sum(
                fn ($t) => ($t->referral_commission_rate ?? 0) / 100 * ($referralTenantRevenue[$t->id] ?? 0)
            );
        }

        $referralCommissionTotal = $propertyReferralCommissionTotal + $tenantReferralCommissionTotal;

        $myLeaves = EmployeeLeave::where('employee_id', $employee->id)
            ->orderByDesc('start_date')
            ->take(12)
            ->get();

        // Contracts expiring in the next 30 days for properties managed by this employee
        $expiringContracts = RentalContract::where('status', 'active')
            ->whereBetween('end_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->whereHas('unit.property', fn($q) => $q->where('employee_id', $employee->id))
            ->with(['tenant.user', 'unit.property'])
            ->orderBy('end_date')
            ->get();

        return view('employee.dashboard', compact(
            'stats',
            'properties',
            'pendingMaintenance',
            'pendingPayments',
            'propertyRentSummary',
            'referredProperties',
            'referralPropertyRevenue',
            'propertyReferralCommissionTotal',
            'referredTenants',
            'referralTenantRevenue',
            'tenantReferralCommissionTotal',
            'referralCommissionTotal',
            'year',
            'myLeaves',
            'expiringContracts'
        ));
    }
}
