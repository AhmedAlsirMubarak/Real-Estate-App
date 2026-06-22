<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Imports\AssociationImport;
use App\Models\Association;
use App\Models\NoObjectionCertificate;
use App\Models\NoObjectionSaleCertificate;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;

class AssociationController extends Controller
{
    public function index()
    {
        $associations = Association::with(['property.owners', 'dues', 'createdBy'])
            ->latest()
            ->paginate(15);

        return view('manager.associations.index', compact('associations'));
    }

    public function create()
    {
        $properties = Property::whereDoesntHave('association')->orderBy('name_ar')->get();
        return view('manager.associations.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'property_id'                => 'required|exists:properties,id|unique:associations,property_id',
            'name_ar'                    => 'required|string|max:255',
            'name_en'                    => 'required|string|max:255',
            'established_date'           => 'nullable|date',
            'monthly_fee_per_unit'       => 'required|numeric|min:0',
            'description_ar'             => 'nullable|string',
            'description_en'             => 'nullable|string',
            'status'                     => 'required|in:active,inactive',
            'electricity_account_number' => 'nullable|string|max:100',
            'water_account_number'       => 'nullable|string|max:100',
            'no_objection_certificate'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'sketch'                     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'association_certificate'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'personal_id'                => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'manager_id'                 => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $association = Association::create(array_filter($data, fn ($k) => !in_array($k, [
            'no_objection_certificate', 'sketch', 'association_certificate', 'personal_id', 'manager_id',
        ]), ARRAY_FILTER_USE_KEY));

        $this->handleDocumentUploads($request, $association);

        return redirect()
            ->route('manager.associations.show', $association)
            ->with('success', __('Created Successfully'));
    }

    public function show(Association $association)
    {
        $association->load([
            'property.owners',
            'property.units',
            'dues' => fn ($q) => $q->latest('due_date')->limit(20),
            'dues.owner.user',
            'meetings' => fn ($q) => $q->latest('scheduled_at')->limit(10),
            'noObjectionCertificates.generatedBy',
            'noObjectionSaleCertificates.generatedBy',
        ]);

        return view('manager.associations.show', compact('association'));
    }

    public function edit(Association $association)
    {
        return view('manager.associations.edit', compact('association'));
    }

    public function update(Request $request, Association $association)
    {
        $data = $request->validate([
            'name_ar'                    => 'required|string|max:255',
            'name_en'                    => 'required|string|max:255',
            'established_date'           => 'nullable|date',
            'monthly_fee_per_unit'       => 'required|numeric|min:0',
            'description_ar'             => 'nullable|string',
            'description_en'             => 'nullable|string',
            'status'                     => 'required|in:active,inactive',
            'electricity_account_number' => 'nullable|string|max:100',
            'water_account_number'       => 'nullable|string|max:100',
            'no_objection_certificate'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'sketch'                     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'association_certificate'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'personal_id'                => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'manager_id'                 => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $association->update(array_filter($data, fn ($k) => !in_array($k, [
            'no_objection_certificate', 'sketch', 'association_certificate', 'personal_id', 'manager_id',
        ]), ARRAY_FILTER_USE_KEY));

        $this->handleDocumentUploads($request, $association);

        return redirect()
            ->route('manager.associations.show', $association)
            ->with('success', __('Updated Successfully'));
    }

    public function deleteDocument(Association $association, string $field)
    {
        $allowed = [
            'no_objection_certificate_path',
            'sketch_path',
            'association_certificate_path',
            'personal_id_path',
            'manager_id_path',
        ];

        abort_unless(in_array($field, $allowed), 403);

        $path = $association->$field;
        if ($path) {
            $abs = storage_path('app/public/' . $path);
            if (file_exists($abs)) {
                @unlink($abs);
            }
        }

        $association->update([$field => null]);

        return back()->with('success', __('Document deleted'));
    }

    public function destroy(Association $association)
    {
        $association->delete();
        return redirect()->route('manager.associations.index')->with('success', __('Deleted Successfully'));
    }

    public function bulkDestroy(Request $request)
    {
        $ids = array_filter(explode(',', $request->input('ids', '')));
        if (empty($ids)) {
            return back()->with('error', 'لم يتم تحديد أي جمعية');
        }

        $count = Association::whereIn('id', $ids)->count();
        Association::whereIn('id', $ids)->delete();

        return back()->with('success', 'تم حذف ' . $count . ' جمعية');
    }

    public function importForm()
    {
        return view('manager.associations.import');
    }

    public function downloadTemplate()
    {
        $q = fn($v) => '"' . str_replace('"', '""', (string)($v ?? '')) . '"';

        $rows = [
            ['property_code', 'name_ar', 'name_en', 'monthly_fee_per_unit', 'established_date',
             'status', 'electricity_account_number', 'water_account_number', 'description_ar', 'description_en'],
            ['TH-V-001', 'جمعية ملاك برج النخيل', 'Palm Tower Owners Association', '50', '2024-01-15',
             'active', '', '', '', ''],
        ];

        $csv = "\xEF\xBB\xBF";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map($q, $row)) . "\r\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="associations-import-template.csv"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'file.required' => 'Please select a file to upload.',
            'file.mimes'    => 'Only Excel (.xlsx, .xls) or CSV (.csv) files are accepted.',
            'file.max'      => 'The file must not exceed 10 MB.',
        ]);

        $uploaded = $request->file('file');
        $ext      = strtolower($uploaded->getClientOriginalExtension()) ?: 'xlsx';
        $tempPath = tempnam(sys_get_temp_dir(), 'import_') . '.' . $ext;

        try {
            copy($uploaded->getPathname(), $tempPath);
            $importer = new AssociationImport($tempPath);
            $importer->run();
        } catch (\Throwable) {
            return back()->withErrors([
                'file' => 'Could not read the file. Make sure it is a valid Excel or CSV file.',
            ]);
        } finally {
            if (file_exists($tempPath)) @unlink($tempPath);
        }

        return back()->with('import_results', [
            'imported' => $importer->imported,
            'errors'   => $importer->rowErrors,
            'warnings' => $importer->warnings,
        ]);
    }

