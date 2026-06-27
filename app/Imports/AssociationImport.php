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

    private const STATUSES        = ['active', 'inactive'];
    private const FEE_FREQUENCIES = ['monthly', 'yearly'];

    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function run(): void
    {
        $ext = strtolower(pathinfo($this->filePath, PATHINFO_EXTENSION));

        if ($ext === 'csv') {
            $this->runCsv();
        } else {
            $this->runSpreadsheet();
        }
    }

    private function runCsv(): void
    {
        // Read raw bytes, strip UTF-8 BOM if present, convert to UTF-8
        $raw = file_get_contents($this->filePath);
        if ($raw === false) {
            throw new \RuntimeException('Cannot read file.');
        }

        // Strip BOM
        if (str_starts_with($raw, "\xEF\xBB\xBF")) {
            $raw = substr($raw, 3);
        } elseif (str_starts_with($raw, "\xFF\xFE") || str_starts_with($raw, "\xFE\xFF")) {
            $raw = mb_convert_encoding($raw, 'UTF-8', 'UTF-16');
        }

        // Detect & convert non-UTF-8 encodings (e.g. Windows-1256 Arabic)
        if (!mb_check_encoding($raw, 'UTF-8')) {
            $enc = mb_detect_encoding($raw, ['Windows-1256', 'ISO-8859-6', 'Windows-1252', 'ISO-8859-1'], true);
            if ($enc) {
                $raw = mb_convert_encoding($raw, 'UTF-8', $enc);
            }
        }

        // Parse CSV from memory
        $lines = array_filter(
            array_map('trim', explode("\n", str_replace("\r\n", "\n", str_replace("\r", "\n", $raw)))),
            fn ($l) => $l !== ''
        );

        if (count($lines) < 2) {
            $this->warnings[] = 'The file only has ' . count($lines) . ' row(s). Row 1 must be the header, with your data from row 2 onwards.';
            return;
        }

        // Auto-detect delimiter
        $firstLine  = reset($lines);
        $counts     = [',' => substr_count($firstLine, ','), ';' => substr_count($firstLine, ';'), "\t" => substr_count($firstLine, "\t")];
        arsort($counts);
        $delimiter  = array_key_first($counts);
        $parseLine  = fn(string $line): array => str_getcsv($line, $delimiter, '"');

        $rawHeaders = $parseLine(array_shift($lines));
        $headers    = array_map(fn($h) => $this->cleanHeader($h), $rawHeaders);

        if (empty(array_filter($headers))) {
            $this->warnings[] = 'Row 1 is empty — cannot read column names.';
            return;
        }

        if (!in_array('property_code', $headers, true)) {
            $found = implode(', ', array_filter($headers));
            $this->warnings[] = 'Column "property_code" not found. Make sure you are using the import template. Columns found in your file: ' . $found;
            return;
        }

        $dataRowCount = 0;
        $rowNum       = 1;
        foreach ($lines as $line) {
            $rowNum++;
            $values = $parseLine($line);
            $data   = [];
            foreach ($headers as $i => $field) {
                $data[$field] = $values[$i] ?? '';
            }

            if ($this->isEmptyRow($data)) continue;

            $dataRowCount++;
            $errors = $this->validateRow($data, $rowNum);

            if (!empty($errors)) {
                $this->rowErrors = array_merge($this->rowErrors, $errors);
                continue;
            }

            try {
                $this->createAssociation($data);
                $this->imported++;
            } catch (\Throwable $e) {
                $this->rowErrors[] = ['row' => $rowNum, 'field' => 'db', 'value' => '', 'error' => 'Database error: ' . $e->getMessage()];
            }
        }

        if ($dataRowCount === 0) {
            $this->warnings[] = 'No data rows found. Add your association data starting from row 2.';
        }
    }

    private function runSpreadsheet(): void
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
                $val = $this->cleanHeader((string) ($cell->getValue() ?? ''));
                if ($val !== '') {
                    $headers[$cell->getColumn()] = $val;
                }
            }
        }

        if (empty($headers)) {
            $this->warnings[] = 'Row 1 is empty — cannot read column names.';
            return;
        }

        if (!in_array('property_code', $headers, true)) {
            $found = implode(', ', array_filter($headers));
            $this->warnings[] = 'Column "property_code" not found. Make sure you are using the import template. Columns found in your file: ' . $found;
            return;
        }

        $dataRowCount = 0;
        for ($rowNum = 2; $rowNum <= $highestRow; $rowNum++) {
            $data = [];
            foreach ($headers as $col => $field) {
                $data[$field] = $sheet->getCell($col . $rowNum)->getFormattedValue();
            }

            if ($this->isEmptyRow($data)) continue;

            $dataRowCount++;
            $errors = $this->validateRow($data, $rowNum);

            if (!empty($errors)) {
                $this->rowErrors = array_merge($this->rowErrors, $errors);
                continue;
            }

            try {
                $this->createAssociation($data);
                $this->imported++;
            } catch (\Throwable $e) {
                $this->rowErrors[] = ['row' => $rowNum, 'field' => 'db', 'value' => '', 'error' => 'Database error: ' . $e->getMessage()];
            }
        }

        if ($dataRowCount === 0) {
            $this->warnings[] = "No data found in rows 2–{$highestRow}. Add your association data starting from row 2.";
        }
    }

    private function cleanHeader(string $h): string
    {
        $h = str_replace(['*', '"', "'"], '', $h);
        $h = preg_replace('/[\x00-\x1F\x7F\x{00A0}\x{FEFF}\x{200B}-\x{200F}\x{202A}-\x{202E}]/u', '', $h);
        return trim($h);
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

        // property_code — required, must exist
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
            }
        }

        // name_ar — required *
        if ($this->get($data, 'name_ar') === '') {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'name_ar',
                'value' => '',
                'error' => 'Required — Arabic association name is missing',
            ];
        }

        // name_en — required *
        if ($this->get($data, 'name_en') === '') {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'name_en',
                'value' => '',
                'error' => 'Required — English association name is missing',
            ];
        }

        // monthly_fee_per_unit — required *, non-negative number
        $fee = $this->get($data, 'monthly_fee_per_unit');
        if ($fee === '' || !is_numeric($fee) || (float) $fee < 0) {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'monthly_fee_per_unit',
                'value' => $fee,
                'error' => 'Required — must be a non-negative number',
            ];
        }

        // established_date — required *, valid date
        $establishedDate = $this->get($data, 'established_date');
        if ($establishedDate === '') {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'established_date',
                'value' => '',
                'error' => 'Required — established date is missing',
            ];
        } elseif (strtotime($establishedDate) === false) {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'established_date',
                'value' => $establishedDate,
                'error' => 'Invalid date format — use YYYY-MM-DD (e.g. 2024-01-15)',
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

        // fee_frequency — optional enum
        $freq = $this->get($data, 'fee_frequency');
        if ($freq !== '' && !in_array($freq, self::FEE_FREQUENCIES, true)) {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'fee_frequency',
                'value' => $freq,
                'error' => 'Invalid value "' . $freq . '". Allowed: ' . implode(', ', self::FEE_FREQUENCIES),
            ];
        }

        // unit_fees — optional, validate each value is numeric if provided
        $unitFeesRaw = $this->get($data, 'unit_fees');
        if ($unitFeesRaw !== '') {
            foreach ($this->parseUnitFees($unitFeesRaw) as $unit => $fee) {
                if (!is_numeric($fee) || (float) $fee < 0) {
                    $errors[] = [
                        'row'   => $rowNum,
                        'field' => 'unit_fees',
                        'value' => $unitFeesRaw,
                        'error' => 'Invalid fee for unit "' . $unit . '" — must be a non-negative number',
                    ];
                    break;
                }
            }
        }

        return $errors;
    }

    private function createAssociation(array $data): void
    {
        $propertyCode = $this->get($data, 'property_code');
        $property     = Property::where('code', $propertyCode)->first();

        $establishedDate = $this->get($data, 'established_date');
        $status          = $this->get($data, 'status');
        $freq            = $this->get($data, 'fee_frequency');
        $unitNumberRaw   = $this->get($data, 'unit_number');
        $unitFeesRaw     = $this->get($data, 'unit_fees');
        $phoneNumber     = $this->get($data, 'phone_number') ?: null;

        // unit_number: accept "60,78" or JSON ["60","78"]
        $unitNumbers = $this->parseUnitNumbers($unitNumberRaw);

        // unit_fees: accept "60:150,78:200" or JSON {"60":150,"78":200}
        $unitFees = $this->parseUnitFees($unitFeesRaw);

        Association::create([
            'property_id'                => $property->id,
            'name_ar'                    => $this->get($data, 'name_ar'),
            'name_en'                    => $this->get($data, 'name_en'),
            'established_date'           => date('Y-m-d', strtotime($establishedDate)),
            'monthly_fee_per_unit'       => (float) $this->get($data, 'monthly_fee_per_unit'),
            'fee_frequency'              => $freq !== '' ? $freq : 'monthly',
            'description_ar'             => $this->get($data, 'description_ar') ?: null,
            'description_en'             => $this->get($data, 'description_en') ?: null,
            'status'                     => $status !== '' ? $status : 'active',
            'phone_number'               => $phoneNumber,
            'unit_number'                => $unitNumbers ?: null,
            'unit_fees'                  => $unitFees ?: null,
            'electricity_account_number' => $this->get($data, 'electricity_account_number') ?: null,
            'water_account_number'       => $this->get($data, 'water_account_number') ?: null,
        ]);
    }

    /**
     * Parse unit numbers from a comma-separated string or JSON array.
     * "60,78,80"  →  ["60","78","80"]
     * '["60","78"]'  →  ["60","78"]
     */
    private function parseUnitNumbers(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') return [];

        // Try JSON first
        if (str_starts_with($raw, '[')) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return array_values(array_filter(array_map('trim', array_map('strval', $decoded))));
            }
        }

        // Comma-separated
        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }

    /**
     * Parse unit fees from a "unit:fee,unit:fee" string or JSON object.
     * "60:150,78:200"     →  ["60" => 150.0, "78" => 200.0]
     * '{"60":150,"78":200}'  →  ["60" => 150.0, "78" => 200.0]
     */
    private function parseUnitFees(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') return [];

        // Try JSON first
        if (str_starts_with($raw, '{')) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $result = [];
                foreach ($decoded as $unit => $fee) {
                    $result[(string) $unit] = (float) $fee;
                }
                return $result;
            }
        }

        // "unit:fee,unit:fee" format
        $result = [];
        foreach (explode(',', $raw) as $pair) {
            $parts = explode(':', trim($pair), 2);
            if (count($parts) === 2) {
                $unit = trim($parts[0]);
                $fee  = trim($parts[1]);
                if ($unit !== '' && is_numeric($fee)) {
                    $result[$unit] = (float) $fee;
                }
            }
        }
        return $result;
    }
}
