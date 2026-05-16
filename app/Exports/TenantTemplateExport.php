<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TenantTemplateExport
{
    private const COLUMNS = [
        'name_ar'           => 'Arabic Name *',
        'name_en'           => 'English Name *',
        'email'             => 'Email *',
        'phone'             => 'Phone',
        'password'          => 'Password (leave blank = auto)',
        'national_id'       => 'National ID',
        'emergency_contact' => 'Emergency Contact',
        'property_code'     => 'Property Code (for contract)',
        'unit_number'       => 'Unit Number (for contract)',
        'start_date'        => 'Contract Start (YYYY-MM-DD)',
        'end_date'          => 'Contract End (YYYY-MM-DD)',
        'monthly_rent'      => 'Monthly Rent',
        'deposit'           => 'Deposit',
    ];

    private const GUIDE_ROWS = [
        ['name_ar',           'Arabic Name *',                'Yes', '',                         'Full name in Arabic'],
        ['name_en',           'English Name *',               'Yes', '',                         'Full name in English'],
        ['email',             'Email *',                      'Yes', 'Valid unique email',        'Used as login username'],
        ['phone',             'Phone',                        'No',  '',                          ''],
        ['password',          'Password',                     'No',  'Min 8 chars',               'Leave blank to auto-generate Tenant@2025'],
        ['national_id',       'National ID',                  'No',  '',                          ''],
        ['emergency_contact', 'Emergency Contact',            'No',  '',                          'Name or phone of emergency contact'],
        ['property_code',     'Property Code',                'No',  'e.g. TH-B-001',            'Must match an existing property code. Required if linking a unit.'],
        ['unit_number',       'Unit Number',                  'No',  'e.g. 101',                 'Must match a unit in the given property. Required if property_code is set.'],
        ['start_date',        'Contract Start',               'No',  'YYYY-MM-DD',               'Required if property_code + unit_number are set'],
        ['end_date',          'Contract End',                 'No',  'YYYY-MM-DD, after start',  'Required if property_code + unit_number are set'],
        ['monthly_rent',      'Monthly Rent',                 'No',  'Number >= 0',              'Required if property_code + unit_number are set'],
        ['deposit',           'Deposit',                      'No',  'Number >= 0',              'Optional. Defaults to 0 if blank.'],
    ];

    public function build(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setTitle('Tenants Import Template');

        $this->buildDataSheet($spreadsheet);
        $this->buildGuideSheet($spreadsheet);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function buildDataSheet(Spreadsheet $spreadsheet): void
    {
        $sheet    = $spreadsheet->getSheet(0);
        $sheet->setTitle('Tenants');

        $colKeys  = array_keys(self::COLUMNS);
        $colCount = count($colKeys);
        $lastCol  = Coordinate::stringFromColumnIndex($colCount);

        // Row 1: field-key headers
        foreach ($colKeys as $i => $key) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1) . '1', $key);
        }

        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF9CA3AF']]],
        ]);

        // Row 2: example row
        $example = [
            'name_ar'           => 'أحمد العمري',
            'name_en'           => 'Ahmed Al-Omari',
            'email'             => 'ahmed@example.com',
            'phone'             => '0501234567',
            'password'          => '',
            'national_id'       => '1234567890',
            'emergency_contact' => 'Mohammed 0507654321',
            'property_code'     => 'TH-B-001',
            'unit_number'       => '101',
            'start_date'        => '2026-01-01',
            'end_date'          => '2026-12-31',
            'monthly_rent'      => '2500',
            'deposit'           => '5000',
        ];

        foreach ($colKeys as $i => $key) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1) . '2', $example[$key] ?? '');
        }

        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF1F5C2E']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD1FAE5']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
        ]);

        // Row heights & column widths
        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getRowDimension(2)->setRowHeight(20);

        $widths = [28, 28, 32, 16, 24, 18, 28, 22, 18, 22, 22, 16, 14];
        foreach ($widths as $i => $w) {
            $sheet->getColumnDimensionByColumn($i + 1)->setWidth($w);
        }

        $sheet->freezePane('A2');
    }

    private function buildGuideSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet(1);
        $sheet->setTitle('Guide');

        $sheet->setCellValue('A1', 'Tenants Import Guide');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $instructions = [
            '• Row 1 contains column keys — do NOT change them.',
            '• Row 2 is a sample row — delete or overwrite it with your data.',
            '• Enter tenant data from row 2 onwards.',
            '• Fields marked * are required.',
            '• Leave password blank to auto-set it to "Tenant@2025".',
            '• To assign a unit, fill property_code + unit_number + start_date + end_date + monthly_rent.',
            '• If any of property_code or unit_number is missing, no contract is created.',
            '• Rows with validation errors are skipped. An error report is shown after upload.',
        ];

        foreach ($instructions as $i => $text) {
            $row = $i + 2;
            $sheet->setCellValue('A' . $row, $text);
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray([
                'font'      => ['size' => 10],
                'alignment' => ['wrapText' => true],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(16);
        }

        $spacerRow  = count($instructions) + 2;
        $tableStart = $spacerRow + 1;
        $sheet->getRowDimension($spacerRow)->setRowHeight(8);

        $guideHeaders = ['Field', 'English Label', 'Required?', 'Format / Allowed Values', 'Notes'];
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

        foreach ([20, 28, 12, 38, 45] as $i => $w) {
            $sheet->getColumnDimensionByColumn($i + 1)->setWidth($w);
        }
    }
}
