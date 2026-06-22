<?php

namespace App\Exports;

use App\Models\Association;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AssociationExport
{
    public function build(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setTitle('Associations Export');

        $sheet = $spreadsheet->getSheet(0);
        $sheet->setTitle('Associations');

        $headers = [
            'ID', 'Property Code', 'Property Name',
            'Name (AR)', 'Name (EN)',
            'Monthly Fee / Unit', 'Established Date', 'Status',
            'Electricity Account', 'Water Account',
            'Owners Count', 'Units Count',
            'Description (AR)', 'Description (EN)',
            'Created At',
        ];

        $colCount = count($headers);
        $lastCol  = Coordinate::stringFromColumnIndex($colCount);

        foreach ($headers as $i => $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1) . '1', $h);
        }

        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF9CA3AF']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        $associations = Association::with(['property.owners', 'property.units'])->latest()->get();
        $rowNum = 2;

        foreach ($associations as $assoc) {
            $row = [
                $assoc->id,
                $assoc->property?->code ?? '',
                $assoc->property?->name ?? '',
                $assoc->name_ar ?? '',
                $assoc->name_en ?? '',
                $assoc->monthly_fee_per_unit,
                $assoc->established_date?->format('Y-m-d') ?? '',
                $assoc->status ?? 'active',
                $assoc->electricity_account_number ?? '',
                $assoc->water_account_number ?? '',
                $assoc->property?->owners?->count() ?? 0,
                $assoc->property?->units?->count() ?? 0,
                $assoc->description_ar ?? '',
                $assoc->description_en ?? '',
                $assoc->created_at?->format('Y-m-d') ?? '',
            ];

            foreach ($row as $j => $val) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($j + 1) . $rowNum, $val);
            }

            $bgColor = $rowNum % 2 === 0 ? 'FFF8FAFC' : 'FFFFFFFF';
            $sheet->getStyle('A' . $rowNum . ':' . $lastCol . $rowNum)->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE5E7EB']]],
            ]);
            $sheet->getRowDimension($rowNum)->setRowHeight(18);

            $rowNum++;
        }

        $widths = [6, 14, 24, 26, 26, 18, 16, 12, 22, 20, 14, 12, 30, 30, 14];
        foreach ($widths as $i => $w) {
            $sheet->getColumnDimensionByColumn($i + 1)->setWidth($w);
        }

        $sheet->freezePane('A2');

        return $spreadsheet;
    }
}
