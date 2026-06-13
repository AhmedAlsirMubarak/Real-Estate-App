<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLeave;
use App\Models\User;
use Illuminate\Http\Request;

class HrLeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeLeave::with('employee')->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }
        if ($employeeId = $request->query('employee_id')) {
            $query->where('employee_id', $employeeId);
        }

        $leaves    = $query->paginate(20)->withQueryString();
        $employees = User::role('employee')->orderBy('name')->get();
        $pending   = EmployeeLeave::where('status', 'pending')->count();

        return view('manager.hr.leaves', compact('leaves', 'employees', 'pending'));
    }

    public function store(Request $request, User $employee)
    {
        $data = $request->validate([
            'type'       => 'required|in:annual,sick,unpaid,emergency',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'nullable|string|max:1000',
            'notes'      => 'nullable|string|max:1000',
        ]);

        $start = \Carbon\Carbon::parse($data['start_date']);
        $end   = \Carbon\Carbon::parse($data['end_date']);
        $data['days']        = (int) $start->diffInDays($end) + 1;
        $data['employee_id'] = $employee->id;
        $data['status']      = 'pending';

        EmployeeLeave::create($data);

        return redirect()->route('manager.employees.show', $employee)
            ->with('success', app()->getLocale() === 'ar' ? 'تم تسجيل طلب الإجازة' : 'Leave request submitted.');
    }

    public function approve(User $employee, EmployeeLeave $leave)
    {
        abort_if((int) $leave->employee_id !== (int) $employee->id, 403);
        $leave->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', app()->getLocale() === 'ar' ? 'تمت الموافقة على الإجازة' : 'Leave approved.');
    }

    public function reject(User $employee, EmployeeLeave $leave)
    {
        abort_if((int) $leave->employee_id !== (int) $employee->id, 403);
        $leave->update(['status' => 'rejected']);
        return back()->with('success', app()->getLocale() === 'ar' ? 'تم رفض الإجازة' : 'Leave rejected.');
    }

    public function destroy(User $employee, EmployeeLeave $leave)
    {
        abort_if((int) $leave->employee_id !== (int) $employee->id, 403);
        $leave->delete();
        return back()->with('success', app()->getLocale() === 'ar' ? 'تم حذف طلب الإجازة' : 'Leave deleted.');
    }
}
