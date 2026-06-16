<?php

namespace App\Imports;

use App\Models\Association;
use App\Models\Property;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AssociationImport
{
    public int $imported = 0;
    public array $rowErrors = [];
    public array $warnings = [];

    private const STATUSES = ['active', 'inactive'];

    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function run(): void
    {
        $spreadsheet = IOFactory::load($this->filePath);
        $sheet       = $spreadsheet->getSheet(0);

        $highestRow = $sheet->getHighestDataRow();
        $highestCol = $sheet->getHighestDataColumn();

        if ($highestRow < 2) {
            $this->warnings[] = "The file only has {$highestRow} row(s). Row 1 must be the header, with your data from row 2 onwards.";
            return;
        }

        // Build header map: column letter => field name (from row 1)
        $headers = [];
        foreach ($sheet->getRowIterator(1, 1) as $row) {
            foreach ($row->getCellIterator('A', $highestCol) as $cell) {
                $val = trim((string) ($cell->getValue() ?? ''));
                if ($val !== '') {
                    $headers[$cell->getColumn()] = $val;
                }
            }
        }

        if (empty($headers)) {
            $this->warnings[] = 'Row 1 is empty — cannot read column names. Make sure row 1 contains the field keys (property_code, name_ar, etc.).';
            return;
        }

        // Data starts at row 2 (row 1 is the header)
        $dataRowCount = 0;
        for ($rowNum = 2; $rowNum <= $highestRow; $rowNum++) {
            $data = [];
            foreach ($headers as $col => $field) {
                $data[$field] = $sheet->getCell($col . $rowNum)->getFormattedValue();
            }

            if ($this->isEmptyRow($data)) {
                continue;
            }

            $dataRowCount++;
            $errors = $this->validateRow($data, $rowNum);

            if (!empty($errors)) {
                $this->rowErrors = array_merge($this->rowErrors, $errors);
                continue;
            }

            $this->createAssociation($data);
            $this->imported++;
        }

        if ($dataRowCount === 0) {
            $this->warnings[] = "No data found in rows 2–{$highestRow}. Add your association data starting from row 2.";
        }
    }

    private function isEmptyRow(array $data): bool
    {
        foreach ($data as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }
        return true;
    }

    private function get(array $data, string $key): string
    {
        return isset($data[$key]) ? trim((string) $data[$key]) : '';
    }

    private function validateRow(array $data, int $rowNum): array
    {
        $errors = [];

        // property_code — required, must exist, must not already have an association
        $propertyCode = $this->get($data, 'property_code');
        if ($propertyCode === '') {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'property_code',
                'value' => '',
                'error' => 'Required — the property code to link this association to',
            ];
        } else {
            $property = Property::where('code', $propertyCode)->first();
            if (!$property) {
                $errors[] = [
                    'row'   => $rowNum,
                    'field' => 'property_code',
                    'value' => $propertyCode,
                    'error' => 'No property found with this code',
                ];
            } elseif (Association::where('property_id', $property->id)->exists()) {
                $errors[] = [
                    'row'   => $rowNum,
                    'field' => 'property_code',
                    'value' => $propertyCode,
                    'error' => 'This property already has an association',
                ];
            }
        }

        // name_ar — required
        if ($this->get($data, 'name_ar') === '') {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'name_ar',
                'value' => '',
                'error' => 'Required — Arabic association name is missing',
            ];
        }

        // monthly_fee_per_unit — required, non-negative number
        $fee = $this->get($data, 'monthly_fee_per_unit');
        if ($fee === '' || !is_numeric($fee) || (float) $fee < 0) {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'monthly_fee_per_unit',
                'value' => $fee,
                'error' => 'Required — must be a non-negative number',
            ];
        }

        // status — optional enum
        $status = $this->get($data, 'status');
        if ($status !== '' && !in_array($status, self::STATUSES, true)) {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'status',
                'value' => $status,
                'error' => 'Invalid value "' . $status . '". Allowed: ' . implode(', ', self::STATUSES),
            ];
        }

        // established_date — optional date
        $establishedDate = $this->get($data, 'established_date');
        if ($establishedDate !== '' && strtotime($establishedDate) === false) {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'established_date',
                'value' => $establishedDate,
                'error' => 'Invalid date format',
            ];
        }

        return $errors;
    }

    private function createAssociation(array $data): void
    {
        $propertyCode = $this->get($data, 'property_code');
        $property     = Property::where('code', $propertyCode)->first();

        $establishedDate = $this->get($data, 'established_date');
        $status           = $this->get($data, 'status');
        $nameAr           = $this->get($data, 'name_ar');

        Association::create([
            'property_id'                => $property->id,
            'name_ar'                    => $nameAr,
            'name_en'                    => $this->get($data, 'name_en') ?: $nameAr,
            'established_date'           => $establishedDate !== '' ? date('Y-m-d', strtotime($establishedDate)) : null,
            'monthly_fee_per_unit'       => (float) $this->get($data, 'monthly_fee_per_unit'),
            'description_ar'             => $this->get($data, 'description_ar') ?: null,
            'description_en'             => $this->get($data, 'description_en') ?: null,
            'status'                     => $status !== '' ? $status : 'active',
            'electricity_account_number' => $this->get($data, 'electricity_account_number') ?: null,
            'water_account_number'       => $this->get($data, 'water_account_number') ?: null,
        ]);
    }
}
