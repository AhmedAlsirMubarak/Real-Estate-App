<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Association;
use App\Models\AssociationDue;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssociationDueController extends Controller
{
    public function invoice(AssociationDue $due)
    {
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

        $filename = 'invoice-INV-' . str_pad($due->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->stream($filename);
    }

    public function index(Request $request)
    {
        $query = AssociationDue::with(['association.property', 'owner.user']);

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($month = $request->query('month')) {
            $query->where('period_month', $month);
        }
        if ($year = $request->query('year')) {
            $query->where('period_year', $year);
        }

        $dues = $query->latest('due_date')->paginate(20)->withQueryString();

        return view('manager.dues.index', compact('dues'));
    }

    public function generate(Request $request, Association $association)
    {
        $request->validate([
            'period_month' => 'required|integer|between:1,12',
            'period_year'  => 'required|integer|min:2020|max:2100',
        ]);

        $month = (int) $request->input('period_month');
        $year  = (int) $request->input('period_year');
        $unitCount = $association->property->units()->count();
        $amountPerOwner = $association->monthly_fee_per_unit * max($unitCount, 1);

        $owners = $association->property->owners;
        if ($owners->isEmpty() && $association->property->owner_id) {
            $owners = collect([$association->property->owner]);
        }

        $created = 0;
        foreach ($owners as $owner) {
            $share = $owner->pivot->ownership_percentage ?? 100;
            $ownerAmount = round($amountPerOwner * ($share / 100), 2);

            $due = AssociationDue::firstOrCreate(
                [
                    'association_id' => $association->id,
                    'owner_id'       => $owner->id,
                    'period_month'   => $month,
                    'period_year'    => $year,
                ],
                [
                    'amount'   => $ownerAmount,
                    'due_date' => Carbon::create($year, $month, 5),
                    'status'   => 'pending',
                ]
            );
            if ($due->wasRecentlyCreated) $created++;
        }

        return back()->with('success', __('Created Successfully') . " ({$created})");
    }

    public function markPaid(AssociationDue $due)
    {
        $due->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);
        return back()->with('success', __('Operation Successful'));
    }

    public function markWaived(AssociationDue $due)
    {
        $due->update(['status' => 'waived']);
        return back()->with('success', __('Operation Successful'));
    }

    public function destroy(AssociationDue $due)
    {
        $due->delete();
        return back()->with('success', __('Deleted Successfully'));
    }
}
