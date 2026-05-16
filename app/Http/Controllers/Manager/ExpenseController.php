<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $scope      = $request->input('scope', '');
        $category   = $request->input('category', '');
        $propertyId = $request->input('property_id', '');
        $year       = $request->input('year', now()->year);

        $query = Expense::with(['paidByUser', 'expensable'])
            ->whereYear('expense_date', $year)
            ->latest('expense_date');

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

        $totals = [
            'company'  => Expense::where('scope', 'company')->whereYear('expense_date', $year)->sum('amount'),
            'property' => Expense::where('scope', 'property')->whereYear('expense_date', $year)->sum('amount'),
            'total'    => Expense::whereYear('expense_date', $year)->sum('amount'),
        ];

        return view('manager.expenses.index', compact('expenses', 'properties', 'totals', 'scope', 'category', 'propertyId', 'year'));
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
            $file    = $request->file('invoice');
            $tmpPath = $file->getPathname();
            if ($tmpPath && file_exists($tmpPath)) {
                $ext      = 'pdf';
                $filename = sha1(uniqid('', true) . microtime()) . '.' . $ext;
                try {
                    $stored = Storage::disk('public')->putFileAs('expense-invoices', $tmpPath, $filename);
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
