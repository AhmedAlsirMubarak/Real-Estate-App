<?php

namespace App\Imports;

use App\Models\Property;
use App\Models\RentalContract;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TenantImport
{
    public int   $imported  = 0;
    public array $rowErrors = [];
    public array $warnings  = [];

    private string $filePath;
    private bool   $hasBilingual;

    public function __construct(string $filePath)
    {
        $this->filePath     = $filePath;
        $this->hasBilingual = Schema::hasColumn('users', 'name_ar') && Schema::hasColumn('users', 'name_en');
    }

    public function run(): void
    {
        $spreadsheet = IOFactory::load($this->filePath);
        $sheet       = $spreadsheet->getSheet(0);

        $highestRow = $sheet->getHighestDataRow();
        $highestCol = $sheet->getHighestDataColumn();

        if ($highestRow < 2) {
            $this->warnings[] = "The file only has {$highestRow} row(s). Row 1 must be the header, with data from row 2 onwards.";
            return;
        }

        // Build header map from row 1
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

            $this->createTenant($data);
            $this->imported++;
        }

        if ($dataRowCount === 0) {
            $this->warnings[] = "No data found in rows 2–{$highestRow}. Add your tenant data starting from row 2.";
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

        if ($this->get($data, 'name_ar') === '') {
            $errors[] = ['row' => $rowNum, 'field' => 'name_ar', 'value' => '', 'error' => 'Required — Arabic name is missing'];
        }

        if ($this->get($data, 'name_en') === '') {
            $errors[] = ['row' => $rowNum, 'field' => 'name_en', 'value' => '', 'error' => 'Required — English name is missing'];
        }

        $email = $this->get($data, 'email');
        if ($email === '') {
            $errors[] = ['row' => $rowNum, 'field' => 'email', 'value' => '', 'error' => 'Required — email is missing'];
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = ['row' => $rowNum, 'field' => 'email', 'value' => $email, 'error' => 'Invalid email format'];
        } elseif (User::where('email', $email)->exists()) {
            $errors[] = ['row' => $rowNum, 'field' => 'email', 'value' => $email, 'error' => 'Email already exists in the system'];
        }

        $password = $this->get($data, 'password');
        if ($password !== '' && strlen($password) < 8) {
            $errors[] = ['row' => $rowNum, 'field' => 'password', 'value' => '(hidden)', 'error' => 'Password must be at least 8 characters'];
        }

        // Contract fields — only validate if property_code or unit_number is given
        $propertyCode = $this->get($data, 'property_code');
        $unitNumber   = $this->get($data, 'unit_number');
        $hasContract  = $propertyCode !== '' || $unitNumber !== '';

        if ($hasContract) {
            if ($propertyCode === '') {
                $errors[] = ['row' => $rowNum, 'field' => 'property_code', 'value' => '', 'error' => 'Required when unit_number is provided'];
            }
            if ($unitNumber === '') {
                $errors[] = ['row' => $rowNum, 'field' => 'unit_number', 'value' => '', 'error' => 'Required when property_code is provided'];
            }

            if ($propertyCode !== '' && $unitNumber !== '') {
                $unit = $this->findUnit($propertyCode, $unitNumber);
                if (!$unit) {
                    $errors[] = ['row' => $rowNum, 'field' => 'unit_number', 'value' => $unitNumber,
                        'error' => "Unit \"{$unitNumber}\" not found in property \"{$propertyCode}\""];
                } elseif ($unit->status !== 'available') {
                    $errors[] = ['row' => $rowNum, 'field' => 'unit_number', 'value' => $unitNumber,
                        'error' => "Unit \"{$unitNumber}\" is not available (status: {$unit->status})"];
                }
            }

            $start = $this->get($data, 'start_date');
            $end   = $this->get($data, 'end_date');
            $rent  = $this->get($data, 'monthly_rent');

            if ($start === '') {
                $errors[] = ['row' => $rowNum, 'field' => 'start_date', 'value' => '', 'error' => 'Required when linking a unit'];
            } elseif (!strtotime($start)) {
                $errors[] = ['row' => $rowNum, 'field' => 'start_date', 'value' => $start, 'error' => 'Invalid date format — use YYYY-MM-DD'];
            }

            if ($end === '') {
                $errors[] = ['row' => $rowNum, 'field' => 'end_date', 'value' => '', 'error' => 'Required when linking a unit'];
            } elseif (!strtotime($end)) {
                $errors[] = ['row' => $rowNum, 'field' => 'end_date', 'value' => $end, 'error' => 'Invalid date format — use YYYY-MM-DD'];
            } elseif ($start !== '' && strtotime($start) && strtotime($end) <= strtotime($start)) {
                $errors[] = ['row' => $rowNum, 'field' => 'end_date', 'value' => $end, 'error' => 'End date must be after start date'];
            }

            if ($rent === '') {
                $errors[] = ['row' => $rowNum, 'field' => 'monthly_rent', 'value' => '', 'error' => 'Required when linking a unit'];
            } elseif (!is_numeric($rent) || (float) $rent < 0) {
                $errors[] = ['row' => $rowNum, 'field' => 'monthly_rent', 'value' => $rent, 'error' => 'Must be a non-negative number'];
            }

            $deposit = $this->get($data, 'deposit');
            if ($deposit !== '' && (!is_numeric($deposit) || (float) $deposit < 0)) {
                $errors[] = ['row' => $rowNum, 'field' => 'deposit', 'value' => $deposit, 'error' => 'Must be a non-negative number'];
            }
        }

        return $errors;
    }

    private function createTenant(array $data): void
    {
        $nameAr   = $this->get($data, 'name_ar');
        $nameEn   = $this->get($data, 'name_en');
        $email    = $this->get($data, 'email');
        $phone    = $this->get($data, 'phone') ?: null;
        $password = $this->get($data, 'password') ?: 'Tenant@2025';

        $userPayload = [
            'name'     => $nameAr,
            'email'    => $email,
            'phone'    => $phone,
            'password' => Hash::make($password),
        ];

        if ($this->hasBilingual) {
            $userPayload['name_ar'] = $nameAr;
            $userPayload['name_en'] = $nameEn;
        }

        $user = User::create($userPayload);
        $user->assignRole('tenant');

        $tenant = Tenant::create([
            'user_id'           => $user->id,
            'national_id'       => $this->get($data, 'national_id') ?: null,
            'phone'             => $phone,
            'emergency_contact' => $this->get($data, 'emergency_contact') ?: null,
        ]);

        $propertyCode = $this->get($data, 'property_code');
        $unitNumber   = $this->get($data, 'unit_number');

        if ($propertyCode !== '' && $unitNumber !== '') {
            $unit = $this->findUnit($propertyCode, $unitNumber);

            if ($unit) {
                RentalContract::create([
                    'unit_id'      => $unit->id,
                    'tenant_id'    => $tenant->id,
                    'start_date'   => $this->get($data, 'start_date'),
                    'end_date'     => $this->get($data, 'end_date'),
                    'monthly_rent' => (float) $this->get($data, 'monthly_rent'),
                    'deposit'      => (float) ($this->get($data, 'deposit') ?: '0'),
                    'status'       => 'active',
                ]);

                $unit->update(['status' => 'rented']);
            }
        }
    }

    private function findUnit(string $propertyCode, string $unitNumber): ?Unit
    {
        $property = Property::where('code', $propertyCode)->first();
        if (!$property) {
            return null;
        }

        return Unit::where('property_id', $property->id)
            ->where('unit_number', $unitNumber)
            ->first();
    }
}
