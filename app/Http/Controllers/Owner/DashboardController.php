<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Installment;
use App\Models\Owner;
use App\Models\Payment;
use App\Models\Property;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user  = auth()->user();
        $owner = $user->owner;

        if (! $owner) {
            abort(403, 'ليس لديك ملف مالك مرتبط بحسابك.');
        }

        $properties = $owner->properties()->with(['units', 'employee'])->get();
        $propertyIds = $properties->pluck('id');

        // Payments for all units inside owner's properties (current year)
        $payments = Payment::with(['tenant.user', 'rentalContract.unit.property'])
            ->whereHas('rentalContract.unit', fn ($q) => $q->whereIn('property_id', $propertyIds))
            ->where('status', 'paid')
            ->whereYear('paid_at', now()->year)
            ->get();

        $totalRevenue    = $payments->sum('amount');
        $commission      = round($totalRevenue * ($owner->commission_rate / 100), 2);
        $ownerEarnings   = $totalRevenue - $commission;

        $monthlyRevenue = Payment::whereHas('rentalContract.unit', fn ($q) => $q->whereIn('property_id', $propertyIds))
            ->where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $pendingPayments = Payment::whereHas('rentalContract.unit', fn ($q) => $q->whereIn('property_id', $propertyIds))
            ->where('status', 'pending')
            ->count();

        $expensesTotal = Expense::where('scope', 'property')
            ->where('expensable_type', Property::class)
            ->whereIn('expensable_id', $propertyIds)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $stats = [
            'total_properties'  => $properties->count(),
            'total_units'       => $properties->sum(fn ($p) => $p->units->count()),
            'rented_units'      => $properties->sum(fn ($p) => $p->units->where('status', 'rented')->count()),
            'available_units'   => $properties->sum(fn ($p) => $p->units->where('status', 'available')->count()),
            'total_revenue'     => $totalRevenue,
            'monthly_revenue'   => $monthlyRevenue,
            'commission_amount' => $commission,
            'owner_earnings'    => $ownerEarnings,
            'pending_payments'  => $pendingPayments,
            'expenses_total'    => $expensesTotal,
            'commission_rate'   => $owner->commission_rate,
        ];

        $recentPayments = Payment::with(['tenant.user', 'rentalContract.unit.property'])
            ->whereHas('rentalContract.unit', fn ($q) => $q->whereIn('property_id', $propertyIds))
            ->latest()
            ->take(6)
            ->get();

        // Revenue chart - last 6 months
        $revenueChart = collect(range(5, 0))->map(function ($monthsAgo) use ($propertyIds) {
            $date = now()->subMonths($monthsAgo);
            $amount = Payment::where('status', 'paid')
                ->whereHas('rentalContract.unit', fn ($q) => $q->whereIn('property_id', $propertyIds))
                ->whereMonth('paid_at', $date->month)
                ->whereYear('paid_at', $date->year)
                ->sum('amount');
            return [
                'label'  => $date->locale(app()->getLocale())->isoFormat('MMM YY'),
                'amount' => (int) $amount,
            ];
        });

        return view('owner.dashboard', compact('owner', 'properties', 'stats', 'recentPayments', 'revenueChart'));
    }
}
