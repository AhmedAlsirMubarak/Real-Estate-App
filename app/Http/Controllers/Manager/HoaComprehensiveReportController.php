<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Association;
use App\Services\HoaReportDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Mpdf\Mpdf;
use setasign\Fpdi\Fpdi;

class HoaComprehensiveReportController extends Controller
{
    public function create(Request $request)
    {
        $associations = Association::with('property')->orderBy('name_ar')->get();
        $selectedId   = $request->input('association_id');

        return view('manager.associations.comprehensive-report-form', compact('associations', 'selectedId'));
    }

    public function generate(Request $request, HoaReportDataService $service)
    {
        $validated = $request->validate([
            'association_id' => ['required', Rule::in(array_merge(['all'], Association::pluck('id')->map(fn($id) => (string)$id)->all()))],
            'from'           => 'required|date',
            'to'             => 'required|date|after_or_equal:from',
            'sections'       => 'nullable|array',
            'sections.*'     => 'string',
            'locale'         => 'nullable|in:ar,en',
        ]);

        $from     = Carbon::parse($validated['from'])->startOfDay();
        $to       = Carbon::parse($validated['to'])->endOfDay();
        $sections = $validated['sections'] ?? $this->allSections();
        $locale   = $validated['locale'] ?? 'ar';

        $isAll = $validated['association_id'] === 'all';

        if ($isAll) {
            $associationIds = Association::pluck('id')->all();
            $reportTitle    = $locale === 'ar' ? 'جميع الجمعيات' : 'All Associations';
        } else {
            $associationIds = [(int) $validated['association_id']];
            $assocObj       = Association::findOrFail($validated['association_id']);
            $reportTitle    = $locale === 'ar'
                ? ($assocObj->name_ar ?? $assocObj->name_en ?? 'HOA')
                : ($assocObj->name_en ?? $assocObj->name_ar ?? 'HOA');
        }

        $data = $service->collectMultiple($associationIds, $from, $to);

        $prevLocale = app()->getLocale();
        app()->setLocale($locale);

        $html = view('manager.associations.comprehensive-report-pdf', compact(
            'data', 'from', 'to', 'sections', 'locale', 'reportTitle'
        ))->render();

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

        $mpdf->SetHTMLFooter('<div style="text-align:center; font-size:7pt; color:#9ca3af; font-family:dejavusans;">
            ' . e($reportTitle) . ' — الصفحة {PAGENO} من {nbpg}
        </div>');

        $mpdf->WriteHTML($html);

        $slug     = preg_replace('/[\s\/]+/', '-', $isAll ? 'all-associations' : ($assocObj->name_en ?? 'hoa'));
        $filename = 'hoa-report-' . $slug . '-' . $from->format('Y-m') . '-to-' . $to->format('Y-m') . '.pdf';

        // ── Save mPDF output to temp file, then merge physical attachments via FPDI ──
        $tempMain = $tempDir . '/hoa_main_' . time() . '_' . rand(1000, 9999) . '.pdf';
        file_put_contents($tempMain, $mpdf->Output('', 'S'));

        try {
            $fpdi = new Fpdi('P', 'mm', 'A4');

            // Import all pages from the main report
            $pageCount = $fpdi->setSourceFile($tempMain);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl  = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($tpl);
                $fpdi->AddPage(($size['width'] > $size['height']) ? 'L' : 'P', [$size['width'], $size['height']]);
                $fpdi->useTemplate($tpl);
            }

            // Append physical files for every association in the report
            foreach ($data['associations_data'] as $assocData) {
                $assoc    = $assocData['association'];
                $expenses = $assocData['expenses'];
                $units    = $assocData['units'];

                // 1. Rental contracts
                foreach ($units as $unit) {
                    $contract = $unit->activeRentalContract;
                    if (! $contract?->contract_file) continue;
                    $abs   = storage_path('app/public/' . $contract->contract_file);
                    $label = 'Rental Contract — ' . ($contract->tenant?->user?->name ?? 'Tenant')
                           . ' (Unit ' . ($unit->unit_number ?? '—') . ')';
                    $this->appendFile($fpdi, $abs, $label);
                }

                // 2. Expense invoices (multi-invoice + legacy single receipt)
                foreach ($expenses as $expense) {
                    foreach ($expense->invoices as $inv) {
                        $abs   = storage_path('app/public/' . $inv->file_path);
                        $label = 'Invoice — ' . ($expense->title ?? 'Expense')
                               . ' (' . $expense->expense_date->format('Y/m/d') . ')';
                        $this->appendFile($fpdi, $abs, $label);
                    }
                    if ($expense->receipt_path) {
                        $abs   = storage_path('app/public/' . $expense->receipt_path);
                        $label = 'Receipt — ' . ($expense->title ?? 'Expense')
                               . ' (' . $expense->expense_date->format('Y/m/d') . ')';
                        $this->appendFile($fpdi, $abs, $label);
                    }
                }

                // 3. Association documents (certificate, sketch, personal IDs, etc.)
                $assocDocs = [
                    'No Objection Certificate'       => $assoc->no_objection_certificate_path,
                    'Sketch'                          => $assoc->sketch_path,
                    'Owners Association Certificate'  => $assoc->association_certificate_path,
                    'Personal ID'                     => $assoc->personal_id_path,
                    "Association Manager's ID"        => $assoc->manager_id_path,
                ];
                foreach ($assocDocs as $label => $relPath) {
                    if (! $relPath) continue;
                    $this->appendFile($fpdi, storage_path('app/public/' . $relPath), $label);
                }

                // 4. Commission invoice PDFs
                foreach ($assocData['commissionInvoices'] as $cinv) {
                    if (! $cinv->file_path) continue;
                    $this->appendFile($fpdi, storage_path('app/public/' . $cinv->file_path),
                        'Commission Invoice — ' . $cinv->invoice_number . ' (' . $cinv->recipient_name . ')');
                }

                // 6. Generated NOC certificates (rental)
                foreach ($assoc->noObjectionCertificates as $noc) {
                    if (! $noc->file_path) continue;
                    $this->appendFile($fpdi, storage_path('app/' . $noc->file_path),
                        'NOC Rental — ' . $noc->ref_number);
                }

                // 7. Generated NOC certificates (sale)
                foreach ($assoc->noObjectionSaleCertificates as $noc) {
                    if (! $noc->file_path) continue;
                    $this->appendFile($fpdi, storage_path('app/' . $noc->file_path),
                        'NOC Sale — ' . $noc->ref_number);
                }
            }

            $merged = $fpdi->Output('', 'S');
        } finally {
            @unlink($tempMain);
        }

        $disposition = $request->input('mode') === 'download' ? 'attachment' : 'inline';

        return response($merged)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', $disposition . '; filename="' . $filename . '"');
    }

