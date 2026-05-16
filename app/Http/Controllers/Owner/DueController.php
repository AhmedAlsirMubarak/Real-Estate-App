<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AssociationDue;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DueController extends Controller
{
    public function invoice(AssociationDue $due)
    {
        $owner = auth()->user()->owner;
        abort_unless($owner && (int) $due->owner_id === (int) $owner->id, 403);

        $due->load(['association.property.units', 'owner.user']);

        $currency        = 'OMR';
        $ownerName       = $due->owner?->user?->name ?? 'Owner';
        $ownerPhone      = $due->owner?->phone ?? $due->owner?->user?->phone ?? null;
        $propertyName    = $due->association?->property?->name ?? 'Property';
        $associationName = $due->association?->name_en ?? $due->association?->name_ar ?? '';
        $unitCount       = $due->association?->property?->units?->count() ?? 0;

        $pdf = Pdf::loadView('manager.dues.invoice', compact(
            'due', 'currency', 'ownerName', 'ownerPhone',
            'propertyName', 'associationName', 'unitCount'
        ))->setPaper('a4');

        return $pdf->stream('invoice-INV-' . str_pad($due->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }

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
