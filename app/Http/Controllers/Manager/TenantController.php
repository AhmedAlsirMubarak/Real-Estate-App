<?php

namespace App\Http\Controllers\Manager;

use App\Exports\TenantExport;
use App\Exports\TenantTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\TenantImport;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Unit;
use App\Models\RentalContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tenants = Tenant::with(['user', 'activeContract.unit.property', 'rentalContracts.unit.property'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");

                    if ($this->supportsBilingualNames()) {
                        $userQuery->orWhere('name_ar', 'like', "%{$search}%")
                            ->orWhere('name_en', 'like', "%{$search}%");
                    }
                });
            })
            ->latest()
            ->paginate(15)
            ->appends($request->query());
        return view('manager.tenants.index', compact('tenants', 'search'));
    }

    public function create()
    {
        $availableUnits = Unit::where('status', 'available')
            ->whereIn('listing_type', ['rent', 'both'])
            ->with('property')
            ->get();
        return view('manager.tenants.create', compact('availableUnits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'national_id' => 'nullable|string|max:50',
            'emergency_contact' => 'nullable|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'electricity_account_number' => 'nullable|string|max:100',
            'water_account_number'       => 'nullable|string|max:100',
        ]);

        $user = User::create(array_merge($this->buildUserNamePayload($validated), [
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]));
        $user->assignRole('tenant');

        $tenant = Tenant::create([
            'user_id' => $user->id,
            'national_id' => $validated['national_id'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'emergency_contact' => $validated['emergency_contact'] ?? null,
        ]);

        RentalContract::create([
            'unit_id'                    => $validated['unit_id'],
            'tenant_id'                  => $tenant->id,
            'start_date'                 => $validated['start_date'],
            'end_date'                   => $validated['end_date'],
            'monthly_rent'               => $validated['monthly_rent'],
            'deposit'                    => $validated['deposit'] ?? 0,
            'status'                     => 'active',
            'electricity_account_number' => $validated['electricity_account_number'] ?? null,
            'water_account_number'       => $validated['water_account_number'] ?? null,
        ]);

        Unit::where('id', $validated['unit_id'])->update(['status' => 'rented']);

        return redirect()->route('manager.tenants.index')
            ->with('success', app()->getLocale() === 'en' ? 'Tenant created successfully.' : 'تم إضافة المستأجر بنجاح');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['user', 'activeContract.unit.property', 'rentalContracts.unit.property', 'maintenanceRequests.unit', 'payments']);
        return view('manager.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        $tenant->load(['user', 'activeContract.unit.property']);

        $availableUnits = Unit::where('status', 'available')
            ->whereIn('listing_type', ['rent', 'both'])
            ->with('property')
            ->get();

        // Always include the tenant's current unit even if status is rented
        if ($tenant->activeContract?->unit) {
            $currentUnit = $tenant->activeContract->unit->load('property');
            if (!$availableUnits->contains('id', $currentUnit->id)) {
                $availableUnits = $availableUnits->push($currentUnit);
            }
        }

        return view('manager.tenants.edit', compact('tenant', 'availableUnits'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name_ar'           => 'required|string|max:255',
            'name_en'           => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email,' . $tenant->user_id,
            'phone'             => 'nullable|string|max:20',
            'national_id'       => 'nullable|string|max:50',
            'emergency_contact' => 'nullable|string|max:255',
            'password'          => 'nullable|string|min:8',
            'unit_id'           => 'nullable|exists:units,id',
            'start_date'        => 'nullable|required_with:unit_id|date',
            'end_date'          => 'nullable|required_with:unit_id|date|after:start_date',
            'monthly_rent'      => 'nullable|required_with:unit_id|numeric|min:0',
            'deposit'           => 'nullable|numeric|min:0',
            'electricity_account_number' => 'nullable|string|max:100',
            'water_account_number'       => 'nullable|string|max:100',
        ]);

        $userUpdate = array_merge($this->buildUserNamePayload($validated), [
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);
        if (!empty($validated['password'])) {
            $userUpdate['password'] = Hash::make($validated['password']);
        }
        $tenant->user->update($userUpdate);

        $tenant->update([
            'national_id'       => $validated['national_id'] ?? null,
            'phone'             => $validated['phone'] ?? null,
            'emergency_contact' => $validated['emergency_contact'] ?? null,
        ]);

        if (!empty($validated['unit_id'])) {
            $contract = $tenant->activeContract;
            if ($contract) {
                if ($contract->unit_id != $validated['unit_id']) {
                    Unit::where('id', $contract->unit_id)->update(['status' => 'available']);
                    Unit::where('id', $validated['unit_id'])->update(['status' => 'rented']);
                }
                $contract->update([
                    'unit_id'                    => $validated['unit_id'],
                    'start_date'                 => $validated['start_date'],
                    'end_date'                   => $validated['end_date'],
                    'monthly_rent'               => $validated['monthly_rent'],
                    'deposit'                    => $validated['deposit'] ?? $contract->deposit,
                    'electricity_account_number' => $validated['electricity_account_number'] ?? $contract->electricity_account_number,
                    'water_account_number'       => $validated['water_account_number'] ?? $contract->water_account_number,
                ]);
            } else {
                RentalContract::create([
                    'unit_id'                    => $validated['unit_id'],
                    'tenant_id'                  => $tenant->id,
                    'start_date'                 => $validated['start_date'],
                    'end_date'                   => $validated['end_date'],
                    'monthly_rent'               => $validated['monthly_rent'],
                    'deposit'                    => $validated['deposit'] ?? 0,
                    'status'                     => 'active',
                    'electricity_account_number' => $validated['electricity_account_number'] ?? null,
                    'water_account_number'       => $validated['water_account_number'] ?? null,
                ]);
                Unit::where('id', $validated['unit_id'])->update(['status' => 'rented']);
            }
        }

        return redirect()->route('manager.tenants.show', $tenant)
            ->with('success', app()->getLocale() === 'en' ? 'Tenant updated successfully.' : 'تم تحديث بيانات المستأجر');
    }

    public function importForm()
    {
        return view('manager.tenants.import');
    }

    public function downloadTemplate()
    {
        $spreadsheet = (new TenantTemplateExport())->build();
        $writer      = IOFactory::createWriter($spreadsheet, 'Xlsx');

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'tenants-import-template.xlsx', [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="tenants-import-template.xlsx"',
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
            $importer = new TenantImport($request->file('file')->getPathname());
            $importer->run();
        } catch (\Throwable $e) {
            return back()->withErrors(['file' => 'Could not read the file. Make sure it is a valid Excel file.']);
        }

        return back()->with('import_results', [
            'imported' => $importer->imported,
            'errors'   => $importer->rowErrors,
            'warnings' => $importer->warnings,
        ]);
    }

    public function export()
    {
        $spreadsheet = (new TenantExport())->build();
        $writer      = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename    = 'tenants-' . now()->format('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->user->delete();
        return redirect()->route('manager.tenants.index')
            ->with('success', 'تم حذف المستأجر بنجاح');
    }

    private function supportsBilingualNames(): bool
    {
        static $supports = null;

        if ($supports !== null) {
            return $supports;
        }

        $supports = Schema::hasColumn('users', 'name_ar') && Schema::hasColumn('users', 'name_en');

        return $supports;
    }

    private function buildUserNamePayload(array $validated): array
    {
        $payload = [
            'name' => $validated['name_ar'],
        ];

        if ($this->supportsBilingualNames()) {
            $payload['name_ar'] = $validated['name_ar'];
            $payload['name_en'] = $validated['name_en'];
        }

        return $payload;
    }
}
