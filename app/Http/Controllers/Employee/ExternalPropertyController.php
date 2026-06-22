<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Property;
use App\Models\User;
use App\Traits\StoresUploadedFiles;
use Illuminate\Http\Request;

class ExternalPropertyController extends Controller
{
    use StoresUploadedFiles;

    public function index(Request $request)
    {
        $search        = $request->input('search');
        $typeFilter    = $request->input('type');
        $purposeFilter = $request->input('purpose');

        $properties = Property::with(['employee', 'owner.user', 'units'])
            ->where('section', 'external')
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('name',    'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('code',    'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city',    'like', "%{$search}%");
            }))
            ->when($typeFilter,    fn($q) => $q->where('type', $typeFilter))
            ->when($purposeFilter, fn($q) => $q->where('purpose', $purposeFilter))
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        return view('employee.external-properties.index', compact('properties', 'search', 'typeFilter', 'purposeFilter'));
    }

    public function create()
    {
        $employees = User::role('employee')->get();
        $owners    = Owner::with('user')->get();
        return view('employee.external-properties.create', compact('employees', 'owners'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated['section']    = 'external';
        $validated['code']       = $validated['code'] ?? $this->generateCode($validated['type']);
        $validated['created_by'] = auth()->id();
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

        $msg = app()->getLocale() === 'ar' ? 'تم إضافة العقار الخارجي بنجاح' : 'External property added successfully';
        return redirect()->route('employee.external-properties.index')->with('success', $msg);
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

        return view('employee.external-properties.show', compact('property', 'rentalContracts'));
    }

    public function edit(Property $property)
    {
        abort_if($property->section !== 'external', 404);

        $property->load('images');
        $employees = User::role('employee')->get();
        $owners    = Owner::with('user')->get();

        return view('employee.external-properties.edit', compact('property', 'employees', 'owners'));
    }

    public function update(Request $request, Property $property)
    {
        abort_if($property->section !== 'external', 404);

        $validated = $this->validated($request, $property);
        $validated['section'] = 'external';
        $validated = $this->mergeLocalizedData($validated);
        $property->update($validated);

        $this->saveImages($request, $property);

        $msg = app()->getLocale() === 'ar' ? 'تم تحديث العقار بنجاح' : 'Property updated successfully';
        return redirect()->route('employee.external-properties.show', $property)->with('success', $msg);
    }

    public function destroy(Property $property)
    {
        abort_if($property->section !== 'external', 404);
        abort_if($property->created_by !== auth()->id(), 403);
        $property->delete();
        $msg = app()->getLocale() === 'ar' ? 'تم حذف العقار بنجاح' : 'Property deleted successfully';
        return redirect()->route('employee.external-properties.index')->with('success', $msg);
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
