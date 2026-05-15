<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssociationMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'association_id',
        'title_ar',
        'title_en',
        'scheduled_at',
        'location_ar',
        'location_en',
        'agenda_ar',
        'agenda_en',
        'minutes_ar',
        'minutes_en',
        'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function association()
    {
        return $this->belongsTo(Association::class);
    }

    public function getTitleAttribute(): string
    {
        $locale = app()->getLocale() === 'en' ? 'en' : 'ar';
        return $this->attributes["title_{$locale}"] ?: ($this->attributes['title_ar'] ?? '');
    }

    public function getLocationAttribute(): ?string
    {
        $locale = app()->getLocale() === 'en' ? 'en' : 'ar';
        return $this->attributes["location_{$locale}"] ?? null;
    }

    public function getAgendaAttribute(): ?string
    {
        $locale = app()->getLocale() === 'en' ? 'en' : 'ar';
        return $this->attributes["agenda_{$locale}"] ?? null;
    }

    public function getMinutesAttribute(): ?string
    {
        $locale = app()->getLocale() === 'en' ? 'en' : 'ar';
        return $this->attributes["minutes_{$locale}"] ?? null;
    }

    public function statusLabel(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return match ($this->status) {
            'scheduled' => $isAr ? 'مجدول'  : 'Scheduled',
            'completed' => $isAr ? 'مُنجز'   : 'Completed',
            'cancelled' => $isAr ? 'ملغى'   : 'Cancelled',
            default     => $this->status,
        };
    }
}
