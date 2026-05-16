<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PropertyTemplateExport
{
    private const COLUMNS = [
        'name_ar'        => 'Arabic Name *',
        'name_en'        => 'English Name',
        'type'           => 'Property Type *',
        'purpose'        => 'Purpose *',
        'section'        => 'Section *',
        'city_ar'        => 'City (Arabic)',
        'city_en'        => 'City (English)',
        'address_ar'     => 'Arabic Address *',
        'address_en'     => 'English Address',
        'floors'         => 'Floors',
        'total_area'     => 'Total Area (m2)',
        'bedrooms'       => 'Bedrooms',
        'bathrooms'      => 'Bathrooms',
        'status'         => 'Status',
        'description_ar' => 'Arabic Description',
        'description_en' => 'English Description',
    ];

    private const GUIDE_ROWS = [
        ['name_ar',        'Arabic Name *',      'Yes', '',                                              'Property name in Arabic'],
        ['name_en',        'English Name',        'No',  '',                                              'Property name in English'],
        ['type',           'Property Type *',     'Yes', 'apartment_building | villa | farm | chalet',   'Must match exactly'],
        ['purpose',        'Purpose *',           'Yes', 'rent | sale | both',                           'Must match exactly'],
        ['section',        'Section *',           'Yes', 'hoa | management | external',                  'hoa=Owners Assoc, management=Building Mgmt, external=External'],
        ['city_ar',        'City (Arabic)',        'No',  '',                                              ''],
        ['city_en',        'City (English)',       'No',  '',                                              ''],
        ['address_ar',     'Arabic Address *',     'Yes', '',                                              'Full address in Arabic'],
        ['address_en',     'English Address',      'No',  '',                                              ''],
        ['floors',         'Floors',               'No',  'Positive integer (1, 2, 3...)',                'Leave blank for villas/chalets/farms'],
        ['total_area',     'Total Area (m2)',       'No',  'Number >= 0',                                  ''],
        ['bedrooms',       'Bedrooms',             'No',  'Integer >= 0',                                  ''],
        ['bathrooms',      'Bathrooms',            'No',  'Integer >= 0',                                  ''],
        ['status',         'Status',               'No',  'active | sold | under_maintenance | archived', 'Defaults to "active" if blank'],
        ['description_ar', 'Arabic Description',   'No',  '',                                              ''],
        ['description_en', 'English Description',  'No',  '',                                              ''],
    ];

    public function build(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setTitle('Properties Import Template');

        $this->buildDataSheet($spreadsheet);
        $this->buildGuideSheet($spreadsheet);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function buildDataSheet(Spreadsheet $spreadsheet): void
    {
        $sheet    = $spreadsheet->getSheet(0);
        $sheet->setTitle('Properties');

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

        // Row 2: example row (green, italics)
        $example = [
            'name_ar'        => 'برج النخيل',
            'name_en'        => 'Palm Tower',
            'type'           => 'apartment_building',
            'purpose'        => 'rent',
            'section'        => 'management',
            'city_ar'        => 'الرياض',
            'city_en'        => 'Riyadh',
            'address_ar'     => 'شارع الملك فهد',
            'address_en'     => 'King Fahd Road',
            'floors'         => 10,
            'total_area'     => 5000,
            'bedrooms'       => '',
            'bathrooms'      => '',
            'status'         => 'active',
            'description_ar' => 'برج سكني حديث',
            'description_en' => 'Modern residential tower',
        ];

        foreach ($colKeys as $i => $key) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1) . '2', $example[$key] ?? '');
        }

        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF1F5C2E']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD1FAE5']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
        ]);

        // Column comments: show label + allowed values as header tooltip alternative
        foreach ($colKeys as $i => $key) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->getComment($col . '1')->getText()->createTextRun(self::COLUMNS[$key]);
        }

        // Row heights & column widths
        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getRowDimension(2)->setRowHeight(20);

        $widths = [28, 28, 22, 12, 16, 18, 18, 35, 35, 10, 16, 12, 12, 22, 35, 35];
        foreach ($widths as $i => $w) {
            $sheet->getColumnDimensionByColumn($i + 1)->setWidth($w);
        }

        // Freeze after header row — data entry starts at row 2
        $sheet->freezePane('A2');
    }

    private function buildGuideSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet(1);
        $sheet->setTitle('Guide');

        // Title
        $sheet->setCellValue('A1', 'Property Import Guide');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Instructions
        $instructions = [
            '• Row 1 of the Properties sheet contains column keys — do NOT change them.',
            '• Row 2 is a sample row — delete it or overwrite it with your data.',
            '• Enter your properties starting from row 2 (or row 3 if you keep the example).',
            '• Fields marked with * are required.',
            '• Enum fields must use the exact values listed in the table below.',
            '• Rows with any validation error are skipped. An error report is shown after upload.',
        ];

        foreach ($instructions as $i => $text) {
            $row = $i + 2;
            $sheet->setCellValue('A' . $row, $text);
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray([
                'font'      => ['size' => 10, 'color' => ['argb' => 'FF1F2937']],
                'alignment' => ['wrapText' => true],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(16);
        }

        // Spacer
        $spacerRow = count($instructions) + 2;
        $sheet->getRowDimension($spacerRow)->setRowHeight(8);

        // Table header
        $tableStart  = $spacerRow + 1;
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

        // Data rows
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

        // Column widths
        foreach ([22, 28, 12, 50, 45] as $i => $w) {
            $sheet->getColumnDimensionByColumn($i + 1)->setWidth($w);
        }
    }
}
