<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScheduledReport extends Model
{
    use HasFactory;

    public const SECTION_HOA = 'hoa';
    public const SECTION_MANAGEMENT = 'management';

    public const DEFAULT_PERIODS_HOA        = [3, 7, 12];
    public const DEFAULT_PERIODS_MANAGEMENT = [3, 6, 12];

    protected $fillable = [
        'name',
        'section',
        'property_id',
        'association_id',
        'period_months',
        'next_run_at',
        'last_run_at',
        'recipients',
        'status',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'next_run_at'   => 'date',
        'last_run_at'   => 'date',
        'recipients'    => 'array',
        'period_months' => 'integer',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function association()
    {
        return $this->belongsTo(Association::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function runs()
    {
        return $this->hasMany(ScheduledReportRun::class);
    }

    public function isDue(): bool
    {
        return $this->status === 'active'
            && $this->next_run_at
            && $this->next_run_at->lte(now()->startOfDay());
    }

    public function advanceSchedule(): void
    {
        $this->last_run_at = now()->toDateString();
        $this->next_run_at = $this->next_run_at
            ? $this->next_run_at->copy()->addMonthsNoOverflow($this->period_months)
            : now()->addMonthsNoOverflow($this->period_months);
        $this->save();
    }

    public function sectionLabel(): string
    {
        if (app()->getLocale() === 'en') {
            return match ($this->section) {
                self::SECTION_HOA        => 'Owners Association',
                self::SECTION_MANAGEMENT => 'Building Management',
                default                  => $this->section,
            };
        }

        return match ($this->section) {
            self::SECTION_HOA        => 'جمعية الملاك',
            self::SECTION_MANAGEMENT => 'إدارة المباني',
            default                  => $this->section,
        };
    }

    public function statusLabel(): string
    {
        if (app()->getLocale() === 'en') {
            return $this->status === 'active' ? 'Active' : 'Paused';
        }
        return $this->status === 'active' ? 'نشط' : 'متوقف';
    }

    public function scopeDue($query)
    {
        return $query->where('status', 'active')
            ->whereDate('next_run_at', '<=', now()->toDateString());
    }
}
