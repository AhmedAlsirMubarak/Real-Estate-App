<?php

namespace App\Imports;

use App\Models\Property;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PropertyImport
{
    public int $imported = 0;
    public array $rowErrors = [];
    public array $warnings = [];

    private const TYPES    = ['apartment_building', 'villa', 'farm', 'chalet'];
    private const PURPOSES = ['rent', 'sale', 'both'];
    private const SECTIONS = ['hoa', 'management', 'external'];
    private const STATUSES = ['active', 'sold', 'under_maintenance', 'archived'];

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

    private function cleanHeader(string $h): string
    {
        // Remove asterisks, quotes, then strip any invisible/control/directional Unicode chars
        $h = str_replace(['*', '"', "'"], '', $h);
        $h = preg_replace('/[\x00-\x1F\x7F\xC2\xA0]|\xE2\x80[\x8B-\x8F\xAA-\xAE]/u', '', $h);
        return trim($h);
    }

    private function runCsv(): void
    {
        $raw = file_get_contents($this->filePath);
        if ($raw === false) {
            throw new \RuntimeException('Cannot read file.');
        }

        // Strip BOM and convert to UTF-8
        if (str_starts_with($raw, "\xEF\xBB\xBF")) {
            $raw = substr($raw, 3);
        } elseif (str_starts_with($raw, "\xFF\xFE") || str_starts_with($raw, "\xFE\xFF")) {
            $raw = mb_convert_encoding($raw, 'UTF-8', 'UTF-16');
        }

        if (!mb_check_encoding($raw, 'UTF-8')) {
            $enc = mb_detect_encoding($raw, ['Windows-1256', 'ISO-8859-6', 'Windows-1252', 'ISO-8859-1'], true);
            if ($enc) {
                $raw = mb_convert_encoding($raw, 'UTF-8', $enc);
            }
        }

        $lines = array_filter(
            array_map('trim', explode("\n", str_replace(["\r\n", "\r"], "\n", $raw))),
            fn($l) => $l !== ''
        );

        if (count($lines) < 2) {
            $this->warnings[] = 'The file only has ' . count($lines) . ' row(s). Row 1 must be the header, with your data from row 2 onwards.';
            return;
        }

        // Auto-detect delimiter: comma, semicolon, or tab
        $firstLine = reset($lines);
        $counts = [
            ',' => substr_count($firstLine, ','),
            ';' => substr_count($firstLine, ';'),
            "\t" => substr_count($firstLine, "\t"),
        ];
        arsort($counts);
        $delimiter = array_key_first($counts);

        $parseLine = fn(string $line): array => str_getcsv($line, $delimiter, '"');

        $headers = array_map(
            fn($h) => $this->cleanHeader($h),
            $parseLine(array_shift($lines))
        );


        if (empty(array_filter($headers))) {
            $this->warnings[] = 'Row 1 is empty — cannot read column names.';
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
                $this->createProperty($data);
                $this->imported++;
            } catch (\Throwable $e) {
                $this->rowErrors[] = ['row' => $rowNum, 'field' => 'db', 'value' => '', 'error' => 'Database error: ' . $e->getMessage()];
            }
        }

        if ($dataRowCount === 0) {
            $this->warnings[] = 'No data rows found. Add your property data starting from row 2.';
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
                $this->createProperty($data);
                $this->imported++;
            } catch (\Throwable $e) {
                $this->rowErrors[] = ['row' => $rowNum, 'field' => 'db', 'value' => '', 'error' => 'Database error: ' . $e->getMessage()];
            }
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

        // address_ar — required (falls back to address_en)
        if ($this->get($data, 'address_ar') === '' && $this->get($data, 'address_en') === '') {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'address_ar',
                'value' => '',
                'error' => 'Required — Arabic or English address is missing',
            ];
        }

        // type — required enum
        $type = $this->get($data, 'type');
        if ($type === '') {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'type',
                'value' => '',
                'error' => 'Required. Allowed: ' . implode(', ', self::TYPES),
            ];
        } elseif (!in_array($type, self::TYPES, true)) {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'type',
                'value' => $type,
                'error' => 'Invalid value "' . $type . '". Allowed: ' . implode(', ', self::TYPES),
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

        // section — required enum
        $section = $this->get($data, 'section');
        if ($section === '') {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'section',
                'value' => '',
                'error' => 'Required. Allowed: ' . implode(', ', self::SECTIONS),
            ];
        } elseif (!in_array($section, self::SECTIONS, true)) {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'section',
                'value' => $section,
                'error' => 'Invalid value "' . $section . '". Allowed: ' . implode(', ', self::SECTIONS),
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

        // floors — optional positive integer
        $floors = $this->get($data, 'floors');
        if ($floors !== '' && (!is_numeric($floors) || (int) $floors < 1)) {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'floors',
                'value' => $floors,
                'error' => 'Must be a positive integer (1 or more)',
            ];
        }

        // total_area — optional non-negative number
        $area = $this->get($data, 'total_area');
        if ($area !== '' && (!is_numeric($area) || (float) $area < 0)) {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'total_area',
                'value' => $area,
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

        // bathrooms — optional non-negative integer
        $bathrooms = $this->get($data, 'bathrooms');
        if ($bathrooms !== '' && (!is_numeric($bathrooms) || (int) $bathrooms < 0)) {
            $errors[] = [
                'row'   => $rowNum,
                'field' => 'bathrooms',
                'value' => $bathrooms,
                'error' => 'Must be 0 or a positive integer',
            ];
        }

        return $errors;
    }

    private function createProperty(array $data): void
    {
        $nameAr = $this->get($data, 'name_ar') ?: $this->get($data, 'name_en');
        $addrAr = $this->get($data, 'address_ar') ?: $this->get($data, 'address_en');
        $cityAr = $this->get($data, 'city_ar') ?: null;
        $descAr = $this->get($data, 'description_ar') ?: null;
        $type   = $this->get($data, 'type');
        $floors = $this->get($data, 'floors');
        $area   = $this->get($data, 'total_area');
        $beds   = $this->get($data, 'bedrooms');
        $baths  = $this->get($data, 'bathrooms');
        $status = $this->get($data, 'status');

        Property::create([
            'code'           => $this->generateCode($type),
            'name_ar'        => $nameAr ?: null,
            'name_en'        => $this->get($data, 'name_en') ?: null,
            'name'           => $nameAr ?: $this->get($data, 'name_en'),
            'type'           => $type,
            'purpose'        => $this->get($data, 'purpose'),
            'section'        => $this->get($data, 'section'),
            'city_ar'        => $cityAr,
            'city_en'        => $this->get($data, 'city_en') ?: null,
            'city'           => $cityAr,
            'address_ar'     => $addrAr,
            'address_en'     => $this->get($data, 'address_en') ?: null,
            'address'        => $addrAr,
            'description_ar' => $descAr,
            'description_en' => $this->get($data, 'description_en') ?: null,
            'description'    => $descAr,
            'floors'         => $floors !== '' ? (int) $floors : null,
            'total_area'     => $area   !== '' ? (float) $area : null,
            'bedrooms'       => $beds   !== '' ? (int) $beds : null,
            'bathrooms'      => $baths  !== '' ? (int) $baths : null,
            'status'         => $status !== '' ? $status : 'active',
        ]);
    }

    private function generateCode(string $type): string
    {
        $prefix = match ($type) {
            'apartment_building' => 'TH-B',
            'villa'              => 'TH-V',
            'farm'               => 'TH-F',
            'chalet'             => 'TH-C',
            default              => 'TH',
        };
        $count = Property::where('type', $type)->count() + 1;
        return sprintf('%s-%03d', $prefix, $count);
    }
}
