<?php

use App\Http\Controllers\Accountant;
use App\Http\Controllers\Buyer;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Employee;
use App\Http\Controllers\Manager;
use App\Http\Controllers\Owner;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Tenant;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['ar', 'en'], true), 404);
    session(['locale' => $locale]);

    return redirect()->back();
})->name('locale.switch');

// Public property pages
Route::get('/properties', [App\Http\Controllers\PropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/{property}', [App\Http\Controllers\PropertyController::class, 'show'])->name('properties.show');

Route::post('/contact', [App\Http\Controllers\ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Manager routes
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [Manager\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('properties', Manager\PropertyController::class);
    Route::patch('properties/{property}/transfer', [Manager\PropertyController::class, 'transfer'])->name('properties.transfer');

    Route::get('properties/{property}/units/create', [Manager\UnitController::class, 'create'])->name('units.create');
    Route::post('properties/{property}/units', [Manager\UnitController::class, 'store'])->name('units.store');
    Route::get('properties/{property}/units/{unit}/edit', [Manager\UnitController::class, 'edit'])->name('units.edit');
    Route::patch('properties/{property}/units/{unit}', [Manager\UnitController::class, 'update'])->name('units.update');
    Route::delete('properties/{property}/units/{unit}', [Manager\UnitController::class, 'destroy'])->name('units.destroy');

    Route::resource('tenants', Manager\TenantController::class);
    Route::resource('employees', Manager\EmployeeController::class);
    Route::resource('users', Manager\UserController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::patch('users/{user}/toggle-block', [Manager\UserController::class, 'toggleBlock'])->name('users.toggle-block');

    Route::get('reports', [Manager\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/properties/{property}', [Manager\ReportController::class, 'propertyReport'])->name('reports.property');

    Route::get('expenses', [Manager\ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('expenses/create', [Manager\ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('expenses', [Manager\ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('expenses/{expense}', [Manager\ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Owners Association (HOA)
    Route::resource('associations', Manager\AssociationController::class);
    Route::post('associations/{association}/dues/generate', [Manager\AssociationDueController::class, 'generate'])->name('associations.dues.generate');

    // Property fractional owners (pivot)
    Route::get('properties/{property}/owners', [Manager\PropertyOwnerController::class, 'index'])->name('properties.owners.index');
    Route::post('properties/{property}/owners', [Manager\PropertyOwnerController::class, 'store'])->name('properties.owners.store');
    Route::patch('properties/{property}/owners/{owner}', [Manager\PropertyOwnerController::class, 'update'])->name('properties.owners.update');
    Route::delete('properties/{property}/owners/{owner}', [Manager\PropertyOwnerController::class, 'destroy'])->name('properties.owners.destroy');

    // Dues management
    Route::get('dues', [Manager\AssociationDueController::class, 'index'])->name('dues.index');
    Route::patch('dues/{due}/paid', [Manager\AssociationDueController::class, 'markPaid'])->name('dues.paid');
    Route::patch('dues/{due}/waived', [Manager\AssociationDueController::class, 'markWaived'])->name('dues.waived');
    Route::delete('dues/{due}', [Manager\AssociationDueController::class, 'destroy'])->name('dues.destroy');

    // Meetings
    Route::resource('meetings', Manager\AssociationMeetingController::class);

    // Salaries
    Route::resource('salaries', Manager\SalaryController::class)->except(['show']);
    Route::post('salaries/generate', [Manager\SalaryController::class, 'generate'])->name('salaries.generate');
    Route::patch('salaries/{salary}/pay', [Manager\SalaryController::class, 'pay'])->name('salaries.pay');

    Route::get('contacts', [Manager\ContactController::class, 'index'])->name('contacts.index');
    Route::get('contacts/{contact}', [Manager\ContactController::class, 'show'])->name('contacts.show');
    Route::delete('contacts/{contact}', [Manager\ContactController::class, 'destroy'])->name('contacts.destroy');
});

// Employee routes
Route::middleware(['auth', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [Employee\DashboardController::class, 'index'])->name('dashboard');

    Route::get('maintenance', [Employee\MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('maintenance/{maintenanceRequest}', [Employee\MaintenanceController::class, 'show'])->name('maintenance.show');
    Route::patch('maintenance/{maintenanceRequest}/status', [Employee\MaintenanceController::class, 'updateStatus'])->name('maintenance.update-status');

    Route::get('payments', [Employee\PaymentController::class, 'index'])->name('payments.index');
    Route::patch('payments/{payment}/confirm', [Employee\PaymentController::class, 'confirm'])->name('payments.confirm');
    Route::patch('payments/{payment}/overdue', [Employee\PaymentController::class, 'markOverdue'])->name('payments.overdue');

    Route::patch('properties/{property}/mark-sold', [Employee\PropertyController::class, 'markSold'])->name('properties.mark-sold');
});

// Accountant routes
Route::middleware(['auth', 'role:accountant'])->prefix('accountant')->name('accountant.')->group(function () {
    Route::get('/dashboard', [Accountant\DashboardController::class, 'index'])->name('dashboard');

    Route::get('payments', [Accountant\PaymentController::class, 'index'])->name('payments.index');
    Route::post('payments/generate', [Accountant\PaymentController::class, 'generateMonthlyPayments'])->name('payments.generate');
    Route::get('payments/export', [Accountant\PaymentController::class, 'exportPdf'])->name('payments.export');
    Route::patch('payments/{payment}/confirm', [Accountant\PaymentController::class, 'confirm'])->name('payments.confirm');
    Route::patch('payments/{payment}/overdue', [Accountant\PaymentController::class, 'markOverdue'])->name('payments.overdue');
});

// Owner portal routes
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [Owner\DashboardController::class, 'index'])->name('dashboard');

    Route::get('properties', [Owner\PropertyController::class, 'index'])->name('properties.index');
    Route::get('properties/{property}', [Owner\PropertyController::class, 'show'])->name('properties.show');

    Route::get('dues', [Owner\DueController::class, 'index'])->name('dues.index');

    Route::get('meetings', [Owner\MeetingController::class, 'index'])->name('meetings.index');
    Route::get('meetings/{meeting}', [Owner\MeetingController::class, 'show'])->name('meetings.show');
});

// Buyer portal routes
Route::middleware(['auth', 'role:buyer'])->prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/dashboard', [Buyer\DashboardController::class, 'index'])->name('dashboard');
});

// Tenant portal routes
Route::middleware(['auth', 'role:tenant'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/dashboard', [Tenant\DashboardController::class, 'index'])->name('dashboard');

    Route::get('maintenance', [Tenant\MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('maintenance/create', [Tenant\MaintenanceController::class, 'create'])->name('maintenance.create');
    Route::post('maintenance', [Tenant\MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::get('maintenance/{maintenanceRequest}', [Tenant\MaintenanceController::class, 'show'])->name('maintenance.show');
    Route::delete('maintenance/{maintenanceRequest}', [Tenant\MaintenanceController::class, 'destroy'])->name('maintenance.destroy');

    Route::get('payments', [Tenant\PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [Tenant\PaymentController::class, 'show'])->name('payments.show');
});

require __DIR__.'/auth.php';
