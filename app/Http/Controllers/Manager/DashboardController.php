<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Expense;
use App\Models\MaintenanceRequest;
use App\Models\Owner;
use App\Models\Payment;
use App\Models\Property;
use App\Models\SaleContract;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
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

        $revenueChart = collect(range(5, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            return [
                'label'  => $date->locale(app()->getLocale())->isoFormat('MMM YY'),
                'amount' => (int) Payment::where('status', 'paid')
                    ->whereMonth('paid_at', $date->month)
                    ->whereYear('paid_at', $date->year)
                    ->sum('amount'),
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

        return view('manager.dashboard', compact('stats', 'recentMaintenance', 'recentPayments', 'revenueChart'));
    }
}
