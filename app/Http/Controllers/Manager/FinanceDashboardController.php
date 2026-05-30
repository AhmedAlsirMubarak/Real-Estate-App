<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\CompanyBudget;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Salary;

class FinanceDashboardController extends Controller
{
    public function index()
    {
        session()->save();

        $year = now()->year;

        // ── Budget totals ──────────────────────────────────────────────────
        $totalAllocated  = (float) CompanyBudget::sum('allocated_amount');
        $totalSpent      = (float) CompanyBudget::sum('spent_amount');
        $totalRemaining  = $totalAllocated - $totalSpent;
        $budgetUsagePct  = $totalAllocated > 0 ? min(100, round(($totalSpent / $totalAllocated) * 100)) : 0;

        // ── Company expenses (this year) ───────────────────────────────────
        $companyExpensesYear = (float) Expense::forCompany()
            ->whereYear('expense_date', $year)
            ->sum('amount');

        $companyExpensesMonth = (float) Expense::forCompany()
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', $year)
            ->sum('amount');

        // ── Revenue (paid payments, this year) ────────────────────────────
        $revenueYear = (float) Payment::where('status', 'paid')
            ->whereYear('paid_at', $year)
            ->sum('amount');

        // ── Salary cost (this year) ────────────────────────────────────────
        $salaryYear = (float) Salary::where('status', 'paid')
            ->where('period_year', $year)
            ->sum('net_paid');

        // ── Monthly company expenses chart (last 6 months) ────────────────
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

        // ── Expense breakdown by category (this year, company scope) ──────
        $expenseByCategory = Expense::forCompany()
            ->whereYear('expense_date', $year)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // ── Budget overview (top 6 by allocated) ──────────────────────────
        $budgetsOverview = CompanyBudget::orderByDesc('allocated_amount')->take(6)->get();

        // ── Budget counts by status ────────────────────────────────────────
        $budgetCounts = CompanyBudget::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // ── Recent company expenses ────────────────────────────────────────
        $recentExpenses = Expense::forCompany()
            ->latest('expense_date')
            ->take(6)
            ->get();

        return view('manager.finance.dashboard', compact(
            'year',
            'totalAllocated', 'totalSpent', 'totalRemaining', 'budgetUsagePct',
            'companyExpensesYear', 'companyExpensesMonth',
            'revenueYear', 'salaryYear',
            'expenseChart', 'expenseByCategory',
            'budgetsOverview', 'budgetCounts',
            'recentExpenses',
        ));
    }
}
