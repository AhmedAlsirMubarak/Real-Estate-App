<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\CompanyBudget;
use Illuminate\Http\Request;

class CompanyBudgetController extends Controller
{
    public function index(Request $request)
    {
        $query = CompanyBudget::query();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }
        if ($year = $request->query('year')) {
            $query->where('period_year', $year);
        }

        $budgets      = $query->latest()->paginate(20)->withQueryString();
        $totalAllocated = CompanyBudget::sum('allocated_amount');
        $totalSpent     = CompanyBudget::sum('spent_amount');

        return view('manager.budgets.index', compact('budgets', 'totalAllocated', 'totalSpent'));
    }

    public function create()
    {
        return view('manager.budgets.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'category'         => 'required|in:hr,operations,it,marketing,maintenance,other',
            'period_year'      => 'required|integer|min:2020|max:2100',
            'period_month'     => 'nullable|integer|between:1,12',
            'allocated_amount' => 'required|numeric|min:0',
            'spent_amount'     => 'nullable|numeric|min:0',
            'status'           => 'required|in:draft,approved,closed',
            'notes'            => 'nullable|string',
        ]);

        $data['spent_amount'] = $data['spent_amount'] ?? 0;

        CompanyBudget::create($data);

        return redirect()->route('manager.budgets.index')->with('success', __('Created Successfully'));
    }

    public function edit(CompanyBudget $budget)
    {
        return view('manager.budgets.edit', compact('budget'));
    }

    public function update(Request $request, CompanyBudget $budget)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'category'         => 'required|in:hr,operations,it,marketing,maintenance,other',
            'period_year'      => 'required|integer|min:2020|max:2100',
            'period_month'     => 'nullable|integer|between:1,12',
            'allocated_amount' => 'required|numeric|min:0',
            'spent_amount'     => 'nullable|numeric|min:0',
            'status'           => 'required|in:draft,approved,closed',
            'notes'            => 'nullable|string',
        ]);

        $data['spent_amount'] = $data['spent_amount'] ?? 0;

        $budget->update($data);

        return redirect()->route('manager.budgets.index')->with('success', __('Updated Successfully'));
    }

    public function destroy(CompanyBudget $budget)
    {
        $budget->delete();
        return back()->with('success', __('Deleted Successfully'));
    }
}
