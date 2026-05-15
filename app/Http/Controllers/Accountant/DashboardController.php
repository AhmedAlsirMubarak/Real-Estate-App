<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\RentalContract;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_revenue' => Payment::where('status', 'paid')->sum('amount'),
            'monthly_revenue' => Payment::where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'overdue_payments' => Payment::where('status', 'overdue')->count(),
            'active_contracts' => RentalContract::where('status', 'active')->count(),
        ];

        $recentPayments = Payment::with(['tenant.user', 'rentalContract.unit.property'])
            ->latest()
            ->take(10)
            ->get();

        $overduePayments = Payment::where('status', 'overdue')
            ->with(['tenant.user', 'rentalContract.unit.property'])
            ->latest()
            ->take(5)
            ->get();

        return view('accountant.dashboard', compact('stats', 'recentPayments', 'overduePayments'));
    }
}
