<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Expense;
use App\Models\MaintenanceRequest;
use App\Models\Owner;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RentalContract;
use App\Models\SaleContract;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Release session lock early so clicking links during page load doesn't block
        session()->save();

        $totalUnits = Unit::count();
        $rentedUnits = Unit::where('status', 'rented')->count();

        $stats = [
            'total_properties'  => Property::count(),
            'rent_properties'   => Property::whereIn('purpose', ['rent', 'both'])->count(),
            'sale_properties'   => Property::whereIn('purpose', ['sale', 'both'])->count(),
            'company_owned'     => Property::whereNull('owner_id')->count(),
            'external_owned'    => Property::whereNotNull('owner_id')->count(),

            'total_units'        => $totalUnits,
            'rented_units'       => $rentedUnits,
            'available_units'    => Unit::where('status', 'available')->count(),
            'sold_units'         => Unit::where('status', 'sold')->count(),

            'total_tenants'       => Tenant::count(),
            'total_buyers'        => Buyer::count(),
            'total_owners'        => Owner::count(),
            'total_employees'     => User::role('employee')->count(),

            'pending_maintenance' => MaintenanceRequest::where('status', 'pending')->count(),
            'overdue_payments'    => Payment::where('status', 'overdue')->count(),
            'active_sales'        => SaleContract::where('status', 'active')->count(),

            'monthly_revenue' => Payment::where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),

            'monthly_expenses' => Expense::whereMonth('expense_date', now()->month)
                ->whereYear('expense_date', now()->year)
                ->sum('amount'),
        ];

        // Single query for 6-month revenue chart instead of 6 separate SUM queries
        $chartStart = now()->subMonths(5)->startOfMonth();
        $rawRevenue = Payment::where('status', 'paid')
            ->where('paid_at', '>=', $chartStart)
            ->selectRaw('YEAR(paid_at) as yr, MONTH(paid_at) as mo, SUM(amount) as total')
            ->groupByRaw('YEAR(paid_at), MONTH(paid_at)')
            ->get()
            ->keyBy(fn($r) => $r->yr . '-' . $r->mo);

        $revenueChart = collect(range(5, 0))->map(function ($monthsAgo) use ($rawRevenue) {
            $date = now()->subMonths($monthsAgo);
            $key  = $date->year . '-' . $date->month;
            return [
                'label'  => $date->locale(app()->getLocale())->isoFormat('MMM YY'),
                'amount' => (int) ($rawRevenue->get($key)?->total ?? 0),
            ];
        });

        $recentMaintenance = MaintenanceRequest::with(['tenant.user', 'unit.property'])
            ->latest()
            ->take(5)
            ->get();

        $recentPayments = Payment::with(['tenant.user', 'rentalContract.unit.property'])
            ->latest()
            ->take(5)
            ->get();

        $expiringContracts = RentalContract::where('status', 'active')
            ->whereBetween('end_date', [now()->toDateString(), now()->addDays(60)->toDateString()])
            ->with(['tenant.user', 'unit.property'])
            ->orderBy('end_date')
            ->get();

        return view('manager.dashboard', compact('stats', 'recentMaintenance', 'recentPayments', 'revenueChart', 'expiringContracts'));
    }
}
