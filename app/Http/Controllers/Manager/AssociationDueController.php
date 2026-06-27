<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Association;
use App\Models\AssociationDue;
use Mpdf\Mpdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssociationDueController extends Controller
{
    public function associationInvoice(Request $request, Association $association)
    {
        $request->validate([
            'period_month' => 'required|integer|between:1,12',
            'period_year'  => 'required|integer|min:2020|max:2100',
        ]);

        $month       = (int) $request->input('period_month');
        $year        = (int) $request->input('period_year');
        $unitNumbers = array_filter((array) ($association->unit_number ?? []));
        $unitFees    = (array) ($association->unit_fees ?? []);
        $amount      = $unitNumbers
            ? array_sum(array_map(fn($u) => (float) ($unitFees[$u] ?? $association->monthly_fee_per_unit), $unitNumbers))
            : (float) $association->monthly_fee_per_unit;
        $dueDate   = Carbon::create($year, $month, 5);

        // Persist the due so it appears in the dues table and can be marked paid/pending.
        $due = AssociationDue::firstOrCreate(
            [
                'association_id' => $association->id,
                'owner_id'       => null,
                'period_month'   => $month,
                'period_year'    => $year,
            ],
            [
                'amount'   => $amount,
                'due_date' => $dueDate,
                'status'   => 'pending',
            ]
        );

        $isAr = app()->getLocale() === 'ar';
        $msg  = $due->wasRecentlyCreated
            ? ($isAr ? 'تمت إضافة الفاتورة إلى الاستحقاقات' : 'Invoice added to dues')
            : ($isAr ? 'الفاتورة موجودة بالفعل في الاستحقاقات' : 'Invoice already exists in dues');

        return redirect()->route('manager.associations.show', $association)->with('success', $msg);
    }

    public function invoice(AssociationDue $due)
    {
        $due->load(['association.property.units', 'owner.user']);

        $isAr            = app()->getLocale() === 'ar';
        $currency        = 'OMR';
        $ownerName       = $due->owner?->user?->name
            ?? ($isAr
                ? ($due->association?->name_ar ?? $due->association?->name_en ?? 'المالك')
                : ($due->association?->name_en ?? $due->association?->name_ar ?? 'Owner'));
        $ownerPhone      = $due->owner?->phone ?? $due->owner?->user?->phone ?? $due->association?->phone_number ?? null;
        $propertyName    = $due->association?->property?->name ?? ($isAr ? 'العقار' : 'Property');
        $associationName = $isAr
            ? ($due->association?->name_ar ?? $due->association?->name_en ?? '')
            : ($due->association?->name_en ?? $due->association?->name_ar ?? '');
        $unitNumbers     = array_values(array_filter((array) ($due->association?->unit_number ?? [])));
        $unitCount       = count($unitNumbers) ?: 1;
        $unitFees        = (array) ($due->association?->unit_fees ?? []);
        $defaultFee      = (float) ($due->association?->monthly_fee_per_unit ?? $due->amount);
        $feeFrequency    = $due->association?->fee_frequency ?? 'monthly';

        $html = view('manager.dues.invoice', compact(
            'due', 'currency', 'ownerName', 'ownerPhone',
            'propertyName', 'associationName', 'unitCount', 'unitNumbers', 'unitFees', 'defaultFee', 'feeFrequency'
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

        $totals = AssociationDue::selectRaw(
            "SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as pending,
             SUM(CASE WHEN status = 'paid'    THEN amount ELSE 0 END) as paid,
             SUM(CASE WHEN status = 'overdue' THEN amount ELSE 0 END) as overdue"
        )->first();

        return view('manager.dues.index', compact('dues', 'totals'));
    }

    public function generate(Request $request, Association $association)
    {
        $request->validate([
            'period_month' => 'required|integer|between:1,12',
            'period_year'  => 'required|integer|min:2020|max:2100',
        ]);

        $month = (int) $request->input('period_month');
        $year  = (int) $request->input('period_year');
        $unitCount = count(array_filter((array) ($association->unit_number ?? []))) ?: 1;
        $amountPerOwner = $association->monthly_fee_per_unit * $unitCount;

        $owners = $association->property->owners;
        if ($owners->isEmpty() && $association->property->owner_id) {
            $owners = collect([$association->property->owner]);
        }

        if ($owners->isEmpty()) {
            return back()->withErrors(['owners' => __('No owners are linked to this property. Add owners to the property first.')]);
        }

        $created = 0;
        foreach ($owners as $owner) {
            $share = $owner->pivot?->ownership_percentage ?? 100;
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

    public function markPending(AssociationDue $due)
    {
        $due->update(['status' => 'pending', 'paid_at' => null]);
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
