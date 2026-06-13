<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\RentalContract;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Mpdf\Mpdf;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $employee = $request->user();

        $managedIds = Tenant::whereHas('activeContract.unit.property', function ($q) use ($employee) {
            $q->where('employee_id', $employee->id);
        })->pluck('id');

        $referredIds = Tenant::where('referral_employee_id', $employee->id)->pluck('id');

        $allIds = $managedIds->merge($referredIds)->unique()->values();

        $tenants = Tenant::whereIn('id', $allIds)
            ->with(['user', 'activeContract.unit.property'])
            ->latest()
            ->paginate(20);

        return view('employee.tenants.index', compact('tenants'));
    }

    public function create()
    {
        $availableUnits = Unit::where('status', 'available')
            ->whereIn('listing_type', ['rent', 'both'])
            ->with('property')
            ->get();

        return view('employee.tenants.create', compact('availableUnits'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_ar'                    => 'required|string|max:255',
            'name_en'                    => 'required|string|max:255',
            'email'                      => 'nullable|email|unique:users,email',
            'phone'                      => 'required|string|max:20',
            'password'                   => 'nullable|string|min:8',
            'national_id'                => 'nullable|string|max:50',
            'emergency_contact'          => 'nullable|string|max:255',
            'unit_id'                    => 'required|exists:units,id',
            'start_date'                 => 'required|date',
            'end_date'                   => 'required|date|after:start_date',
            'monthly_rent'               => 'required|numeric|min:0',
            'deposit'                    => 'nullable|numeric|min:0',
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
            'referral_employee_id'     => $request->user()->id,
            'referral_commission_rate' => $request->user()->commission_rate ?? null,
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

        $startDate = Carbon::parse($validated['start_date']);

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

        $msg = app()->getLocale() === 'ar' ? 'تم إضافة المستأجر وعقده بنجاح.' : 'Tenant and contract added successfully.';
        return redirect()->route('employee.tenants.index')->with('success', $msg);
    }

    public function show(Request $request, Tenant $tenant)
    {
        $this->authorizeEmployee($request->user(), $tenant);

        $tenant->load([
            'user',
            'activeContract.unit.property',
            'rentalContracts.unit.property',
            'payments' => fn($q) => $q->orderByDesc('year')->orderByDesc('month'),
        ]);

        return view('employee.tenants.show', compact('tenant'));
    }

    public function generatePayment(Request $request, Tenant $tenant): RedirectResponse
    {
        $this->authorizeEmployee($request->user(), $tenant);

        $validated = $request->validate([
            'month'  => 'required|integer|min:1|max:12',
            'year'   => 'required|integer|min:2000|max:2100',
            'amount' => 'required|numeric|min:0',
        ]);

        $contract = $tenant->activeContract ?? $tenant->rentalContracts()->latest()->first();

        if (!$contract) {
            return back()->withErrors(['payment' => app()->getLocale() === 'ar'
                ? 'لا يوجد عقد لهذا المستأجر، يرجى إضافة عقد أولاً.'
                : 'No contract found for this tenant.']);
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
        } catch (\Throwable) {
            return back()->withErrors(['payment' => app()->getLocale() === 'ar'
                ? 'يوجد فاتورة إيجار لهذا الشهر مسبقاً.'
                : 'A payment already exists for this month.']);
        }

        return redirect()->route('employee.tenants.show', $tenant)
            ->with('success', app()->getLocale() === 'ar' ? 'تم توليد الفاتورة بنجاح' : 'Invoice generated successfully.');
    }

    public function markPaymentPaid(Request $request, Tenant $tenant, Payment $payment): RedirectResponse
    {
        $this->authorizeEmployee($request->user(), $tenant);
        abort_if((int) $payment->tenant_id !== (int) $tenant->id, 403);

        $validated = $request->validate(['paid_at' => 'nullable|date']);

        $payment->update([
            'status'  => 'paid',
            'paid_at' => $validated['paid_at'] ?? now(),
        ]);

        return redirect()->route('employee.tenants.show', $tenant)
            ->with('success', app()->getLocale() === 'ar' ? 'تم تسجيل الدفع بنجاح' : 'Payment marked as paid.');
    }

    public function destroyPayment(Request $request, Tenant $tenant, Payment $payment): RedirectResponse
    {
        $this->authorizeEmployee($request->user(), $tenant);
        abort_if((int) $payment->tenant_id !== (int) $tenant->id, 403);

        $payment->delete();

        return redirect()->route('employee.tenants.show', $tenant)
            ->with('success', app()->getLocale() === 'ar' ? 'تم حذف الفاتورة' : 'Payment deleted.');
    }

    public function paymentInvoice(Request $request, Tenant $tenant, Payment $payment)
    {
        $this->authorizeEmployee($request->user(), $tenant);
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

    private function authorizeEmployee(User $employee, Tenant $tenant): void
    {
        $isManaged = RentalContract::where('tenant_id', $tenant->id)
            ->whereHas('unit.property', fn($q) => $q->where('employee_id', $employee->id))
            ->exists();
        $isReferred = (int) $tenant->referral_employee_id === (int) $employee->id;
        abort_if(!$isManaged && !$isReferred, 403);
    }

    private function supportsBilingualNames(): bool
    {
        static $supports = null;
        if ($supports !== null) return $supports;
        $supports = Schema::hasColumn('users', 'name_ar') && Schema::hasColumn('users', 'name_en');
        return $supports;
    }

    private function buildUserNamePayload(array $validated): array
    {
        $payload = ['name' => $validated['name_ar']];
        if ($this->supportsBilingualNames()) {
            $payload['name_ar'] = $validated['name_ar'];
            $payload['name_en'] = $validated['name_en'];
        }
        return $payload;
    }
}
