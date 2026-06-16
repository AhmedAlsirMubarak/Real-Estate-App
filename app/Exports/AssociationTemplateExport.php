<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AssociationTemplateExport
{
    private const COLUMNS = [
        'property_code'               => 'Property Code *',
        'name_ar'                     => 'Arabic Name *',
        'name_en'                     => 'English Name',
        'monthly_fee_per_unit'        => 'Monthly Fee per Unit *',
        'established_date'            => 'Established Date',
        'status'                      => 'Status',
        'electricity_account_number'  => 'Electricity Account Number',
        'water_account_number'        => 'Water Account Number',
        'description_ar'              => 'Arabic Description',
        'description_en'              => 'English Description',
    ];

    private const GUIDE_ROWS = [
        ['property_code',              'Property Code *',              'Yes', '',                  'Must match an existing property\'s code, and that property must not already have an association'],
        ['name_ar',                    'Arabic Name *',                'Yes', '',                  'Association name in Arabic'],
        ['name_en',                    'English Name',                 'No',  '',                  'Defaults to the Arabic name if left blank'],
        ['monthly_fee_per_unit',       'Monthly Fee per Unit *',       'Yes', 'Number >= 0',        ''],
        ['established_date',           'Established Date',             'No',  'YYYY-MM-DD',         ''],
        ['status',                     'Status',                       'No',  'active | inactive',  'Defaults to "active" if blank'],
        ['electricity_account_number', 'Electricity Account Number',   'No',  '',                  ''],
        ['water_account_number',       'Water Account Number',         'No',  '',                  ''],
        ['description_ar',             'Arabic Description',           'No',  '',                  ''],
        ['description_en',             'English Description',          'No',  '',                  ''],
    ];

    public function build(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setTitle('Associations Import Template');

        $this->buildDataSheet($spreadsheet);
        $this->buildGuideSheet($spreadsheet);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function buildDataSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->getSheet(0);
        $sheet->setTitle('Associations');

        $colKeys  = array_keys(self::COLUMNS);
        $colCount = count($colKeys);
        $lastCol  = Coordinate::stringFromColumnIndex($colCount);

        foreach ($colKeys as $i => $key) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1) . '1', $key);
        }

        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF9CA3AF']]],
        ]);

        $example = [
            'property_code'               => 'TH-V-001',
            'name_ar'                     => 'جمعية ملاك برج النخيل',
            'name_en'                     => 'Palm Tower Owners Association',
            'monthly_fee_per_unit'        => 50,
            'established_date'            => '2024-01-15',
            'status'                      => 'active',
            'electricity_account_number'  => '',
            'water_account_number'        => '',
            'description_ar'              => '',
            'description_en'              => '',
        ];

        foreach ($colKeys as $i => $key) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1) . '2', $example[$key] ?? '');
        }

        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'font'    => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF1F5C2E']],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD1FAE5']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
        ]);

        foreach ($colKeys as $i => $key) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->getComment($col . '1')->getText()->createTextRun(self::COLUMNS[$key]);
        }

        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getRowDimension(2)->setRowHeight(20);

        $widths = [18, 28, 28, 20, 18, 12, 24, 22, 32, 32];
        foreach ($widths as $i => $w) {
            $sheet->getColumnDimensionByColumn($i + 1)->setWidth($w);
        }

        $sheet->freezePane('A2');
    }

    private function buildGuideSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet(1);
        $sheet->setTitle('Guide');

        $sheet->setCellValue('A1', 'Association Import Guide');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $instructions = [
            '• Row 1 of the Associations sheet contains column keys — do NOT change them.',
            '• Row 2 is a sample row — delete it or overwrite it with your data.',
            '• Enter your associations starting from row 2 (or row 3 if you keep the example).',
            '• Fields marked with * are required.',
            '• property_code must match an existing property that does not already have an association.',
            '• Rows with any validation error are skipped. An error report is shown after upload.',
        ];

        foreach ($instructions as $i => $text) {
            $row = $i + 2;
            $sheet->setCellValue('A' . $row, $text);
            $sheet->mergeCells('A' . $row . ':E' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray([
                'font'      => ['size' => 10, 'color' => ['argb' => 'FF1F2937']],
                'alignment' => ['wrapText' => true],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(16);
        }

        $spacerRow = count($instructions) + 2;
        $sheet->getRowDimension($spacerRow)->setRowHeight(8);

        $tableStart   = $spacerRow + 1;
        $guideHeaders = ['Field (column name)', 'English Label', 'Required?', 'Allowed Values', 'Notes'];
        foreach ($guideHeaders as $i => $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1) . $tableStart, $h);
        }
        $sheet->getStyle('A' . $tableStart . ':E' . $tableStart)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFBFDBFE']]],
        ]);
        $sheet->getRowDimension($tableStart)->setRowHeight(20);

        foreach (self::GUIDE_ROWS as $i => $row) {
            $rowNum  = $tableStart + $i + 1;
            $bgColor = $i % 2 === 0 ? 'FFF8FAFC' : 'FFFFFFFF';
            $req     = $row[2] === 'Yes';

            foreach ($row as $j => $val) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($j + 1) . $rowNum, $val);
            }

            $sheet->getStyle('A' . $rowNum . ':E' . $rowNum)->applyFromArray([
                'font'      => ['size' => 10],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE5E7EB']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);

            if ($req) {
                $sheet->getStyle('C' . $rowNum)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFDC2626']],
                ]);
            }

            $sheet->getRowDimension($rowNum)->setRowHeight(20);
        }

        foreach ([22, 28, 12, 50, 55] as $i => $w) {
            $sheet->getColumnDimensionByColumn($i + 1)->setWidth($w);
        }
    }
}
