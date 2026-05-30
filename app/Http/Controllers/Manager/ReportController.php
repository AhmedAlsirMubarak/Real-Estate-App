<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Mpdf\Mpdf;
use ZipArchive;

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
            'units' => fn ($q) => $q->orderBy('unit_number'),
            'units.activeRentalContract.tenant.user',
            'units.activeRentalContract.tenant',
            'units.activeSaleContract.buyer.user',
            'units.maintenanceRequests.tenant.user',
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

        $payments = $paymentsQuery->with(['tenant.user', 'rentalContract.unit'])->orderBy('year')->orderBy('month')->get();

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
        if ($export === 'pdf' || $export === 'preview' || $export === 'zip') {
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

            $reportName = "تقرير-{$property->name}-{$year}.pdf";
            $reportPdf  = $mpdf->Output($reportName, 'S');

            if ($export === 'zip') {
                return $this->buildAttachmentsZip($property, $expenses, $reportName, $reportPdf);
            }

            $disposition = $export === 'preview' ? 'inline' : 'attachment';
            return response($reportPdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', $disposition . '; filename="' . $reportName . '"');
        }

        return view('manager.reports.property', compact('property', 'payments', 'maintenanceRequests', 'expenses', 'stats', 'year', 'month'));
    }

    private function buildAttachmentsZip(Property $property, Collection $expenses, string $reportName, string $reportPdf)
    {
        $tmpZip = storage_path('app/mpdf/' . uniqid('report_', true) . '.zip');

        $zip = new ZipArchive();
        if ($zip->open($tmpZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return response($reportPdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $reportName . '"');
        }

        // 1. Main report PDF
        $zip->addFromString($reportName, $reportPdf);

        // 2. Expense invoices
        $invoiceIndex = 1;
        foreach ($expenses as $expense) {
            if (! $expense->receipt_path) continue;

            $filePath = storage_path('app/public/' . $expense->receipt_path);
            if (! file_exists($filePath)) continue;

            $label = 'فاتورة-' . $invoiceIndex . '-' . preg_replace('/[^\w\-]/u', '_', $expense->title ?? 'مصروف') . '.pdf';
            $zip->addFile($filePath, 'الفواتير/' . $label);
            $invoiceIndex++;
        }

        // 3. Rental contract files
        $contractIndex = 1;
        foreach ($property->units as $unit) {
            $contract = $unit->activeRentalContract ?? null;
            if (! $contract || ! $contract->contract_file) continue;

            $filePath = storage_path('app/public/' . $contract->contract_file);
            if (! file_exists($filePath)) continue;

            $tenantName = $contract->tenant?->user?->name ?? 'مستأجر';
            $label = 'عقد-' . $contractIndex . '-' . preg_replace('/[^\w\-]/u', '_', $tenantName) . '.pdf';
            $zip->addFile($filePath, 'العقود/' . $label);
            $contractIndex++;
        }

        $zip->close();

        $zipName = "تقرير-{$property->name}-مع-المرفقات.zip";
        $content = file_get_contents($tmpZip);
        @unlink($tmpZip);

        return response($content)
            ->header('Content-Type', 'application/zip')
            ->header('Content-Disposition', 'attachment; filename="' . $zipName . '"');
    }
}
