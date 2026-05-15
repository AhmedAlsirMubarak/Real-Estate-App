<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('manager')) {
            return redirect()->route('manager.dashboard');
        }

        if ($user->hasRole('employee')) {
            return redirect()->route('employee.dashboard');
        }

        if ($user->hasRole('accountant')) {
            return redirect()->route('accountant.dashboard');
        }

        if ($user->hasRole('tenant')) {
            return redirect()->route('tenant.dashboard');
        }

        if ($user->hasRole('owner')) {
            return redirect()->route('owner.dashboard');
        }

        if ($user->hasRole('buyer')) {
            return redirect()->route('buyer.dashboard');
        }

        abort(403, 'لا تملك صلاحية للوصول');
    }
}
