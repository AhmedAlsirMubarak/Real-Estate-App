<?php

namespace App\Services;

use App\Models\AssociationDue;
use App\Models\Expense;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\Property;
use App\Models\ScheduledReport;
use App\Models\ScheduledReportRun;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

class ScheduledReportRunner
{
    public function runDue(): array
    {
        $results = ['generated' => 0, 'failed' => 0];

        ScheduledReport::due()->with(['property', 'association.property'])->get()
            ->each(function (ScheduledReport $report) use (&$results) {
                try {
                    $this->generate($report);
                    $report->advanceSchedule();
                    $results['generated']++;
                } catch (\Throwable $e) {
                    Log::error("Scheduled report {$report->id} failed: {$e->getMessage()}");
                    ScheduledReportRun::create([
                        'scheduled_report_id' => $report->id,
                        'period_start'        => $report->last_run_at ?? now()->subMonths($report->period_months)->toDateString(),
                        'period_end'          => now()->toDateString(),
                        'generated_at'        => now(),
                        'status'              => 'failed',
                        'error_message'       => $e->getMessage(),
                    ]);
                    $results['failed']++;
                }
            });

        return $results;
    }

    public function generate(ScheduledReport $report): ScheduledReportRun
    {
        $end   = CarbonImmutable::now()->startOfDay();
        $start = $end->subMonthsNoOverflow($report->period_months);

        $data = $report->section === ScheduledReport::SECTION_HOA
            ? $this->collectHoaData($report, $start, $end)
            : $this->collectManagementData($report, $start, $end);

        $prevLocale = app()->getLocale();
        app()->setLocale('ar');

        $html = view('manager.scheduled-reports.pdf', [
            'report' => $report,
            'data'   => $data,
            'start'  => $start,
            'end'    => $end,
        ])->render();

        app()->setLocale($prevLocale);

        if (! is_dir(storage_path('app/mpdf'))) {
            mkdir(storage_path('app/mpdf'), 0755, true);
        }
        if (! Storage::disk('local')->exists('scheduled-reports')) {
            Storage::disk('local')->makeDirectory('scheduled-reports');
        }

        $mpdf = new Mpdf([
            'mode'         => 'utf-8',
            'format'       => 'A4',
            'orientation'  => 'P',
            'default_font' => 'dejavusans',
            'tempDir'      => storage_path('app/mpdf'),
        ]);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);

        $relativePath = 'scheduled-reports/' . $report->id . '-' . now()->format('Y-m-d_His') . '.pdf';
        Storage::disk('local')->put($relativePath, $mpdf->Output('', 'S'));

        return ScheduledReportRun::create([
            'scheduled_report_id' => $report->id,
            'period_start'        => $start->toDateString(),
            'period_end'          => $end->toDateString(),
            'generated_at'        => now(),
            'file_path'           => $relativePath,
            'status'              => 'success',
        ]);
    }

    private function collectManagementData(ScheduledReport $report, CarbonImmutable $start, CarbonImmutable $end): array
    {
        $propertyIds = $this->resolvePropertyIds($report, ['management', 'external']);

        $properties = Property::whereIn('id', $propertyIds)
            ->with([
                'owner.user',
                'employee',
                'units'                                   => fn ($q) => $q->orderBy('unit_number'),
                'units.activeRentalContract.tenant.user',
                'units.activeRentalContract.tenant',
                'units.activeSaleContract.buyer.user',
            ])
            ->get();

        $allPayments = Payment::whereHas('rentalContract.unit', function ($q) use ($propertyIds) {
            $q->whereIn('property_id', $propertyIds);
        })->whereBetween('created_at', [$start, $end])
          ->with(['tenant.user', 'rentalContract.unit'])
          ->orderBy('year')->orderBy('month')
          ->get();

        $allExpenses = Expense::where('expensable_type', Property::class)
            ->whereIn('expensable_id', $propertyIds)
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('expense_date')
            ->get();

        $allMaintenance = MaintenanceRequest::whereHas('unit', function ($q) use ($propertyIds) {
            $q->whereIn('property_id', $propertyIds);
        })->whereBetween('created_at', [$start, $end])
          ->with(['tenant.user', 'unit'])
          ->get();

        $paymentsByProperty    = $allPayments->groupBy(fn ($p) => $p->rentalContract?->unit?->property_id);
        $expensesByProperty    = $allExpenses->groupBy('expensable_id');
        $maintenanceByProperty = $allMaintenance->groupBy(fn ($m) => $m->unit?->property_id);

        return [
            // ── Aggregates (used for summary cards) ──
            'properties_count'        => count($propertyIds),
            'total_revenue'           => $allPayments->where('status', 'paid')->sum('amount'),
            'pending_payments'        => $allPayments->where('status', 'pending')->count(),
            'overdue_payments'        => $allPayments->where('status', 'overdue')->count(),
            'total_expenses'          => $allExpenses->sum('amount'),
            'net_income'              => $allPayments->where('status', 'paid')->sum('amount') - $allExpenses->sum('amount'),
            'maintenance_total'       => $allMaintenance->count(),
            'maintenance_done'        => $allMaintenance->where('status', 'completed')->count(),
            // ── Full data for detailed sections ──
            'properties'              => $properties,
            'payments_by_property'    => $paymentsByProperty,
            'expenses_by_property'    => $expensesByProperty,
            'maintenance_by_property' => $maintenanceByProperty,
        ];
    }

    private function collectHoaData(ScheduledReport $report, CarbonImmutable $start, CarbonImmutable $end): array
    {
        $associationIds = $report->association_id
            ? [$report->association_id]
            : \App\Models\Association::query()
                ->when($report->property_id, fn ($q) => $q->where('property_id', $report->property_id))
                ->pluck('id')
                ->all();

        $associations = \App\Models\Association::whereIn('id', $associationIds)
            ->with([
                'property',
                'dues' => fn ($q) => $q
                    ->whereBetween('due_date', [$start->toDateString(), $end->toDateString()])
                    ->with('owner.user')
                    ->orderBy('due_date'),
                'meetings' => fn ($q) => $q
                    ->whereBetween('scheduled_at', [$start, $end])
                    ->orderBy('scheduled_at'),
            ])
            ->get();

        $allDues = $associations->flatMap(fn ($a) => $a->dues);

        $propertyIds = $associations->pluck('property_id')->filter()->all();
        $allExpenses = Expense::where('expensable_type', Property::class)
            ->whereIn('expensable_id', $propertyIds)
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('expense_date')
            ->get();

        $expensesByProperty = $allExpenses->groupBy('expensable_id');

        return [
            // ── Aggregates ──
            'associations_count' => count($associationIds),
            'dues_total'         => $allDues->sum('amount'),
            'dues_paid'          => $allDues->where('status', 'paid')->sum('amount'),
            'dues_unpaid'        => $allDues->whereIn('status', ['pending', 'overdue'])->sum('amount'),
            'dues_waived'        => $allDues->where('status', 'waived')->sum('amount'),
            'meetings_count'     => $associations->sum(fn ($a) => $a->meetings->count()),
            'total_expenses'     => $allExpenses->sum('amount'),
            'balance'            => $allDues->where('status', 'paid')->sum('amount') - $allExpenses->sum('amount'),
            // ── Full objects ──
            'associations'          => $associations,
            'expenses_by_property'  => $expensesByProperty,
        ];
    }

    private function resolvePropertyIds(ScheduledReport $report, array $sections): array
    {
        if ($report->property_id) {
            return [$report->property_id];
        }

        return Property::whereIn('section', $sections)->pluck('id')->all();
    }
}
