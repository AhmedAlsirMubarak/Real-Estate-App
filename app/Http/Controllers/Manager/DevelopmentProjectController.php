<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentExpense;
use App\Models\DevelopmentProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class DevelopmentProjectController extends Controller
{
    public function index()
    {
        $projects = DevelopmentProject::with(['expenses', 'contractors.payments'])
            ->latest()
            ->get();

        $totalBudget  = $projects->sum('total_budget');
        $totalSpent   = $projects->sum(fn ($p) => $p->totalSpent());
        $activeCount  = $projects->whereNotIn('status', ['completed'])->count();

        return view('manager.development.index', compact('projects', 'totalBudget', 'totalSpent', 'activeCount'));
    }

    public function create()
    {
        $isAr       = app()->getLocale() === 'ar';
        $categories = DevelopmentExpense::categories();
        $catLabels  = DevelopmentExpense::categoryLabels($isAr);
        return view('manager.development.create', compact('categories', 'catLabels'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                      => 'required|string|max:255',
            'type'                      => 'required|in:residential,commercial,mixed',
            'location'                  => 'required|string|max:255',
            'status'                    => 'required|in:planning,foundation,structure,finishing,handover,completed',
            'total_budget'              => 'required|numeric|min:0',
            'progress_percentage'       => 'required|integer|min:0|max:100',
            'start_date'                => 'required|date',
            'estimated_completion_date' => 'required|date|after_or_equal:start_date',
            'project_manager_name'      => 'required|string|max:255',
            'notes'                     => 'nullable|string',
            'category_budgets_json'     => 'nullable|array',
            'category_budgets_json.*'   => 'nullable|string',
        ]);

        $data['category_budgets'] = $this->parseCategoryBudgets($data['category_budgets_json'] ?? []);
        unset($data['category_budgets_json']);

        DevelopmentProject::create($data);

        $msg = app()->getLocale() === 'ar' ? 'تم إنشاء المشروع بنجاح.' : 'Project created successfully.';
        return redirect()->route('manager.development.index')->with('success', $msg);
    }

    public function show(DevelopmentProject $development)
    {
        $development->load(['expenses', 'contractors.payments', 'documents']);

        $chartStart  = now()->subMonths(5)->startOfMonth();
        $rawExpenses = $development->expenses()
            ->where('expense_date', '>=', $chartStart)
            ->selectRaw('YEAR(expense_date) as yr, MONTH(expense_date) as mo, SUM(amount) as total')
            ->groupByRaw('YEAR(expense_date), MONTH(expense_date)')
            ->get()
            ->keyBy(fn ($r) => $r->yr . '-' . $r->mo);

        $months          = max(1, (int) Carbon::parse($development->start_date)->diffInMonths($development->estimated_completion_date));
        $expectedMonthly = (int) round((float) $development->total_budget / $months);

        $monthlyChart = collect(range(5, 0))->map(function ($ago) use ($rawExpenses, $expectedMonthly) {
            $date = now()->subMonths($ago);
            $key  = $date->year . '-' . $date->month;
            return [
                'label'    => $date->locale(app()->getLocale())->isoFormat('MMM YY'),
                'actual'   => (int) ($rawExpenses->get($key)?->total ?? 0),
                'expected' => $expectedMonthly,
            ];
        });

        $expenseByCategory = $development->expenses()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->keyBy('category');

        $recentExpenses = $development->expenses()
            ->latest('expense_date')
            ->take(15)
            ->get();

        $thisMonthSpending = (float) $development->expenses()
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $isAr       = app()->getLocale() === 'ar';
        $categories = DevelopmentExpense::categories();
        $catLabels  = DevelopmentExpense::categoryLabels($isAr);

        return view('manager.development.show', compact(
            'development', 'monthlyChart', 'expenseByCategory',
            'recentExpenses', 'thisMonthSpending', 'categories', 'catLabels'
        ));
    }

    public function edit(DevelopmentProject $development)
    {
        $isAr       = app()->getLocale() === 'ar';
        $categories = DevelopmentExpense::categories();
        $catLabels  = DevelopmentExpense::categoryLabels($isAr);
        return view('manager.development.edit', compact('development', 'categories', 'catLabels'));
    }

    public function update(Request $request, DevelopmentProject $development)
    {
        $data = $request->validate([
            'name'                      => 'required|string|max:255',
            'type'                      => 'required|in:residential,commercial,mixed',
            'location'                  => 'required|string|max:255',
            'status'                    => 'required|in:planning,foundation,structure,finishing,handover,completed',
            'total_budget'              => 'required|numeric|min:0',
            'progress_percentage'       => 'required|integer|min:0|max:100',
            'start_date'                => 'required|date',
            'estimated_completion_date' => 'required|date|after_or_equal:start_date',
            'project_manager_name'      => 'required|string|max:255',
            'notes'                     => 'nullable|string',
            'category_budgets_json'     => 'nullable|array',
            'category_budgets_json.*'   => 'nullable|string',
        ]);

        $data['category_budgets'] = $this->parseCategoryBudgets($data['category_budgets_json'] ?? []);
        unset($data['category_budgets_json']);

        $development->update($data);

        $msg = app()->getLocale() === 'ar' ? 'تم حفظ التغييرات بنجاح.' : 'Project updated successfully.';
        return redirect()->route('manager.development.show', $development)->with('success', $msg);
    }

    public function destroy(DevelopmentProject $development)
    {
        $development->delete();
        return redirect()->route('manager.development.index')
            ->with('success', 'Project deleted.');
    }

    public function updateProgress(Request $request, DevelopmentProject $development)
    {
        $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
            'status'              => 'required|in:planning,foundation,structure,finishing,handover,completed',
        ]);

        $development->update($request->only(['progress_percentage', 'status']));

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم تحديث التقدم بنجاح.' : 'Progress updated.');
    }

    private function parseCategoryBudgets(array $raw): array
    {
        $result = [];
        foreach ($raw as $cat => $json) {
            $items = json_decode($json ?? '', true);
            if (!is_array($items)) continue;
            $filtered = array_values(array_filter($items, fn ($i) => isset($i['amount']) && (float) ($i['amount']) > 0));
            if (empty($filtered)) continue;
            $result[$cat] = array_map(fn ($i) => [
                'name'   => trim($i['name'] ?? ''),
                'amount' => (float) $i['amount'],
            ], $filtered);
        }
        return $result;
    }

    public function report(DevelopmentProject $development)
    {
        $development->load(['expenses', 'contractors.payments', 'documents']);

        $expenseByCategory = $development->expenses()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->keyBy('category');

        $monthlyBreakdown = $development->expenses()
            ->selectRaw('YEAR(expense_date) as yr, MONTH(expense_date) as mo, SUM(amount) as total')
            ->groupByRaw('YEAR(expense_date), MONTH(expense_date)')
            ->orderByRaw('YEAR(expense_date), MONTH(expense_date)')
            ->get();

        $isAr       = app()->getLocale() === 'ar';
        $categories = DevelopmentExpense::categories();
        $catLabels  = DevelopmentExpense::categoryLabels($isAr);

        $html = view('manager.development.report', compact(
            'development', 'expenseByCategory', 'monthlyBreakdown', 'categories', 'catLabels', 'isAr'
        ))->render();

        $tempDir = storage_path('app/mpdf');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $mpdf = new Mpdf([
            'mode'         => 'utf-8',
            'format'       => 'A4',
            'orientation'  => 'P',
            'default_font' => 'dejavusans',
            'margin_top'   => 0,
            'margin_bottom'=> 14,
            'margin_left'  => 0,
            'margin_right' => 0,
            'tempDir'      => $tempDir,
        ]);

        if ($isAr) {
            $mpdf->SetDirectionality('rtl');
        }

        $mpdf->WriteHTML($html);
        $mainContent = $mpdf->Output('', 'S');

        // Save to temp file so FPDI can import pages
        $tempMain = $tempDir . '/dev_report_' . time() . '_' . rand(1000, 9999) . '.pdf';
        file_put_contents($tempMain, $mainContent);

        try {
            $fpdi = new \setasign\Fpdi\Fpdi('P', 'mm', 'A4');

            $pageCount = $fpdi->setSourceFile($tempMain);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl  = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($tpl);
                $fpdi->AddPage(($size['width'] > $size['height']) ? 'L' : 'P', [$size['width'], $size['height']]);
                $fpdi->useTemplate($tpl);
            }

            // Append all contract & invoice PDFs
            $attachments = $development->documents
                ->filter(fn ($d) => in_array($d->type, ['contract', 'invoice']) && $d->isPdf());

            foreach ($attachments as $doc) {
                $absPath = storage_path('app/public/' . $doc->file_path);
                if (! file_exists($absPath)) {
                    continue;
                }
                try {
                    $pdfPages = $fpdi->setSourceFile($absPath);
                    for ($i = 1; $i <= $pdfPages; $i++) {
                        $tpl  = $fpdi->importPage($i);
                        $size = $fpdi->getTemplateSize($tpl);
                        $fpdi->AddPage(($size['width'] > $size['height']) ? 'L' : 'P', [$size['width'], $size['height']]);
                        $fpdi->useTemplate($tpl);
                    }
                } catch (\Exception) {
                    // Skip unreadable / encrypted PDFs
                }
            }

            $merged = $fpdi->Output('', 'S');
        } finally {
            @unlink($tempMain);
        }

        $filename = 'development-report-' . $development->id . '-' . now()->format('Y-m-d') . '.pdf';

        return response($merged)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }
}