    private function appendFile(Fpdi $fpdi, string $absPath, string $label): void
    {
        if (! file_exists($absPath)) {
            return;
        }

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
            } catch (\Exception) {
                // Skip password-protected or corrupted PDFs
            }
            return;
        }

        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $fpdi->AddPage('P', [210, 297]);

            // Header label (FPDF built-in fonts are Latin-only — strip non-ASCII)
            $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $label) ?: 'Document';
            $fpdi->SetFont('Helvetica', 'B', 10);
            $fpdi->SetTextColor(30, 58, 138);
            $fpdi->SetY(7);
            $fpdi->Cell(0, 8, $ascii, 0, 1, 'C');
            $fpdi->SetDrawColor(191, 219, 254);
            $fpdi->Line(10, 16, 200, 16);

            // Fit image within printable area
            [$imgW, $imgH] = @getimagesize($absPath) ?: [210, 270];
            $maxW = 190; $maxH = 263;
            if (($imgW / $maxW) > ($imgH / $maxH)) {
                $w = $maxW; $h = $maxW * $imgH / max($imgW, 1);
            } else {
                $h = $maxH; $w = $maxH * $imgW / max($imgH, 1);
            }
            $fpdi->Image($absPath, (210 - $w) / 2, 19, $w, $h);
        }
    }

    private function allSections(): array
    {
        return [
            'executive_summary',
            'association_info',
            'financial_summary',
            'contributions',
            'owner_statements',
            'aging',
            'unit_status',
            'expenses',
            'maintenance',
            'meetings',
            'reserve_fund',
            'commission_invoices',
            'attachments',
        ];
    }
}
