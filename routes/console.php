<?php

use App\Services\ScheduledReportRunner;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('reports:run-scheduled', function (ScheduledReportRunner $runner) {
    $result = $runner->runDue();
    $this->info("Generated: {$result['generated']}, Failed: {$result['failed']}");
})->purpose('Generate any scheduled reports that are due (HOA + Building Mgmt)');

Schedule::command('reports:run-scheduled')->dailyAt('02:00');
