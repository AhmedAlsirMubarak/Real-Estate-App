<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLeave;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $leaves = EmployeeLeave::where('employee_id', $request->user()->id)
            ->orderByDesc('start_date')
            ->paginate(15);

        return view('employee.leaves.index', compact('leaves'));
    }

    public function create()
    {
        return view('employee.leaves.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type'       => 'required|in:annual,sick,unpaid,emergency',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'nullable|string|max:1000',
        ]);

        $days = (int) Carbon::parse($data['start_date'])->diffInDays(Carbon::parse($data['end_date'])) + 1;

        EmployeeLeave::create([
            'employee_id' => $request->user()->id,
            'type'        => $data['type'],
            'start_date'  => $data['start_date'],
            'end_date'    => $data['end_date'],
            'days'        => $days,
            'reason'      => $data['reason'] ?? null,
            'status'      => 'pending',
        ]);

        $msg = app()->getLocale() === 'ar'
            ? 'تم إرسال طلب الإجازة بنجاح، في انتظار الموافقة.'
            : 'Leave request submitted. Awaiting approval.';

        // Redirect to history if coming from dedicated page, otherwise back
        $from = $request->input('_from');
        if ($from === 'create') {
            return redirect()->route('employee.leaves.index')->with('success', $msg);
        }

        return back()->with('leave_success', $msg);
    }
}
