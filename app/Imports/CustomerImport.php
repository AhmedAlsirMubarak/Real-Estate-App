<?php

namespace App\Imports;

use App\Models\Customer;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CustomerImport
{
    public int $imported = 0;
    public array $rowErrors = [];
    public array $warnings = [];

    private const PROPERTY_TYPES = ['any', 'apartment_building', 'villa', 'farm', 'chalet'];
    private const PURPOSES       = ['rent', 'sale', 'both'];
    private const STATUSES       = ['new', 'contacted', 'interested', 'closed'];

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
            $this->warnings[] = 'Row 1 is empty — cannot read column names. Make sure row 1 contains the field keys (name, mobile, property_type, etc.).';
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

            $this->createCustomer($data);
            $this->imported++;
        }

        if ($dataRowCount === 0) {
            $this->warnings[] = "No data found in rows 2–{$highestRow}. Add your customer data starting from row 2.";
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

        // name — required
        if ($this->get($data, 'name') === '') {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'name',
                'value' => '',
                'error' => 'Required — customer name is missing',
            ];
        }

        // email — optional, must be valid format
        $email = $this->get($data, 'email');
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'email',
                'value' => $email,
                'error' => 'Invalid email format',
            ];
        }

        // property_type — required enum
        $type = $this->get($data, 'property_type');
        if ($type === '') {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'property_type',
                'value' => '',
                'error' => 'Required. Allowed: ' . implode(', ', self::PROPERTY_TYPES),
            ];
        } elseif (!in_array($type, self::PROPERTY_TYPES, true)) {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'property_type',
                'value' => $type,
                'error' => 'Invalid value "' . $type . '". Allowed: ' . implode(', ', self::PROPERTY_TYPES),
            ];
        }

        // purpose — required enum
        $purpose = $this->get($data, 'purpose');
        if ($purpose === '') {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'purpose',
                'value' => '',
                'error' => 'Required. Allowed: ' . implode(', ', self::PURPOSES),
            ];
        } elseif (!in_array($purpose, self::PURPOSES, true)) {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'purpose',
                'value' => $purpose,
                'error' => 'Invalid value "' . $purpose . '". Allowed: ' . implode(', ', self::PURPOSES),
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

        // min_budget — optional non-negative number
        $minBudget = $this->get($data, 'min_budget');
        if ($minBudget !== '' && (!is_numeric($minBudget) || (float) $minBudget < 0)) {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'min_budget',
                'value' => $minBudget,
                'error' => 'Must be a non-negative number',
            ];
        }

        // max_budget — optional non-negative number
        $maxBudget = $this->get($data, 'max_budget');
        if ($maxBudget !== '' && (!is_numeric($maxBudget) || (float) $maxBudget < 0)) {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'max_budget',
                'value' => $maxBudget,
                'error' => 'Must be a non-negative number',
            ];
        }

        // bedrooms — optional non-negative integer
        $bedrooms = $this->get($data, 'bedrooms');
        if ($bedrooms !== '' && (!is_numeric($bedrooms) || (int) $bedrooms < 0)) {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'bedrooms',
                'value' => $bedrooms,
                'error' => 'Must be 0 or a positive integer',
            ];
        }

        return $errors;
    }

    private function createCustomer(array $data): void
    {
        $minBudget = $this->get($data, 'min_budget');
        $maxBudget = $this->get($data, 'max_budget');
        $bedrooms  = $this->get($data, 'bedrooms');
        $status    = $this->get($data, 'status');

        Customer::create([
            'name'          => $this->get($data, 'name'),
            'mobile'        => $this->get($data, 'mobile') ?: null,
            'email'         => $this->get($data, 'email') ?: null,
            'location'      => $this->get($data, 'location') ?: null,
            'property_type' => $this->get($data, 'property_type'),
            'purpose'       => $this->get($data, 'purpose'),
            'min_budget'    => $minBudget !== '' ? (float) $minBudget : null,
            'max_budget'    => $maxBudget !== '' ? (float) $maxBudget : null,
            'bedrooms'      => $bedrooms  !== '' ? (int) $bedrooms : null,
            'notes'         => $this->get($data, 'notes') ?: null,
            'status'        => $status !== '' ? $status : 'new',
        ]);
    }
}
