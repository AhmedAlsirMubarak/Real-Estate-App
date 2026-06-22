<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Imports\TenantImport;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Unit;
use App\Models\RentalContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Mpdf\Mpdf;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tenants = Tenant::with(['user', 'referralEmployee', 'latestPayment', 'activeContract.unit.property', 'rentalContracts.unit.property'])
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
        $employees = User::role('employee')->get();
        return view('manager.tenants.create', compact('availableUnits', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
            'national_id' => 'nullable|string|max:50',
            'emergency_contact' => 'nullable|string|max:255',
            'referral_employee_id'     => 'nullable|exists:users,id',
            'referral_commission_rate' => 'nullable|numeric|min:0|max:100',
            'unit_id' => 'required|exists:units,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'electricity_account_number' => 'nullable|string|max:100',
            'water_account_number'       => 'nullable|string|max:100',
        ]);

        $email    = $validated['email'] ?? ('tenant_' . uniqid() . '@noemail.local');
        $password = Hash::make($validated['password'] ?? Str::random(16));

        $user = User::create(array_merge($this->buildUserNamePayload($validated), [
            'email'    => $email,
            'phone'    => $validated['phone'] ?? null,
            'password' => $password,
        ]));
        $user->assignRole('tenant');

        $tenant = Tenant::create([
            'user_id'                  => $user->id,
            'national_id'              => $validated['national_id'] ?? null,
            'phone'                    => $validated['phone'] ?? null,
            'emergency_contact'        => $validated['emergency_contact'] ?? null,
            'referral_employee_id'     => $validated['referral_employee_id'] ?? null,
            'referral_commission_rate' => $validated['referral_commission_rate'] ?? null,
        ]);

        $contract = RentalContract::create([
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

        $startDate = \Carbon\Carbon::parse($validated['start_date']);

        Payment::create([
            'rental_contract_id' => $contract->id,
            'tenant_id'          => $tenant->id,
            'type'               => 'rent',
            'amount'             => $validated['monthly_rent'],
            'month'              => $startDate->month,
            'year'               => $startDate->year,
            'status'             => 'pending',
        ]);

        if (!empty($validated['deposit']) && $validated['deposit'] > 0) {
            Payment::create([
                'rental_contract_id' => $contract->id,
                'tenant_id'          => $tenant->id,
                'type'               => 'deposit',
                'amount'             => $validated['deposit'],
                'month'              => $startDate->month,
                'year'               => $startDate->year,
                'status'             => 'pending',
            ]);
        }

        Unit::where('id', $validated['unit_id'])->update(['status' => 'rented']);

        return redirect()->route('manager.tenants.index')
            ->with('success', app()->getLocale() === 'en' ? 'Tenant created successfully.' : 'تم إضافة المستأجر بنجاح');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['user', 'activeContract.unit.property', 'rentalContracts.unit.property', 'maintenanceRequests.unit', 'payments']);
        return view('manager.tenants.show', compact('tenant'));
    }

    public function generatePayment(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'month'  => 'required|integer|min:1|max:12',
            'year'   => 'required|integer|min:2000|max:2100',
            'amount' => 'required|numeric|min:0',
        ]);

        $contract = $tenant->activeContract ?? $tenant->rentalContracts()->latest()->first();

        if (!$contract) {
            return redirect()->route('manager.tenants.show', $tenant)
                ->withErrors(['payment' => 'لا يوجد عقد لهذا المستأجر، يرجى إضافة عقد أولاً.']);
        }

        try {
            Payment::create([
                'rental_contract_id' => $contract->id,
                'tenant_id'          => $tenant->id,
                'type'               => 'rent',
                'amount'             => $validated['amount'],
                'month'              => $validated['month'],
                'year'               => $validated['year'],
                'status'             => 'pending',
            ]);
        } catch (\Throwable $e) {
            return redirect()->route('manager.tenants.show', $tenant)
                ->withErrors(['payment' => 'يوجد فاتورة إيجار لهذا الشهر مسبقاً.']);
        }

        return redirect()->route('manager.tenants.show', $tenant)
            ->with('success', 'تم توليد الفاتورة بنجاح');
    }

    public function destroyPayment(Tenant $tenant, Payment $payment)
    {
        if ((int) $payment->tenant_id !== (int) $tenant->id) {
            return redirect()->route('manager.tenants.show', $tenant)
                ->withErrors(['payment' => 'لا يمكن حذف هذه الفاتورة.']);
        }
        $payment->delete();
        return redirect()->route('manager.tenants.show', $tenant)
            ->with('success', 'تم حذف الفاتورة بنجاح');
    }

    public function markPaymentPaid(Request $request, Tenant $tenant, Payment $payment)
    {
        if ((int) $payment->tenant_id !== (int) $tenant->id) {
            return redirect()->route('manager.tenants.show', $tenant)
                ->withErrors(['payment' => 'لا يمكن تعديل هذه الفاتورة.']);
        }

        $validated = $request->validate([
            'paid_at' => 'nullable|date',
        ]);

        $payment->update([
            'status'  => 'paid',
            'paid_at' => $validated['paid_at'] ?? now(),
        ]);

        return redirect()->route('manager.tenants.show', $tenant)
            ->with('success', 'تم تسجيل الدفع بنجاح');
    }

    public function paymentInvoice(Tenant $tenant, Payment $payment)
    {
        abort_if((int) $payment->tenant_id !== (int) $tenant->id, 403);

        $payment->load(['rentalContract.unit.property', 'tenant.user']);

        $isAr = app()->getLocale() === 'ar';
        $html = view('manager.tenants.payment-invoice', compact('tenant', 'payment', 'isAr'))->render();

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

        if ($isAr) {
            $mpdf->SetDirectionality('rtl');
        }

        $mpdf->WriteHTML($html);

        $filename = 'rent-invoice-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
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

        $employees = User::role('employee')->get();
        return view('manager.tenants.edit', compact('tenant', 'availableUnits', 'employees'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name_ar'           => 'required|string|max:255',
            'name_en'           => 'required|string|max:255',
            'email'             => 'nullable|email|unique:users,email,' . $tenant->user_id,
            'phone'             => 'nullable|string|max:20',
            'national_id'              => 'nullable|string|max:50',
            'emergency_contact'        => 'nullable|string|max:255',
            'referral_employee_id'     => 'nullable|exists:users,id',
            'referral_commission_rate' => 'nullable|numeric|min:0|max:100',
            'password'                 => 'nullable|string|min:8',
            'unit_id'           => 'nullable|exists:units,id',
            'start_date'        => 'nullable|required_with:unit_id|date',
            'end_date'          => 'nullable|required_with:unit_id|date|after:start_date',
            'monthly_rent'      => 'nullable|required_with:unit_id|numeric|min:0',
            'deposit'           => 'nullable|numeric|min:0',
            'electricity_account_number' => 'nullable|string|max:100',
            'water_account_number'       => 'nullable|string|max:100',
        ]);

        $userUpdate = array_merge($this->buildUserNamePayload($validated), [
            'email' => $validated['email'] ?? $tenant->user->email,
            'phone' => $validated['phone'] ?? null,
        ]);
        if (!empty($validated['password'])) {
            $userUpdate['password'] = Hash::make($validated['password']);
        }
        $tenant->user->update($userUpdate);

        $tenant->update([
            'national_id'              => $validated['national_id'] ?? null,
            'phone'                    => $validated['phone'] ?? null,
            'emergency_contact'        => $validated['emergency_contact'] ?? null,
            'referral_employee_id'     => $validated['referral_employee_id'] ?? null,
            'referral_commission_rate' => $validated['referral_commission_rate'] ?? null,
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
        $q = fn($v) => '"' . str_replace('"', '""', (string)($v ?? '')) . '"';

        $rows = [
            ['name_ar', 'name_en', 'email', 'phone', 'password', 'national_id', 'emergency_contact',
             'property_code', 'unit_number', 'start_date', 'end_date', 'monthly_rent', 'deposit'],
            ['أحمد العمري', 'Ahmed Al-Omari', 'ahmed@example.com', '0501234567', '',
             '1234567890', 'Mohammed 0507654321', 'TH-B-001', '101',
             '2026-01-01', '2026-12-31', '2500', '5000'],
        ];

        $csv = "\xEF\xBB\xBF";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map($q, $row)) . "\r\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="tenants-import-template.csv"',
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
            $importer = new TenantImport($tempPath);
            $importer->run();
        } catch (\Throwable $e) {
            return back()->withErrors(['file' => 'Could not read the file. Make sure it is a valid Excel or CSV file.']);
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
        $tenants = Tenant::with(['user', 'rentalContracts.unit.property'])->get();

        $rows = [['ID', 'Arabic Name', 'English Name', 'Email', 'Phone',
                   'National ID', 'Emergency Contact',
                   'Property Code', 'Property Name', 'Unit Number',
                   'Contract Status', 'Contract Start', 'Contract End',
                   'Monthly Rent', 'Deposit', 'Created At']];

        foreach ($tenants as $tenant) {
            $contract = $tenant->rentalContracts->where('status', 'active')->first()
                     ?? $tenant->rentalContracts->sortByDesc('created_at')->first();
            $rows[] = [
                $tenant->id,
                $tenant->user?->name_ar ?? $tenant->user?->name ?? '',
                $tenant->user?->name_en ?? $tenant->user?->name ?? '',
                $tenant->user?->email ?? '',
                $tenant->user?->phone ?? '',
                $tenant->national_id ?? '',
                $tenant->emergency_contact ?? '',
                $contract?->unit?->property?->code ?? '',
                $contract?->unit?->property?->name_en ?? $contract?->unit?->property?->name ?? '',
                $contract?->unit?->unit_number ?? '',
                $contract?->status ?? '',
                $contract?->start_date?->format('Y-m-d') ?? '',
                $contract?->end_date?->format('Y-m-d') ?? '',
                $contract?->monthly_rent ?? '',
                $contract?->deposit ?? '',
                $tenant->created_at?->format('Y-m-d') ?? '',
            ];
        }

        $csv = "\xEF\xBB\xBF";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', (string)($v ?? '')) . '"', $row)) . "\r\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="tenants-' . now()->format('Y-m-d') . '.csv"',
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
