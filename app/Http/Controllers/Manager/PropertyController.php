<?php

namespace App\Http\Controllers\Manager;

use App\Exports\PropertyExport;
use App\Exports\PropertyTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\PropertyImport;
use App\Models\Owner;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use App\Traits\StoresUploadedFiles;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
        if (in_array($property->type, ['villa', 'farm', 'chalet'])) {
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
        $request->validate(['images' => 'required', 'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048']);

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
        $employees = User::role('employee')->get();
        return view('manager.properties.show', compact('property', 'employees'));
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

        return back()->with('success', 'تم تحديث العقار بنجاح');
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
        $spreadsheet = (new PropertyTemplateExport())->build();
        $writer      = IOFactory::createWriter($spreadsheet, 'Xlsx');

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'properties-import-template.xlsx', [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="properties-import-template.xlsx"',
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

        try {
            $importer = new PropertyImport($request->file('file')->getPathname());
            $importer->run();
        } catch (\Throwable $e) {
            return back()->withErrors([
                'file' => 'Could not read the file. Make sure it is a valid Excel or CSV file.',
            ]);
        }

        return back()->with('import_results', [
            'imported'  => $importer->imported,
            'errors'    => $importer->rowErrors,
            'warnings'  => $importer->warnings,
        ]);
    }

    public function export()
    {
        $spreadsheet = (new PropertyExport())->build();
        $writer      = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename    = 'properties-' . now()->format('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
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
            'type'        => 'required|in:apartment_building,villa,farm,chalet',
            'purpose'     => 'required|in:rent,sale,both',
            'section'     => 'required|in:hoa,management,external',
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
            'status'      => 'nullable|in:active,sold,under_maintenance,archived',
            'electricity_account_number' => 'nullable|string|max:100',
            'water_account_number'       => 'nullable|string|max:100',
            'latitude'                   => 'nullable|numeric|between:-90,90',
            'longitude'                  => 'nullable|numeric|between:-180,180',
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
            default              => 'TH',
        };
        $count = Property::where('type', $type)->count() + 1;
        return sprintf('%s-%03d', $prefix, $count);
    }
}
