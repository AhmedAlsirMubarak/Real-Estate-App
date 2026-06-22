<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class CustomerTemplateExport
{
    private const COLUMNS = [
        'name'          => 'Customer Name *',
        'mobile'        => 'Mobile',
        'email'         => 'Email',
        'location'      => 'Desired Location',
        'property_type' => 'Property Type *',
        'purpose'       => 'Purpose *',
        'min_budget'    => 'Min Budget',
        'max_budget'    => 'Max Budget',
        'bedrooms'      => 'Bedrooms',
        'status'        => 'Status',
        'notes'         => 'Notes',
    ];

    private const GUIDE_ROWS = [
        ['name',          'Customer Name *',   'Yes', '',                                                  'Full name of the customer'],
        ['mobile',        'Mobile',             'No',  '',                                                  'Used for the WhatsApp quick-contact button'],
        ['email',         'Email',              'No',  'Valid email address',                               ''],
        ['location',      'Desired Location',   'No',  '',                                                  'City or area the customer is looking in'],
        ['property_type', 'Property Type *',    'Yes', 'any | apartment_building | villa | farm | chalet', 'Must match exactly'],
        ['purpose',       'Purpose *',          'Yes', 'rent | sale | both',                               'Must match exactly'],
        ['min_budget',    'Min Budget',         'No',  'Number >= 0',                                      ''],
        ['max_budget',    'Max Budget',         'No',  'Number >= 0',                                      ''],
        ['bedrooms',      'Bedrooms',           'No',  'Integer >= 0',                                     ''],
        ['status',        'Status',             'No',  'new | contacted | interested | closed',            'Defaults to "new" if blank'],
        ['notes',         'Notes',              'No',  '',                                                  ''],
    ];

    public function build(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setTitle('Customers Import Template');

        $this->buildDataSheet($spreadsheet);
        $this->buildGuideSheet($spreadsheet);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function buildDataSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->getSheet(0);
        $sheet->setTitle('Customers');

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
            'name'          => 'Ahmed Al Saidi',
            'mobile'        => '99123456',
            'email'         => 'ahmed@example.com',
            'location'      => 'Bowsher',
            'property_type' => 'villa',
            'purpose'       => 'sale',
            'min_budget'    => 80000,
            'max_budget'    => 120000,
            'bedrooms'      => 4,
            'status'        => 'new',
            'notes'         => '',
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

        $widths = [24, 16, 26, 20, 22, 12, 14, 14, 12, 14, 32];
        foreach ($widths as $i => $w) {
            $sheet->getColumnDimensionByColumn($i + 1)->setWidth($w);
        }

        $sheet->freezePane('A2');
    }

    private function buildGuideSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet(1);
        $sheet->setTitle('Guide');

        $sheet->setCellValue('A1', 'Customer Import Guide');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $instructions = [
            '• Row 1 of the Customers sheet contains column keys — do NOT change them.',
            '• Row 2 is a sample row — delete it or overwrite it with your data.',
            '• Enter your customers starting from row 2 (or row 3 if you keep the example).',
            '• Fields marked with * are required.',
            '• Enum fields must use the exact values listed in the table below.',
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

        foreach ([22, 28, 12, 50, 45] as $i => $w) {
            $sheet->getColumnDimensionByColumn($i + 1)->setWidth($w);
        }
    }
}
