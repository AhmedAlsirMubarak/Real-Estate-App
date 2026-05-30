<?php

namespace App\Console\Commands;

use App\Models\RentalContract;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckContractExpiry extends Command
{
    protected $signature = 'contracts:check-expiry {--dry-run : Log actions without sending}';
    protected $description = 'Mark expired contracts and send WhatsApp reminders for contracts expiring soon';

    // Days before expiry at which reminders are sent
    private const REMINDER_DAYS = [30, 14, 7, 1];

    public function handle(WhatsAppService $wa): int
    {
        $dryRun = $this->option('dry-run');
        $today  = Carbon::today();

        // ── 1. Mark expired contracts ────────────────────────────────────────
        $expired = RentalContract::where('status', 'active')
            ->where('end_date', '<', $today)
            ->with(['unit'])
            ->get();

        foreach ($expired as $contract) {
            if (!$dryRun) {
                $contract->update(['status' => 'expired']);
                $contract->unit?->update(['status' => 'available']);
            }
            $this->info("[EXPIRED] Contract #{$contract->id} — end_date: {$contract->end_date->toDateString()}" . ($dryRun ? ' (dry-run)' : ''));
        }

        // ── 2. Send reminders for contracts expiring soon ────────────────────
        $active = RentalContract::where('status', 'active')
            ->whereBetween('end_date', [$today, $today->copy()->addDays(max(self::REMINDER_DAYS))])
            ->with(['tenant.user'])
            ->get();

        $sent = 0;
        foreach ($active as $contract) {
            $daysLeft = (int) $today->diffInDays($contract->end_date);

            if (!in_array($daysLeft, self::REMINDER_DAYS, true)) {
                continue;
            }

            $phone   = $contract->tenant?->user?->phone ?? $contract->tenant?->phone;
            $tenantName = $contract->tenant?->user?->name ?? 'المستأجر';
            $message = $this->buildMessage($tenantName, $contract->end_date->format('Y/m/d'), $daysLeft);

            if ($dryRun) {
                $this->line("[DRY-RUN] Would send reminder to {$phone} — {$daysLeft}d left");
                continue;
            }

            if ($phone && $wa->isEnabled()) {
                $result = $wa->send($phone, $message);
                $status = $result ? 'sent' : 'failed';
            } else {
                $status = 'skipped (no API or phone)';
            }

            $this->info("[REMINDER-{$daysLeft}d] Contract #{$contract->id} ({$tenantName}) → {$status}");
            $sent++;
        }

        $this->info("Done. Expired: {$expired->count()}, Reminders processed: {$sent}.");
        return self::SUCCESS;
    }

    private function buildMessage(string $name, string $endDate, int $days): string
    {
        if ($days === 1) {
            return "السلام عليكم {$name}،\nتذكير: عقد إيجارك ينتهي *غداً* بتاريخ {$endDate}.\nيرجى التواصل مع المكتب لتجديد العقد.\nشكراً لكم.";
        }

        return "السلام عليكم {$name}،\nنود تذكيركم بأن عقد الإيجار سينتهي خلال *{$days} يوماً* بتاريخ {$endDate}.\nيرجى التواصل مع مكتبنا في أقرب وقت لتجديد العقد أو ترتيب الإخلاء.\nشكراً لكم.";
    }
}
