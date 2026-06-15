<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseInvoice;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Mpdf\Mpdf;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $scope      = $request->input('scope', '');
        $category   = $request->input('category', '');
        $propertyId = $request->input('property_id', '');
        $year       = $request->input('year', now()->year);
        $month      = $request->input('month', '');

        $query = Expense::with(['paidByUser', 'expensable'])
            ->whereYear('expense_date', $year)
            ->latest('expense_date');

        if ($month) {
            $query->whereMonth('expense_date', $month);
        }
        if ($scope) {
            $query->where('scope', $scope);
        }
        if ($category) {
            $query->where('category', $category);
        }
        if ($propertyId) {
            $query->where('expensable_type', Property::class)
                  ->where('expensable_id', $propertyId);
        }

        $expenses   = $query->with('invoices')->paginate(20)->withQueryString();
        $properties = Property::orderBy('name')->get();

        $totalsQuery = fn () => Expense::whereYear('expense_date', $year)
            ->when($month, fn ($q) => $q->whereMonth('expense_date', $month));

        $totals = [
            'company'  => (clone $totalsQuery())->where('scope', 'company')->sum('amount'),
            'property' => (clone $totalsQuery())->where('scope', 'property')->sum('amount'),
            'total'    => $totalsQuery()->sum('amount'),
        ];

        return view('manager.expenses.index', compact('expenses', 'properties', 'totals', 'scope', 'category', 'propertyId', 'year', 'month'));
    }

    public function create()
    {
        $properties = Property::orderBy('name')->get();
        $employees  = User::role(['manager', 'accountant'])->orderBy('name')->get();
        return view('manager.expenses.create', compact('properties', 'employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'scope'          => 'required|in:company,property',
            'category'       => 'required|in:utilities,maintenance,salaries,marketing,taxes,supplies,insurance,legal,other',
            'title_ar'       => 'required|string|max:255',
            'title_en'       => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0.01',
            'expense_date'   => 'required|date',
            'description_ar' => 'nullable|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'property_id'    => 'required_if:scope,property|nullable|exists:properties,id',
            'paid_by'        => 'nullable|exists:users,id',
            'invoices'       => 'nullable|array',
            'invoices.*'     => 'file|max:10240',
        ]);

        $expense = new Expense();
        $expense->scope        = $data['scope'];
        $expense->category     = $data['category'];
        $expense->title        = $data['title_ar'];
        $expense->amount       = $data['amount'];
        $expense->expense_date = $data['expense_date'];
        $expense->description  = $data['description_ar'] ?? null;
        $expense->paid_by      = $data['paid_by'] ?? auth()->user()?->id;

        if ($this->supportsBilingualExpenseFields()) {
            $expense->title_ar       = $data['title_ar'];
            $expense->title_en       = $data['title_en'];
            $expense->description_ar = $data['description_ar'] ?? null;
            $expense->description_en = $data['description_en'] ?? null;
        }

        if ($data['scope'] === 'property' && ! empty($data['property_id'])) {
            $expense->expensable_type = Property::class;
            $expense->expensable_id   = $data['property_id'];
        }

        $expense->save();

        $this->storeInvoiceFiles($request, $expense);

        return redirect()->route('manager.expenses.index')
            ->with('success', 'تم تسجيل المصروف بنجاح');
    }

    public function edit(Expense $expense)
    {
        $properties = Property::orderBy('name')->get();
        $employees  = User::role(['manager', 'accountant'])->orderBy('name')->get();
        return view('manager.expenses.edit', compact('expense', 'properties', 'employees'));
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'scope'          => 'required|in:company,property',
            'category'       => 'required|in:utilities,maintenance,salaries,marketing,taxes,supplies,insurance,legal,other',
            'title_ar'       => 'required|string|max:255',
            'title_en'       => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0.01',
            'expense_date'   => 'required|date',
            'description_ar' => 'nullable|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'property_id'    => 'required_if:scope,property|nullable|exists:properties,id',
            'paid_by'        => 'nullable|exists:users,id',
            'invoices'       => 'nullable|array',
            'invoices.*'     => 'file|max:10240',
        ]);

        $expense->scope        = $data['scope'];
        $expense->category     = $data['category'];
        $expense->title        = $data['title_ar'];
        $expense->amount       = $data['amount'];
        $expense->expense_date = $data['expense_date'];
        $expense->description  = $data['description_ar'] ?? null;
        if (isset($data['paid_by'])) {
            $expense->paid_by = $data['paid_by'];
        }

        if ($this->supportsBilingualExpenseFields()) {
            $expense->title_ar       = $data['title_ar'];
            $expense->title_en       = $data['title_en'];
            $expense->description_ar = $data['description_ar'] ?? null;
            $expense->description_en = $data['description_en'] ?? null;
        }

        if ($data['scope'] === 'property' && ! empty($data['property_id'])) {
            $expense->expensable_type = Property::class;
            $expense->expensable_id   = $data['property_id'];
        } elseif ($data['scope'] === 'company') {
            $expense->expensable_type = null;
            $expense->expensable_id   = null;
        }

        $expense->save();

        $this->storeInvoiceFiles($request, $expense);

        return redirect()->route('manager.expenses.index')
            ->with('success', 'تم تحديث المصروف بنجاح');
    }

    public function exportPdf(Request $request)
    {
        $pdf = $this->buildExpensePdf($request, inline: false);
        return $pdf;
    }

    public function previewPdf(Request $request)
    {
        return $this->buildExpensePdf($request, inline: true);
    }

    public function destroyReceipt(Expense $expense)
    {
        if ($expense->receipt_path) {
            $abs = storage_path('app/public/' . $expense->receipt_path);
            if (file_exists($abs)) {
                @unlink($abs);
            }
            $expense->update(['receipt_path' => null]);
        }
        return redirect()->route('manager.expenses.edit', $expense)
            ->with('success', 'تم حذف الفاتورة');
    }

    public function destroyInvoice(ExpenseInvoice $invoice)
    {
        $expenseId = $invoice->expense_id;
        $abs = storage_path('app/public/' . $invoice->file_path);
        if (file_exists($abs)) {
            @unlink($abs);
        }
        $invoice->delete();
        return redirect()->route('manager.expenses.edit', $expenseId)
            ->with('success', 'تم حذف الفاتورة');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = array_filter(explode(',', $request->input('ids', '')));
        if (empty($ids)) {
            return back()->with('error', 'لم يتم تحديد أي مصروف');
        }

        $expenses = Expense::whereIn('id', $ids)->get();
        foreach ($expenses as $expense) {
            foreach ($expense->invoices as $inv) {
                $abs = storage_path('app/public/' . $inv->file_path);
                if (file_exists($abs)) @unlink($abs);
            }
            if ($expense->receipt_path) {
                $abs = storage_path('app/public/' . $expense->receipt_path);
                if (file_exists($abs)) @unlink($abs);
            }
            $expense->delete();
        }

        return back()->with('success', 'تم حذف ' . $expenses->count() . ' مصروف');
    }

    public function destroy(Expense $expense)
    {
        // Delete all invoice files from disk
        foreach ($expense->invoices as $inv) {
            $abs = storage_path('app/public/' . $inv->file_path);
            if (file_exists($abs)) {
                @unlink($abs);
            }
        }
        // Also clean up legacy single receipt_path
        if ($expense->receipt_path) {
            $abs = storage_path('app/public/' . $expense->receipt_path);
            if (file_exists($abs)) {
                @unlink($abs);
            }
        }
        $expense->delete();
        return back()->with('success', 'تم حذف المصروف');
    }

    private function storeInvoiceFiles(Request $request, Expense $expense): void
    {
        if (! $request->hasFile('invoices')) {
            return;
        }

        $dir = storage_path('app/public/expense-invoices');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        foreach ($request->file('invoices') as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $filename = sha1(uniqid('', true) . microtime()) . '.pdf';
            $dest     = $dir . DIRECTORY_SEPARATOR . $filename;

            if (move_uploaded_file($file->getPathname(), $dest)) {
                ExpenseInvoice::create([
                    'expense_id'    => $expense->id,
                    'file_path'     => 'expense-invoices/' . $filename,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }
    }

    private function buildExpensePdf(Request $request, bool $inline)
    {
        $scope      = $request->input('scope', '');
        $category   = $request->input('category', '');
        $propertyId = $request->input('property_id', '');
        $year       = $request->input('year', now()->year);
        $month      = $request->input('month', '');

        $query = Expense::with(['paidByUser', 'expensable', 'invoices'])
            ->whereYear('expense_date', $year)
            ->when($month,      fn ($q) => $q->whereMonth('expense_date', $month))
            ->when($scope,      fn ($q) => $q->where('scope', $scope))
            ->when($category,   fn ($q) => $q->where('category', $category))
            ->when($propertyId, fn ($q) => $q->where('expensable_type', Property::class)->where('expensable_id', $propertyId))
            ->orderBy('expense_date')
            ->get();

        $totals = [
            'company'  => $query->where('scope', 'company')->sum('amount'),
            'property' => $query->where('scope', 'property')->sum('amount'),
            'total'    => $query->sum('amount'),
        ];

        $html = view('manager.expenses.expense-pdf', compact('query', 'totals', 'year', 'month', 'scope', 'category'))->render();

        $tempDir = storage_path('app/mpdf');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
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
            'tempDir'          => $tempDir,
        ]);

        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);
        $mainContent = $mpdf->Output('', 'S');

        // Save main report to temp file so FPDI can import its pages
        $tempMain = $tempDir . '/expenses_main_' . time() . '_' . rand(1000, 9999) . '.pdf';
        file_put_contents($tempMain, $mainContent);

        try {
            $fpdi = new \setasign\Fpdi\Fpdi('L', 'mm', 'A4');

            // Import main report pages
            $pageCount = $fpdi->setSourceFile($tempMain);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl  = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($tpl);
                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                $fpdi->AddPage($orientation, [$size['width'], $size['height']]);
                $fpdi->useTemplate($tpl);
            }

            // Append each expense's invoice PDFs
            foreach ($query as $expense) {
                $files = $expense->invoices->pluck('file_path')->toArray();

                // Also include legacy single receipt_path
                if ($expense->receipt_path && ! in_array($expense->receipt_path, $files)) {
                    $files[] = $expense->receipt_path;
                }

                foreach ($files as $filePath) {
                    $absPath = storage_path('app/public/' . $filePath);
                    if (! file_exists($absPath)) {
                        continue;
                    }
                    try {
                        $pdfPageCount = $fpdi->setSourceFile($absPath);
                        for ($i = 1; $i <= $pdfPageCount; $i++) {
                            $tpl  = $fpdi->importPage($i);
                            $size = $fpdi->getTemplateSize($tpl);
                            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                            $fpdi->AddPage($orientation, [$size['width'], $size['height']]);
                            $fpdi->useTemplate($tpl);
                        }
                    } catch (\Exception) {
                        // Skip unreadable/encrypted PDFs
                    }
                }
            }

            $merged = $fpdi->Output('', 'S');
        } finally {
            @unlink($tempMain);
        }

        $monthNames = ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        $periodPart = $month ? ($monthNames[$month] ?? $month) . '-' . $year : $year;
        $filename   = "expenses-{$periodPart}.pdf";

        $disposition = $inline ? 'inline' : 'attachment';

        return response($merged)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "{$disposition}; filename=\"{$filename}\"");
    }

    private function supportsBilingualExpenseFields(): bool
    {
        static $supports = null;

        if ($supports !== null) {
            return $supports;
        }

        $supports = Schema::hasColumn('expenses', 'title_ar')
            && Schema::hasColumn('expenses', 'title_en')
            && Schema::hasColumn('expenses', 'description_ar')
            && Schema::hasColumn('expenses', 'description_en');

        return $supports;
    }
}