    public function export()
    {
        $associations = Association::with(['property.owners', 'property.units'])->latest()->get();

        $rows = [['ID', 'Property Code', 'Property Name', 'Name (AR)', 'Name (EN)',
                   'Monthly Fee / Unit', 'Established Date', 'Status',
                   'Electricity Account', 'Water Account', 'Owners Count', 'Units Count',
                   'Description (AR)', 'Description (EN)', 'Created At']];

        foreach ($associations as $a) {
            $rows[] = [
                $a->id,
                $a->property?->code ?? '',
                $a->property?->name ?? '',
                $a->name_ar ?? '',
                $a->name_en ?? '',
                $a->monthly_fee_per_unit ?? '',
                $a->established_date?->format('Y-m-d') ?? '',
                $a->status ?? 'active',
                $a->electricity_account_number ?? '',
                $a->water_account_number ?? '',
                $a->property?->owners?->count() ?? 0,
                $a->property?->units?->count() ?? 0,
                $a->description_ar ?? '',
                $a->description_en ?? '',
                $a->created_at?->format('Y-m-d') ?? '',
            ];
        }

        $csv = "\xEF\xBB\xBF";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', (string)($v ?? '')) . '"', $row)) . "\r\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="associations-' . date('Y-m-d') . '.csv"',
        ]);
    }

    public function noObjectionPdf(Request $request, Association $association)
    {
        $validated = $request->validate([
            'lessor_name'  => 'required|string|max:255',
            'lessor_phone' => 'required|string|max:30',
            'lessor_id'    => 'required|string|max:50',
            'lessee_name'  => 'required|string|max:255',
            'lessee_id'    => 'required|string|max:50',
            'unit_number'  => 'nullable|string|max:50',
        ]);

        $association->load(['property', 'property.owners.user']);

        $refNumber = 'NOC-' . $association->id . '-' . date('Ymd') . '-' . rand(100, 999);

        $docMap = [
            'No Objection Certificate'      => $association->no_objection_certificate_path,
            'Sketch'                         => $association->sketch_path,
            'Owners Association Certificate' => $association->association_certificate_path,
            'Personal ID'                    => $association->personal_id_path,
            "Association Manager's ID"       => $association->manager_id_path,
        ];

        $uploadedLabels = array_keys(array_filter($docMap));

        $prevLocale = app()->getLocale();
        app()->setLocale('ar');
        $html = view('manager.associations.no-objection-pdf', [
            'association'    => $association,
            'lessor'         => $validated,
            'refNumber'      => $refNumber,
            'documentLabels' => $uploadedLabels,
        ])->render();
        app()->setLocale($prevLocale);

        $tempDir = storage_path('app/mpdf');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Step 1 — render certificate with mPDF (handles Arabic/RTL)
        $mpdf = new Mpdf([
            'mode'         => 'utf-8',
            'format'       => 'A4',
            'orientation'  => 'P',
            'default_font' => 'dejavusans',
            'tempDir'      => $tempDir,
        ]);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);
        $certContent = $mpdf->Output('', 'S');

        // Step 2 — save cert to temp file so FPDI can import its pages
        $tempCert = $tempDir . '/noc_cert_' . time() . '_' . rand(1000, 9999) . '.pdf';
        file_put_contents($tempCert, $certContent);

        try {
            // Step 3 — use FPDI to assemble final merged PDF
            $fpdi = new \setasign\Fpdi\Fpdi('P', 'mm', 'A4');

            // Import certificate pages
            $certPageCount = $fpdi->setSourceFile($tempCert);
            for ($i = 1; $i <= $certPageCount; $i++) {
                $tpl  = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($tpl);
                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                $fpdi->AddPage($orientation, [$size['width'], $size['height']]);
                $fpdi->useTemplate($tpl);
            }

            // Append each uploaded document
            foreach ($docMap as $label => $relPath) {
                if (!$relPath) {
                    continue;
                }
                $absPath = storage_path('app/public/' . $relPath);
                if (!file_exists($absPath)) {
                    continue;
                }

                $ext = strtolower(pathinfo($absPath, PATHINFO_EXTENSION));

                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $fpdi->AddPage('P', [210, 297]);

                    // Simple English label header (FPDF built-in fonts are Latin only)
                    $fpdi->SetFont('Helvetica', 'B', 11);
                    $fpdi->SetTextColor(30, 58, 138);
                    $fpdi->SetY(7);
                    $fpdi->Cell(0, 8, $label, 0, 1, 'C');
                    $fpdi->SetDrawColor(191, 219, 254);
                    $fpdi->Line(10, 16, 200, 16);

                    // Fit image within printable area
                    [$imgW, $imgH] = getimagesize($absPath);
                    $maxW  = 190;
                    $maxH  = 263;
                    $ratio = $imgW / $imgH;
                    if (($imgW / $maxW) > ($imgH / $maxH)) {
                        $w = $maxW;
                        $h = $maxW / $ratio;
                    } else {
                        $h = $maxH;
                        $w = $maxH * $ratio;
                    }
                    $x = (210 - $w) / 2;
                    $fpdi->Image($absPath, $x, 19, $w, $h);

                } elseif ($ext === 'pdf') {
                    try {
                        $pdfPageCount = $fpdi->setSourceFile($absPath);
                        for ($i = 1; $i <= $pdfPageCount; $i++) {
                            $tpl  = $fpdi->importPage($i);
                            $size = $fpdi->getTemplateSize($tpl);
                            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                            $fpdi->AddPage($orientation, [$size['width'], $size['height']]);
                            $fpdi->useTemplate($tpl);
                        }
                    } catch (\Exception) {
                        // Skip encrypted or unreadable PDFs silently
                    }
                }
            }

            $merged = $fpdi->Output('', 'S');
        } finally {
            @unlink($tempCert);
        }

        // Save the generated PDF to local storage for re-download
        $nocDir = storage_path('app/no-objection-certs/' . $association->id);
        if (!is_dir($nocDir)) {
            mkdir($nocDir, 0755, true);
        }
        $storedFilename = $refNumber . '.pdf';
        file_put_contents($nocDir . DIRECTORY_SEPARATOR . $storedFilename, $merged);
        $filePath = 'no-objection-certs/' . $association->id . '/' . $storedFilename;

        // Persist the record
        NoObjectionCertificate::create([
            'association_id' => $association->id,
            'generated_by'   => $request->user()?->id,
            'ref_number'     => $refNumber,
            'lessor_name'    => $validated['lessor_name'],
            'lessor_phone'   => $validated['lessor_phone'],
            'lessor_id'      => $validated['lessor_id'],
            'file_path'      => $filePath,
        ]);

        $filename = 'no-objection-' . $association->id . '-' . date('Ymd') . '.pdf';

        return response($merged)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    public function downloadNoc(NoObjectionCertificate $noc, Request $request)
    {
        $abs = storage_path('app/' . $noc->file_path);
        abort_unless($noc->file_path && file_exists($abs), 404);

        $filename = $noc->ref_number . '.pdf';

        if ($request->boolean('preview')) {
            return response()->file($abs, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        }

        return response()->download($abs, $filename, ['Content-Type' => 'application/pdf']);
    }

    public function noSalePdf(Request $request, Association $association)
    {
        $validated = $request->validate([
            'seller_name' => 'required|string|max:255',
            'seller_id'   => 'required|string|max:50',
            'unit_number' => 'nullable|string|max:50',
            'buyer_name'  => 'required|string|max:255',
            'buyer_phone' => 'required|string|max:30',
            'buyer_id'    => 'required|string|max:50',
        ]);

        $association->load(['property', 'property.owners.user']);

        $refNumber = 'NOS-' . $association->id . '-' . date('Ymd') . '-' . rand(100, 999);

        $docMap = [
            'No Objection Certificate'      => $association->no_objection_certificate_path,
            'Sketch'                         => $association->sketch_path,
            'Owners Association Certificate' => $association->association_certificate_path,
            'Personal ID'                    => $association->personal_id_path,
            "Association Manager's ID"       => $association->manager_id_path,
        ];

        $uploadedLabels = array_keys(array_filter($docMap));

        $seller = [
            'seller_name' => $validated['seller_name'],
            'seller_id'   => $validated['seller_id'],
            'unit_number' => $validated['unit_number'] ?? '',
        ];

        $buyer = [
            'buyer_name'  => $validated['buyer_name'],
            'buyer_phone' => $validated['buyer_phone'],
            'buyer_id'    => $validated['buyer_id'],
        ];

        $prevLocale = app()->getLocale();
        app()->setLocale('ar');
        $html = view('manager.associations.no-objection-sale-pdf', [
            'association'    => $association,
            'seller'         => $seller,
            'buyer'          => $buyer,
            'refNumber'      => $refNumber,
            'documentLabels' => $uploadedLabels,
        ])->render();
        app()->setLocale($prevLocale);

        $tempDir = storage_path('app/mpdf');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $mpdf = new Mpdf([
            'mode'         => 'utf-8',
            'format'       => 'A4',
            'orientation'  => 'P',
            'default_font' => 'dejavusans',
            'tempDir'      => $tempDir,
        ]);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);
        $certContent = $mpdf->Output('', 'S');

        $tempCert = $tempDir . '/nos_cert_' . time() . '_' . rand(1000, 9999) . '.pdf';
        file_put_contents($tempCert, $certContent);

        try {
            $fpdi = new \setasign\Fpdi\Fpdi('P', 'mm', 'A4');

            $certPageCount = $fpdi->setSourceFile($tempCert);
            for ($i = 1; $i <= $certPageCount; $i++) {
                $tpl  = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($tpl);
                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                $fpdi->AddPage($orientation, [$size['width'], $size['height']]);
                $fpdi->useTemplate($tpl);
            }

            foreach ($docMap as $label => $relPath) {
                if (!$relPath) continue;
                $absPath = storage_path('app/public/' . $relPath);
                if (!file_exists($absPath)) continue;

                $ext = strtolower(pathinfo($absPath, PATHINFO_EXTENSION));

                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $fpdi->AddPage('P', [210, 297]);
                    $fpdi->SetFont('Helvetica', 'B', 11);
                    $fpdi->SetTextColor(15, 118, 110);
                    $fpdi->SetY(7);
                    $fpdi->Cell(0, 8, $label, 0, 1, 'C');
                    $fpdi->SetDrawColor(153, 246, 228);
                    $fpdi->Line(10, 16, 200, 16);

                    [$imgW, $imgH] = getimagesize($absPath);
                    $maxW  = 190; $maxH = 263;
                    $ratio = $imgW / $imgH;
                    if (($imgW / $maxW) > ($imgH / $maxH)) {
                        $w = $maxW; $h = $maxW / $ratio;
                    } else {
                        $h = $maxH; $w = $maxH * $ratio;
                    }
                    $fpdi->Image($absPath, (210 - $w) / 2, 19, $w, $h);

                } elseif ($ext === 'pdf') {
                    try {
                        $pdfPageCount = $fpdi->setSourceFile($absPath);
                        for ($i = 1; $i <= $pdfPageCount; $i++) {
                            $tpl  = $fpdi->importPage($i);
                            $size = $fpdi->getTemplateSize($tpl);
                            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                            $fpdi->AddPage($orientation, [$size['width'], $size['height']]);
                            $fpdi->useTemplate($tpl);
                        }
                    } catch (\Exception) {
                        // Skip unreadable PDFs
                    }
                }
            }

            $merged = $fpdi->Output('', 'S');
        } finally {
            @unlink($tempCert);
        }

        $nocDir = storage_path('app/no-objection-sale-certs/' . $association->id);
        if (!is_dir($nocDir)) {
            mkdir($nocDir, 0755, true);
        }
        $storedFilename = $refNumber . '.pdf';
        file_put_contents($nocDir . DIRECTORY_SEPARATOR . $storedFilename, $merged);
        $filePath = 'no-objection-sale-certs/' . $association->id . '/' . $storedFilename;

        NoObjectionSaleCertificate::create([
            'association_id' => $association->id,
            'generated_by'   => $request->user()?->id,
            'ref_number'     => $refNumber,
            'seller_name'    => $validated['seller_name'],
            'seller_id'      => $validated['seller_id'],
            'unit_number'    => $validated['unit_number'] ?? null,
            'buyer_name'     => $validated['buyer_name'],
            'buyer_phone'    => $validated['buyer_phone'],
            'buyer_id'       => $validated['buyer_id'],
            'file_path'      => $filePath,
        ]);

        $filename = 'no-objection-sale-' . $association->id . '-' . date('Ymd') . '.pdf';

        return response($merged)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    public function downloadNocSale(NoObjectionSaleCertificate $noc, Request $request)
    {
        $abs = storage_path('app/' . $noc->file_path);
        abort_unless($noc->file_path && file_exists($abs), 404);

        $filename = $noc->ref_number . '.pdf';

        if ($request->boolean('preview')) {
            return response()->file($abs, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        }

        return response()->download($abs, $filename, ['Content-Type' => 'application/pdf']);
    }

    public function deleteNoc(NoObjectionCertificate $noc)
    {
        $associationId = $noc->association_id;

        if ($noc->file_path) {
            $abs = storage_path('app/' . $noc->file_path);
            if (file_exists($abs)) {
                @unlink($abs);
            }
        }

        $noc->delete();

        return redirect()
            ->route('manager.associations.show', $associationId)
            ->with('success', __('Certificate deleted'));
    }

    public function deleteNocSale(NoObjectionSaleCertificate $noc)
    {
        $associationId = $noc->association_id;

        if ($noc->file_path) {
            $abs = storage_path('app/' . $noc->file_path);
            if (file_exists($abs)) {
                @unlink($abs);
            }
        }

        $noc->delete();

        return redirect()
            ->route('manager.associations.show', $associationId)
            ->with('success', __('Certificate deleted'));
    }

    private function handleDocumentUploads(Request $request, Association $association): void
    {
        $documents = [
            'no_objection_certificate' => 'no_objection_certificate_path',
            'sketch'                   => 'sketch_path',
            'association_certificate'  => 'association_certificate_path',
            'personal_id'              => 'personal_id_path',
            'manager_id'               => 'manager_id_path',
        ];

        $updates = [];
        foreach ($documents as $inputName => $column) {
            if (!$request->hasFile($inputName)) {
                continue;
            }

            $file = $request->file($inputName);

            if (!$file->isValid()) {
                continue;
            }

            $ext      = $file->getClientOriginalExtension() ?: 'pdf';
            $filename = $inputName . '_' . time() . '.' . $ext;
            $destDir  = storage_path('app/public/associations/' . $association->id);
            $destPath = $destDir . DIRECTORY_SEPARATOR . $filename;

            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            // Use copy() with raw pathname to avoid getRealPath() failing on Windows
            if (!copy($file->getPathname(), $destPath)) {
                Log::error("Association upload copy failed for {$inputName}", [
                    'src' => $file->getPathname(),
                    'dst' => $destPath,
                ]);
                continue;
            }

            // Remove old file only after new one is in place
            if (!empty($association->$column)) {
                $oldAbs = storage_path('app/public/' . $association->$column);
                if (file_exists($oldAbs)) {
                    @unlink($oldAbs);
                }
            }

            $updates[$column] = 'associations/' . $association->id . '/' . $filename;
        }

        if ($updates) {
            $association->update($updates);
        }
    }
}
