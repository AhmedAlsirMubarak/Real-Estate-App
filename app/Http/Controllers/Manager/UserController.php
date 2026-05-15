<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Owner;
use App\Models\RentalContract;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $role = $request->input('role');
        $status = $request->input('status');

        $users = User::query()
            ->with('roles')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");

                    if ($this->supportsBilingualNames()) {
                        $subQuery->orWhere('name_ar', 'like', "%{$search}%")
                            ->orWhere('name_en', 'like', "%{$search}%");
                    }
                });
            })
            ->when($role, fn ($query) => $query->role($role))
            ->when($status === 'blocked', fn ($query) => $query->where('is_blocked', true))
            ->when($status === 'active', fn ($query) => $query->where('is_blocked', false))
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        $roles = Role::query()->orderBy('name')->pluck('name');

        return view('manager.users.index', compact('users', 'roles', 'search', 'role', 'status'));
    }

    public function create()
    {
        $roles = Role::query()->orderBy('name')->pluck('name');
        $availableUnits = Unit::where('status', 'available')
            ->whereIn('listing_type', ['rent', 'both'])
            ->with('property')
            ->get();

        return view('manager.users.create', compact('roles', 'availableUnits'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->storeRules($request));

        DB::transaction(function () use ($validated) {
            $user = User::create(array_merge($this->buildUserNamePayload($validated), [
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => $validated['password'],
            ]));

            $user->syncRoles([$validated['role']]);
            $this->createRoleProfile($user, $validated);
        });

        return redirect()->route('manager.users.index')
            ->with('success', 'تم إنشاء المستخدم وتعيين الدور بنجاح');
    }

    public function edit(User $user)
    {
        $user->load(['roles', 'tenant', 'owner', 'buyer']);
        $roles = Role::query()->orderBy('name')->pluck('name');

        return view('manager.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate($this->updateRules($request, $user));

        if ($request->user()->id === $user->id && $validated['role'] !== 'manager') {
            return back()->with('error', 'لا يمكنك تغيير دور حسابك الحالي من مدير إلى دور آخر.');
        }

        $updateData = array_merge($this->buildUserNamePayload($validated), [
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if (! empty($validated['password'])) {
            $updateData['password'] = $validated['password'];
        }

        DB::transaction(function () use ($user, $validated, $updateData) {
            $user->update($updateData);
            $user->syncRoles([$validated['role']]);
            $this->updateRoleProfile($user, $validated);
        });

        return redirect()->route('manager.users.index')
            ->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    public function toggleBlock(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->id === $user->id) {
            return back()->with('error', 'لا يمكنك حظر حسابك الحالي.');
        }

        $blocked = ! $user->is_blocked;

        $user->update([
            'is_blocked' => $blocked,
            'blocked_at' => $blocked ? now() : null,
        ]);

        if ($blocked) {
            $this->clearDatabaseSessions($user->id);
        }

        return back()->with(
            'success',
            $blocked ? 'تم حظر المستخدم بنجاح' : 'تم إلغاء حظر المستخدم بنجاح'
        );
    }

    private function storeRules(Request $request): array
    {
        $rules = [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
            'password' => 'required|string|min:8|confirmed',
        ];

        return $this->appendRoleSpecificRules($rules, $request->input('role'), true);
    }

    private function updateRules(Request $request, User $user): array
    {
        $rules = [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
            'password' => 'nullable|string|min:8|confirmed',
        ];

        return $this->appendRoleSpecificRules($rules, $request->input('role'), false);
    }

    private function appendRoleSpecificRules(array $rules, ?string $role, bool $tenantContractRequired): array
    {
        if ($role === 'tenant') {
            $rules['tenant_national_id'] = 'nullable|string|max:50';
            $rules['tenant_emergency_contact'] = 'nullable|string|max:255';

            if ($tenantContractRequired) {
                $rules['tenant_unit_id'] = [
                    'required',
                    Rule::exists('units', 'id')->where(fn ($query) => $query
                        ->where('status', 'available')
                        ->whereIn('listing_type', ['rent', 'both'])),
                ];
                $rules['tenant_start_date'] = 'required|date';
                $rules['tenant_end_date'] = 'required|date|after:tenant_start_date';
                $rules['tenant_monthly_rent'] = 'required|numeric|min:0';
                $rules['tenant_deposit'] = 'nullable|numeric|min:0';
            }
        } elseif ($role === 'owner') {
            $rules['owner_national_id'] = 'nullable|string|max:50';
            $rules['owner_bank_account'] = 'nullable|string|max:100';
            $rules['owner_commission_rate'] = 'nullable|numeric|min:0|max:100';
            $rules['owner_notes'] = 'nullable|string|max:5000';
        } elseif ($role === 'buyer') {
            $rules['buyer_national_id'] = 'nullable|string|max:50';
            $rules['buyer_address'] = 'nullable|string|max:255';
            $rules['buyer_notes'] = 'nullable|string|max:5000';
        }

        return $rules;
    }

    private function createRoleProfile(User $user, array $validated): void
    {
        if ($validated['role'] === 'tenant') {
            $unit = Unit::query()->lockForUpdate()->findOrFail($validated['tenant_unit_id']);

            if ($unit->status !== 'available' || ! in_array($unit->listing_type, ['rent', 'both'], true)) {
                throw ValidationException::withMessages([
                    'tenant_unit_id' => 'الوحدة المختارة غير متاحة للتأجير الآن.',
                ]);
            }

            $tenant = Tenant::create([
                'user_id' => $user->id,
                'national_id' => $validated['tenant_national_id'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'emergency_contact' => $validated['tenant_emergency_contact'] ?? null,
            ]);

            RentalContract::create([
                'unit_id' => $unit->id,
                'tenant_id' => $tenant->id,
                'start_date' => $validated['tenant_start_date'],
                'end_date' => $validated['tenant_end_date'],
                'monthly_rent' => $validated['tenant_monthly_rent'],
                'deposit' => $validated['tenant_deposit'] ?? 0,
                'status' => 'active',
            ]);

            $unit->update(['status' => 'rented']);
        }

        if ($validated['role'] === 'owner') {
            Owner::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'national_id' => $validated['owner_national_id'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'bank_account' => $validated['owner_bank_account'] ?? null,
                    'commission_rate' => $validated['owner_commission_rate'] ?? 10,
                    'notes' => $validated['owner_notes'] ?? null,
                ]
            );
        }

        if ($validated['role'] === 'buyer') {
            Buyer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'national_id' => $validated['buyer_national_id'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['buyer_address'] ?? null,
                    'notes' => $validated['buyer_notes'] ?? null,
                ]
            );
        }
    }

    private function updateRoleProfile(User $user, array $validated): void
    {
        if ($validated['role'] === 'tenant') {
            Tenant::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'national_id' => $validated['tenant_national_id'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'emergency_contact' => $validated['tenant_emergency_contact'] ?? null,
                ]
            );
        }

        if ($validated['role'] === 'owner') {
            $owner = Owner::firstOrNew(['user_id' => $user->id]);
            $owner->national_id = $validated['owner_national_id'] ?? null;
            $owner->phone = $validated['phone'] ?? null;
            $owner->bank_account = $validated['owner_bank_account'] ?? null;
            $owner->notes = $validated['owner_notes'] ?? null;
            $owner->commission_rate = $validated['owner_commission_rate'] ?? ($owner->commission_rate ?? 10);
            $owner->save();
        }

        if ($validated['role'] === 'buyer') {
            Buyer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'national_id' => $validated['buyer_national_id'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['buyer_address'] ?? null,
                    'notes' => $validated['buyer_notes'] ?? null,
                ]
            );
        }
    }

    private function clearDatabaseSessions(int $userId): void
    {
        if (config('session.driver') === 'database' && Schema::hasTable('sessions')) {
            DB::table('sessions')->where('user_id', $userId)->delete();
        }
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
