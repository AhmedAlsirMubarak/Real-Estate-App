<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\Property;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class ReportController extends Controller
{
    public function index()
    {
        $properties = Property::with('employee', 'owner.user')->get();
        return view('manager.reports.index', compact('properties'));
    }

    public function propertyReport(Request $request, Property $property)
    {
        $property->load([
            'employee',
            'owner.user',
            'units.activeRentalContract.tenant.user',
            'units.activeSaleContract.buyer.user',
            'units.maintenanceRequests',
            'expenses',
        ]);

        $year  = $request->filled('year') ? (int) $request->input('year') : now()->year;
        $month = $request->filled('month') ? (int) $request->input('month') : null;

        $paymentsQuery = Payment::whereHas('rentalContract.unit', function ($q) use ($property) {
            $q->where('property_id', $property->id);
        })->where('year', $year);

        if ($month) {
            $paymentsQuery->where('month', $month);
        }

        $payments = $paymentsQuery->with(['tenant.user', 'rentalContract.unit'])->get();

        $maintenanceRequests = MaintenanceRequest::whereHas('unit', function ($q) use ($property) {
            $q->where('property_id', $property->id);
        })->with(['tenant.user', 'unit'])->get();

        $expenseQuery = $property->expenses()->where('expensable_type', Property::class);
        if ($month) {
            $expenseQuery->whereMonth('expense_date', $month);
        }
        $expenses = $expenseQuery->whereYear('expense_date', $year)->get();

        $stats = [
            'total_units'           => $property->units->count(),
            'rented_units'          => $property->units->where('status', 'rented')->count(),
            'sold_units'            => $property->units->where('status', 'sold')->count(),
            'total_revenue'         => $payments->where('status', 'paid')->sum('amount'),
            'total_expenses'        => $expenses->sum('amount'),
            'net_income'            => $payments->where('status', 'paid')->sum('amount') - $expenses->sum('amount'),
            'pending_payments'      => $payments->where('status', 'pending')->count(),
            'overdue_payments'      => $payments->where('status', 'overdue')->count(),
            'maintenance_count'     => $maintenanceRequests->count(),
            'completed_maintenance' => $maintenanceRequests->where('status', 'completed')->count(),
        ];

        $export = $request->input('export');
        if ($export === 'pdf' || $export === 'preview') {
            $html = view(
                'manager.reports.property-pdf',
                compact('property', 'payments', 'maintenanceRequests', 'expenses', 'stats', 'year', 'month')
            )->render();

            if (! is_dir(storage_path('app/mpdf'))) {
                mkdir(storage_path('app/mpdf'), 0755, true);
            }

            $mpdf = new Mpdf([
                'mode'             => 'utf-8',
                'format'           => 'A4',
                'orientation'      => 'P',
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

            $filename = "تقرير-{$property->name}-{$year}.pdf";

            $disposition = $export === 'preview' ? 'inline' : 'attachment';
            return response($mpdf->Output($filename, 'S'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', $disposition . '; filename="' . $filename . '"');
        }

        return view('manager.reports.property', compact('property', 'payments', 'maintenanceRequests', 'expenses', 'stats', 'year', 'month'));
    }
}
