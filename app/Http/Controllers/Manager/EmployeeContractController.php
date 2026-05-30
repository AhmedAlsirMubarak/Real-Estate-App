<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\EmployeeContract;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeContractController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeContract::with('employee');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }
        if ($employee = $request->query('employee_id')) {
            $query->where('employee_id', $employee);
        }

        $contracts  = $query->latest()->paginate(20)->withQueryString();
        $employees  = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['employee', 'accountant', 'manager']))->orderBy('name')->get();

        return view('manager.contracts.index', compact('contracts', 'employees'));
    }

    public function create()
    {
        $employees = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['employee', 'accountant', 'manager']))->orderBy('name')->get();
        return view('manager.contracts.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:employment,service,freelance,supplier,other',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'value'       => 'nullable|numeric|min:0',
            'status'      => 'required|in:draft,active,expired,terminated',
            'document'    => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'notes'       => 'nullable|string',
        ]);

        if ($request->hasFile('document')) {
            $file    = $request->file('document');
            if ($file->isValid()) {
                $filename = sha1(uniqid('', true) . microtime()) . '.' . $file->getClientOriginalExtension();
                $destDir  = storage_path('app/public/contracts');
                $destPath = $destDir . DIRECTORY_SEPARATOR . $filename;
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0755, true);
                }
                if (move_uploaded_file($file->getPathname(), $destPath)) {
                    $data['document_path'] = 'contracts/' . $filename;
                }
            }
        }

        unset($data['document']);
        EmployeeContract::create($data);

        return redirect()->route('manager.contracts.index')->with('success', __('Created Successfully'));
    }

    public function edit(EmployeeContract $contract)
    {
        $employees = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['employee', 'accountant', 'manager']))->orderBy('name')->get();
        return view('manager.contracts.edit', compact('contract', 'employees'));
    }

    public function update(Request $request, EmployeeContract $contract)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:employment,service,freelance,supplier,other',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'value'       => 'nullable|numeric|min:0',
            'status'      => 'required|in:draft,active,expired,terminated',
            'document'    => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'notes'       => 'nullable|string',
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            if ($file->isValid()) {
                $filename = sha1(uniqid('', true) . microtime()) . '.' . $file->getClientOriginalExtension();
                $destDir  = storage_path('app/public/contracts');
                $destPath = $destDir . DIRECTORY_SEPARATOR . $filename;
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0755, true);
                }
                if (move_uploaded_file($file->getPathname(), $destPath)) {
                    // Delete old file only after new one is safely moved
                    if ($contract->document_path) {
                        $old = storage_path('app/public/' . $contract->document_path);
                        if (file_exists($old)) {
                            @unlink($old);
                        }
                    }
                    $data['document_path'] = 'contracts/' . $filename;
                }
            }
        }

        unset($data['document']);
        $contract->update($data);

        return redirect()->route('manager.contracts.index')->with('success', __('Updated Successfully'));
    }

    public function destroy(EmployeeContract $contract)
    {
        if ($contract->document_path) {
            $path = storage_path('app/public/' . $contract->document_path);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
        $contract->delete();
        return back()->with('success', __('Deleted Successfully'));
    }
}
