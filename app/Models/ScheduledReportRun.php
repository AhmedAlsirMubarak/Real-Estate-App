<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScheduledReportRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'scheduled_report_id',
        'period_start',
        'period_end',
        'generated_at',
        'file_path',
        'status',
        'error_message',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'generated_at' => 'datetime',
    ];

    public function scheduledReport()
    {
        return $this->belongsTo(ScheduledReport::class);
    }
}
