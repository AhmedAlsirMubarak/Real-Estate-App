<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DevelopmentDocument extends Model
{
    protected $fillable = [
        'development_project_id', 'type', 'title', 'file_path', 'original_name',
    ];

    public static function types(): array
    {
        return ['contract', 'invoice', 'other'];
    }

    public static function typeLabels(bool $isAr = false): array
    {
        if ($isAr) {
            return ['contract' => "\u{639}\u{642}\u{62F}", 'invoice' => "\u{641}\u{627}\u{62A}\u{648}\u{631}\u{629}", 'other' => "\u{623}\u{62E}\u{631}\u{649}"];
        }
        return ['contract' => 'Contract', 'invoice' => 'Invoice', 'other' => 'Other'];
    }

    public function typeLabel(bool $isAr = false): string
    {
        return self::typeLabels($isAr)[$this->type] ?? $this->type;
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function extension(): string
    {
        return strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
    }

    public function isPdf(): bool
    {
        return $this->extension() === 'pdf';
    }

    public function project()
    {
        return $this->belongsTo(DevelopmentProject::class, 'development_project_id');
    }
}
