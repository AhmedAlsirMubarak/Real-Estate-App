<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentContractor;
use App\Models\DevelopmentProject;
use Illuminate\Http\Request;

class DevelopmentContractorController extends Controller
{
    public function store(Request $request, DevelopmentProject $development)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'scope_of_work'  => 'required|string|max:1000',
            'contract_value' => 'required|numeric|min:0',
        ]);

        $development->contractors()->create($request->only(['name', 'scope_of_work', 'contract_value']));

        return back()->with('success', app()->getLocale() === 'ar' ? 'تمت إضافة المقاول بنجاح.' : 'Contractor added.');
    }

    public function destroy(DevelopmentProject $development, DevelopmentContractor $contractor)
    {
        abort_if($contractor->development_project_id !== $development->id, 404);
        $contractor->delete();
        return back()->with('success', app()->getLocale() === 'ar' ? 'تم حذف المقاول.' : 'Contractor removed.');
    }
}
