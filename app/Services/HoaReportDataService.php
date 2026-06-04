<?php

namespace App\Services;

use App\Models\Association;
use App\Models\AssociationDue;
use App\Models\Expense;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use Carbon\Carbon;

class HoaReportDataService
{
    public function collectMultiple(array $associationIds, Carbon $from, Carbon $to): array
    {
        $allData = [];
        foreach ($associationIds as $id) {
            $assoc     = Association::with(['property', 'meetings'])->findOrFail($id);
            $allData[] = $this->collect($assoc, $from, $to);
        }

        $isMulti = count($allData) > 1;

        $totalDues     = (float) collect($allData)->sum('totalDues');
        $paidDues      = (float) collect($allData)->sum('paidDues');
        $unpaidDues    = (float) collect($allData)->sum('unpaidDues');
        $totalExpenses = (float) collect($allData)->sum('totalExpenses');

        $aggregate = [
            'count'          => count($allData),
            'totalDues'      => $totalDues,
            'paidDues'       => $paidDues,
            'unpaidDues'     => $unpaidDues,
            'totalExpenses'  => $totalExpenses,
            'netBalance'     => $paidDues - $totalExpenses,
            'collectionRate' => $totalDues > 0 ? round($paidDues / $totalDues * 100, 1) : 0,
            'totalUnits'     => (int) collect($allData)->sum('totalUnits'),
            'occupiedUnits'  => (int) collect($allData)->sum('occupiedUnits'),
            'ownersCount'    => (int) collect($allData)->sum('ownersCount'),
        ];

        return [
            'isMulti'           => $isMulti,
            'associations_data' => $allData,
            'aggregate'         => $aggregate,
        ];
    }

