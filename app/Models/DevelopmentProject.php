<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevelopmentProject extends Model
{
    protected $fillable = [
        'name', 'type', 'location', 'status', 'total_budget',
        'category_budgets', 'progress_percentage', 'start_date',
        'estimated_completion_date', 'project_manager_name', 'notes',
    ];

    protected $casts = [
        'start_date'                => 'date',
        'estimated_completion_date' => 'date',
        'total_budget'              => 'decimal:2',
        'category_budgets'          => 'array',
        'progress_percentage'       => 'integer',
    ];

    public static function phases(): array
    {
        return ['planning', 'foundation', 'structure', 'finishing', 'handover', 'completed'];
    }

    public function phaseIndex(): int
    {
        return (int) array_search($this->status, self::phases());
    }

    public function contractors()
    {
        return $this->hasMany(DevelopmentContractor::class);
    }

    public function expenses()
    {
        return $this->hasMany(DevelopmentExpense::class);
    }

    public function documents()
    {
        return $this->hasMany(DevelopmentDocument::class);
    }

    public function categoryItems(string $category): array
    {
        $val = $this->category_budgets[$category] ?? null;
        if (is_array($val) && isset($val[0]) && is_array($val[0])) {
            return $val;
        }
        return [];
    }

    public function totalSpent(): float
    {
        if (empty($this->category_budgets)) return 0;
        $total = 0;
        foreach ($this->category_budgets as $val) {
            $total += is_array($val) ? (float) collect($val)->sum('amount') : (float) $val;
        }
        return $total;
    }

    public function remaining(): float
    {
        return max(0, (float) $this->total_budget - $this->totalSpent());
    }

    public function budgetUsedPercent(): int
    {
        if ((float) $this->total_budget <= 0) return 0;
        return min(100, (int) round(($this->totalSpent() / (float) $this->total_budget) * 100));
    }

    public function daysToCompletion(): int
    {
        return (int) now()->diffInDays($this->estimated_completion_date, false);
    }

    public function budgetHealth(): string
    {
        $spent    = $this->budgetUsedPercent();
        $progress = $this->progress_percentage;
        if ($spent > $progress + 15) return 'over_budget';
        if ($spent > $progress + 5)  return 'at_risk';
        return 'on_track';
    }

    public function categoryBudget(string $category): float
    {
        $val = $this->category_budgets[$category] ?? 0;
        if (is_array($val)) {
            return (float) collect($val)->sum('amount');
        }
        return (float) $val;
    }

    public function typeLabel(bool $isAr = false): string
    {
        $labels = $isAr
            ? ['residential' => "\u{633}\u{643}\u{646}\u{64A}", 'commercial' => "\u{62A}\u{62C}\u{627}\u{631}\u{64A}", 'mixed' => "\u{645}\u{62A}\u{639}\u{62F}\u{62F}"]
            : ['residential' => 'Residential', 'commercial' => 'Commercial', 'mixed' => 'Mixed Use'];
        return $labels[$this->type] ?? $this->type;
    }

    public function statusLabel(bool $isAr = false): string
    {
        $ar = [
            'planning'   => "\u{62A}\u{62E}\u{637}\u{64A}\u{637}",
            'foundation' => "\u{623}\u{633}\u{627}\u{633}\u{627}\u{62A}",
            'structure'  => "\u{647}\u{64A}\u{643}\u{644}",
            'finishing'  => "\u{62A}\u{634}\u{637}\u{64A}\u{628}",
            'handover'   => "\u{62A}\u{633}\u{644}\u{64A}\u{645}",
            'completed'  => "\u{645}\u{643}\u{62A}\u{645}\u{644}",
        ];
        $en = [
            'planning'   => 'Planning',
            'foundation' => 'Foundation',
            'structure'  => 'Structure',
            'finishing'  => 'Finishing',
            'handover'   => 'Handover',
            'completed'  => 'Completed',
        ];
        return ($isAr ? $ar : $en)[$this->status] ?? $this->status;
    }
}
