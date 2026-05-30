<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\RentalContract;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $year = $request->input('year', now()->year);
        $month = $request->input('month');

        $payments = Payment::with(['tenant.user', 'rentalContract.unit.property'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->where('year', $year)
            ->when($month, fn($q) => $q->where('month', $month))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('accountant.payments.index', compact('payments', 'status', 'year', 'month'));
    }

    public function generateMonthlyPayments(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $activeContracts = RentalContract::where('status', 'active')->get();

        // Load all contract IDs that already have a payment this month in one query
        $alreadyPaid = Payment::where('year', $request->year)
            ->where('month', $request->month)
            ->whereIn('rental_contract_id', $activeContracts->pluck('id'))
            ->pluck('rental_contract_id')
            ->flip()
            ->all();

        $created = 0;
        $newPayments = [];

        foreach ($activeContracts as $contract) {
            if (isset($alreadyPaid[$contract->id])) continue;

            $newPayments[] = [
                'rental_contract_id' => $contract->id,
                'tenant_id'          => $contract->tenant_id,
                'amount'             => $contract->monthly_rent,
                'month'              => $request->month,
                'year'               => $request->year,
                'status'             => 'pending',
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
            $created++;
        }

        if ($newPayments) {
            Payment::insert($newPayments);
        }

        return redirect()->route('accountant.payments.index')
            ->with('success', "تم إنشاء {$created} إشعار دفع للشهر المحدد");
    }

    public function confirm(Request $request, Payment $payment)
    {
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
            'confirmed_by' => $request->user()->id,
            'notes' => $request->input('notes'),
        ]);

        return redirect()->back()->with('success', 'تم تأكيد الدفعة بنجاح');
    }

    public function markOverdue(Payment $payment)
    {
        $payment->update(['status' => 'overdue']);
        return redirect()->back()->with('success', 'تم تحديث حالة الدفعة');
    }

    public function exportPdf(Request $request)
    {
        $status = $request->input('status', 'all');
        $year   = $request->input('year', now()->year);
        $month  = $request->input('month');
        $mode   = $request->input('mode', 'download');

        $payments = Payment::with(['tenant.user', 'rentalContract.unit.property'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->where('year', $year)
            ->when($month, fn($q) => $q->where('month', $month))
            ->latest()
            ->get();

        $totals = [
            'paid'    => $payments->where('status', 'paid')->sum('amount'),
            'pending' => $payments->where('status', 'pending')->sum('amount'),
            'overdue' => $payments->where('status', 'overdue')->sum('amount'),
            'count'   => $payments->count(),
        ];

        $fontPath     = storage_path('fonts/NotoNaskhArabic-Regular.ttf');
        $fontBoldPath = storage_path('fonts/NotoNaskhArabic-Bold.ttf');

        $pdf = Pdf::loadView('accountant.payments.pdf', compact(
                'payments', 'totals', 'year', 'month', 'status', 'fontPath', 'fontBoldPath'
            ))
            ->setPaper('a4', 'portrait');

        $filename = "تقرير-المدفوعات-{$year}" . ($month ? "-{$month}" : '') . '.pdf';

        return $mode === 'preview'
            ? $pdf->stream($filename)
            : $pdf->download($filename);
    }
}
