<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $query = Salary::with(['employee', 'paidBy']);

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($month = $request->query('month')) {
            $query->where('period_month', $month);
        }
        if ($year = $request->query('year')) {
            $query->where('period_year', $year);
        }

        $salaries = $query->latest()->paginate(20)->withQueryString();
        $totalPaid = Salary::where('status', 'paid')
            ->when($month, fn ($q) => $q->where('period_month', $month))
            ->when($year, fn ($q) => $q->where('period_year', $year))
            ->sum('net_paid');

        return view('manager.salaries.index', compact('salaries', 'totalPaid'));
    }

    public function create()
    {
        $employees = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['employee', 'accountant', 'manager']))
            ->orderBy('name_ar')->get();
        return view('manager.salaries.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id'  => 'required|exists:users,id',
            'period_month' => 'required|integer|between:1,12',
            'period_year'  => 'required|integer|min:2020|max:2100',
            'base_salary'  => 'required|numeric|min:0',
            'bonuses'      => 'nullable|numeric|min:0',
            'deductions'   => 'nullable|numeric|min:0',
            'notes'        => 'nullable|string',
            'status'       => 'required|in:draft,pending,paid',
        ]);

        $bonuses = (float) ($data['bonuses'] ?? 0);
        $deductions = (float) ($data['deductions'] ?? 0);
        $data['bonuses'] = $bonuses;
        $data['deductions'] = $deductions;
        $data['net_paid'] = $data['base_salary'] + $bonuses - $deductions;

        if ($data['status'] === 'paid') {
            $data['paid_at'] = now();
            $data['paid_by'] = auth()->id();
        }

        Salary::create($data);

        return redirect()->route('manager.salaries.index')->with('success', __('Created Successfully'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'period_month' => 'required|integer|between:1,12',
            'period_year'  => 'required|integer|min:2020|max:2100',
            'default_base' => 'required|numeric|min:0',
        ]);

        $month = (int) $request->input('period_month');
        $year  = (int) $request->input('period_year');
        $base  = (float) $request->input('default_base');

        $employees = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['employee', 'accountant']))->get();

        $created = 0;
        foreach ($employees as $employee) {
            $existing = Salary::where('employee_id', $employee->id)
                ->where('period_month', $month)
                ->where('period_year', $year)
                ->exists();
            if ($existing) continue;

            Salary::create([
                'employee_id'  => $employee->id,
                'period_month' => $month,
                'period_year'  => $year,
                'base_salary'  => $base,
                'bonuses'      => 0,
                'deductions'   => 0,
                'net_paid'     => $base,
                'status'       => 'pending',
            ]);
            $created++;
        }

        return back()->with('success', __('Created Successfully') . " ({$created})");
    }

    public function edit(Salary $salary)
    {
        return view('manager.salaries.edit', compact('salary'));
    }

    public function update(Request $request, Salary $salary)
    {
        $data = $request->validate([
            'base_salary' => 'required|numeric|min:0',
            'bonuses'     => 'nullable|numeric|min:0',
            'deductions'  => 'nullable|numeric|min:0',
            'status'      => 'required|in:draft,pending,paid',
            'notes'       => 'nullable|string',
        ]);

        $bonuses = (float) ($data['bonuses'] ?? 0);
        $deductions = (float) ($data['deductions'] ?? 0);
        $data['bonuses'] = $bonuses;
        $data['deductions'] = $deductions;
        $data['net_paid'] = $data['base_salary'] + $bonuses - $deductions;

        if ($data['status'] === 'paid' && $salary->status !== 'paid') {
            $data['paid_at'] = now();
            $data['paid_by'] = auth()->id();
        }

        $salary->update($data);

        return redirect()->route('manager.salaries.index')->with('success', __('Updated Successfully'));
    }

    public function pay(Salary $salary)
    {
        $salary->update([
            'status'  => 'paid',
            'paid_at' => now(),
            'paid_by' => auth()->id(),
        ]);
        return back()->with('success', __('Operation Successful'));
    }

    public function destroy(Salary $salary)
    {
        $salary->delete();
        return back()->with('success', __('Deleted Successfully'));
    }
}
