<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Imports\PropertyImport;
use App\Models\CommissionInvoice;
use App\Models\Owner;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use App\Traits\StoresUploadedFiles;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class PropertyController extends Controller
{
    use StoresUploadedFiles;
    public function index(Request $request)
    {
        $search       = $request->input('search');
        $typeFilter   = $request->input('type');
        $purposeFilter = $request->input('purpose');
        $section      = $request->input('section');

        $properties = Property::with(['employee', 'owner.user', 'units'])
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('address_ar', 'like', "%{$search}%")
                  ->orWhere('address_en', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('city_ar', 'like', "%{$search}%")
                  ->orWhere('city_en', 'like', "%{$search}%");
            }))
            ->when($typeFilter, fn($q) => $q->where('type', $typeFilter))
            ->when($purposeFilter, fn($q) => $q->where('purpose', $purposeFilter))
            ->when($section, fn($q) => $q->where('section', $section))
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view('manager.properties.index', compact('properties', 'search', 'section', 'typeFilter', 'purposeFilter'));
    }

    public function create()
    {
        $employees = User::role('employee')->get();
        $owners    = Owner::with('user')->get();
        return view('manager.properties.create', compact('employees', 'owners'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated['code'] = $validated['code'] ?? $this->generateCode($validated['type']);
        $validated = $this->mergeLocalizedPropertyData($validated);

        $property = Property::create($validated);

        // For non-apartment types, auto-create a single unit
        if (in_array($property->type, ['villa', 'farm', 'chalet', 'flat', 'land'])) {
            $property->units()->create([
                'type'         => $property->type . '_unit',
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

        return redirect()->route('manager.properties.edit', $property)
            ->with('success', 'تم إضافة العقار بنجاح');
    }

    public function storeImage(Request $request, Property $property)
    {
        $request->validate(['images' => 'required', 'images.*' => 'image|max:10240']);

        $existingCount = $property->images()->count();
        $maxImages = 7;
        if ($existingCount >= $maxImages) {
            return back()->withErrors(['images' => 'الحد الأقصى للصور هو ' . $maxImages . ' صور لكل عقار. / Maximum ' . $maxImages . ' images allowed per property.']);
        }

        $isFirst = $existingCount === 0;
        $remaining = $maxImages - $existingCount;
        $saved = 0;
        $errors = [];
        foreach (array_slice($request->file('images'), 0, $remaining) as $i => $file) {
            $path = $this->storeUploadedFile($file, 'properties/' . $property->id);
            if (!$path) {
                $errors[] = $file->getClientOriginalName() . ': failed to save file';
                continue;
            }
            try {
                $property->images()->create([
                    'path'       => $path,
                    'is_primary' => $isFirst && $i === 0,
                    'sort_order' => $existingCount + $i,
                ]);
                $saved++;
            } catch (\Throwable $e) {
                \Log::error('PropertyImage create failed: ' . $e->getMessage(), ['path' => $path, 'property_id' => $property->id]);
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }

        if ($errors) {
            return back()->with('success', $saved . ' image(s) saved.')->withErrors(['images' => implode(' | ', $errors)]);
        }

        return back()->with('success', 'تم رفع الصور بنجاح');
    }

    public function destroyImage(Property $property, PropertyImage $image)
    {
        $file = public_path('storage' . DIRECTORY_SEPARATOR . $image->path);
        if (file_exists($file)) {
            @unlink($file);
        }
        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $next = $property->images()->first();
            $next?->update(['is_primary' => true]);
        }

        return back()->with('success', 'تم حذف الصورة');
    }

    public function setPrimaryImage(Property $property, PropertyImage $image)
    {
        $property->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'تم تعيين الصورة الرئيسية');
    }

    public function show(Property $property)
    {
        $property->load([
            'employee',
            'owner.user',
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
        return view('manager.properties.show', compact('property', 'employees', 'rentalContracts', 'commissionInvoices'));
    }

    public function edit(Property $property)
    {
        $property->load('images');
        $employees = User::role('employee')->get();
        $owners    = Owner::with('user')->get();
        return view('manager.properties.edit', compact('property', 'employees', 'owners'));
    }

    public function update(Request $request, Property $property)
    {
        $validated = $this->validated($request, $property);
        $validated = $this->mergeLocalizedPropertyData($validated);
        $property->update($validated);

        $this->saveImages($request, $property);

        return redirect()->route('manager.properties.index')
            ->with('success', 'تم تحديث العقار بنجاح');
    }

    private function saveImages(Request $request, Property $property): void
    {
        if (!$request->hasFile('images')) {
            return;
        }

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

    public function importForm()
    {
        return view('manager.properties.import');
    }

    public function downloadTemplate()
    {
        $q = fn($v) => '"' . str_replace('"', '""', (string)($v ?? '')) . '"';

        $rows = [
            ['name_ar', 'name_en', 'type', 'purpose', 'section', 'city_ar', 'city_en',
             'address_ar', 'address_en', 'floors', 'total_area', 'bedrooms', 'bathrooms',
             'status', 'description_ar', 'description_en'],
            ['برج النخيل', 'Palm Tower', 'apartment_building', 'rent', 'management',
             'الرياض', 'Riyadh', 'شارع الملك فهد', 'King Fahd Road',
             '10', '5000', '', '', 'active', 'برج سكني حديث', 'Modern residential tower'],
        ];

        $csv = "\xEF\xBB\xBF";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map($q, $row)) . "\r\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="properties-import-template.csv"',
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
            $importer = new PropertyImport($tempPath);
            $importer->run();
        } catch (\Throwable $e) {
            return back()->withErrors([
                'file' => 'Could not read the file. Make sure it is a valid Excel or CSV file.',
            ]);
        } finally {
            if (file_exists($tempPath)) @unlink($tempPath);
        }

        return back()->with('import_results', [
            'imported'  => $importer->imported,
            'errors'    => $importer->rowErrors,
            'warnings'  => $importer->warnings,
        ]);
    }

    public function export()
    {
        $properties = Property::with(['owner.user', 'employee', 'units'])->get();

        $rows = [['ID', 'Code', 'Arabic Name', 'English Name', 'Type', 'Purpose', 'Section',
                   'Address', 'City', 'Owner', 'Commission %', 'Employee',
                   'Floors', 'Total Area (m²)', 'Bedrooms', 'Bathrooms',
                   'Status', 'Total Units', 'Available', 'Rented', 'Created At']];

        foreach ($properties as $p) {
            $units = $p->units;
            $rows[] = [
                $p->id, $p->code ?? '',
                $p->name_ar ?? $p->name ?? '', $p->name_en ?? $p->name ?? '',
                $p->type ?? '', $p->purpose ?? '', $p->section ?? '',
                $p->address_en ?? $p->address ?? '', $p->city_en ?? $p->city ?? '',
                $p->owner?->user?->name ?? 'Company', $p->owner?->commission_rate ?? '',
                $p->employee?->name ?? '',
                $p->floors ?? '', $p->total_area ?? '', $p->bedrooms ?? '', $p->bathrooms ?? '',
                ucfirst($p->status ?? ''),
                $units->count(),
                $units->where('status', 'available')->count(),
                $units->where('status', 'rented')->count(),
                $p->created_at?->format('Y-m-d') ?? '',
            ];
        }

        $csv = "\xEF\xBB\xBF";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', (string)($v ?? '')) . '"', $row)) . "\r\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="properties-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    public function commissionInvoice(Request $request, Property $property)
    {
        $data = $request->validate([
            'invoice_for'     => 'required|in:owner,client',
            'recipient_name'  => 'required|string|max:255',
            'duration_months' => 'required|numeric|min:1',
            'monthly_rent'    => 'required|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'invoice_date'    => 'nullable|date',
            'notes'           => 'nullable|string',
        ]);

        $totalRent        = $data['duration_months'] * $data['monthly_rent'];
        $commissionAmount = $totalRent * $data['commission_rate'] / 100;
        $invoiceDate      = $data['invoice_date'] ?? now()->toDateString();
        $invoiceNumber    = 'COM-' . $property->id . '-' . now()->format('YmdHis');

        $html = view('manager.properties.commission-invoice-pdf', [
            'property'         => $property,
            'invoiceFor'       => $data['invoice_for'],
            'recipientName'    => $data['recipient_name'],
            'durationMonths'   => $data['duration_months'],
            'monthlyRent'      => $data['monthly_rent'],
            'commissionRate'   => $data['commission_rate'],
            'totalRent'        => $totalRent,
            'commissionAmount' => $commissionAmount,
            'invoiceDate'      => $invoiceDate,
            'invoiceNumber'    => $invoiceNumber,
            'notes'            => $data['notes'] ?? null,
        ])->render();

        $tempDir = storage_path('app/mpdf');
        if (!is_dir($tempDir)) mkdir($tempDir, 0755, true);

        $mpdf = new Mpdf([
            'mode'         => 'utf-8',
            'format'       => 'A4',
            'orientation'  => 'P',
            'default_font' => 'dejavusans',
            'tempDir'      => $tempDir,
        ]);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);
        $pdfContent = $mpdf->Output('', 'S');

        // Save PDF to disk
        $filename   = $invoiceNumber . '.pdf';
        $storageDir = storage_path('app/public/commission-invoices');
        if (!is_dir($storageDir)) mkdir($storageDir, 0755, true);
        file_put_contents($storageDir . DIRECTORY_SEPARATOR . $filename, $pdfContent);

        // Save record
        CommissionInvoice::create([
            'property_id'      => $property->id,
            'invoice_number'   => $invoiceNumber,
            'invoice_for'      => $data['invoice_for'],
            'recipient_name'   => $data['recipient_name'],
            'duration_months'  => $data['duration_months'],
            'monthly_rent'     => $data['monthly_rent'],
            'commission_rate'  => $data['commission_rate'],
            'total_rent'       => $totalRent,
            'commission_amount'=> $commissionAmount,
            'invoice_date'     => $invoiceDate,
            'notes'            => $data['notes'] ?? null,
            'file_path'        => 'commission-invoices/' . $filename,
        ]);

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    public function downloadCommissionInvoice(Request $request, Property $property, CommissionInvoice $invoice)
    {
        $path = storage_path('app/public/' . $invoice->file_path);
        if (!file_exists($path)) {
            abort(404, 'Invoice file not found.');
        }
        $filename = $invoice->invoice_number . '.pdf';
        if ($request->boolean('download')) {
            return response()->download($path, $filename);
        }
        return response()->file($path, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function destroyCommissionInvoice(Property $property, CommissionInvoice $invoice)
    {
        $path = storage_path('app/public/' . $invoice->file_path);
        if (file_exists($path)) @unlink($path);
        $invoice->delete();

        return redirect()->route('manager.properties.show', $property)
            ->with('success', 'تم حذف الفاتورة');
    }

    public function managementCommissions(Request $request)
    {
        $search        = $request->input('search');
        $invoiceFor    = $request->input('invoice_for');
        $from          = $request->input('from');
        $to            = $request->input('to');

        $managementPropertyIds = Property::where('section', 'management')->pluck('id');

        $baseQuery = fn() => CommissionInvoice::with('property')
            ->whereIn('property_id', $managementPropertyIds)
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhereHas('property', fn($q) => $q->where('name', 'like', "%{$search}%"));
            }))
            ->when($invoiceFor, fn($q) => $q->where('invoice_for', $invoiceFor))
            ->when($from, fn($q) => $q->whereDate('invoice_date', '>=', $from))
            ->when($to,   fn($q) => $q->whereDate('invoice_date', '<=', $to));

        $invoices         = $baseQuery()->orderByDesc('invoice_date')->paginate(20)->appends($request->query());
        $totalCommissions = $baseQuery()->sum('commission_amount');
        $ownerCount       = $baseQuery()->where('invoice_for', 'owner')->count();
        $clientCount      = $baseQuery()->where('invoice_for', 'client')->count();

        return view('manager.properties.commissions', compact(
            'invoices', 'search', 'invoiceFor', 'from', 'to', 'totalCommissions', 'ownerCount', 'clientCount'
        ));
    }

    public function destroy(Property $property)
    {
        $property->delete();
        return redirect()->route('manager.properties.index')
            ->with('success', 'تم حذف العقار بنجاح');
    }

    public function transfer(Request $request, Property $property)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|exists:users,id',
        ]);

        $property->update(['employee_id' => $validated['employee_id']]);

        return redirect()->route('manager.properties.show', $property)
            ->with('success', 'تم تحويل العقار بنجاح');
    }

    private function validated(Request $request, ?Property $property = null): array
    {
        return $request->validate([
            'code'        => 'nullable|string|max:50|unique:properties,code,' . ($property?->id ?? 'NULL'),
            'name_ar'     => 'required|string|max:255',
            'name_en'     => 'nullable|string|max:255',
            'type'        => 'required|in:apartment_building,villa,farm,chalet,flat,land',
            'purpose'     => 'required|in:rent,sale,both,exclusive_rent,exclusive_sale',
            'section'     => 'required|in:hoa,management',
            'address_ar'  => 'required|string|max:500',
            'address_en'  => 'nullable|string|max:500',
            'city_ar'     => 'nullable|string|max:100',
            'city_en'     => 'nullable|string|max:100',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
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

    private function mergeLocalizedPropertyData(array $validated): array
    {
        $validated['name'] = $validated['name_ar'];
        $validated['address'] = $validated['address_ar'];
        $validated['city'] = $validated['city_ar'] ?? null;
        $validated['description'] = $validated['description_ar'] ?? null;

        return $validated;
    }

    private function generateCode(string $type): string
    {
        $prefix = match ($type) {
            'apartment_building' => 'TH-B',
            'villa'              => 'TH-V',
            'farm'               => 'TH-F',
            'chalet'             => 'TH-C',
            'flat'               => 'TH-FL',
            'land'               => 'TH-L',
            default              => 'TH',
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
