<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AssociationDue;
use Illuminate\Http\Request;

class DueController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user  = auth()->user();
        $owner = $user->owner;
        abort_unless($owner, 403);

        $query = AssociationDue::with('association.property')
            ->where('owner_id', $owner->id);

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $dues = $query->latest('due_date')->paginate(20)->withQueryString();

        $totals = [
            'pending'  => AssociationDue::where('owner_id', $owner->id)->where('status', 'pending')->sum('amount'),
            'paid'     => AssociationDue::where('owner_id', $owner->id)->where('status', 'paid')->sum('amount'),
            'overdue'  => AssociationDue::where('owner_id', $owner->id)->where('status', 'overdue')->sum('amount'),
        ];

        return view('owner.dues.index', compact('dues', 'totals', 'owner'));
    }
}
