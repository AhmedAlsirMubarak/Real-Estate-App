<?php

namespace App\Http\Controllers\Manager;

use App\Exports\ExternalPropertyTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\ExternalPropertyImport;
use App\Models\CommissionInvoice;
use App\Models\Owner;
use App\Models\Property;
use App\Models\User;
use App\Traits\StoresUploadedFiles;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExternalPropertyController extends Controller
{
    use StoresUploadedFiles;

    public function index(Request $request)
    {
        $search        = $request->input('search');
        $typeFilter    = $request->input('type');
        $purposeFilter = $request->input('purpose');

        $properties = Property::with(['employee', 'owner.user', 'units', 'createdBy'])
            ->where('section', 'external')
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            }))
            ->when($typeFilter, fn($q) => $q->where('type', $typeFilter))
            ->when($purposeFilter, fn($q) => $q->where('purpose', $purposeFilter))
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        return view('manager.external-properties.index', compact('properties', 'search', 'typeFilter', 'purposeFilter'));
    }

    public function create()
    {
        $employees = User::role('employee')->get();
        $owners    = Owner::with('user')->get();
        return view('manager.external-properties.create', [
            'employees' => $employees,
            'owners'    => $owners,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated['section'] = 'external';
        $validated['code'] = $validated['code'] ?? $this->generateCode($validated['type']);
        $validated = $this->mergeLocalizedData($validated);

        $property = Property::create($validated);

        if (in_array($property->type, ['villa', 'farm', 'chalet', 'flat'])) {
            $unitType = match($property->type) {
                'flat'   => 'apartment',
                default  => $property->type . '_unit',
            };
            $property->units()->create([
                'type'         => $unitType,
                'area'         => $property->total_area,
                'bedrooms'     => $property->bedrooms,
                'bathrooms'    => $property->bathrooms,
                'listing_type' => $property->purpose === 'both' ? 'both' : $property->purpose,
                'rent_price'   => $request->input('rent_price'),
                'sale_price'   => $request->input('sale_price'),
                'status'       => 'available',
            ]);
        }

        $this->saveImages($request, $property);

        return redirect()->route('manager.external-properties.index')
            ->with('success', 'تم إضافة العقار الخارجي بنجاح');
    }

    public function show(Property $property)
    {
        abort_if($property->section !== 'external', 404);

        $property->load([
            'employee', 'owner.user',
            'units.activeRentalContract.tenant.user',
            'units.activeSaleContract.buyer.user',
            'units.maintenanceRequests',
            'images',
            'expenses' => fn($q) => $q->latest('expense_date')->limit(10),
        ]);

        $rentalContracts = \App\Models\RentalContract::whereHas('unit', fn($q) => $q->where('property_id', $property->id))
            ->with(['unit', 'tenant.user'])
            ->where('status', 'active')
            ->get();

        $commissionInvoices = $property->commissionInvoices;
        $employees = User::role('employee')->get();

        return view('manager.external-properties.show', [
            'property'           => $property,
            'employees'          => $employees,
            'rentalContracts'    => $rentalContracts,
            'commissionInvoices' => $commissionInvoices,
        ]);
    }

    public function edit(Property $property)
    {
        abort_if($property->section !== 'external', 404);

        $property->load('images');
        $employees = User::role('employee')->get();
        $owners    = Owner::with('user')->get();

        return view('manager.external-properties.edit', [
            'property'  => $property,
            'employees' => $employees,
            'owners'    => $owners,
        ]);
    }

    public function update(Request $request, Property $property)
    {
        abort_if($property->section !== 'external', 404);

        $validated = $this->validated($request, $property);
        $validated['section'] = 'external';
        $validated = $this->mergeLocalizedData($validated);
        $property->update($validated);

        $this->saveImages($request, $property);

        return redirect()->route('manager.external-properties.index')
            ->with('success', 'تم تحديث العقار بنجاح');
    }

    public function importForm()
    {
        return view('manager.external-properties.import');
    }

    public function downloadTemplate()
    {
        $spreadsheet = (new ExternalPropertyTemplateExport())->build();
        $writer      = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $temp    = storage_path('app/export_' . uniqid() . '.xlsx');
        $writer->save($temp);
        $content = file_get_contents($temp);
        @unlink($temp);

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="external-properties-import-template.xlsx"',
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
            $importer = new ExternalPropertyImport($tempPath);
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

    public function export(Request $request)
    {
        $search        = $request->input('search');
        $typeFilter    = $request->input('type');
        $purposeFilter = $request->input('purpose');

        $properties = Property::with(['owner.user', 'employee', 'units'])
            ->where('section', 'external')
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            }))
            ->when($typeFilter, fn($q) => $q->where('type', $typeFilter))
            ->when($purposeFilter, fn($q) => $q->where('purpose', $purposeFilter))
            ->latest()
            ->get();

        $rows = [['ID', 'Code', 'Name (AR)', 'Name (EN)', 'Type', 'Purpose',
                   'Address (AR)', 'City', 'Floors', 'Area (m²)', 'Beds', 'Baths',
                   'Status', 'Owner', 'Rent Commission %', 'Sale Commission %',
                   'Commission Payer', 'Units Count', 'Created At']];

        foreach ($properties as $p) {
            $rows[] = [
                $p->id, $p->code, $p->name_ar ?? '', $p->name_en ?? '',
                $p->type, $p->purpose,
                $p->address_ar ?? '', $p->city_ar ?? $p->city ?? '',
                $p->floors ?? '', $p->total_area ?? '', $p->bedrooms ?? '', $p->bathrooms ?? '',
                $p->status ?? 'active',
                $p->owner?->user?->name ?? '',
                $p->rent_commission_rate ?? '', $p->sale_commission_rate ?? '',
                $p->commission_payer ?? '',
                $p->units->count(),
                $p->created_at?->format('Y-m-d') ?? '',
            ];
        }

        $csv = "\xEF\xBB\xBF";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', (string)($v ?? '')) . '"', $row)) . "\r\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="external-properties-' . date('Y-m-d') . '.csv"',
        ]);
    }

    public function commissions(Request $request)
    {
        $search        = $request->input('search');
        $invoiceFor    = $request->input('invoice_for');
        $from          = $request->input('from');
        $to            = $request->input('to');

        $externalPropertyIds = Property::where('section', 'external')->pluck('id');

        $baseQuery = fn() => CommissionInvoice::with('property')
            ->whereIn('property_id', $externalPropertyIds)
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhereHas('property', fn($q) => $q->where('name', 'like', "%{$search}%"));
            }))
            ->when($invoiceFor, fn($q) => $q->where('invoice_for', $invoiceFor))
            ->when($from, fn($q) => $q->whereDate('invoice_date', '>=', $from))
            ->when($to,   fn($q) => $q->whereDate('invoice_date', '<=', $to));

        $invoices = $baseQuery()
            ->orderByDesc('invoice_date')
            ->paginate(20)
            ->appends($request->query());

        $totalCommissions = $baseQuery()->sum('commission_amount');
        $ownerCount       = $baseQuery()->where('invoice_for', 'owner')->count();
        $clientCount      = $baseQuery()->where('invoice_for', 'client')->count();

        return view('manager.external-properties.commissions', compact(
            'invoices', 'search', 'invoiceFor', 'from', 'to', 'totalCommissions', 'ownerCount', 'clientCount'
        ));
    }

    public function destroy(Property $property)
    {
        abort_if($property->section !== 'external', 404);
        $property->delete();
        return redirect()->route('manager.external-properties.index')
            ->with('success', 'تم حذف العقار بنجاح');
    }

    private function saveImages(Request $request, Property $property): void
    {
        if (!$request->hasFile('images')) return;
        $request->validate(['images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048']);
        $existingCount = $property->images()->count();
        $isFirst = $existingCount === 0;
        foreach ($request->file('images') as $i => $file) {
            $path = $this->storeUploadedFile($file, 'properties/' . $property->id);
            if (!$path) continue;
            $property->images()->create([
                'path'       => $path,
                'is_primary' => $isFirst && $i === 0,
                'sort_order' => $existingCount + $i,
            ]);
        }
    }

    private function validated(Request $request, ?Property $property = null): array
    {
        return $request->validate([
            'code'        => 'nullable|string|max:50|unique:properties,code,' . ($property?->id ?? 'NULL'),
            'name_ar'     => 'required|string|max:255',
            'name_en'     => 'nullable|string|max:255',
            'type'        => 'required|in:apartment_building,villa,farm,chalet,flat,land',
            'purpose'     => 'required|in:rent,sale,both,exclusive_rent,exclusive_sale',
            'address_ar'  => 'required|string|max:500',
            'address_en'  => 'nullable|string|max:500',
            'city_ar'     => 'nullable|string|max:100',
            'city_en'     => 'nullable|string|max:100',
            'description_ar'           => 'nullable|string',
            'description_en'           => 'nullable|string',
            'owner_id'                 => 'nullable|exists:owners,id',
            'employee_id'              => 'nullable|exists:users,id',
            'referral_employee_id'     => 'nullable|exists:users,id',
            'referral_commission_rate' => 'nullable|numeric|min:0|max:100',
            'floors'      => 'nullable|integer|min:1',
            'total_area'  => 'nullable|numeric|min:0',
            'bedrooms'    => 'nullable|integer|min:0',
            'bathrooms'   => 'nullable|integer|min:0',
            'status'      => 'nullable|in:active,sold,rented,under_maintenance,archived',
            'electricity_account_number' => 'nullable|string|max:100',
            'water_account_number'       => 'nullable|string|max:100',
            'latitude'                   => 'nullable|numeric|between:-90,90',
            'longitude'                  => 'nullable|numeric|between:-180,180',
            'rent_commission_rate'       => 'nullable|numeric|min:0|max:100',
            'sale_commission_rate'       => 'nullable|numeric|min:0|max:100',
            'commission_payer'           => 'nullable|in:owner,tenant,buyer,shared',
            'commission_notes'           => 'nullable|string',
        ]);
    }

    private function mergeLocalizedData(array $validated): array
    {
        $validated['name']        = $validated['name_ar'];
        $validated['address']     = $validated['address_ar'];
        $validated['city']        = $validated['city_ar'] ?? null;
        $validated['description'] = $validated['description_ar'] ?? null;
        return $validated;
    }

    private function generateCode(string $type): string
    {
        $prefix = match ($type) {
            'apartment_building' => 'EX-B',
            'villa'              => 'EX-V',
            'farm'               => 'EX-F',
            'chalet'             => 'EX-C',
            'flat'               => 'EX-FL',
            'land'               => 'EX-L',
            default              => 'EX',
        };
        do {
            $last = Property::where('code', 'like', $prefix . '-%')
                ->orderByRaw('CAST(SUBSTRING_INDEX(code, \'-\', -1) AS UNSIGNED) DESC')
                ->value('code');
            $next = $last ? ((int) preg_replace('/.*-(\d+)$/', '$1', $last)) + 1 : 1;
            $code = sprintf('%s-%03d', $prefix, $next);
        } while (Property::where('code', $code)->exists());
        return $code;
    }
}
