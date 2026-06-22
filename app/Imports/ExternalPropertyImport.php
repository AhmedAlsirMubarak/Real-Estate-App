<?php

namespace App\Imports;

use App\Models\Property;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExternalPropertyImport
{
    public int $imported = 0;
    public array $rowErrors = [];
    public array $warnings = [];

    private const TYPES = ['apartment_building', 'villa', 'farm', 'chalet', 'flat', 'land'];
    private const PURPOSES = ['rent', 'sale', 'both', 'exclusive_rent', 'exclusive_sale'];
    private const STATUSES = ['active', 'sold', 'rented', 'under_maintenance', 'archived'];
    private const COMMISSION_PAYERS = ['owner', 'tenant', 'buyer', 'shared'];

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
            $this->warnings[] = 'Row 1 is empty — cannot read column names.';
            return;
        }

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

            $this->createProperty($data);
            $this->imported++;
        }

        if ($dataRowCount === 0) {
            $this->warnings[] = "No data found in rows 2–{$highestRow}. Add your property data starting from row 2.";
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

        $nameAr = $this->get($data, 'name_ar');
        if ($nameAr === '') {
            $errors[] = ['row' => $rowNum, 'field' => 'name_ar', 'value' => '', 'error' => 'Required — Arabic property name is missing'];
        }

        $type = $this->get($data, 'type');
        if ($type === '') {
            $errors[] = ['row' => $rowNum, 'field' => 'type', 'value' => '', 'error' => 'Required'];
        } elseif (!in_array($type, self::TYPES, true)) {
            $errors[] = ['row' => $rowNum, 'field' => 'type', 'value' => $type, 'error' => 'Invalid. Allowed: ' . implode(', ', self::TYPES)];
        }

        $purpose = $this->get($data, 'purpose');
        if ($purpose === '') {
            $errors[] = ['row' => $rowNum, 'field' => 'purpose', 'value' => '', 'error' => 'Required'];
        } elseif (!in_array($purpose, self::PURPOSES, true)) {
            $errors[] = ['row' => $rowNum, 'field' => 'purpose', 'value' => $purpose, 'error' => 'Invalid. Allowed: ' . implode(', ', self::PURPOSES)];
        }

        if ($this->get($data, 'address_ar') === '') {
            $errors[] = ['row' => $rowNum, 'field' => 'address_ar', 'value' => '', 'error' => 'Required — Arabic address is missing'];
        }

        $status = $this->get($data, 'status');
        if ($status !== '' && !in_array($status, self::STATUSES, true)) {
            $errors[] = ['row' => $rowNum, 'field' => 'status', 'value' => $status, 'error' => 'Invalid. Allowed: ' . implode(', ', self::STATUSES)];
        }

        $commissionPayer = $this->get($data, 'commission_payer');
        if ($commissionPayer !== '' && !in_array($commissionPayer, self::COMMISSION_PAYERS, true)) {
            $errors[] = ['row' => $rowNum, 'field' => 'commission_payer', 'value' => $commissionPayer, 'error' => 'Invalid. Allowed: ' . implode(', ', self::COMMISSION_PAYERS)];
        }

        foreach (['floors', 'bedrooms', 'bathrooms'] as $intField) {
            $val = $this->get($data, $intField);
            if ($val !== '' && (!is_numeric($val) || (int) $val < 0)) {
                $errors[] = ['row' => $rowNum, 'field' => $intField, 'value' => $val, 'error' => 'Must be a non-negative integer'];
            }
        }

        foreach (['total_area', 'rent_commission_rate', 'sale_commission_rate'] as $numField) {
            $val = $this->get($data, $numField);
            if ($val !== '' && (!is_numeric($val) || (float) $val < 0)) {
                $errors[] = ['row' => $rowNum, 'field' => $numField, 'value' => $val, 'error' => 'Must be a non-negative number'];
            }
        }

        $code = $this->get($data, 'code');
        if ($code !== '' && Property::where('code', $code)->exists()) {
            $errors[] = ['row' => $rowNum, 'field' => 'code', 'value' => $code, 'error' => 'A property with this code already exists'];
        }

        return $errors;
    }

    private function createProperty(array $data): void
    {
        $type    = $this->get($data, 'type');
        $nameAr  = $this->get($data, 'name_ar');
        $code    = $this->get($data, 'code');
        $status  = $this->get($data, 'status');
        $address = $this->get($data, 'address_ar');
        $cityAr  = $this->get($data, 'city_ar');

        if ($code === '') {
            $prefix = match ($type) {
                'apartment_building' => 'EX-B',
                'villa'              => 'EX-V',
                'farm'               => 'EX-F',
                'chalet'             => 'EX-C',
                'flat'               => 'EX-FL',
                'land'               => 'EX-L',
                default              => 'EX',
            };
            $count = Property::where('type', $type)->where('section', 'external')->count() + 1;
            $code  = sprintf('%s-%03d', $prefix, $count);
        }

        $floors    = $this->get($data, 'floors');
        $area      = $this->get($data, 'total_area');
        $beds      = $this->get($data, 'bedrooms');
        $baths     = $this->get($data, 'bathrooms');
        $rentComm  = $this->get($data, 'rent_commission_rate');
        $saleComm  = $this->get($data, 'sale_commission_rate');

        Property::create([
            'section'                    => 'external',
            'code'                       => $code,
            'name_ar'                    => $nameAr,
            'name_en'                    => $this->get($data, 'name_en') ?: $nameAr,
            'name'                       => $nameAr,
            'type'                       => $type,
            'purpose'                    => $this->get($data, 'purpose'),
            'address_ar'                 => $address,
            'address_en'                 => $this->get($data, 'address_en') ?: null,
            'address'                    => $address,
            'city_ar'                    => $cityAr ?: null,
            'city_en'                    => $this->get($data, 'city_en') ?: null,
            'city'                       => $cityAr ?: null,
            'floors'                     => $floors !== '' ? (int) $floors : null,
            'total_area'                 => $area !== '' ? (float) $area : null,
            'bedrooms'                   => $beds !== '' ? (int) $beds : null,
            'bathrooms'                  => $baths !== '' ? (int) $baths : null,
            'status'                     => $status !== '' ? $status : 'active',
            'electricity_account_number' => $this->get($data, 'electricity_account_number') ?: null,
            'water_account_number'       => $this->get($data, 'water_account_number') ?: null,
            'rent_commission_rate'       => $rentComm !== '' ? (float) $rentComm : null,
            'sale_commission_rate'       => $saleComm !== '' ? (float) $saleComm : null,
            'commission_payer'           => $this->get($data, 'commission_payer') ?: null,
            'description_ar'             => $this->get($data, 'description_ar') ?: null,
            'description_en'             => $this->get($data, 'description_en') ?: null,
            'description'               => $this->get($data, 'description_ar') ?: null,
        ]);
    }
}
