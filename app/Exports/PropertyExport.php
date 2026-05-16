<?php

namespace App\Exports;

use App\Models\Property;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PropertyExport
{
    public function build(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setTitle('Properties Export');

        $sheet = $spreadsheet->getSheet(0);
        $sheet->setTitle('Properties');

        $headers = [
            'ID', 'Code', 'Arabic Name', 'English Name',
            'Type', 'Purpose', 'Section',
            'Address', 'City',
            'Owner', 'Commission %', 'Employee',
            'Floors', 'Total Area (m²)', 'Bedrooms', 'Bathrooms',
            'Status', 'Total Units', 'Available', 'Rented',
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

        $properties = Property::with(['owner.user', 'employee', 'units'])->get();
        $rowNum     = 2;

        foreach ($properties as $property) {
            $units     = $property->units;
            $available = $units->where('status', 'available')->count();
            $rented    = $units->where('status', 'rented')->count();

            $row = [
                $property->id,
                $property->code ?? '',
                $property->name_ar ?? $property->name ?? '',
                $property->name_en ?? $property->name ?? '',
                match ($property->type) {
                    'apartment_building' => 'Apartment Building',
                    'villa'  => 'Villa',
                    'farm'   => 'Farm',
                    'chalet' => 'Chalet',
                    default  => $property->type,
                },
                match ($property->purpose) {
                    'rent' => 'Rent',
                    'sale' => 'Sale',
                    'both' => 'Rent & Sale',
                    default => $property->purpose,
                },
                match ($property->section) {
                    'hoa'        => 'Owners Association',
                    'management' => 'Building Management',
                    'external'   => 'External Property',
                    default      => $property->section ?? '',
                },
                $property->address_en ?? $property->address ?? '',
                $property->city_en    ?? $property->city    ?? '',
                $property->owner?->user?->name ?? 'Company',
                $property->owner?->commission_rate ?? '',
                $property->employee?->name ?? '',
                $property->floors      ?? '',
                $property->total_area  ?? '',
                $property->bedrooms    ?? '',
                $property->bathrooms   ?? '',
                ucfirst($property->status ?? ''),
                $units->count(),
                $available,
                $rented,
                $property->created_at?->format('Y-m-d') ?? '',
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

        $widths = [6, 14, 26, 26, 20, 14, 20, 30, 16, 22, 14, 22, 10, 16, 12, 12, 12, 12, 12, 12, 14];
        foreach ($widths as $i => $w) {
            $sheet->getColumnDimensionByColumn($i + 1)->setWidth($w);
        }

        $sheet->freezePane('A2');

        return $spreadsheet;
    }
}
