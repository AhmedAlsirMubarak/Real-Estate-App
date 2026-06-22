<?php

namespace App\Http\Controllers\Employee;

use App\Exports\AssociationTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\AssociationImport;
use App\Models\Association;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AssociationController extends Controller
{
    public function index()
    {
        $associations = Association::with(['property.owners', 'dues'])
            ->latest()
            ->paginate(15);

        return view('employee.associations.index', compact('associations'));
    }

    public function create()
    {
        $properties = Property::whereDoesntHave('association')->orderBy('name_ar')->get();
        return view('employee.associations.create', compact('properties'));
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
        ]), ARRAY_FILTER_USE_KEY) + ['created_by' => auth()->id()]);

        $this->handleDocumentUploads($request, $association);

        $msg = app()->getLocale() === 'ar' ? 'تم إنشاء الجمعية بنجاح' : 'Association created successfully';
        return redirect()->route('employee.associations.show', $association)->with('success', $msg);
    }

    public function show(Association $association)
    {
        $association->load([
            'property.owners',
            'property.units',
            'dues'     => fn ($q) => $q->latest('due_date')->limit(20),
            'dues.owner.user',
            'meetings' => fn ($q) => $q->latest('scheduled_at')->limit(10),
        ]);

        return view('employee.associations.show', compact('association'));
    }

    public function edit(Association $association)
    {
        return view('employee.associations.edit', compact('association'));
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

        $msg = app()->getLocale() === 'ar' ? 'تم تحديث الجمعية بنجاح' : 'Association updated successfully';
        return redirect()->route('employee.associations.show', $association)->with('success', $msg);
    }

    public function destroy(Association $association)
    {
        abort_if($association->created_by !== auth()->id(), 403);
        $association->delete();
        $msg = app()->getLocale() === 'ar' ? 'تم حذف الجمعية بنجاح' : 'Association deleted successfully';
        return redirect()->route('employee.associations.index')->with('success', $msg);
    }

    public function importForm()
    {
        return view('employee.associations.import');
    }

    public function downloadTemplate()
    {
        $spreadsheet = (new AssociationTemplateExport())->build();
        $writer      = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $temp    = storage_path('app/export_' . uniqid() . '.xlsx');
        $writer->save($temp);
        $content = file_get_contents($temp);
        @unlink($temp);

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="associations-import-template.xlsx"',
            'Content-Length'      => strlen($content),
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ], [
            'file.required' => 'Please select a file to upload.',
            'file.mimes'    => 'Only Excel files (.xlsx, .xls) are accepted.',
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
            return back()->withErrors(['file' => 'Could not read the file. Make sure it is a valid Excel file.']);
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
            if (!$request->hasFile($inputName)) continue;
            $file = $request->file($inputName);
            if (!$file->isValid()) continue;

            $ext      = $file->getClientOriginalExtension() ?: 'pdf';
            $filename = $inputName . '_' . time() . '.' . $ext;
            $destDir  = storage_path('app/public/associations/' . $association->id);
            $destPath = $destDir . DIRECTORY_SEPARATOR . $filename;

            if (!is_dir($destDir)) mkdir($destDir, 0755, true);

            if (!copy($file->getPathname(), $destPath)) {
                Log::error("Association upload copy failed for {$inputName}", [
                    'src' => $file->getPathname(),
                    'dst' => $destPath,
                ]);
                continue;
            }

            if (!empty($association->$column)) {
                $oldAbs = storage_path('app/public/' . $association->$column);
                if (file_exists($oldAbs)) @unlink($oldAbs);
            }

            $updates[$column] = 'associations/' . $association->id . '/' . $filename;
        }

        if ($updates) $association->update($updates);
    }
}
