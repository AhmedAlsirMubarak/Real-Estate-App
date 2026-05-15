<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $tenant = $request->user()->tenant;

        $payments = $tenant->payments()
            ->with('rentalContract.unit.property')
            ->latest()
            ->paginate(10);

        return view('tenant.payments.index', compact('payments'));
    }

    public function show(Request $request, Payment $payment)
    {
        $tenant = $request->user()->tenant;

        if ($payment->tenant_id !== $tenant->id) {
            abort(403);
        }

        $payment->load(['rentalContract.unit.property', 'confirmedByUser']);
        return view('tenant.payments.show', compact('payment'));
    }
}
