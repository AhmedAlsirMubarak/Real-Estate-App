<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AssociationDue;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class DueController extends Controller
{
    public function invoice(AssociationDue $due)
    {
        /** @var \App\Models\User $authUser */
        $authUser = auth()->user();
        $owner = $authUser->owner;
        abort_unless($owner && (int) $due->owner_id === (int) $owner->id, 403);

        $due->load(['association.property.units', 'owner.user']);

        $isAr            = app()->getLocale() === 'ar';
        $currency        = 'OMR';
        $ownerName       = $due->owner?->user?->name ?? ($isAr ? 'المالك' : 'Owner');
        $ownerPhone      = $due->owner?->phone ?? $due->owner?->user?->phone ?? null;
        $propertyName    = $due->association?->property?->name ?? ($isAr ? 'العقار' : 'Property');
        $associationName = $isAr
            ? ($due->association?->name_ar ?? $due->association?->name_en ?? '')
            : ($due->association?->name_en ?? $due->association?->name_ar ?? '');
        $unitCount       = $due->association?->property?->units?->count() ?? 0;

        $html = view('manager.dues.invoice', compact(
            'due', 'currency', 'ownerName', 'ownerPhone',
            'propertyName', 'associationName', 'unitCount'
        ))->render();

        $tempDir = storage_path('app/mpdf');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $mpdf = new Mpdf([
            'mode'         => 'utf-8',
            'format'       => 'A4',
            'orientation'  => 'P',
            'default_font' => 'dejavusans',
            'tempDir'      => $tempDir,
        ]);

        if ($isAr) {
            $mpdf->SetDirectionality('rtl');
        }

        $mpdf->WriteHTML($html);

        $filename = 'invoice-INV-' . str_pad($due->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
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

        $totalsRow = AssociationDue::where('owner_id', $owner->id)
            ->selectRaw(
                "SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as pending,
                 SUM(CASE WHEN status = 'paid'    THEN amount ELSE 0 END) as paid,
                 SUM(CASE WHEN status = 'overdue' THEN amount ELSE 0 END) as overdue"
            )->first();

        $totals = [
            'pending' => (float) ($totalsRow->pending ?? 0),
            'paid'    => (float) ($totalsRow->paid    ?? 0),
            'overdue' => (float) ($totalsRow->overdue ?? 0),
        ];

        return view('owner.dues.index', compact('dues', 'totals', 'owner'));
    }
}
