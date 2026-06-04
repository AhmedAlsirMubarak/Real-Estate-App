<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Property;
use App\Models\User;
use App\Services\BuildingReportDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use setasign\Fpdi\Fpdi;

class BuildingComprehensiveReportController extends Controller
{
    public function create(Request $request)
    {
        $properties = Property::orderBy('name_ar')->get();
        $owners     = Owner::with('user')->get();
        $employees  = User::role('employee')->orderBy('name')->get();
        $selectedPropertyId = $request->input('property_id');

        return view('manager.properties.comprehensive-report-form',
            compact('properties', 'owners', 'employees', 'selectedPropertyId'));
    }

    public function generate(Request $request, BuildingReportDataService $service)
    {
        $validated = $request->validate([
            'property_id'  => 'nullable|exists:properties,id',
            'type'         => 'nullable|in:apartment_building,villa,farm,chalet',
            'owner_id'     => 'nullable|exists:owners,id',
            'employee_id'  => 'nullable|exists:users,id',
            'from'         => 'required|date',
            'to'           => 'required|date|after_or_equal:from',
            'sections'     => 'nullable|array',
            'sections.*'   => 'string',
            'locale'       => 'nullable|in:ar,en',
        ]);

        $from     = Carbon::parse($validated['from'])->startOfDay();
        $to       = Carbon::parse($validated['to'])->endOfDay();
        $sections = $validated['sections'] ?? $this->allSections();
        $locale   = $validated['locale'] ?? 'ar';

        $filters = [
            'property_id' => $validated['property_id'] ?? null,
            'type'        => $validated['type'] ?? null,
            'owner_id'    => $validated['owner_id'] ?? null,
            'employee_id' => $validated['employee_id'] ?? null,
        ];

        $data = $service->collect($filters, $from, $to);

        // Report title
        if ($filters['property_id']) {
            $prop         = Property::find($filters['property_id']);
            $reportTitle  = $locale === 'ar' ? ($prop->name_ar ?? $prop->name_en) : ($prop->name_en ?? $prop->name_ar);
        } else {
            $reportTitle = $locale === 'ar' ? 'تقرير إدارة المباني الشامل' : 'Comprehensive Building Management Report';
        }

        $prevLocale = app()->getLocale();
        app()->setLocale($locale);

        $html = view('manager.properties.comprehensive-report-pdf',
            compact('data', 'from', 'to', 'sections', 'locale', 'reportTitle', 'filters')
        )->render();

        app()->setLocale($prevLocale);

        $tempDir = storage_path('app/mpdf');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $mpdf = new Mpdf([
            'mode'             => 'utf-8',
            'format'           => 'A4',
            'orientation'      => 'P',
            'margin_top'       => 0,
            'margin_bottom'    => 16,
            'margin_left'      => 8,
            'margin_right'     => 8,
            'default_font'     => 'dejavusans',
            'autoLangToFont'   => true,
            'autoScriptToLang' => true,
            'tempDir'          => $tempDir,
        ]);

        if ($locale === 'ar') {
            $mpdf->SetDirectionality('rtl');
        }

        $mpdf->SetHTMLFooter('<div style="text-align:center;font-size:7pt;color:#9ca3af;font-family:dejavusans;">'
            . e($reportTitle) . ' — {PAGENO} / {nbpg}</div>');

        $mpdf->WriteHTML($html);

        // ── Merge physical attachments via FPDI ───────────────────────────────
        $tempMain = $tempDir . '/bldg_main_' . time() . '_' . rand(1000, 9999) . '.pdf';
        file_put_contents($tempMain, $mpdf->Output('', 'S'));

        try {
            $fpdi = new Fpdi('P', 'mm', 'A4');

            $pageCount = $fpdi->setSourceFile($tempMain);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl  = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($tpl);
                $fpdi->AddPage(($size['width'] > $size['height']) ? 'L' : 'P', [$size['width'], $size['height']]);
                $fpdi->useTemplate($tpl);
            }

            // Append rental contracts
            foreach ($data['allContracts'] as $contract) {
                if (! $contract->contract_file) continue;
                $abs   = storage_path('app/public/' . $contract->contract_file);
                $label = 'Contract — ' . ($contract->tenant?->user?->name ?? 'Tenant')
                       . ' (Unit ' . ($contract->unit?->unit_number ?? '—') . ')';
                $this->appendFile($fpdi, $abs, $label);
            }

            // Append expense invoices
            foreach ($data['expenses'] as $expense) {
                foreach ($expense->invoices as $inv) {
                    $this->appendFile($fpdi, storage_path('app/public/' . $inv->file_path),
                        'Invoice — ' . ($expense->title ?? 'Expense'));
                }
                if ($expense->receipt_path) {
                    $this->appendFile($fpdi, storage_path('app/public/' . $expense->receipt_path),
                        'Receipt — ' . ($expense->title ?? 'Expense'));
                }
            }

            $merged = $fpdi->Output('', 'S');
        } finally {
            @unlink($tempMain);
        }

        $slug     = preg_replace('/[\s\/]+/', '-', $filters['property_id']
            ? (Property::find($filters['property_id'])?->name_en ?? 'property')
            : 'all-properties');
        $filename = 'building-report-' . $slug . '-' . $from->format('Y-m') . '-to-' . $to->format('Y-m') . '.pdf';

        $disposition = $request->input('mode') === 'download' ? 'attachment' : 'inline';

        return response($merged)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', $disposition . '; filename="' . $filename . '"');
    }

    private function appendFile(Fpdi $fpdi, string $absPath, string $label): void
    {
        if (! file_exists($absPath)) return;
        $ext = strtolower(pathinfo($absPath, PATHINFO_EXTENSION));

        if ($ext === 'pdf') {
            try {
                $pages = $fpdi->setSourceFile($absPath);
                for ($i = 1; $i <= $pages; $i++) {
                    $tpl  = $fpdi->importPage($i);
                    $size = $fpdi->getTemplateSize($tpl);
                    $fpdi->AddPage(($size['width'] > $size['height']) ? 'L' : 'P', [$size['width'], $size['height']]);
                    $fpdi->useTemplate($tpl);
                }
            } catch (\Exception) {}
            return;
        }

        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $fpdi->AddPage('P', [210, 297]);
            $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $label) ?: 'Document';
            $fpdi->SetFont('Helvetica', 'B', 10);
            $fpdi->SetTextColor(30, 58, 138);
            $fpdi->SetY(7);
            $fpdi->Cell(0, 8, $ascii, 0, 1, 'C');
            $fpdi->SetDrawColor(191, 219, 254);
            $fpdi->Line(10, 16, 200, 16);
            [$imgW, $imgH] = @getimagesize($absPath) ?: [210, 270];
            $maxW = 190; $maxH = 263;
            $w = ($imgW / $maxW) > ($imgH / $maxH) ? $maxW : $maxH * $imgW / max($imgH, 1);
            $h = ($imgW / $maxW) > ($imgH / $maxH) ? $maxW * $imgH / max($imgW, 1) : $maxH;
            $fpdi->Image($absPath, (210 - $w) / 2, 19, $w, $h);
        }
    }

    private function allSections(): array
    {
        return [
            'executive_summary', 'property_info', 'financial_performance',
            'occupancy', 'unit_details', 'contracts', 'tenants',
            'rental_income', 'outstanding', 'expenses', 'profitability',
            'maintenance', 'vacancy', 'alerts', 'attachments',
        ];
    }
}