    public function collect(Association $association, Carbon $from, Carbon $to): array
    {
        $association->load([
            'property.units.activeRentalContract.tenant.user',
            'property.owners.user',
            'noObjectionCertificates',
            'noObjectionSaleCertificates',
        ]);
        // Ensure contract_file is available on each rental contract
        $association->property?->units->each(fn ($u) => $u->activeRentalContract?->contract_file);

        $property = $association->property;
        $units    = $property?->units ?? collect();
        $owners   = $property?->owners ?? collect();

        // ── Dues in reporting period ──────────────────────────────────────────
        $dues = AssociationDue::where('association_id', $association->id)
            ->whereBetween('due_date', [$from->toDateString(), $to->toDateString()])
            ->with('owner.user')
            ->orderBy('due_date')
            ->get();

        // ── All outstanding dues ever (for aging + statements) ────────────────
        $allOutstanding = AssociationDue::where('association_id', $association->id)
            ->whereIn('status', ['pending', 'overdue'])
            ->with('owner.user')
            ->orderBy('due_date')
            ->get();

        // ── All dues ever (for owner account statements) ──────────────────────
        $allDues = AssociationDue::where('association_id', $association->id)
            ->with('owner.user')
            ->orderBy('due_date')
            ->get();

        // ── Expenses for the property in period ───────────────────────────────
        $expenses = collect();
        if ($property) {
            $expenses = Expense::where('expensable_type', Property::class)
                ->where('expensable_id', $property->id)
                ->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])
                ->with('invoices')
                ->orderBy('expense_date')
                ->get();
        }

        // ── Maintenance requests through units ────────────────────────────────
        $unitIds     = $units->pluck('id')->all();
        $maintenance = collect();
        if ($unitIds) {
            $maintenance = MaintenanceRequest::whereIn('unit_id', $unitIds)
                ->with(['unit', 'tenant.user'])
                ->orderByDesc('created_at')
                ->get();
        }

        // ── Meetings in period ────────────────────────────────────────────────
        $meetings = $association->meetings()
            ->whereBetween('scheduled_at', [$from, $to])
            ->orderBy('scheduled_at')
            ->get();

        // ── Financial totals ──────────────────────────────────────────────────
        $totalDues      = (float) $dues->sum('amount');
        $paidDues       = (float) $dues->where('status', 'paid')->sum('amount');
        $unpaidDues     = (float) $dues->whereIn('status', ['pending', 'overdue'])->sum('amount');
        $waivedDues     = (float) $dues->where('status', 'waived')->sum('amount');
        $overdueDues    = (float) $dues->where('status', 'overdue')->sum('amount');
        $totalExpenses  = (float) $expenses->sum('amount');
        $netBalance     = $paidDues - $totalExpenses;
        $collectionRate = $totalDues > 0 ? round($paidDues / $totalDues * 100, 1) : 0;

        // ── Unit occupancy ────────────────────────────────────────────────────
        $totalUnits    = $units->count();
        $occupiedUnits = $units->whereIn('status', ['rented', 'sold'])->count();
        $vacantUnits   = $units->where('status', 'available')->count();

        // ── Aging buckets (days past due_date) ────────────────────────────────
        $today = now()->startOfDay();
        $aging = [
            'not_due'  => $allOutstanding->filter(fn ($d) => $d->due_date && $today->lessThanOrEqualTo($d->due_date)),
            '0_30'     => $allOutstanding->filter(function ($d) use ($today) {
                if (! $d->due_date) return false;
                $age = (int) $d->due_date->diffInDays($today, false);
                return $age >= 0 && $age <= 30;
            }),
            '31_60'    => $allOutstanding->filter(function ($d) use ($today) {
                if (! $d->due_date) return false;
                $age = (int) $d->due_date->diffInDays($today, false);
                return $age > 30 && $age <= 60;
            }),
            '61_90'    => $allOutstanding->filter(function ($d) use ($today) {
                if (! $d->due_date) return false;
                $age = (int) $d->due_date->diffInDays($today, false);
                return $age > 60 && $age <= 90;
            }),
            'over_90'  => $allOutstanding->filter(function ($d) use ($today) {
                if (! $d->due_date) return false;
                $age = (int) $d->due_date->diffInDays($today, false);
                return $age > 90;
            }),
        ];

        // ── Per-owner account statements ──────────────────────────────────────
        $ownerStatements = [];
        foreach ($owners as $owner) {
            $ownerAllDues = $allDues->where('owner_id', $owner->id)->sortBy('due_date');
            $runBalance   = 0.0;
            $transactions = [];

            foreach ($ownerAllDues as $due) {
                $runBalance += (float) $due->amount;
                $transactions[] = [
                    'date'        => $due->due_date,
                    'description' => 'اشتراك شهري — ' . $due->periodLabel(),
                    'debit'       => (float) $due->amount,
                    'credit'      => 0.0,
                    'balance'     => $runBalance,
                    'status'      => $due->status,
                    'type'        => 'charge',
                ];
                if (in_array($due->status, ['paid', 'waived']) && $due->paid_at) {
                    $runBalance -= (float) $due->amount;
                    $transactions[] = [
                        'date'        => $due->paid_at,
                        'description' => $due->status === 'waived' ? 'إعفاء' : 'دفعة مستلمة',
                        'debit'       => 0.0,
                        'credit'      => (float) $due->amount,
                        'balance'     => $runBalance,
                        'status'      => 'credit',
                        'type'        => 'credit',
                    ];
                }
            }

            $periodDues   = $allDues->where('owner_id', $owner->id)
                ->whereBetween('due_date', [$from->toDateString(), $to->toDateString()]);

            $ownerStatements[$owner->id] = [
                'owner'        => $owner,
                'transactions' => collect($transactions)->sortBy('date')->values(),
                'total_due'    => (float) $periodDues->sum('amount'),
                'total_paid'   => (float) $periodDues->where('status', 'paid')->sum('amount'),
                'outstanding'  => (float) $periodDues->whereIn('status', ['pending', 'overdue'])->sum('amount'),
                'balance'      => $runBalance,
                'all_total'    => (float) $ownerAllDues->sum('amount'),
                'all_paid'     => (float) $ownerAllDues->where('status', 'paid')->sum('amount'),
                'payment_pct'  => $ownerAllDues->sum('amount') > 0
                    ? round($ownerAllDues->where('status', 'paid')->sum('amount') / $ownerAllDues->sum('amount') * 100, 1)
                    : 0,
            ];
        }

        // ── Unit contribution status ──────────────────────────────────────────
        $unitFeeMap = [];
        foreach ($dues->groupBy('owner_id') as $ownerId => $ownerDues) {
            // We attribute dues equally to units owned by each owner
            $ownerObj = $owners->firstWhere('id', $ownerId);
            if (! $ownerObj) continue;
            $ownerPivot = $ownerObj->pivot->ownership_percentage ?? 100;
            $unitFeeMap[$ownerId] = [
                'owner'       => $ownerObj,
                'total_due'   => (float) $ownerDues->sum('amount'),
                'total_paid'  => (float) $ownerDues->where('status', 'paid')->sum('amount'),
                'outstanding' => (float) $ownerDues->whereIn('status', ['pending', 'overdue'])->sum('amount'),
                'pct'         => $ownerDues->sum('amount') > 0
                    ? round($ownerDues->where('status', 'paid')->sum('amount') / $ownerDues->sum('amount') * 100, 1)
                    : 0,
                'has_overdue' => $ownerDues->where('status', 'overdue')->count() > 0,
                'share_pct'   => $ownerPivot,
            ];
        }

        // ── Monthly financial trends ──────────────────────────────────────────
        $monthlyTrends = [];
        $cursor        = $from->copy()->startOfMonth();
        $toMonth       = $to->copy()->endOfMonth();
        while ($cursor->lte($toMonth)) {
            $mo      = $cursor->month;
            $yr      = $cursor->year;
            $mDues   = $dues->filter(fn ($d) => $d->due_date && $d->due_date->month === $mo && $d->due_date->year === $yr);
            $mExp    = $expenses->filter(fn ($e) => $e->expense_date && $e->expense_date->month === $mo && $e->expense_date->year === $yr);
            $monthlyTrends[] = [
                'label'       => $cursor->locale('ar')->isoFormat('MMM YY'),
                'label_en'    => $cursor->locale('en')->isoFormat('MMM YY'),
                'collected'   => (float) $mDues->where('status', 'paid')->sum('amount'),
                'outstanding' => (float) $mDues->whereIn('status', ['pending', 'overdue'])->sum('amount'),
                'expenses'    => (float) $mExp->sum('amount'),
                'net'         => (float) ($mDues->where('status', 'paid')->sum('amount') - $mExp->sum('amount')),
            ];
            $cursor->addMonth();
        }

        // ── Expense by category ───────────────────────────────────────────────
        $expensesByCategory = $expenses->groupBy('category');

        return compact(
            'association', 'property', 'units', 'owners',
            'totalUnits', 'occupiedUnits', 'vacantUnits',
            'dues', 'allDues', 'allOutstanding',
            'expenses', 'expensesByCategory',
            'maintenance', 'meetings',
            'totalDues', 'paidDues', 'unpaidDues', 'waivedDues', 'overdueDues',
            'totalExpenses', 'netBalance', 'collectionRate',
            'aging', 'ownerStatements', 'unitFeeMap', 'monthlyTrends'
        );
    }
}
