<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendance;
use App\Models\User;
use Illuminate\Http\Request;

class HrAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date      = $request->query('date', now()->format('Y-m-d'));
        $employeeId = $request->query('employee_id');

        $query = EmployeeAttendance::with('employee')
            ->where('date', $date);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $records   = $query->get();
        $employees = User::role('employee')->orderBy('name')->get();

        $presentToday = EmployeeAttendance::where('date', now()->toDateString())
            ->whereIn('status', ['present', 'late', 'half_day'])
            ->count();

        return view('manager.hr.attendance', compact('records', 'employees', 'date', 'presentToday'));
    }

    public function store(Request $request, User $employee)
    {
        $data = $request->validate([
            'date'         => 'required|date',
            'status'       => 'required|in:present,absent,late,half_day,holiday',
            'check_in'     => 'nullable|date_format:H:i',
            'check_out'    => 'nullable|date_format:H:i|after:check_in',
            'hours_worked' => 'nullable|numeric|min:0|max:24',
            'notes'        => 'nullable|string|max:500',
        ]);

        $data['employee_id'] = $employee->id;

        EmployeeAttendance::updateOrCreate(
            ['employee_id' => $employee->id, 'date' => $data['date']],
            $data
        );

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم تسجيل الحضور' : 'Attendance recorded.');
    }

    public function update(Request $request, User $employee, EmployeeAttendance $attendance)
    {
        abort_if((int) $attendance->employee_id !== (int) $employee->id, 403);

        $data = $request->validate([
            'status'       => 'required|in:present,absent,late,half_day,holiday',
            'check_in'     => 'nullable|date_format:H:i',
            'check_out'    => 'nullable|date_format:H:i',
            'hours_worked' => 'nullable|numeric|min:0|max:24',
            'notes'        => 'nullable|string|max:500',
        ]);

        $attendance->update($data);

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم تحديث الحضور' : 'Attendance updated.');
    }

    public function destroy(User $employee, EmployeeAttendance $attendance)
    {
        abort_if((int) $attendance->employee_id !== (int) $employee->id, 403);
        $attendance->delete();
        return back()->with('success', app()->getLocale() === 'ar' ? 'تم حذف سجل الحضور' : 'Record deleted.');
    }
}
