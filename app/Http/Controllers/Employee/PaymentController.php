<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeCommission;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    private const RENT_COLLECTION_COMMISSION_RATE = 2.00;

    public function index(Request $request)
    {
        $employee = $request->user();
        $status = $request->input('status', 'all');
        $year = (int) $request->input('year', now()->year);
        $month = $request->filled('month') ? (int) $request->input('month') : null;

        if ($year < 2000 || $year > ((int) now()->year + 2)) {
            $year = (int) now()->year;
        }

        if ($month !== null && ($month < 1 || $month > 12)) {
            $month = null;
        }

        $baseQuery = Payment::whereHas('rentalContract.unit.property', function ($q) use ($employee) {
            $q->where('employee_id', $employee->id);
        });

        $payments = (clone $baseQuery)
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->where('year', $year)
            ->when($month, fn($q) => $q->where('month', $month))
            ->with(['tenant.user', 'rentalContract.unit.property'])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(15)
            ->appends($request->query());

        $yearPayments = (clone $baseQuery)
            ->where('year', $year)
            ->get();

        $monthlySummary = collect(range(1, 12))->map(function (int $itemMonth) use ($yearPayments) {
            $rows = $yearPayments->where('month', $itemMonth);

            return [
                'month' => $itemMonth,
                'paid' => (float) $rows->where('status', 'paid')->sum('amount'),
                'pending' => (float) $rows->where('status', 'pending')->sum('amount'),
                'overdue' => (float) $rows->where('status', 'overdue')->sum('amount'),
            ];
        });

        return view('employee.payments.index', compact('payments', 'status', 'year', 'month', 'monthlySummary'));
    }

    public function confirm(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);
        $employee = $request->user();
        $payment = $this->ensureEmployeeCanManagePayment($employee->id, $payment);

        DB::transaction(function () use ($payment, $employee, $validated) {
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'confirmed_by' => $employee->id,
                'notes' => $validated['notes'] ?? null,
            ]);

            $property = $payment->rentalContract?->unit?->property;
            $rate = self::RENT_COLLECTION_COMMISSION_RATE;
            $commissionAmount = round(((float) $payment->amount) * ($rate / 100), 2);

            if ($property && $commissionAmount > 0) {
                EmployeeCommission::updateOrCreate(
                    [
                        'payment_id' => $payment->id,
                        'type' => 'rent_collection',
                    ],
                    [
                        'employee_id' => $employee->id,
                        'property_id' => $property->id,
                        'base_amount' => $payment->amount,
                        'rate' => $rate,
                        'commission_amount' => $commissionAmount,
                        'notes' => $validated['notes'] ?? null,
                        'recorded_at' => now(),
                    ]
                );
            }
        });

        return redirect()->back()->with(
            'success',
            app()->getLocale() === 'en'
                ? 'Payment confirmed and commission calculated automatically.'
                : 'تم تأكيد الدفعة واحتساب العمولة تلقائياً'
        );
    }

    public function markOverdue(Request $request, Payment $payment)
    {
        $this->ensureEmployeeCanManagePayment($request->user()->id, $payment);
        $payment->update(['status' => 'overdue']);
        return redirect()->back()->with(
            'success',
            app()->getLocale() === 'en' ? 'Payment status updated.' : 'تم تحديث حالة الدفعة'
        );
    }

    private function ensureEmployeeCanManagePayment(int $employeeId, Payment $payment): Payment
    {
        $payment->loadMissing('rentalContract.unit.property');
        $property = $payment->rentalContract?->unit?->property;

        abort_if(! $property || (int) $property->employee_id !== $employeeId, 403);

        return $payment;
    }
}
