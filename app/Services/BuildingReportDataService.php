<?php

namespace App\Services;

use App\Models\CommissionInvoice;
use App\Models\Expense;
use App\Models\MaintenanceRequest;
use App\Models\Owner;
use App\Models\Payment;
use App\Models\Property;
use App\Models\User;
use Carbon\Carbon;

class BuildingReportDataService
{
    public function collect(array $filters, Carbon $from, Carbon $to): array
    {
        // ── Load properties with all eager relations ──────────────────────────
        $propQuery = Property::with([
            'owner.user',
            'employee',
            'units.activeRentalContract.tenant.user',
            'units.rentalContracts.tenant.user',
            'units.rentalContracts.payments',
            'units.maintenanceRequests.assignedEmployee',
            'units.maintenanceRequests.tenant.user',
            'images',
        ])->where(function ($q) use ($filters) {
            if (! empty($filters['section'])) {
                $q->where('section', $filters['section']);
            } else {
                $q->whereIn('section', ['management', 'external', 'hoa']);
            }
        });

        if (! empty($filters['property_id'])) {
            $propQuery->where('id', $filters['property_id']);
        }
        if (! empty($filters['type'])) {
            $propQuery->where('type', $filters['type']);
        }
        if (! empty($filters['owner_id'])) {
            $propQuery->where('owner_id', $filters['owner_id']);
        }
        if (! empty($filters['employee_id'])) {
            $propQuery->where('employee_id', $filters['employee_id']);
        }

        $properties  = $propQuery->orderBy('name_ar')->get();
        $propertyIds = $properties->pluck('id')->all();
        $allUnits    = $properties->flatMap(fn ($p) => $p->units);
        $unitIds     = $allUnits->pluck('id')->all();

        // ── All rental contracts for included units ───────────────────────────
        $allContracts = $allUnits->flatMap(fn ($u) => $u->rentalContracts);
        $activeContracts = $allContracts->where('status', 'active');

        // ── Payments in reporting period (year+month based) ───────────────────
        $allPayments = collect();
        if ($propertyIds) {
            $allPayments = Payment::whereHas('rentalContract.unit', fn ($q) => $q->whereIn('property_id', $propertyIds))
                ->with(['tenant.user', 'rentalContract.unit.property'])
                ->where(function ($q) use ($from, $to) {
                    $q->where(function ($inner) use ($from) {
                        $inner->where('year', '>', $from->year)
                              ->orWhere(fn ($i) => $i->where('year', $from->year)->where('month', '>=', $from->month));
                    })->where(function ($inner) use ($to) {
                        $inner->where('year', '<', $to->year)
                              ->orWhere(fn ($i) => $i->where('year', $to->year)->where('month', '<=', $to->month));
                    });
                })
                ->get();
        }

        // ── Expenses in period ────────────────────────────────────────────────
        $expenses = collect();
        if ($propertyIds) {
            $expenses = Expense::where('expensable_type', Property::class)
                ->whereIn('expensable_id', $propertyIds)
                ->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])
                ->with('invoices')
                ->orderBy('expense_date')
                ->get();
        }

        // ── Maintenance requests ──────────────────────────────────────────────
        $maintenance = collect();
        if ($unitIds) {
            $maintenance = MaintenanceRequest::whereIn('unit_id', $unitIds)
                ->with(['unit.property', 'tenant.user', 'assignedEmployee'])
                ->orderByDesc('created_at')
                ->get();
        }

        // ── Financial aggregates ──────────────────────────────────────────────
        $expectedRevenue  = (float) $allPayments->sum('amount');
        $collectedRevenue = (float) $allPayments->where('status', 'paid')->sum('amount');
        $outstandingRev   = (float) $allPayments->whereIn('status', ['pending', 'overdue'])->sum('amount');
        $totalExpenses    = (float) $expenses->sum('amount');
        $netProfit        = $collectedRevenue - $totalExpenses;
        $collectionRate   = $expectedRevenue > 0 ? round($collectedRevenue / $expectedRevenue * 100, 1) : 0;
        $profitMargin     = $collectedRevenue > 0 ? round($netProfit / $collectedRevenue * 100, 1) : 0;

        // ── Occupancy aggregates ──────────────────────────────────────────────
        $totalUnits       = $allUnits->count();
        $occupiedUnits    = $allUnits->whereIn('status', ['rented', 'sold'])->count();
        $vacantUnits      = $allUnits->where('status', 'available')->count();
        $reservedUnits    = $allUnits->where('status', 'reserved')->count();
        $maintenanceUnits = $allUnits->where('status', 'maintenance')->count();
        $occupancyRate    = $totalUnits > 0 ? round($occupiedUnits / $totalUnits * 100, 1) : 0;

        // ── Contract expiry alerts ────────────────────────────────────────────
        $today = now()->startOfDay();
        $expiringIn30  = $activeContracts->filter(fn ($c) => $c->end_date && $c->end_date->diffInDays($today, false) >= -30 && $c->end_date->gte($today));
        $expiringIn60  = $activeContracts->filter(fn ($c) => $c->end_date && $c->end_date->diffInDays($today, false) >= -60 && $c->end_date->diffInDays($today, false) < -30);
        $expiringIn90  = $activeContracts->filter(fn ($c) => $c->end_date && $c->end_date->diffInDays($today, false) >= -90 && $c->end_date->diffInDays($today, false) < -60);
        $expiredContracts = $activeContracts->filter(fn ($c) => $c->end_date && $c->end_date->lt($today));

        // ── Aging (outstanding payments) ──────────────────────────────────────
        $outstanding = $allPayments->whereIn('status', ['pending', 'overdue']);
        $aging = [
            'current' => $outstanding->filter(fn ($p) => $p->status === 'pending'
                && Carbon::create($p->year, $p->month)->endOfMonth()->gte($today)),
            '0_30'    => $outstanding->filter(function ($p) use ($today) {
                $due = Carbon::create($p->year, $p->month)->endOfMonth();
                $d   = (int) $due->diffInDays($today, false);
                return $d >= 0 && $d <= 30;
            }),
            '31_60'   => $outstanding->filter(function ($p) use ($today) {
                $due = Carbon::create($p->year, $p->month)->endOfMonth();
                $d   = (int) $due->diffInDays($today, false);
                return $d > 30 && $d <= 60;
            }),
            '61_90'   => $outstanding->filter(function ($p) use ($today) {
                $due = Carbon::create($p->year, $p->month)->endOfMonth();
                $d   = (int) $due->diffInDays($today, false);
                return $d > 60 && $d <= 90;
            }),
            'over_90' => $outstanding->filter(function ($p) use ($today) {
                $due = Carbon::create($p->year, $p->month)->endOfMonth();
                return (int) $due->diffInDays($today, false) > 90;
            }),
        ];

        // ── Monthly revenue trends ────────────────────────────────────────────
        $monthlyTrends = [];
        $cursor = $from->copy()->startOfMonth();
        while ($cursor->lte($to->copy()->endOfMonth())) {
            $mo = $cursor->month;
            $yr = $cursor->year;
            $mPay = $allPayments->filter(fn ($p) => $p->month === $mo && $p->year === $yr);
            $mExp = $expenses->filter(fn ($e) => $e->expense_date && $e->expense_date->month === $mo && $e->expense_date->year === $yr);
            $mCollected = (float) $mPay->where('status', 'paid')->sum('amount');
            $mExpenses  = (float) $mExp->sum('amount');
            $monthlyTrends[] = [
                'label'       => $cursor->locale('ar')->isoFormat('MMM YY'),
                'label_en'    => $cursor->locale('en')->isoFormat('MMM YY'),
                'expected'    => (float) $mPay->sum('amount'),
                'collected'   => $mCollected,
                'outstanding' => (float) $mPay->whereIn('status', ['pending', 'overdue'])->sum('amount'),
                'expenses'    => $mExpenses,
                'net'         => $mCollected - $mExpenses,
            ];
            $cursor->addMonth();
        }

        // ── Per-property profitability ────────────────────────────────────────
        $propertyProfitability = [];
        foreach ($properties as $prop) {
            $pPay = $allPayments->filter(fn ($p) => $p->rentalContract?->unit?->property_id === $prop->id);
            $pExp = $expenses->where('expensable_id', $prop->id);
            $pCol = (float) $pPay->where('status', 'paid')->sum('amount');
            $pExpTotal = (float) $pExp->sum('amount');
            $propertyProfitability[$prop->id] = [
                'property'       => $prop,
                'collected'      => $pCol,
                'outstanding'    => (float) $pPay->whereIn('status', ['pending', 'overdue'])->sum('amount'),
                'expenses'       => $pExpTotal,
                'net'            => $pCol - $pExpTotal,
                'margin'         => $pCol > 0 ? round(($pCol - $pExpTotal) / $pCol * 100, 1) : 0,
            ];
        }

        // ── Vacancy analysis (vacant units with estimate days vacant) ─────────
        $vacantUnitsList = $allUnits->where('status', 'available')->map(function ($unit) {
            $lastContract = $unit->rentalContracts->sortByDesc('end_date')->first();
            $vacantSince  = $lastContract?->end_date ?? $unit->created_at;
            return [
                'unit'         => $unit,
                'vacant_since' => $vacantSince,
                'days_vacant'  => $vacantSince ? (int) $vacantSince->diffInDays(now()) : null,
            ];
        })->sortByDesc('days_vacant')->values();

        // ── Upcoming alerts ───────────────────────────────────────────────────
        $alerts = [];

        // Expiring contracts
        foreach ($expiringIn30 as $c) {
            $alerts[] = [
                'type'     => 'contract_expiry',
                'priority' => 'high',
                'label_ar' => 'عقد يقترب من الانتهاء',
                'label_en' => 'Contract expiring soon',
                'detail'   => ($c->tenant?->user?->name ?? '—') . ' — ' . ($c->unit?->unit_number ?? '—'),
                'date'     => $c->end_date,
            ];
        }

        // Overdue payments
        foreach ($outstanding->where('status', 'overdue')->take(10) as $p) {
            $alerts[] = [
                'type'     => 'overdue_payment',
                'priority' => 'high',
                'label_ar' => 'إيجار متأخر',
                'label_en' => 'Overdue rent',
                'detail'   => ($p->tenant?->user?->name ?? '—') . ' — ' . $p->monthName() . '/' . $p->year,
                'date'     => Carbon::create($p->year, $p->month)->endOfMonth(),
            ];
        }

        // Open maintenance
        foreach ($maintenance->whereIn('status', ['pending'])->take(10) as $mr) {
            $alerts[] = [
                'type'     => 'maintenance',
                'priority' => $mr->priority === 'urgent' ? 'high' : ($mr->priority === 'high' ? 'high' : 'medium'),
                'label_ar' => 'طلب صيانة معلق',
                'label_en' => 'Pending maintenance',
                'detail'   => $mr->title . ' — ' . ($mr->unit?->unit_number ?? '—'),
                'date'     => $mr->created_at,
            ];
        }

        usort($alerts, fn ($a, $b) => ['high' => 0, 'medium' => 1, 'low' => 2][$a['priority']] <=> ['high' => 0, 'medium' => 1, 'low' => 2][$b['priority']]);

        // ── Commission invoices in period ─────────────────────────────────────
        $commissionInvoices = collect();
        if ($propertyIds) {
            $commissionInvoices = CommissionInvoice::whereIn('property_id', $propertyIds)
                ->whereBetween('invoice_date', [$from->toDateString(), $to->toDateString()])
                ->with('property')
                ->orderBy('invoice_date')
                ->get();
        }
        $totalCommissions = (float) $commissionInvoices->sum('commission_amount');

        // ── Filter helpers ────────────────────────────────────────────────────
        $allOwners    = Owner::with('user')->get();
        $allEmployees = User::role('employee')->get();

        return compact(
            'properties', 'propertyIds', 'allUnits', 'allContracts', 'activeContracts',
            'allPayments', 'expenses', 'maintenance',
            'expectedRevenue', 'collectedRevenue', 'outstandingRev',
            'totalExpenses', 'netProfit', 'collectionRate', 'profitMargin',
            'totalUnits', 'occupiedUnits', 'vacantUnits', 'reservedUnits',
            'maintenanceUnits', 'occupancyRate',
            'expiringIn30', 'expiringIn60', 'expiringIn90', 'expiredContracts',
            'aging', 'monthlyTrends', 'propertyProfitability',
            'vacantUnitsList', 'alerts',
            'commissionInvoices', 'totalCommissions',
            'allOwners', 'allEmployees'
        );
    }
}
