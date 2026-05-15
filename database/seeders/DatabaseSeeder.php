<?php

namespace Database\Seeders;

use App\Models\Association;
use App\Models\AssociationDue;
use App\Models\AssociationMeeting;
use App\Models\Buyer;
use App\Models\Expense;
use App\Models\Installment;
use App\Models\MaintenanceRequest;
use App\Models\Owner;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RentalContract;
use App\Models\Salary;
use App\Models\SaleContract;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedRoles();
        $this->call(PermissionsSeeder::class);

        [$manager, $accountant, $employee1, $employee2] = $this->seedStaff();
        [$owner1, $owner2] = $this->seedOwners();
        [$owner3] = $this->seedExtraOwners();

        // ------- Property 1: عمارة (مملوكة للشركة) — للإيجار
        $building1 = Property::create([
            'code'        => 'TH-B-001',
            'name'        => 'برج ثروة 1',
            'name_ar'     => 'برج ثروة 1',
            'name_en'     => 'Tharwa Tower 1',
            'type'        => 'apartment_building',
            'purpose'     => 'rent',
            'address'     => 'شارع الملك فهد، حي العليا',
            'address_ar'  => 'شارع الملك فهد، حي العليا',
            'address_en'  => 'King Fahd Rd, Al Olaya',
            'city'        => 'الرياض',
            'city_ar'     => 'الرياض',
            'city_en'     => 'Riyadh',
            'description' => 'عمارة سكنية حديثة — مملوكة للشركة',
            'description_ar' => 'عمارة سكنية حديثة — مملوكة للشركة',
            'description_en' => 'Modern residential building owned by the company',
            'owner_id'    => null,
            'employee_id' => $employee1->id,
            'floors'      => 10,
            'status'      => 'active',
        ]);

        $this->seedApartmentUnits($building1, [
            ['unit_number' => '101', 'floor' => 1, 'type' => 'apartment', 'area' => 120, 'bedrooms' => 3, 'bathrooms' => 2, 'rent_price' => 3000, 'status' => 'rented'],
            ['unit_number' => '102', 'floor' => 1, 'type' => 'apartment', 'area' => 100, 'bedrooms' => 2, 'bathrooms' => 1, 'rent_price' => 2500, 'status' => 'available'],
            ['unit_number' => '201', 'floor' => 2, 'type' => 'apartment', 'area' => 130, 'bedrooms' => 3, 'bathrooms' => 2, 'rent_price' => 3500, 'status' => 'available'],
            ['unit_number' => '301', 'floor' => 3, 'type' => 'office',    'area' => 80,  'bedrooms' => 0, 'bathrooms' => 1, 'rent_price' => 4000, 'status' => 'available'],
        ]);

        // ------- Property 2: عمارة (مالك خارجي) — إدارة + إيجار
        $building2 = Property::create([
            'code'        => 'TH-B-002',
            'name'        => 'عمارة النور',
            'name_ar'     => 'عمارة النور',
            'name_en'     => 'Al Noor Building',
            'type'        => 'apartment_building',
            'purpose'     => 'rent',
            'address'     => 'شارع الأمير سلطان، حي المروج',
            'address_ar'  => 'شارع الأمير سلطان، حي المروج',
            'address_en'  => 'Prince Sultan St, Al Muruj',
            'city'        => 'الرياض',
            'city_ar'     => 'الرياض',
            'city_en'     => 'Riyadh',
            'description' => 'عمارة — تُدار لصالح المالك مع عمولة 10%',
            'description_ar' => 'عمارة — تُدار لصالح المالك مع عمولة 10%',
            'description_en' => 'Building managed for an external owner with 10% commission',
            'owner_id'    => $owner1->id,
            'employee_id' => $employee2->id,
            'floors'      => 6,
            'status'      => 'active',
        ]);

        $this->seedApartmentUnits($building2, [
            ['unit_number' => '101', 'floor' => 1, 'type' => 'shop',      'area' => 80,  'bedrooms' => 0, 'bathrooms' => 1, 'rent_price' => 5000, 'status' => 'available'],
            ['unit_number' => '201', 'floor' => 2, 'type' => 'apartment', 'area' => 110, 'bedrooms' => 2, 'bathrooms' => 2, 'rent_price' => 2800, 'status' => 'available'],
        ]);

        // ------- Property 3: فيلا (مالك خارجي) — للبيع
        $villa = Property::create([
            'code'        => 'TH-V-001',
            'name'        => 'فيلا الياسمين',
            'name_ar'     => 'فيلا الياسمين',
            'name_en'     => 'Al Yasmin Villa',
            'type'        => 'villa',
            'purpose'     => 'sale',
            'address'     => 'حي الملقا، شارع الأمير محمد بن سلمان',
            'address_ar'  => 'حي الملقا، شارع الأمير محمد بن سلمان',
            'address_en'  => 'Al Malqa, Prince Mohammed bin Salman St',
            'city'        => 'الرياض',
            'city_ar'     => 'الرياض',
            'city_en'     => 'Riyadh',
            'description' => 'فيلا فاخرة دورين + ملحق — مملوكة لمالك خارجي وتُعرض للبيع',
            'description_ar' => 'فيلا فاخرة دورين + ملحق — مملوكة لمالك خارجي وتُعرض للبيع',
            'description_en' => 'Luxury two-floor villa with annex, externally owned and listed for sale',
            'owner_id'    => $owner2->id,
            'employee_id' => $employee1->id,
            'floors'      => 2,
            'total_area'  => 450,
            'bedrooms'    => 5,
            'bathrooms'   => 4,
            'status'      => 'active',
        ]);

        $villaUnit = Unit::create([
            'property_id'  => $villa->id,
            'unit_number'  => null,
            'floor'        => null,
            'type'         => 'villa_unit',
            'area'         => 450,
            'bedrooms'     => 5,
            'bathrooms'    => 4,
            'listing_type' => 'sale',
            'sale_price'   => 2500000,
            'status'       => 'reserved',
        ]);

        // ------- Property 4: مزرعة (مملوكة للشركة) — للإيجار الموسمي
        $farm = Property::create([
            'code'        => 'TH-F-001',
            'name'        => 'مزرعة الواحة',
            'name_ar'     => 'مزرعة الواحة',
            'name_en'     => 'Al Waha Farm',
            'type'        => 'farm',
            'purpose'     => 'rent',
            'address'     => 'طريق الخرج، كيلو 45',
            'address_ar'  => 'طريق الخرج، كيلو 45',
            'address_en'  => 'Al Kharj Road, Km 45',
            'city'        => 'الرياض',
            'city_ar'     => 'الرياض',
            'city_en'     => 'Riyadh',
            'description' => 'مزرعة 10 آلاف م² مع مسبح ومجلس — للإيجار الموسمي',
            'description_ar' => 'مزرعة 10 آلاف م² مع مسبح ومجلس — للإيجار الموسمي',
            'description_en' => '10,000 m² farm with pool and majlis for seasonal rent',
            'owner_id'    => null,
            'employee_id' => $employee2->id,
            'total_area'  => 10000,
            'bedrooms'    => 4,
            'bathrooms'   => 3,
            'status'      => 'active',
        ]);

        Unit::create([
            'property_id'  => $farm->id,
            'type'         => 'farm_unit',
            'area'         => 10000,
            'bedrooms'     => 4,
            'bathrooms'    => 3,
            'listing_type' => 'rent',
            'rent_price'   => 8000,
            'status'       => 'available',
        ]);

        // ------- Property 5: شاليه (مملوكة للشركة) — للإيجار
        $chalet = Property::create([
            'code'        => 'TH-C-001',
            'name'        => 'شاليه الموج',
            'name_ar'     => 'شاليه الموج',
            'name_en'     => 'Al Mawj Chalet',
            'type'        => 'chalet',
            'purpose'     => 'rent',
            'address'     => 'كورنيش الخبر',
            'address_ar'  => 'كورنيش الخبر',
            'address_en'  => 'Khobar Corniche',
            'city'        => 'الخبر',
            'city_ar'     => 'الخبر',
            'city_en'     => 'Khobar',
            'description' => 'شاليه على البحر — إيجار يومي/أسبوعي',
            'description_ar' => 'شاليه على البحر — إيجار يومي/أسبوعي',
            'description_en' => 'Seafront chalet available for daily/weekly rent',
            'owner_id'    => null,
            'employee_id' => $employee1->id,
            'total_area'  => 300,
            'bedrooms'    => 3,
            'bathrooms'   => 2,
            'status'      => 'active',
        ]);

        Unit::create([
            'property_id'  => $chalet->id,
            'type'         => 'chalet_unit',
            'area'         => 300,
            'bedrooms'     => 3,
            'bathrooms'    => 2,
            'listing_type' => 'rent',
            'rent_price'   => 1200,
            'status'       => 'available',
        ]);

        // ------- Tenant + Rental Contract + Payments + Maintenance
        $this->seedTenantFlow($building1);

        // ------- Buyer + Sale Contract + Installments
        $this->seedBuyerFlow($villaUnit);

        // ------- Expenses (company + property-level)
        $this->seedExpenses($manager, $building1, $villa);
        $this->syncBilingualUserNames();

        // ------- HOA: Association + fractional ownership + dues + meeting
        $this->seedAssociation($building2, $owner1, $owner3);

        // ------- Salaries (last 2 months)
        $this->seedSalaries($manager, $accountant, $employee1, $employee2);

        $this->command->info('✅ تم إنشاء البيانات التجريبية الجديدة');
        $this->command->info('👤 Manager:    manager@tharwa.com');
        $this->command->info('👤 Employee:   employee1@tharwa.com');
        $this->command->info('👤 Accountant: accountant@tharwa.com');
        $this->command->info('👤 Tenant:     tenant@tharwa.com');
        $this->command->info('👤 Owner:      owner1@tharwa.com');
        $this->command->info('👤 Buyer:      buyer@tharwa.com');
        $this->command->info('🔐 Password:   password');
    }

    private function seedRoles(): void
    {
        foreach (['manager', 'employee', 'accountant', 'tenant', 'owner', 'buyer'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }

    private function seedStaff(): array
    {
        $manager = User::firstOrCreate(
            ['email' => 'manager@tharwa.com'],
            ['name' => 'مدير ثروة', 'password' => Hash::make('password'), 'phone' => '0501234567']
        );
        $manager->syncRoles(['manager']);

        $accountant = User::firstOrCreate(
            ['email' => 'accountant@tharwa.com'],
            ['name' => 'سارة المحاسبة', 'password' => Hash::make('password'), 'phone' => '0502345678']
        );
        $accountant->syncRoles(['accountant']);

        $employee1 = User::firstOrCreate(
            ['email' => 'employee1@tharwa.com'],
            ['name' => 'أحمد المسؤول', 'password' => Hash::make('password'), 'phone' => '0507654321']
        );
        $employee1->syncRoles(['employee']);

        $employee2 = User::firstOrCreate(
            ['email' => 'employee2@tharwa.com'],
            ['name' => 'محمد الموظف', 'password' => Hash::make('password'), 'phone' => '0509876543']
        );
        $employee2->syncRoles(['employee']);

        return [$manager, $accountant, $employee1, $employee2];
    }

    private function seedOwners(): array
    {
        $owner1User = User::firstOrCreate(
            ['email' => 'owner1@tharwa.com'],
            ['name' => 'خالد المالك', 'password' => Hash::make('password'), 'phone' => '0551111111']
        );
        $owner1User->syncRoles(['owner']);
        $owner1 = Owner::firstOrCreate(
            ['user_id' => $owner1User->id],
            ['national_id' => '1010101010', 'phone' => '0551111111', 'bank_account' => 'SA0312345678901234', 'commission_rate' => 10.00]
        );

        $owner2User = User::firstOrCreate(
            ['email' => 'owner2@tharwa.com'],
            ['name' => 'فهد بن سعد', 'password' => Hash::make('password'), 'phone' => '0552222222']
        );
        $owner2User->syncRoles(['owner']);
        $owner2 = Owner::firstOrCreate(
            ['user_id' => $owner2User->id],
            ['national_id' => '2020202020', 'phone' => '0552222222', 'bank_account' => 'SA0398765432109876', 'commission_rate' => 8.00]
        );

        return [$owner1, $owner2];
    }

    private function seedApartmentUnits(Property $property, array $units): void
    {
        foreach ($units as $unit) {
            Unit::create(array_merge($unit, [
                'property_id'  => $property->id,
                'listing_type' => 'rent',
            ]));
        }
    }

    private function seedTenantFlow(Property $building): void
    {
        $tenantUser = User::firstOrCreate(
            ['email' => 'tenant@tharwa.com'],
            ['name' => 'علي المستأجر', 'password' => Hash::make('password'), 'phone' => '0503456789']
        );
        $tenantUser->syncRoles(['tenant']);

        $tenant = Tenant::firstOrCreate(
            ['user_id' => $tenantUser->id],
            ['national_id' => '1234567890', 'phone' => '0503456789', 'emergency_contact' => 'محمد علي - 0509999999']
        );

        $unit101 = $building->units()->where('unit_number', '101')->first();
        if (! $unit101) return;

        $contract = RentalContract::create([
            'unit_id'      => $unit101->id,
            'tenant_id'    => $tenant->id,
            'start_date'   => now()->subMonths(3),
            'end_date'     => now()->addMonths(9),
            'monthly_rent' => 3000,
            'deposit'      => 6000,
            'status'       => 'active',
        ]);

        for ($i = 3; $i >= 1; $i--) {
            $month = now()->subMonths($i);
            Payment::create([
                'rental_contract_id' => $contract->id,
                'tenant_id'          => $tenant->id,
                'amount'             => 3000,
                'month'              => $month->month,
                'year'               => $month->year,
                'status'             => $i > 1 ? 'paid' : 'pending',
                'paid_at'            => $i > 1 ? $month->copy()->addDays(5) : null,
            ]);
        }

        MaintenanceRequest::create([
            'tenant_id'   => $tenant->id,
            'unit_id'     => $unit101->id,
            'title'       => 'تسريب مياه في الحمام',
            'description' => 'يوجد تسريب في حنفية الحمام الرئيسي',
            'priority'    => 'high',
            'status'      => 'pending',
        ]);
    }

    private function seedBuyerFlow(Unit $villaUnit): void
    {
        $buyerUser = User::firstOrCreate(
            ['email' => 'buyer@tharwa.com'],
            ['name' => 'عبدالعزيز المشتري', 'password' => Hash::make('password'), 'phone' => '0554444444']
        );
        $buyerUser->syncRoles(['buyer']);

        $buyer = Buyer::firstOrCreate(
            ['user_id' => $buyerUser->id],
            ['national_id' => '3030303030', 'phone' => '0554444444', 'address' => 'الرياض']
        );

        $totalPrice   = 2500000;
        $downPayment  = 500000;
        $installments = 24;
        $installmentAmount = round(($totalPrice - $downPayment) / $installments, 2);

        $contract = SaleContract::create([
            'contract_number'    => 'SC-2026-0001',
            'unit_id'            => $villaUnit->id,
            'buyer_id'           => $buyer->id,
            'total_price'        => $totalPrice,
            'down_payment'       => $downPayment,
            'payment_plan'       => 'installments',
            'installment_count'  => $installments,
            'installment_amount' => $installmentAmount,
            'contract_date'      => now()->subMonths(2),
            'status'             => 'active',
        ]);

        for ($i = 1; $i <= $installments; $i++) {
            $dueDate = now()->subMonths(2)->addMonths($i);
            $isPaid  = $i <= 2;
            Installment::create([
                'sale_contract_id'   => $contract->id,
                'installment_number' => $i,
                'amount'             => $installmentAmount,
                'due_date'           => $dueDate,
                'status'             => $isPaid ? 'paid' : 'pending',
                'paid_at'            => $isPaid ? $dueDate->copy()->addDays(3) : null,
            ]);
        }
    }

    private function syncBilingualUserNames(): void
    {
        if (! Schema::hasColumn('users', 'name_ar') || ! Schema::hasColumn('users', 'name_en')) {
            return;
        }

        DB::table('users')
            ->whereNull('name_ar')
            ->orWhere('name_ar', '')
            ->update(['name_ar' => DB::raw('name')]);

        DB::table('users')
            ->whereNull('name_en')
            ->orWhere('name_en', '')
            ->update(['name_en' => DB::raw('name')]);

        $knownNames = [
            'manager@tharwa.com' => ['مدير ثروة', 'Tharwa Manager'],
            'accountant@tharwa.com' => ['سارة المحاسبة', 'Sarah Accountant'],
            'employee1@tharwa.com' => ['أحمد المسؤول', 'Ahmad Supervisor'],
            'employee2@tharwa.com' => ['محمد الموظف', 'Mohammed Employee'],
            'owner1@tharwa.com' => ['خالد المالك', 'Khalid Owner'],
            'owner2@tharwa.com' => ['فهد بن سعد', 'Fahad Bin Saad'],
            'tenant@tharwa.com' => ['علي المستأجر', 'Ali Tenant'],
            'buyer@tharwa.com' => ['عبدالعزيز المشتري', 'Abdulaziz Buyer'],
        ];

        foreach ($knownNames as $email => [$nameAr, $nameEn]) {
            User::where('email', $email)->update([
                'name' => $nameAr,
                'name_ar' => $nameAr,
                'name_en' => $nameEn,
            ]);
        }
    }

    private function seedExtraOwners(): array
    {
        $u = User::firstOrCreate(
            ['email' => 'owner3@tharwa.com'],
            ['name' => 'سعد العبدالله', 'password' => Hash::make('password'), 'phone' => '0553333333']
        );
        $u->syncRoles(['owner']);
        $owner = Owner::firstOrCreate(
            ['user_id' => $u->id],
            ['national_id' => '4040404040', 'phone' => '0553333333', 'bank_account' => 'SA0311112222333344', 'commission_rate' => 12.00]
        );
        return [$owner];
    }

    private function seedAssociation(Property $property, Owner $primaryOwner, Owner $coOwner): void
    {
        // Make property co-owned (fractional) — 60% / 40%
        $property->owners()->syncWithoutDetaching([
            $primaryOwner->id => [
                'ownership_percentage' => 60,
                'is_primary'           => true,
                'since_date'           => now()->subYears(3)->toDateString(),
            ],
            $coOwner->id => [
                'ownership_percentage' => 40,
                'is_primary'           => false,
                'since_date'           => now()->subYears(2)->toDateString(),
            ],
        ]);

        $association = Association::firstOrCreate(
            ['property_id' => $property->id],
            [
                'name_ar'              => 'جمعية ملاك ' . ($property->getRawOriginal('name_ar') ?? 'العمارة'),
                'name_en'              => ($property->getRawOriginal('name_en') ?? 'Building') . ' Owners Association',
                'established_date'     => now()->subYears(3),
                'monthly_fee_per_unit' => 200,
                'description_ar'       => 'جمعية ملاك مسؤولة عن إدارة المرافق المشتركة وصيانة المبنى',
                'description_en'       => 'Owners association managing shared facilities and building maintenance',
                'status'               => 'active',
            ]
        );

        // Generate dues for last 3 months
        $unitCount = max($property->units()->count(), 1);
        $totalAmount = $association->monthly_fee_per_unit * $unitCount;
        foreach ([$primaryOwner, $coOwner] as $owner) {
            $share = $owner->id === $primaryOwner->id ? 60 : 40;
            for ($i = 2; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                AssociationDue::firstOrCreate(
                    [
                        'association_id' => $association->id,
                        'owner_id'       => $owner->id,
                        'period_month'   => $month->month,
                        'period_year'    => $month->year,
                    ],
                    [
                        'amount'   => round($totalAmount * ($share / 100), 2),
                        'due_date' => $month->copy()->day(5),
                        'status'   => $i > 0 ? 'paid' : 'pending',
                        'paid_at'  => $i > 0 ? $month->copy()->day(7) : null,
                    ]
                );
            }
        }

        // Schedule one upcoming meeting + one completed
        AssociationMeeting::firstOrCreate(
            ['association_id' => $association->id, 'title_ar' => 'الاجتماع السنوي العمومي'],
            [
                'title_en'     => 'Annual General Meeting',
                'scheduled_at' => now()->addDays(15)->setTime(19, 0),
                'location_ar'  => 'صالة المبنى',
                'location_en'  => 'Building lobby',
                'agenda_ar'    => "1. اعتماد الميزانية\n2. صيانة المصعد\n3. اقتراح زيادة الرسم الشهري",
                'agenda_en'    => "1. Approve budget\n2. Elevator maintenance\n3. Proposed fee increase",
                'status'       => 'scheduled',
            ]
        );

        AssociationMeeting::firstOrCreate(
            ['association_id' => $association->id, 'title_ar' => 'اجتماع طارئ — صيانة'],
            [
                'title_en'     => 'Emergency Meeting — Maintenance',
                'scheduled_at' => now()->subMonths(2)->setTime(20, 0),
                'location_ar'  => 'صالة المبنى',
                'location_en'  => 'Building lobby',
                'agenda_ar'    => 'مناقشة عرض صيانة المصعد',
                'agenda_en'    => 'Discuss elevator maintenance quote',
                'minutes_ar'   => 'تمت الموافقة على عرض شركة الصيانة بمبلغ 2500 ريال',
                'minutes_en'   => 'Approved maintenance company quote of 2500 SAR',
                'status'       => 'completed',
            ]
        );
    }

    private function seedSalaries(User $manager, User $accountant, User $employee1, User $employee2): void
    {
        $employees = [
            [$manager, 12000],
            [$accountant, 8000],
            [$employee1, 5500],
            [$employee2, 5000],
        ];
        for ($i = 1; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            foreach ($employees as [$user, $base]) {
                Salary::firstOrCreate(
                    [
                        'employee_id'  => $user->id,
                        'period_month' => $month->month,
                        'period_year'  => $month->year,
                    ],
                    [
                        'base_salary' => $base,
                        'bonuses'     => $i === 1 ? 500 : 0,
                        'deductions'  => 0,
                        'net_paid'    => $base + ($i === 1 ? 500 : 0),
                        'status'      => $i === 1 ? 'paid' : 'pending',
                        'paid_at'     => $i === 1 ? $month->copy()->endOfMonth() : null,
                        'paid_by'     => $i === 1 ? $manager->id : null,
                    ]
                );
            }
        }
    }

    private function seedExpenses(User $manager, Property $building1, Property $villa): void
    {
        // Company-level expenses
        Expense::create([
            'scope'        => 'company',
            'category'     => 'salaries',
            'title'        => 'رواتب الموظفين — شهر ' . now()->format('m/Y'),
            'amount'       => 35000,
            'expense_date' => now()->startOfMonth(),
            'description'  => 'رواتب المدير + 2 موظفين + محاسب',
            'paid_by'      => $manager->id,
        ]);

        Expense::create([
            'scope'        => 'company',
            'category'     => 'marketing',
            'title'        => 'حملة إعلانية على سناب شات',
            'amount'       => 4500,
            'expense_date' => now()->subDays(10),
            'paid_by'      => $manager->id,
        ]);

        Expense::create([
            'scope'        => 'company',
            'category'     => 'utilities',
            'title'        => 'فواتير المكتب (كهرباء + إنترنت)',
            'amount'       => 1800,
            'expense_date' => now()->subDays(5),
            'paid_by'      => $manager->id,
        ]);

        // Property-level expenses
        Expense::create([
            'expensable_type' => Property::class,
            'expensable_id'   => $building1->id,
            'scope'           => 'property',
            'category'        => 'maintenance',
            'title'           => 'صيانة المصعد',
            'amount'          => 2500,
            'expense_date'    => now()->subDays(20),
            'description'     => 'صيانة دورية للمصعد الرئيسي',
            'paid_by'         => $manager->id,
        ]);

        Expense::create([
            'expensable_type' => Property::class,
            'expensable_id'   => $building1->id,
            'scope'           => 'property',
            'category'        => 'utilities',
            'title'           => 'كهرباء المناطق المشتركة',
            'amount'           => 900,
            'expense_date'    => now()->subDays(7),
            'paid_by'         => $manager->id,
        ]);

        Expense::create([
            'expensable_type' => Property::class,
            'expensable_id'   => $villa->id,
            'scope'           => 'property',
            'category'        => 'marketing',
            'title'           => 'تصوير احترافي للفيلا',
            'amount'          => 1500,
            'expense_date'    => now()->subMonths(2),
            'description'     => 'جلسة تصوير + فيديو drone لعرض الفيلا',
            'paid_by'         => $manager->id,
        ]);
    }
}
