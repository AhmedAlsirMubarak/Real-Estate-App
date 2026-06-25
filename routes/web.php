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

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['ar', 'en'], true), 404);
    session(['locale' => $locale]);

    return redirect()->back()->withCookie(
        cookie('app_locale', $locale, 60 * 24 * 365) // 1 year
    );
})->name('locale.switch');

// Public property pages
Route::get('/properties', [App\Http\Controllers\PropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/{property}', [App\Http\Controllers\PropertyController::class, 'show'])->name('properties.show');

// Public news pages
Route::get('/news', [App\Http\Controllers\NewsController::class, 'index'])->name('news.index');
Route::get('/news/{article:slug}', [App\Http\Controllers\NewsController::class, 'show'])->name('news.show');

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

    // Building Comprehensive Report â€” must be before resource() to avoid {property} wildcard capture
    Route::get('properties/comprehensive-report', [Manager\BuildingComprehensiveReportController::class, 'create'])->name('properties.report.create');
    Route::post('properties/comprehensive-report', [Manager\BuildingComprehensiveReportController::class, 'generate'])->name('properties.report.generate');
    Route::get('properties/commissions', [Manager\PropertyController::class, 'managementCommissions'])->name('properties.commissions');

    Route::delete('properties/bulk-destroy', [Manager\PropertyController::class, 'bulkDestroy'])->name('properties.bulk-destroy');
    Route::get('properties/export', [Manager\PropertyController::class, 'export'])->name('properties.export');
    Route::get('properties/import', [Manager\PropertyController::class, 'importForm'])->name('properties.import.form');
    Route::post('properties/import', [Manager\PropertyController::class, 'import'])->name('properties.import');
    Route::get('properties/import/template', [Manager\PropertyController::class, 'downloadTemplate'])->name('properties.import.template');

    Route::resource('properties', Manager\PropertyController::class);
    Route::patch('properties/{property}/transfer', [Manager\PropertyController::class, 'transfer'])->name('properties.transfer');
    Route::patch('properties/{property}/toggle-public', [Manager\PropertyController::class, 'togglePublic'])->name('properties.toggle-public');

    Route::get('external-properties/comprehensive-report', [Manager\ExternalPropertyReportController::class, 'create'])->name('external-properties.report.create');
    Route::post('external-properties/comprehensive-report', [Manager\ExternalPropertyReportController::class, 'generate'])->name('external-properties.report.generate');
    Route::get('external-properties/import', [Manager\ExternalPropertyController::class, 'importForm'])->name('external-properties.import.form');
    Route::post('external-properties/import', [Manager\ExternalPropertyController::class, 'import'])->name('external-properties.import');
    Route::get('external-properties/import/template', [Manager\ExternalPropertyController::class, 'downloadTemplate'])->name('external-properties.import.template');
    Route::get('external-properties/export', [Manager\ExternalPropertyController::class, 'export'])->name('external-properties.export');
    Route::resource('external-properties', Manager\ExternalPropertyController::class)->parameters(['external-properties' => 'property']);
    Route::get('external-properties-commissions', [Manager\ExternalPropertyController::class, 'commissions'])->name('external-properties.commissions');
    Route::post('properties/{property}/commission-invoice', [Manager\PropertyController::class, 'commissionInvoice'])->name('properties.commission-invoice');
    Route::get('properties/{property}/commission-invoices/{invoice}/download', [Manager\PropertyController::class, 'downloadCommissionInvoice'])->name('properties.commission-invoice.download');
    Route::delete('properties/{property}/commission-invoices/{invoice}', [Manager\PropertyController::class, 'destroyCommissionInvoice'])->name('properties.commission-invoice.destroy');
    Route::post('properties/{property}/images', [Manager\PropertyController::class, 'storeImage'])->name('properties.images.store');
    Route::delete('properties/{property}/images/{image}', [Manager\PropertyController::class, 'destroyImage'])->name('properties.images.destroy');
    Route::patch('properties/{property}/images/{image}/primary', [Manager\PropertyController::class, 'setPrimaryImage'])->name('properties.images.primary');

    Route::get('properties/{property}/units/create', [Manager\UnitController::class, 'create'])->name('units.create');
    Route::post('properties/{property}/units', [Manager\UnitController::class, 'store'])->name('units.store');
    Route::get('properties/{property}/units/{unit}/edit', [Manager\UnitController::class, 'edit'])->name('units.edit');
    Route::patch('properties/{property}/units/{unit}', [Manager\UnitController::class, 'update'])->name('units.update');
    Route::delete('properties/{property}/units/{unit}', [Manager\UnitController::class, 'destroy'])->name('units.destroy');
    Route::post('properties/{property}/units/{unit}/images', [Manager\UnitController::class, 'storeImage'])->name('units.images.store');
    Route::delete('properties/{property}/units/{unit}/images/{image}', [Manager\UnitController::class, 'destroyImage'])->name('units.images.destroy');
    Route::patch('properties/{property}/units/{unit}/images/{image}/primary', [Manager\UnitController::class, 'setPrimaryImage'])->name('units.images.primary');

    Route::get('tenants/import', [Manager\TenantController::class, 'importForm'])->name('tenants.import.form');
    Route::post('tenants/import', [Manager\TenantController::class, 'import'])->name('tenants.import');
    Route::get('tenants/import/template', [Manager\TenantController::class, 'downloadTemplate'])->name('tenants.import.template');
    Route::get('tenants/export', [Manager\TenantController::class, 'export'])->name('tenants.export');
    Route::resource('tenants', Manager\TenantController::class);
    Route::post('tenants/{tenant}/payments/generate', [Manager\TenantController::class, 'generatePayment'])->name('tenants.payments.generate');
    Route::patch('tenants/{tenant}/payments/{payment}/mark-paid', [Manager\TenantController::class, 'markPaymentPaid'])->name('tenants.payments.mark-paid');
    Route::delete('tenants/{tenant}/payments/{payment}', [Manager\TenantController::class, 'destroyPayment'])->name('tenants.payments.destroy');
    Route::get('tenants/{tenant}/payments/{payment}/invoice', [Manager\TenantController::class, 'paymentInvoice'])->name('tenants.payments.invoice');
    Route::post('rental-contracts/{contract}/upload-file', [Manager\RentalContractController::class, 'uploadFile'])->name('rental-contracts.upload-file');
    Route::delete('rental-contracts/{contract}/delete-file', [Manager\RentalContractController::class, 'deleteFile'])->name('rental-contracts.delete-file');
    Route::resource('employees', Manager\EmployeeController::class);

    // HR â€” Leaves
    Route::get('hr/leaves', [Manager\HrLeaveController::class, 'index'])->name('hr.leaves.index');
    Route::post('employees/{employee}/leaves', [Manager\HrLeaveController::class, 'store'])->name('employees.leaves.store');
    Route::patch('employees/{employee}/leaves/{leave}/approve', [Manager\HrLeaveController::class, 'approve'])->name('employees.leaves.approve');
    Route::patch('employees/{employee}/leaves/{leave}/reject', [Manager\HrLeaveController::class, 'reject'])->name('employees.leaves.reject');
    Route::delete('employees/{employee}/leaves/{leave}', [Manager\HrLeaveController::class, 'destroy'])->name('employees.leaves.destroy');

    // HR â€” Attendance
    Route::get('hr/attendance', [Manager\HrAttendanceController::class, 'index'])->name('hr.attendance.index');
    Route::post('employees/{employee}/attendance', [Manager\HrAttendanceController::class, 'store'])->name('employees.attendance.store');
    Route::patch('employees/{employee}/attendance/{attendance}', [Manager\HrAttendanceController::class, 'update'])->name('employees.attendance.update');
    Route::delete('employees/{employee}/attendance/{attendance}', [Manager\HrAttendanceController::class, 'destroy'])->name('employees.attendance.destroy');
    Route::post('users/bulk-destroy', [Manager\UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
    Route::resource('users', Manager\UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::patch('users/{user}/toggle-block', [Manager\UserController::class, 'toggleBlock'])->name('users.toggle-block');

    Route::get('reports', [Manager\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/properties/{property}', [Manager\ReportController::class, 'propertyReport'])->name('reports.property');

    // Scheduled reports (HOA + Building Mgmt) â€” period_months + custom cadence
    Route::get('scheduled-reports', [Manager\ScheduledReportController::class, 'index'])->name('scheduled-reports.index');
    Route::get('scheduled-reports/create', [Manager\ScheduledReportController::class, 'create'])->name('scheduled-reports.create');
    Route::post('scheduled-reports', [Manager\ScheduledReportController::class, 'store'])->name('scheduled-reports.store');
    Route::get('scheduled-reports/{scheduledReport}/edit', [Manager\ScheduledReportController::class, 'edit'])->name('scheduled-reports.edit');
    Route::patch('scheduled-reports/{scheduledReport}', [Manager\ScheduledReportController::class, 'update'])->name('scheduled-reports.update');
    Route::delete('scheduled-reports/{scheduledReport}', [Manager\ScheduledReportController::class, 'destroy'])->name('scheduled-reports.destroy');
    Route::post('scheduled-reports/{scheduledReport}/run', [Manager\ScheduledReportController::class, 'runNow'])->name('scheduled-reports.run');
    Route::get('scheduled-reports/runs/{run}/download', [Manager\ScheduledReportController::class, 'download'])->name('scheduled-reports.download');

    // Owners Association (HOA) â€” specific routes MUST come before resource() to avoid {association} wildcard capture
    Route::get('associations/comprehensive-report', [Manager\HoaComprehensiveReportController::class, 'create'])->name('associations.report.create');
    Route::post('associations/comprehensive-report', [Manager\HoaComprehensiveReportController::class, 'generate'])->name('associations.report.generate');
    Route::get('associations/no-objection-certs/{noc}/download', [Manager\AssociationController::class, 'downloadNoc'])->name('associations.noc.download');
    Route::delete('associations/no-objection-certs/{noc}', [Manager\AssociationController::class, 'deleteNoc'])->name('associations.noc.delete');
    Route::get('associations/no-objection-sale-certs/{noc}/download', [Manager\AssociationController::class, 'downloadNocSale'])->name('associations.noc-sale.download');
    Route::delete('associations/no-objection-sale-certs/{noc}', [Manager\AssociationController::class, 'deleteNocSale'])->name('associations.noc-sale.delete');
    Route::delete('associations/bulk-destroy', [Manager\AssociationController::class, 'bulkDestroy'])->name('associations.bulk-destroy');
    Route::get('associations/import', [Manager\AssociationController::class, 'importForm'])->name('associations.import.form');
    Route::post('associations/import', [Manager\AssociationController::class, 'import'])->name('associations.import');
    Route::get('associations/import/template', [Manager\AssociationController::class, 'downloadTemplate'])->name('associations.import.template');
    Route::get('associations/export', [Manager\AssociationController::class, 'export'])->name('associations.export');
    Route::resource('associations', Manager\AssociationController::class);
    Route::post('associations/{association}/dues/generate', [Manager\AssociationDueController::class, 'generate'])->name('associations.dues.generate');
    Route::post('associations/{association}/invoice-pdf', [Manager\AssociationDueController::class, 'associationInvoice'])->name('associations.invoice-pdf');
    Route::delete('associations/{association}/documents/{field}', [Manager\AssociationController::class, 'deleteDocument'])->name('associations.documents.delete');
    Route::post('associations/{association}/no-objection-pdf', [Manager\AssociationController::class, 'noObjectionPdf'])->name('associations.no-objection-pdf');
    Route::post('associations/{association}/no-objection-sale-pdf', [Manager\AssociationController::class, 'noSalePdf'])->name('associations.no-objection-sale-pdf');

    // Property fractional owners (pivot)
    Route::get('properties/{property}/owners', [Manager\PropertyOwnerController::class, 'index'])->name('properties.owners.index');
    Route::post('properties/{property}/owners', [Manager\PropertyOwnerController::class, 'store'])->name('properties.owners.store');
    Route::patch('properties/{property}/owners/{owner}', [Manager\PropertyOwnerController::class, 'update'])->name('properties.owners.update');
    Route::delete('properties/{property}/owners/{owner}', [Manager\PropertyOwnerController::class, 'destroy'])->name('properties.owners.destroy');

    // Dues management
    Route::get('dues', [Manager\AssociationDueController::class, 'index'])->name('dues.index');
    Route::get('dues/{due}/invoice', [Manager\AssociationDueController::class, 'invoice'])->name('dues.invoice');
    Route::patch('dues/{due}/paid', [Manager\AssociationDueController::class, 'markPaid'])->name('dues.paid');
    Route::patch('dues/{due}/pending', [Manager\AssociationDueController::class, 'markPending'])->name('dues.pending');
    Route::patch('dues/{due}/waived', [Manager\AssociationDueController::class, 'markWaived'])->name('dues.waived');
    Route::delete('dues/{due}', [Manager\AssociationDueController::class, 'destroy'])->name('dues.destroy');

    // Meetings
    Route::resource('meetings', Manager\AssociationMeetingController::class);

    // News articles management
    Route::resource('news', Manager\NewsController::class)->except(['show']);
    Route::post('news/{news}/images', [Manager\NewsController::class, 'storeImages'])->name('news.images.store');
    Route::delete('news/{news}/images/{image}', [Manager\NewsController::class, 'destroyImage'])->name('news.images.destroy');
    Route::patch('news/{news}/images/{image}/primary', [Manager\NewsController::class, 'setPrimaryImage'])->name('news.images.primary');

    // Website Content CMS
    Route::get('website', [Manager\WebsiteController::class, 'index'])->name('website.index');
    Route::get('website/{page}', [Manager\WebsiteController::class, 'showPage'])->name('website.page');
    Route::get('website/{page}/{key}', [Manager\WebsiteController::class, 'editSection'])->name('website.section.edit');
    Route::post('website/{page}/{key}', [Manager\WebsiteController::class, 'updateSection'])->name('website.section.update');
    Route::patch('website/{page}/{key}/toggle', [Manager\WebsiteController::class, 'toggleSection'])->name('website.section.toggle');
    Route::get('website/{page}/{key}/items/create', [Manager\WebsiteController::class, 'createItem'])->name('website.items.create');
    Route::post('website/{page}/{key}/items', [Manager\WebsiteController::class, 'storeItem'])->name('website.items.store');
    Route::get('website/{page}/{key}/items/{item}/edit', [Manager\WebsiteController::class, 'editItem'])->name('website.items.edit');
    Route::put('website/{page}/{key}/items/{item}', [Manager\WebsiteController::class, 'updateItem'])->name('website.items.update');
    Route::delete('website/{page}/{key}/items/{item}', [Manager\WebsiteController::class, 'destroyItem'])->name('website.items.destroy');

    Route::get('contacts', [Manager\ContactController::class, 'index'])->name('contacts.index');
    Route::get('contacts/{contact}', [Manager\ContactController::class, 'show'])->name('contacts.show');
    Route::delete('contacts/{contact}', [Manager\ContactController::class, 'destroy'])->name('contacts.destroy');

    // Customers (leads / requirements)
    Route::delete('customers/bulk-destroy', [Manager\CustomerController::class, 'bulkDestroy'])->name('customers.bulk-destroy');
    Route::get('customers/export', [Manager\CustomerController::class, 'export'])->name('customers.export');
    Route::get('customers/import', [Manager\CustomerController::class, 'importForm'])->name('customers.import.form');
    Route::post('customers/import', [Manager\CustomerController::class, 'import'])->name('customers.import');
    Route::get('customers/import/template', [Manager\CustomerController::class, 'downloadTemplate'])->name('customers.import.template');
    Route::resource('customers', Manager\CustomerController::class);

    // Owner Leads
    Route::delete('owner-leads/bulk-destroy', [Manager\OwnerLeadController::class, 'bulkDestroy'])->name('owner-leads.bulk-destroy');
    Route::get('owner-leads/export', [Manager\OwnerLeadController::class, 'export'])->name('owner-leads.export');
    Route::get('owner-leads/import', [Manager\OwnerLeadController::class, 'importForm'])->name('owner-leads.import.form');
    Route::post('owner-leads/import', [Manager\OwnerLeadController::class, 'import'])->name('owner-leads.import');
    Route::get('owner-leads/import/template', [Manager\OwnerLeadController::class, 'downloadTemplate'])->name('owner-leads.import.template');
    Route::resource('owner-leads', Manager\OwnerLeadController::class);

    // Company Departments â€” HR (manager only)
    Route::resource('contracts', Manager\EmployeeContractController::class)->except(['show']);
});

// Real Estate Development (manager only)
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::resource('development', Manager\DevelopmentProjectController::class);
    Route::patch('development/{development}/progress', [Manager\DevelopmentProjectController::class, 'updateProgress'])->name('development.progress');
    Route::get('development/{development}/report', [Manager\DevelopmentProjectController::class, 'report'])->name('development.report');
    Route::post('development/{development}/expenses', [Manager\DevelopmentExpenseController::class, 'store'])->name('development.expenses.store');
    Route::delete('development/{development}/expenses/{expense}', [Manager\DevelopmentExpenseController::class, 'destroy'])->name('development.expenses.destroy');
    Route::post('development/{development}/contractors', [Manager\DevelopmentContractorController::class, 'store'])->name('development.contractors.store');
    Route::delete('development/{development}/contractors/{contractor}', [Manager\DevelopmentContractorController::class, 'destroy'])->name('development.contractors.destroy');
    Route::post('development/{development}/contractors/{contractor}/payments', [Manager\DevelopmentContractorPaymentController::class, 'store'])->name('development.contractors.payments.store');
    Route::post('development/{development}/documents', [Manager\DevelopmentDocumentController::class, 'store'])->name('development.documents.store');
    Route::delete('development/{development}/documents/{document}', [Manager\DevelopmentDocumentController::class, 'destroy'])->name('development.documents.destroy');
});

// Finance routes â€” shared between manager and accountant
Route::middleware(['auth', 'role:manager|accountant'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('finance', [Manager\FinanceDashboardController::class, 'index'])->name('finance.dashboard');
    Route::resource('budgets', Manager\CompanyBudgetController::class)->except(['show']);
    Route::resource('assets', Manager\CompanyAssetController::class)->except(['show']);

    Route::get('expenses/export',  [Manager\ExpenseController::class, 'exportPdf'])->name('expenses.export');
    Route::get('expenses/preview', [Manager\ExpenseController::class, 'previewPdf'])->name('expenses.preview');
    Route::delete('expenses/bulk-destroy', [Manager\ExpenseController::class, 'bulkDestroy'])->name('expenses.bulk-destroy');
    Route::get('expenses', [Manager\ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('expenses/create', [Manager\ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('expenses', [Manager\ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('expenses/{expense}/edit', [Manager\ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::patch('expenses/{expense}', [Manager\ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('expenses/{expense}', [Manager\ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::delete('expense-invoices/{invoice}', [Manager\ExpenseController::class, 'destroyInvoice'])->name('expenses.invoices.destroy');
    Route::delete('expenses/{expense}/receipt', [Manager\ExpenseController::class, 'destroyReceipt'])->name('expenses.receipt.destroy');

    Route::get('salaries/export', [Manager\SalaryController::class, 'exportPdf'])->name('salaries.export');
    Route::resource('salaries', Manager\SalaryController::class)->except([]);
    Route::post('salaries/generate', [Manager\SalaryController::class, 'generate'])->name('salaries.generate');
    Route::patch('salaries/{salary}/pay', [Manager\SalaryController::class, 'pay'])->name('salaries.pay');
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

    Route::get('tenants', [Employee\TenantController::class, 'index'])->name('tenants.index');
    Route::get('tenants/export', [Employee\TenantController::class, 'export'])->name('tenants.export');
    Route::get('tenants/create', [Employee\TenantController::class, 'create'])->name('tenants.create');
    Route::post('tenants', [Employee\TenantController::class, 'store'])->name('tenants.store');
    Route::delete('tenants/{tenant}', [Employee\TenantController::class, 'destroy'])->name('tenants.destroy');
    Route::get('tenants/{tenant}', [Employee\TenantController::class, 'show'])->name('tenants.show');
    Route::post('tenants/{tenant}/payments', [Employee\TenantController::class, 'generatePayment'])->name('tenants.payments.generate');
    Route::patch('tenants/{tenant}/payments/{payment}/paid', [Employee\TenantController::class, 'markPaymentPaid'])->name('tenants.payments.mark-paid');
    Route::delete('tenants/{tenant}/payments/{payment}', [Employee\TenantController::class, 'destroyPayment'])->name('tenants.payments.destroy');
    Route::get('tenants/{tenant}/payments/{payment}/invoice', [Employee\TenantController::class, 'paymentInvoice'])->name('tenants.payments.invoice');

    Route::get('properties', [Employee\PropertyController::class, 'index'])->name('properties.index');
    Route::get('properties/create', [Employee\PropertyController::class, 'create'])->name('properties.create');
    Route::post('properties', [Employee\PropertyController::class, 'store'])->name('properties.store');
    Route::patch('properties/{property}/mark-sold', [Employee\PropertyController::class, 'markSold'])->name('properties.mark-sold');
    Route::delete('properties/{property}', [Employee\PropertyController::class, 'destroy'])->name('properties.destroy');

    Route::get('leaves', [Employee\LeaveController::class, 'index'])->name('leaves.index');
    Route::get('leaves/create', [Employee\LeaveController::class, 'create'])->name('leaves.create');
    Route::post('leaves', [Employee\LeaveController::class, 'store'])->name('leaves.store');

    Route::get('contacts', [Employee\ContactController::class, 'index'])->name('contacts.index');
    Route::get('contacts/{contact}', [Employee\ContactController::class, 'show'])->name('contacts.show');

    Route::get('associations/import', [Employee\AssociationController::class, 'importForm'])->name('associations.import.form');
    Route::post('associations/import', [Employee\AssociationController::class, 'import'])->name('associations.import');
    Route::get('associations/import/template', [Employee\AssociationController::class, 'downloadTemplate'])->name('associations.import.template');
    Route::get('associations/export', [Employee\AssociationController::class, 'export'])->name('associations.export');
    Route::get('associations', [Employee\AssociationController::class, 'index'])->name('associations.index');
    Route::get('associations/create', [Employee\AssociationController::class, 'create'])->name('associations.create');
    Route::post('associations', [Employee\AssociationController::class, 'store'])->name('associations.store');
    Route::get('associations/{association}', [Employee\AssociationController::class, 'show'])->name('associations.show');
    Route::get('associations/{association}/edit', [Employee\AssociationController::class, 'edit'])->name('associations.edit');
    Route::put('associations/{association}', [Employee\AssociationController::class, 'update'])->name('associations.update');
    Route::delete('associations/{association}', [Employee\AssociationController::class, 'destroy'])->name('associations.destroy');

    Route::get('external-properties/export', [Employee\ExternalPropertyController::class, 'export'])->name('external-properties.export');
    Route::get('external-properties', [Employee\ExternalPropertyController::class, 'index'])->name('external-properties.index');
    Route::get('external-properties/create', [Employee\ExternalPropertyController::class, 'create'])->name('external-properties.create');
    Route::post('external-properties', [Employee\ExternalPropertyController::class, 'store'])->name('external-properties.store');
    Route::get('external-properties/{property}', [Employee\ExternalPropertyController::class, 'show'])->name('external-properties.show');
    Route::get('external-properties/{property}/edit', [Employee\ExternalPropertyController::class, 'edit'])->name('external-properties.edit');
    Route::put('external-properties/{property}', [Employee\ExternalPropertyController::class, 'update'])->name('external-properties.update');
    Route::delete('external-properties/{property}', [Employee\ExternalPropertyController::class, 'destroy'])->name('external-properties.destroy');

    Route::get('customers', [Employee\CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/export', [Employee\CustomerController::class, 'export'])->name('customers.export');
    Route::get('customers/import', [Employee\CustomerController::class, 'importForm'])->name('customers.import.form');
    Route::post('customers/import', [Employee\CustomerController::class, 'import'])->name('customers.import');
    Route::get('customers/import/template', [Employee\CustomerController::class, 'downloadTemplate'])->name('customers.import.template');
    Route::get('customers/create', [Employee\CustomerController::class, 'create'])->name('customers.create');
    Route::post('customers', [Employee\CustomerController::class, 'store'])->name('customers.store');
    Route::delete('customers/{customer}', [Employee\CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::get('customers/{customer}', [Employee\CustomerController::class, 'show'])->name('customers.show');
    Route::get('customers/{customer}/edit', [Employee\CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('customers/{customer}', [Employee\CustomerController::class, 'update'])->name('customers.update');
    Route::patch('customers/{customer}/reply', [Employee\CustomerController::class, 'reply'])->name('customers.reply');

    Route::get('owner-leads', [Employee\OwnerLeadController::class, 'index'])->name('owner-leads.index');
    Route::get('owner-leads/export', [Employee\OwnerLeadController::class, 'export'])->name('owner-leads.export');
    Route::get('owner-leads/import', [Employee\OwnerLeadController::class, 'importForm'])->name('owner-leads.import.form');
    Route::post('owner-leads/import', [Employee\OwnerLeadController::class, 'import'])->name('owner-leads.import');
    Route::get('owner-leads/import/template', [Employee\OwnerLeadController::class, 'downloadTemplate'])->name('owner-leads.import.template');
    Route::get('owner-leads/create', [Employee\OwnerLeadController::class, 'create'])->name('owner-leads.create');
    Route::post('owner-leads', [Employee\OwnerLeadController::class, 'store'])->name('owner-leads.store');
    Route::delete('owner-leads/{ownerLead}', [Employee\OwnerLeadController::class, 'destroy'])->name('owner-leads.destroy');
    Route::get('owner-leads/{ownerLead}', [Employee\OwnerLeadController::class, 'show'])->name('owner-leads.show');
    Route::get('owner-leads/{ownerLead}/edit', [Employee\OwnerLeadController::class, 'edit'])->name('owner-leads.edit');
    Route::put('owner-leads/{ownerLead}', [Employee\OwnerLeadController::class, 'update'])->name('owner-leads.update');
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
    Route::get('dues/{due}/invoice', [Owner\DueController::class, 'invoice'])->name('dues.invoice');

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

