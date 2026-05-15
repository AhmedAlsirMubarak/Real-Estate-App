<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Installment;
use App\Models\SaleContract;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user  = auth()->user();
        $buyer = $user->buyer;

        if (! $buyer) {
            abort(403, 'ليس لديك ملف مشترٍ مرتبط بحسابك.');
        }

        $contracts = $buyer->saleContracts()
            ->with(['unit.property', 'installments'])
            ->latest()
            ->get();

        $activeContract = $contracts->where('status', 'active')->first();

        $totalInstallments  = 0;
        $paidInstallments   = 0;
        $pendingInstallments = 0;
        $overdueInstallments = 0;
        $totalPaid          = 0;
        $totalRemaining     = 0;

        foreach ($contracts as $contract) {
            $totalInstallments  += $contract->installments->count();
            $paidInstallments   += $contract->installments->where('status', 'paid')->count();
            $pendingInstallments += $contract->installments->where('status', 'pending')->count();
            $overdueInstallments += $contract->installments->where('status', 'overdue')->count();
            $totalPaid          += $contract->installments->where('status', 'paid')->sum('amount');
            $totalRemaining     += $contract->installments->whereIn('status', ['pending', 'overdue'])->sum('amount');
        }

        $stats = [
            'total_contracts'     => $contracts->count(),
            'total_installments'  => $totalInstallments,
            'paid_installments'   => $paidInstallments,
            'pending_installments'=> $pendingInstallments,
            'overdue_installments'=> $overdueInstallments,
            'total_paid'          => $totalPaid,
            'total_remaining'     => $totalRemaining,
        ];

        // Next due installment across all active contracts
        $nextInstallment = Installment::whereHas('saleContract', fn ($q) => $q->where('buyer_id', $buyer->id))
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->with('saleContract.unit.property')
            ->first();

        $recentInstallments = Installment::whereHas('saleContract', fn ($q) => $q->where('buyer_id', $buyer->id))
            ->with('saleContract.unit.property')
            ->latest('due_date')
            ->take(8)
            ->get();

        return view('buyer.dashboard', compact('buyer', 'contracts', 'activeContract', 'stats', 'nextInstallment', 'recentInstallments'));
    }
}
