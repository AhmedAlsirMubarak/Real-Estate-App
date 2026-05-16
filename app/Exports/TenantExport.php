<?php

namespace App\Exports;

use App\Models\Tenant;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TenantExport
{
    public function build(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setTitle('Tenants Export');

        $sheet = $spreadsheet->getSheet(0);
        $sheet->setTitle('Tenants');

        $headers = [
            'ID', 'Arabic Name', 'English Name', 'Email', 'Phone',
            'National ID', 'Emergency Contact',
            'Property Code', 'Property Name', 'Unit Number',
            'Contract Status', 'Contract Start', 'Contract End',
            'Monthly Rent', 'Deposit', 'Created At',
        ];

        $colCount = count($headers);
        $lastCol  = Coordinate::stringFromColumnIndex($colCount);

        // Header row
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

        // Data rows
        $tenants = Tenant::with(['user', 'rentalContracts.unit.property'])->get();
        $rowNum  = 2;

        foreach ($tenants as $tenant) {
            $contract = $tenant->rentalContracts->where('status', 'active')->first()
                     ?? $tenant->rentalContracts->sortByDesc('created_at')->first();

            $row = [
                $tenant->id,
                $tenant->user?->name_ar ?? $tenant->user?->name ?? '',
                $tenant->user?->name_en ?? $tenant->user?->name ?? '',
                $tenant->user?->email ?? '',
                $tenant->user?->phone ?? '',
                $tenant->national_id ?? '',
                $tenant->emergency_contact ?? '',
                $contract?->unit?->property?->code ?? '',
                $contract?->unit?->property?->name_en ?? $contract?->unit?->property?->name ?? '',
                $contract?->unit?->unit_number ?? '',
                $contract?->status ?? '',
                $contract?->start_date?->format('Y-m-d') ?? '',
                $contract?->end_date?->format('Y-m-d') ?? '',
                $contract?->monthly_rent ?? '',
                $contract?->deposit ?? '',
                $tenant->created_at?->format('Y-m-d') ?? '',
            ];

            foreach ($row as $j => $val) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($j + 1) . $rowNum, $val);
            }

            $isEven  = $rowNum % 2 === 0;
            $bgColor = $isEven ? 'FFF8FAFC' : 'FFFFFFFF';
            $sheet->getStyle('A' . $rowNum . ':' . $lastCol . $rowNum)->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE5E7EB']]],
            ]);
            $sheet->getRowDimension($rowNum)->setRowHeight(18);

            $rowNum++;
        }

        // Column widths
        $widths = [8, 26, 26, 30, 16, 18, 28, 16, 28, 14, 14, 14, 14, 14, 12, 14];
        foreach ($widths as $i => $w) {
            $sheet->getColumnDimensionByColumn($i + 1)->setWidth($w);
        }

        $sheet->freezePane('A2');

        return $spreadsheet;
    }
}
