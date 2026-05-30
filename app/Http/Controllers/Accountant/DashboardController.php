<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\CompanyBudget;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\RentalContract;
use App\Models\Salary;

class DashboardController extends Controller
{
    public function index()
    {
        session()->save();

        $year = now()->year;

        $stats = [
            'total_revenue'    => Payment::where('status', 'paid')->sum('amount'),
            'monthly_revenue'  => Payment::where('status', 'paid')
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

        // ── Finance: Budget totals ─────────────────────────────────────────
        $totalAllocated = (float) CompanyBudget::sum('allocated_amount');
        $totalSpent     = (float) CompanyBudget::sum('spent_amount');
        $totalRemaining = $totalAllocated - $totalSpent;
        $budgetUsagePct = $totalAllocated > 0 ? min(100, round(($totalSpent / $totalAllocated) * 100)) : 0;

        // ── Finance: Company expenses ──────────────────────────────────────
        $companyExpensesYear = (float) Expense::forCompany()
            ->whereYear('expense_date', $year)
            ->sum('amount');

        $companyExpensesMonth = (float) Expense::forCompany()
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', $year)
            ->sum('amount');

        // ── Finance: Salaries paid this year ──────────────────────────────
        $salaryYear = (float) Salary::where('status', 'paid')
            ->where('period_year', $year)
            ->sum('net_paid');

        // ── Finance: Monthly expense chart (last 6 months) ────────────────
        $chartStart  = now()->subMonths(5)->startOfMonth();
        $rawExpenses = Expense::forCompany()
            ->where('expense_date', '>=', $chartStart)
            ->selectRaw('YEAR(expense_date) as yr, MONTH(expense_date) as mo, SUM(amount) as total')
            ->groupByRaw('YEAR(expense_date), MONTH(expense_date)')
            ->get()
            ->keyBy(fn ($r) => $r->yr . '-' . $r->mo);

        $expenseChart = collect(range(5, 0))->map(function ($ago) use ($rawExpenses) {
            $date = now()->subMonths($ago);
            $key  = $date->year . '-' . $date->month;
            return [
                'label'  => $date->locale(app()->getLocale())->isoFormat('MMM YY'),
                'amount' => (int) ($rawExpenses->get($key)?->total ?? 0),
            ];
        });

        // ── Finance: Expense breakdown by category ────────────────────────
        $expenseByCategory = Expense::forCompany()
            ->whereYear('expense_date', $year)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // ── Finance: Budget overview (top 6) ──────────────────────────────
        $budgetsOverview = CompanyBudget::orderByDesc('allocated_amount')->take(6)->get();

        // ── Finance: Recent company expenses ──────────────────────────────
        $recentExpenses = Expense::forCompany()
            ->latest('expense_date')
            ->take(6)
            ->get();

        return view('accountant.dashboard', compact(
            'stats', 'recentPayments', 'overduePayments',
            'year',
            'totalAllocated', 'totalSpent', 'totalRemaining', 'budgetUsagePct',
            'companyExpensesYear', 'companyExpensesMonth',
            'salaryYear',
            'expenseChart', 'expenseByCategory',
            'budgetsOverview', 'recentExpenses',
        ));
    }
}
