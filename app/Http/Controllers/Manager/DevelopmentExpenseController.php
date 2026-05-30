<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentExpense;
use App\Models\DevelopmentProject;
use Illuminate\Http\Request;

class DevelopmentExpenseController extends Controller
{
    public function store(Request $request, DevelopmentProject $development)
    {
        $validated = $request->validate([
            'category'     => 'required|in:' . implode(',', DevelopmentExpense::categories()),
            'item_name'    => 'required|string|max:255',
            'quantity'     => 'required|numeric|min:0.001',
            'unit'         => 'nullable|string|max:50',
            'unit_cost'    => 'required|numeric|min:0',
            'description'  => 'nullable|string|max:500',
            'expense_date' => 'required|date',
        ]);

        $validated['amount'] = round((float) $validated['quantity'] * (float) $validated['unit_cost'], 2);

        $development->expenses()->create($validated);

        return back()->with('success', app()->getLocale() === 'ar' ? 'تمت إضافة المصروف بنجاح.' : 'Expense added.');
    }

    public function destroy(DevelopmentProject $development, DevelopmentExpense $expense)
    {
        abort_if($expense->development_project_id !== $development->id, 404);
        $expense->delete();
        return back()->with('success', app()->getLocale() === 'ar' ? 'تم حذف المصروف.' : 'Expense removed.');
    }
}
