# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# First-time setup (install deps, copy .env, generate key, migrate, build assets)
composer setup

# Local development (runs server, queue, log watcher, and Vite concurrently)
composer dev

# Run tests
composer test

# Run a single test file or filter by name
php artisan test --filter=TestName
php artisan test tests/Feature/SomeTest.php

# Asset pipeline only
npm run dev       # Vite dev server
npm run build     # Production build

# Common artisan
php artisan migrate
php artisan queue:listen --tries=1
php artisan tinker
```

> **Warning (from a prior incident):** `php artisan migrate:fresh` runs against the real `.env` database — it is NOT sandboxed. Never run destructive migrations outside an explicit test environment.

## Stack

- **Laravel 13**, PHP, MySQL
- **Spatie Permissions** for RBAC (roles: Manager, Accountant, Employee, Owner, Tenant, Buyer)
- **Pest PHP** for testing (not PHPUnit)
- **Vite + Tailwind CSS + Alpine.js** for frontend
- **mPDF** for PDF report generation (comprehensive reports, salary slips, payment receipts)
- **PhpSpreadsheet** for Excel import/export
- **Sentry** for error tracking
- Fully **bilingual** (Arabic/English) throughout — every user-facing string uses `$tr($ar, $en)` helpers in Blade, and models have `name_ar`/`name_en` field pairs

## Architecture

### Role-based Controller Split

Controllers are namespaced by role under `app/Http/Controllers/`:

| Namespace | Audience | Scope |
|-----------|----------|-------|
| `Manager/` | Admin/manager | Full CRUD for all entities, reports, CMS, HR, finance |
| `Employee/` | Staff | Assigned properties, tenants, maintenance, leaves |
| `Accountant/` | Finance | Payments, salaries |
| `Owner/` | Property owners | Their properties, HOA dues, meetings |
| `Tenant/` | Renters | Their payments, maintenance requests |
| `Buyer/` | Purchasers | Sale contract dashboard |

Routes follow the same prefix pattern in `routes/web.php` and `routes/auth.php`.

### Property Sections

`Property` has a `section` enum (`management`, `external`, `hoa`) that drives which report and management flow applies:

- `management` — company-managed rental buildings
- `external` — client-owned properties managed for commission (tracked via `CommissionInvoice`)
- `hoa` — properties linked to an `Association` (Owners Association / HOA)

Each section has its own comprehensive report controller:
- `BuildingComprehensiveReportController` → `BuildingReportDataService`
- `ExternalPropertyReportController` → same `BuildingReportDataService` but forced `section=external`
- `HoaComprehensiveReportController` → `HoaReportDataService`

### PDF Reports

PDF generation uses mPDF. The pattern is always:
1. Controller calls a `*DataService` to aggregate all data into a flat array
2. Controller renders a Blade view to HTML string
3. mPDF converts HTML → PDF
4. FPDI merges physical attachment files (contracts, invoices, NOCs) as extra pages

PDF Blade views are standalone HTML documents (not `x-app-layout`) with embedded CSS and `@page` margins for mPDF.

### Bilingual Pattern

All Blade views (especially PDF views) declare:
```php
$tr = fn($ar, $en) => $isAr ? $ar : $en;
```
and call `$tr('النص العربي', 'English text')` throughout. Locale is set per-request via `SetLocaleFromSession` middleware and can be forced per-report via a `locale` form field.

### HOA (Owners Association) Module

`Association` belongs to one `Property`. It has:
- `AssociationDue` — monthly fee records per owner
- `AssociationMeeting` — governance meetings
- `NoObjectionCertificate` / `NoObjectionSaleCertificate` — generated PDFs stored in `storage/app/`

The `HoaReportDataService::collect()` returns a rich array per association. `collectMultiple()` wraps it for multi-association reports. Key gotcha: the returned array must include `ownersCount` explicitly (it does not fall through from the `owners` collection automatically).

### Finance & HR

- **Expenses** are polymorphic (`expensable_type` / `expensable_id`) — can belong to a `Property`, `Association`, or other entity.
- **Salary** model tracks base + allowances; `ScheduledReportRunner` auto-generates monthly salary slips.
- `CommissionInvoice` links to a `Property` and records `invoice_for` (owner or client), `commission_rate`, and `commission_amount`. Used for both `section=external` properties (viewed via `ExternalPropertyController@commissions`) and `section=management` properties (viewed via `PropertyController@managementCommissions`).

### Route Ordering

Static routes **must** be registered before `Route::resource()` to prevent the resource's `{property}` wildcard from capturing them. Examples:

```php
Route::get('properties/commissions', ...);            // BEFORE
Route::get('properties/comprehensive-report', ...);   // BEFORE
Route::resource('properties', PropertyController::class);  // AFTER
```

This applies to any route whose path shares a prefix with a resource.

### PDF Page Breaks

Wrap any section that must not split across pages in a `<div style="page-break-inside: avoid;">`. Combine with compact font sizes (7–9pt) and reduced cell padding to fit dense content on one page. mPDF moves the entire block to a new page rather than splitting it.

### 419 / CSRF Expiry Handling

`bootstrap/app.php` uses `$exceptions->respond()` (not `render()`) to intercept the final 419 HTTP response and redirect to login. `respond()` fires after the response is fully formed, so it reliably catches all 419s regardless of how the exception was converted:

```php
$exceptions->respond(function (\Illuminate\Http\Response $response) {
    if ($response->getStatusCode() === 419) {
        return redirect()->route('login')->withErrors(['session' => '...']);
    }
    return $response;
});
```

### Imports / Exports

`app/Exports/` and `app/Imports/` use PhpSpreadsheet via `maatwebsite/excel`. Template exports provide downloadable blank sheets; imports validate and create records with detailed error reporting back to the UI.

### Custom Middleware

- `EnsureUserIsNotBlocked` — checks `users.is_blocked`; redirects blocked users on every request
- `SetLocaleFromSession` — reads `session('locale')` and calls `app()->setLocale()`

### File Storage

Uploaded files (contracts, IDs, images, expense receipts) are stored under `storage/app/public/` via `StoresUploadedFiles` trait. NOC PDFs generated by the system go to `storage/app/` (no `public/` subfolder). Always use `storage_path('app/public/' . $relPath)` for user-uploaded files and `storage_path('app/' . $relPath)` for system-generated ones.
