<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Unit;
use App\Models\RentalContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tenants = Tenant::with(['user', 'activeContract.unit.property'])
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
            'unit_id' => $validated['unit_id'],
            'tenant_id' => $tenant->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'monthly_rent' => $validated['monthly_rent'],
            'deposit' => $validated['deposit'] ?? 0,
            'status' => 'active',
        ]);

        Unit::find($validated['unit_id'])->update(['status' => 'rented']);

        return redirect()->route('manager.tenants.index')
            ->with('success', app()->getLocale() === 'en' ? 'Tenant created successfully.' : 'تم إضافة المستأجر بنجاح');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['user', 'rentalContracts.unit.property', 'maintenanceRequests.unit', 'payments']);
        return view('manager.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        $tenant->load('user');
        return view('manager.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:50',
            'emergency_contact' => 'nullable|string|max:255',
        ]);

        $tenant->user->update(array_merge($this->buildUserNamePayload($validated), [
            'phone' => $validated['phone'] ?? null,
        ]));
        $tenant->update([
            'national_id' => $validated['national_id'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'emergency_contact' => $validated['emergency_contact'] ?? null,
        ]);

        return redirect()->route('manager.tenants.show', $tenant)
            ->with('success', 'تم تحديث بيانات المستأجر');
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
