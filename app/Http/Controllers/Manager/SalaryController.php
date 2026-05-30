<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

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
            'employee_id'         => 'required|exists:users,id',
            'period_month'        => 'required|integer|between:1,12',
            'period_year'         => 'required|integer|min:2020|max:2100',
            'base_salary'         => 'required|numeric|min:0',
            'housing_allowance'   => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'food_allowance'      => 'nullable|numeric|min:0',
            'other_allowances'    => 'nullable|numeric|min:0',
            'bonuses'             => 'nullable|numeric|min:0',
            'deductions'          => 'nullable|numeric|min:0',
            'notes'               => 'nullable|string',
            'status'              => 'required|in:draft,pending,paid',
        ]);

        $data['housing_allowance']   = (float) ($data['housing_allowance'] ?? 0);
        $data['transport_allowance'] = (float) ($data['transport_allowance'] ?? 0);
        $data['food_allowance']      = (float) ($data['food_allowance'] ?? 0);
        $data['other_allowances']    = (float) ($data['other_allowances'] ?? 0);
        $data['bonuses']             = (float) ($data['bonuses'] ?? 0);
        $data['deductions']          = (float) ($data['deductions'] ?? 0);
        $data['net_paid']            = Salary::calcNet($data);

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
        ]);

        $month = (int) $request->input('period_month');
        $year  = (int) $request->input('period_year');

        $employees = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['employee', 'accountant']))->get();

        $created = 0;
        foreach ($employees as $employee) {
            $existing = Salary::where('employee_id', $employee->id)
                ->where('period_month', $month)
                ->where('period_year', $year)
                ->exists();
            if ($existing) continue;

            $base = (float) ($employee->base_salary ?? 0);

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

    public function show(Salary $salary)
    {
        $salary->load(['employee', 'paidBy']);
        return view('manager.salaries.show', compact('salary'));
    }

    public function edit(Salary $salary)
    {
        return view('manager.salaries.edit', compact('salary'));
    }

    public function update(Request $request, Salary $salary)
    {
        $data = $request->validate([
            'base_salary'         => 'required|numeric|min:0',
            'housing_allowance'   => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'food_allowance'      => 'nullable|numeric|min:0',
            'other_allowances'    => 'nullable|numeric|min:0',
            'bonuses'             => 'nullable|numeric|min:0',
            'deductions'          => 'nullable|numeric|min:0',
            'status'              => 'required|in:draft,pending,paid',
            'notes'               => 'nullable|string',
        ]);

        $data['housing_allowance']   = (float) ($data['housing_allowance'] ?? 0);
        $data['transport_allowance'] = (float) ($data['transport_allowance'] ?? 0);
        $data['food_allowance']      = (float) ($data['food_allowance'] ?? 0);
        $data['other_allowances']    = (float) ($data['other_allowances'] ?? 0);
        $data['bonuses']             = (float) ($data['bonuses'] ?? 0);
        $data['deductions']          = (float) ($data['deductions'] ?? 0);
        $data['net_paid']            = Salary::calcNet($data);

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

    public function exportPdf(Request $request)
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

        $salaries = $query->orderBy('period_year')->orderBy('period_month')->orderBy('employee_id')->get();

        $totals = [
            'base'       => $salaries->sum('base_salary'),
            'housing'    => $salaries->sum('housing_allowance'),
            'transport'  => $salaries->sum('transport_allowance'),
            'food'       => $salaries->sum('food_allowance'),
            'other'      => $salaries->sum('other_allowances'),
            'bonuses'    => $salaries->sum('bonuses'),
            'deductions' => $salaries->sum('deductions'),
            'net'        => $salaries->sum('net_paid'),
            'paid'       => $salaries->where('status', 'paid')->sum('net_paid'),
        ];

        $html = view('manager.salaries.salary-pdf', compact('salaries', 'totals', 'month', 'year', 'status'))->render();

        if (! is_dir(storage_path('app/mpdf'))) {
            mkdir(storage_path('app/mpdf'), 0755, true);
        }

        $mpdf = new Mpdf([
            'mode'             => 'utf-8',
            'format'           => 'A4',
            'orientation'      => 'L',
            'margin_top'       => 0,
            'margin_bottom'    => 15,
            'margin_left'      => 0,
            'margin_right'     => 0,
            'default_font'     => 'dejavusans',
            'autoLangToFont'   => true,
            'autoScriptToLang' => true,
            'tempDir'          => storage_path('app/mpdf'),
        ]);

        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);

        $monthNames = ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        $periodPart = $month ? ($monthNames[$month] ?? $month) . '-' . ($year ?? now()->year) : ($year ?? now()->year);
        $filename   = "تقرير-الرواتب-{$periodPart}.pdf";

        return response($mpdf->Output($filename, 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function destroy(Salary $salary)
    {
        $salary->delete();
        return back()->with('success', __('Deleted Successfully'));
    }
}
