<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
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

        $expenses   = $query->paginate(20)->withQueryString();
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
            'category'       => 'required|in:utilities,maintenance,salaries,marketing,repairs,other',
            'title_ar'       => 'required|string|max:255',
            'title_en'       => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0.01',
            'expense_date'   => 'required|date',
            'description_ar' => 'nullable|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'property_id'    => 'required_if:scope,property|nullable|exists:properties,id',
            'paid_by'        => 'nullable|exists:users,id',
            'invoice'        => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $expense = new Expense();
        $expense->scope        = $data['scope'];
        $expense->category     = $data['category'];
        $expense->title        = $data['title_ar'];
        $expense->amount       = $data['amount'];
        $expense->expense_date = $data['expense_date'];
        $expense->description  = $data['description_ar'] ?? null;
        $expense->paid_by      = $data['paid_by'] ?? auth()->id();

        if ($this->supportsBilingualExpenseFields()) {
            $expense->title_ar = $data['title_ar'];
            $expense->title_en = $data['title_en'];
            $expense->description_ar = $data['description_ar'] ?? null;
            $expense->description_en = $data['description_en'] ?? null;
        }

        if ($data['scope'] === 'property' && ! empty($data['property_id'])) {
            $expense->expensable_type = Property::class;
            $expense->expensable_id   = $data['property_id'];
        }

        if ($request->hasFile('invoice')) {
            $file = $request->file('invoice');
            if ($file->isValid()) {
                $ext      = 'pdf';
                $filename = sha1(uniqid('', true) . microtime()) . '.' . $ext;
                try {
                    $stored = Storage::disk('public')->putFileAs('expense-invoices', $file, $filename);
                    if ($stored) {
                        $expense->receipt_path = $stored;
                    }
                } catch (\Throwable) {
                    // silently skip if storage fails
                }
            }
        }

        $expense->save();

        return redirect()->route('manager.expenses.index')
            ->with('success', 'تم تسجيل المصروف بنجاح');
    }

    public function exportPdf(Request $request)
    {
        $scope      = $request->input('scope', '');
        $category   = $request->input('category', '');
        $propertyId = $request->input('property_id', '');
        $year       = $request->input('year', now()->year);
        $month      = $request->input('month', '');

        $query = Expense::with(['paidByUser', 'expensable'])
            ->whereYear('expense_date', $year)
            ->when($month, fn ($q) => $q->whereMonth('expense_date', $month))
            ->when($scope, fn ($q) => $q->where('scope', $scope))
            ->when($category, fn ($q) => $q->where('category', $category))
            ->when($propertyId, fn ($q) => $q->where('expensable_type', Property::class)->where('expensable_id', $propertyId))
            ->orderBy('expense_date')
            ->get();

        $totals = [
            'company'  => $query->where('scope', 'company')->sum('amount'),
            'property' => $query->where('scope', 'property')->sum('amount'),
            'total'    => $query->sum('amount'),
        ];

        $html = view('manager.expenses.expense-pdf', compact('query', 'totals', 'year', 'month', 'scope', 'category'))->render();

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
        $periodPart = $month ? ($monthNames[$month] ?? $month) . '-' . $year : $year;
        $filename   = "تقرير-المصروفات-{$periodPart}.pdf";

        return response($mpdf->Output($filename, 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'تم حذف المصروف');
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
