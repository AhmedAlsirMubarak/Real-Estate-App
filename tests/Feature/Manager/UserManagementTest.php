<?php

use App\Models\User;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Unit;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['manager', 'employee', 'accountant', 'tenant', 'owner', 'buyer'] as $role) {
        Role::firstOrCreate(['name' => $role]);
    }
});

test('manager can create user and assign role', function () {
    $manager = User::factory()->create();
    $manager->assignRole('manager');

    $response = $this->actingAs($manager)->post(route('manager.users.store'), [
        'name_ar' => 'موظف تجريبي',
        'name_en' => 'Test Employee',
        'email' => 'test-employee@example.com',
        'phone' => '96890000000',
        'role' => 'employee',
        'password' => 'StrongPass123',
        'password_confirmation' => 'StrongPass123',
    ]);

    $response->assertRedirect(route('manager.users.index'));

    $this->assertDatabaseHas('users', [
        'email' => 'test-employee@example.com',
        'is_blocked' => false,
    ]);

    $createdUser = User::where('email', 'test-employee@example.com')->first();
    expect($createdUser)->not->toBeNull();
    expect($createdUser->hasRole('employee'))->toBeTrue();
});

test('manager can block and unblock user', function () {
    $manager = User::factory()->create();
    $manager->assignRole('manager');

    $employee = User::factory()->create();
    $employee->assignRole('employee');

    $this->actingAs($manager)->patch(route('manager.users.toggle-block', $employee))
        ->assertRedirect();

    $employee->refresh();
    expect($employee->is_blocked)->toBeTrue();
    expect($employee->blocked_at)->not->toBeNull();

    $this->actingAs($manager)->patch(route('manager.users.toggle-block', $employee))
        ->assertRedirect();

    $employee->refresh();
    expect($employee->is_blocked)->toBeFalse();
    expect($employee->blocked_at)->toBeNull();
});

test('blocked users cannot login', function () {
    $blockedUser = User::factory()->create([
        'is_blocked' => true,
        'blocked_at' => now(),
    ]);

    $this->post('/login', [
        'email' => $blockedUser->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('manager can create tenant user with profile and contract from users form', function () {
    $manager = User::factory()->create();
    $manager->assignRole('manager');

    $property = Property::create([
        'code' => 'TEST-PROP-001',
        'name' => 'Test Property',
        'type' => 'apartment_building',
        'purpose' => 'rent',
        'address' => 'Muscat',
        'city' => 'Muscat',
        'status' => 'active',
    ]);

    $unit = Unit::create([
        'property_id' => $property->id,
        'unit_number' => 'A1',
        'type' => 'apartment',
        'listing_type' => 'rent',
        'status' => 'available',
        'rent_price' => 200,
    ]);

    $response = $this->actingAs($manager)->post(route('manager.users.store'), [
        'name_ar' => 'مستأجر تجريبي',
        'name_en' => 'Tenant User',
        'email' => 'tenant-user@example.com',
        'phone' => '96891111111',
        'role' => 'tenant',
        'password' => 'StrongPass123',
        'password_confirmation' => 'StrongPass123',
        'tenant_national_id' => '12345678',
        'tenant_emergency_contact' => 'Emergency Contact',
        'tenant_unit_id' => $unit->id,
        'tenant_start_date' => now()->toDateString(),
        'tenant_end_date' => now()->addYear()->toDateString(),
        'tenant_monthly_rent' => 250,
        'tenant_deposit' => 100,
    ]);

    $response->assertRedirect(route('manager.users.index'));

    $createdUser = User::where('email', 'tenant-user@example.com')->first();
    expect($createdUser)->not->toBeNull();
    expect($createdUser->hasRole('tenant'))->toBeTrue();

    $tenant = Tenant::where('user_id', $createdUser->id)->first();
    expect($tenant)->not->toBeNull();

    $this->assertDatabaseHas('rental_contracts', [
        'tenant_id' => $tenant->id,
        'unit_id' => $unit->id,
        'status' => 'active',
    ]);

    $unit->refresh();
    expect($unit->status)->toBe('rented');
});
