<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $employees = User::role('employee')
            ->withCount('managedProperties')
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
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:employee,accountant',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create(array_merge($this->buildUserNamePayload($validated), [
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]));
        $user->assignRole($validated['role']);

        return redirect()->route('manager.employees.index')
            ->with('success', app()->getLocale() === 'en' ? 'Employee created successfully.' : 'تم إضافة الموظف بنجاح');
    }

    public function show(User $employee)
    {
        $employee->load('managedProperties.units');
        return view('manager.employees.show', compact('employee'));
    }

    public function edit(User $employee)
    {
        return view('manager.employees.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $employee->update(array_merge($this->buildUserNamePayload($validated), [
            'phone' => $validated['phone'] ?? null,
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
