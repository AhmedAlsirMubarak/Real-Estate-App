<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevelopmentExpense extends Model
{
    protected $fillable = [
        'development_project_id', 'category', 'item_name',
        'quantity', 'unit', 'unit_cost', 'amount', 'description', 'expense_date',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
        'quantity'     => 'decimal:2',
        'unit_cost'    => 'decimal:2',
    ];

    public static function categories(): array
    {
        return [
            'construction', 'manpower', 'materials', 'contractor_fees',
            'permits', 'equipment_rental', 'design_engineering', 'utilities',
        ];
    }

    public static function categoryLabels(bool $isAr = false): array
    {
        if ($isAr) {
            return [
                'construction'       => 'أعمال البناء والإنشاء',
                'manpower'           => 'العمالة والأيدي العاملة',
                'materials'          => 'المواد والمستلزمات',
                'contractor_fees'    => 'رسوم المقاولين والمقاولين الفرعيين',
                'permits'            => 'التصاريح والرسوم القانونية',
                'equipment_rental'   => 'إيجار المعدات',
                'design_engineering' => 'رسوم التصميم والهندسة',
                'utilities'          => 'المرافق أثناء الإنشاء',
            ];
        }
        return [
            'construction'       => 'Building & Construction',
            'manpower'           => 'Manpower & Labour',
            'materials'          => 'Materials & Supplies',
            'contractor_fees'    => 'Contractor & Subcontractor Fees',
            'permits'            => 'Permits & Legal Fees',
            'equipment_rental'   => 'Equipment Rental',
            'design_engineering' => 'Design & Engineering Fees',
            'utilities'          => 'Utilities (Construction)',
        ];
    }

    public function categoryLabel(bool $isAr = false): string
    {
        return self::categoryLabels($isAr)[$this->category] ?? $this->category;
    }

    public function project()
    {
        return $this->belongsTo(DevelopmentProject::class, 'development_project_id');
    }
}
