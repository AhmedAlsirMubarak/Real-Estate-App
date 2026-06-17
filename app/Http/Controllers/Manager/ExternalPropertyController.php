<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\CommissionInvoice;
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
        $count = Property::where('type', $type)->where('section', 'external')->count() + 1;
        return sprintf('%s-%03d', $prefix, $count);
    }
}
