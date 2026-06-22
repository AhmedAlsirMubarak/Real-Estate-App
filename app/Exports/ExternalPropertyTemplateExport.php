<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExternalPropertyTemplateExport
{
    private const COLUMNS = [
        'code'                       => 'Code (auto if blank)',
        'name_ar'                    => 'Arabic Name *',
        'name_en'                    => 'English Name',
        'type'                       => 'Type *',
        'purpose'                    => 'Purpose *',
        'address_ar'                 => 'Arabic Address *',
        'address_en'                 => 'English Address',
        'city_ar'                    => 'City (Arabic)',
        'city_en'                    => 'City (English)',
        'floors'                     => 'Floors',
        'total_area'                 => 'Total Area (m²)',
        'bedrooms'                   => 'Bedrooms',
        'bathrooms'                  => 'Bathrooms',
        'status'                     => 'Status',
        'electricity_account_number' => 'Electricity Account',
        'water_account_number'       => 'Water Account',
        'rent_commission_rate'       => 'Rent Commission %',
        'sale_commission_rate'       => 'Sale Commission %',
        'commission_payer'           => 'Commission Payer',
        'description_ar'             => 'Description (Arabic)',
        'description_en'             => 'Description (English)',
    ];

    private const GUIDE_ROWS = [
        ['code',                       'Code',                  'No',  '',                                                               'Leave blank to auto-generate'],
        ['name_ar',                    'Arabic Name',           'Yes', '',                                                               'Property name in Arabic'],
        ['name_en',                    'English Name',          'No',  '',                                                               'Defaults to Arabic name if blank'],
        ['type',                       'Type',                  'Yes', 'apartment_building | villa | farm | chalet | flat | land',       ''],
        ['purpose',                    'Purpose',               'Yes', 'rent | sale | both | exclusive_rent | exclusive_sale',           ''],
        ['address_ar',                 'Arabic Address',        'Yes', '',                                                               ''],
        ['address_en',                 'English Address',       'No',  '',                                                               ''],
        ['city_ar',                    'City (Arabic)',         'No',  '',                                                               ''],
        ['city_en',                    'City (English)',        'No',  '',                                                               ''],
        ['floors',                     'Floors',                'No',  'Integer >= 1',                                                   'Applicable to apartment_building'],
        ['total_area',                 'Total Area (m²)',       'No',  'Number >= 0',                                                    ''],
        ['bedrooms',                   'Bedrooms',              'No',  'Integer >= 0',                                                   ''],
        ['bathrooms',                  'Bathrooms',             'No',  'Integer >= 0',                                                   ''],
        ['status',                     'Status',                'No',  'active | sold | rented | under_maintenance | archived',          'Defaults to "active"'],
        ['electricity_account_number', 'Electricity Account',   'No',  '',                                                               ''],
        ['water_account_number',       'Water Account',         'No',  '',                                                               ''],
        ['rent_commission_rate',       'Rent Commission %',     'No',  '0 – 100',                                                        ''],
        ['sale_commission_rate',       'Sale Commission %',     'No',  '0 – 100',                                                        ''],
        ['commission_payer',           'Commission Payer',      'No',  'owner | tenant | buyer | shared',                               ''],
        ['description_ar',             'Description (Arabic)',  'No',  '',                                                               ''],
        ['description_en',             'Description (English)', 'No',  '',                                                               ''],
    ];

    public function build(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setTitle('External Properties Import Template');

        $this->buildDataSheet($spreadsheet);
        $this->buildGuideSheet($spreadsheet);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function buildDataSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->getSheet(0);
        $sheet->setTitle('External Properties');

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
            'code'                       => '',
            'name_ar'                    => 'فيلا الياسمين',
            'name_en'                    => 'Jasmine Villa',
            'type'                       => 'villa',
            'purpose'                    => 'rent',
            'address_ar'                 => 'شارع 10، بوشر',
            'address_en'                 => 'Street 10, Bowsher',
            'city_ar'                    => 'مسقط',
            'city_en'                    => 'Muscat',
            'floors'                     => '',
            'total_area'                 => 450,
            'bedrooms'                   => 4,
            'bathrooms'                  => 3,
            'status'                     => 'active',
            'electricity_account_number' => '',
            'water_account_number'       => '',
            'rent_commission_rate'       => 5,
            'sale_commission_rate'       => '',
            'commission_payer'           => 'owner',
            'description_ar'             => '',
            'description_en'             => '',
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

        $widths = [16, 28, 28, 20, 22, 30, 30, 18, 18, 10, 16, 12, 12, 16, 22, 20, 18, 18, 18, 32, 32];
        foreach ($widths as $i => $w) {
            $sheet->getColumnDimensionByColumn($i + 1)->setWidth($w);
        }

        $sheet->freezePane('A2');
    }

    private function buildGuideSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet(1);
        $sheet->setTitle('Guide');

        $sheet->setCellValue('A1', 'External Property Import Guide');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $instructions = [
            '• Row 1 of the External Properties sheet contains column keys — do NOT change them.',
            '• Row 2 is a sample row — delete it or overwrite it with your data.',
            '• Enter your properties starting from row 2 (or row 3 if you keep the example).',
            '• Fields marked with * are required.',
            '• Rows with any validation error are skipped. An error report is shown after upload.',
            '• Properties are created with section = external automatically.',
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

        $spacerRow  = count($instructions) + 2;
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
