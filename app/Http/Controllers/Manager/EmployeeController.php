<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $referralCommissionSub = DB::raw("(
            SELECT COALESCE(SUM(
                props.referral_commission_rate / 100 *
                COALESCE(rev.total_paid, 0)
            ), 0)
            FROM properties props
            LEFT JOIN (
                SELECT units.property_id, SUM(payments.amount) AS total_paid
                FROM payments
                INNER JOIN rental_contracts ON payments.rental_contract_id = rental_contracts.id
                INNER JOIN units ON rental_contracts.unit_id = units.id
                WHERE payments.status = 'paid'
                GROUP BY units.property_id
            ) rev ON rev.property_id = props.id
            WHERE props.referral_employee_id = users.id
            AND props.referral_commission_rate IS NOT NULL
        ) + (
            SELECT COALESCE(SUM(
                t.referral_commission_rate / 100 *
                COALESCE(trev.total_paid, 0)
            ), 0)
            FROM tenants t
            LEFT JOIN (
                SELECT payments.tenant_id, SUM(payments.amount) AS total_paid
                FROM payments
                WHERE payments.status = 'paid'
                GROUP BY payments.tenant_id
            ) trev ON trev.tenant_id = t.id
            WHERE t.referral_employee_id = users.id
            AND t.referral_commission_rate IS NOT NULL
        ) AS referral_commission_earned");

        $employees = User::role('employee')
            ->withCount([
                'managedProperties',
                'referredProperties as referred_properties_count',
                'referredTenants as referred_tenants_count',
            ])
            ->addSelect($referralCommissionSub)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");

                    if ($this->supportsBilingualNames()) {
                        $subQuery->orWhere('name_ar', 'like', "%{$search}%")
                            ->orWhere('name_en', 'like', "%{$search}%");
                    }
                });
            })
            ->latest()
            ->paginate(15)
            ->appends($request->query());
        return view('manager.employees.index', compact('employees', 'search'));
    }

    public function create()
    {
        return view('manager.employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar'         => 'required|string|max:255',
            'name_en'         => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'phone'           => 'nullable|string|max:20',
            'base_salary'     => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'role'            => 'required|in:employee,accountant',
            'password'        => 'required|string|min:8',
        ]);

        $user = User::create(array_merge($this->buildUserNamePayload($validated), [
            'email'           => $validated['email'],
            'phone'           => $validated['phone'] ?? null,
            'base_salary'     => (float) ($validated['base_salary'] ?? 0),
            'commission_rate' => isset($validated['commission_rate']) ? (float) $validated['commission_rate'] : null,
            'password'        => Hash::make($validated['password']),
        ]));
        $user->assignRole($validated['role']);

        return redirect()->route('manager.employees.index')
            ->with('success', app()->getLocale() === 'en' ? 'Employee created successfully.' : 'تم إضافة الموظف بنجاح');
    }

    public function show(User $employee)
    {
        $employee->load(['managedProperties.units', 'referredProperties', 'referredTenants.user', 'leaves', 'attendance', 'salaries']);

        // Total paid rent per referred property
        $referredIds = $employee->referredProperties->pluck('id');
        $propertyRevenue = collect();
        if ($referredIds->isNotEmpty()) {
            $propertyRevenue = DB::table('payments')
                ->join('rental_contracts', 'payments.rental_contract_id', '=', 'rental_contracts.id')
                ->join('units', 'rental_contracts.unit_id', '=', 'units.id')
                ->where('payments.status', 'paid')
                ->whereIn('units.property_id', $referredIds)
                ->selectRaw('units.property_id, SUM(payments.amount) as total_paid')
                ->groupBy('units.property_id')
                ->pluck('total_paid', 'property_id');
        }

        $propertyReferralCommissionTotal = $employee->referredProperties->sum(
            fn ($p) => ($p->referral_commission_rate ?? 0) / 100 * ($propertyRevenue[$p->id] ?? 0)
        );

        // Total paid rent per referred tenant
        $referredTenantIds = $employee->referredTenants->pluck('id');
        $tenantRevenue = collect();
        if ($referredTenantIds->isNotEmpty()) {
            $tenantRevenue = DB::table('payments')
                ->where('status', 'paid')
                ->whereIn('tenant_id', $referredTenantIds)
                ->selectRaw('tenant_id, SUM(amount) as total_paid')
                ->groupBy('tenant_id')
                ->pluck('total_paid', 'tenant_id');
        }

        $tenantReferralCommissionTotal = $employee->referredTenants->sum(
            fn ($t) => ($t->referral_commission_rate ?? 0) / 100 * ($tenantRevenue[$t->id] ?? 0)
        );

        $referralCommissionTotal = $propertyReferralCommissionTotal + $tenantReferralCommissionTotal;

        return view('manager.employees.show', compact(
            'employee',
            'propertyRevenue',
            'tenantRevenue',
            'propertyReferralCommissionTotal',
            'tenantReferralCommissionTotal',
            'referralCommissionTotal',
        ));
    }

    public function edit(User $employee)
    {
        return view('manager.employees.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        $validated = $request->validate([
            'name_ar'         => 'required|string|max:255',
            'name_en'         => 'required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'base_salary'     => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $employee->update(array_merge($this->buildUserNamePayload($validated), [
            'phone'           => $validated['phone'] ?? null,
            'base_salary'     => (float) ($validated['base_salary'] ?? 0),
            'commission_rate' => isset($validated['commission_rate']) ? (float) $validated['commission_rate'] : null,
        ]));

        return redirect()->route('manager.employees.show', $employee)
            ->with('success', 'تم تحديث بيانات الموظف');
    }

    public function destroy(User $employee)
    {
        $employee->managedProperties()->update(['employee_id' => null]);
        $employee->delete();
        return redirect()->route('manager.employees.index')
            ->with('success', 'تم حذف الموظف بنجاح');
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
