<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'manage_properties', 'view_properties',
            'manage_owners',     'view_owners',
            'manage_associations','view_associations',
            'manage_dues',       'view_dues',
            'manage_meetings',   'view_meetings',
            'manage_employees',  'view_employees',
            'manage_salaries',   'view_salaries',
            'manage_payments',   'confirm_payments', 'view_payments',
            'view_reports',      'export_reports',
            'manage_maintenance','view_maintenance',
            'manage_expenses',   'view_expenses',
            'manage_users',
            'manage_contacts',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->syncPermissions($permissions);

        $accountant = Role::firstOrCreate(['name' => 'accountant']);
        $accountant->syncPermissions([
            'view_properties',
            'view_owners',
            'view_associations', 'manage_dues', 'view_dues',
            'view_salaries', 'manage_salaries',
            'manage_payments', 'confirm_payments', 'view_payments',
            'view_reports', 'export_reports',
            'view_expenses',
        ]);

        $employee = Role::firstOrCreate(['name' => 'employee']);
        $employee->syncPermissions([
            'view_properties',
            'view_associations',
            'view_dues',
            'view_meetings',
            'manage_maintenance', 'view_maintenance',
            'confirm_payments', 'view_payments',
        ]);

        $owner = Role::firstOrCreate(['name' => 'owner']);
        $owner->syncPermissions([
            'view_properties',
            'view_associations',
            'view_dues',
            'view_meetings',
            'view_reports',
        ]);

        Role::firstOrCreate(['name' => 'tenant']);
        Role::firstOrCreate(['name' => 'buyer']);

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
