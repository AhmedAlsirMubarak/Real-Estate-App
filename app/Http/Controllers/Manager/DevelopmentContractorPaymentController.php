<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentContractor;
use App\Models\DevelopmentProject;
use Illuminate\Http\Request;

class DevelopmentContractorPaymentController extends Controller
{
    public function store(Request $request, DevelopmentProject $development, DevelopmentContractor $contractor)
    {
        abort_if($contractor->development_project_id !== $development->id, 404);

        $request->validate([
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'paid_at'     => 'required|date',
        ]);

        $contractor->payments()->create($request->only(['amount', 'description', 'paid_at']));

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم تسجيل الدفعة بنجاح.' : 'Payment recorded.');
    }
}
