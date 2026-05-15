# مرجع مشروع ثروة للعقارات — Tharwa Real Estate App

## نظرة عامة
تطبيق ويب متكامل لإدارة العقارات لشركة **ثروة** — يشمل صفحة تعريفية ثنائية اللغة (AR/EN)، لوحات تحكم لأدوار متعددة، بوابة مستأجرين، طلبات صيانة، تقارير PDF، ورسوم بيانية تفاعلية.

---

## التقنيات المستخدمة

| التقنية | الإصدار |
|---|---|
| PHP | 8.4 |
| Laravel | 13 |
| Laravel Breeze (Blade) | 2.4 |
| Spatie Laravel Permission | 7.3 |
| barryvdh/laravel-dompdf | 3.1 |
| MySQL | 8.0 |
| Tailwind CSS | CDN (landing page) / Vite (app) |
| Alpine.js | مدمج مع Breeze |
| Chart.js | 4.4 CDN (dashboard) |
| Font: Tajawal + Cairo | Google Fonts |

---

## قاعدة البيانات

**اسم قاعدة البيانات:** `real_estate_app`  
**بيانات الاتصال:** host=127.0.0.1 / user=root / password=1993816

### الجداول

| الجدول | الوصف |
|---|---|
| `users` | المستخدمون (مدير، موظف، محاسب، مستأجر) + حقل phone |
| `roles` / `permissions` | Spatie Permission tables |
| `buildings` | المباني — name, address, description, employee_id |
| `units` | الوحدات — unit_number, floor, type, rent_amount, status, building_id |
| `tenants` | المستأجرون — user_id, national_id, emergency_contact |
| `rental_contracts` | العقود — unit_id, tenant_id, start/end_date, monthly_rent, status |
| `maintenance_requests` | طلبات الصيانة — tenant_id, title, priority, status (pending/in_progress/completed/rejected) |
| `payments` | المدفوعات — contract_id, month, year, amount, status, due_date, paid_at |
| `contact_messages` | رسائل نموذج التواصل — is_read |

### أوامر Migration

```bash
php artisan migrate
php artisan db:seed
```

---

## الأدوار والصلاحيات

| الدور | الوصف | المسار |
|---|---|---|
| `manager` | يرى كل شيء، يدير الموظفين والعقارات | `/manager/*` |
| `employee` | يدير المباني المسندة إليه، يراجع الصيانة والمدفوعات | `/employee/*` |
| `accountant` | يولّد إشعارات الدفع الشهرية، يتابع المدفوعات، يصدّر PDF | `/accountant/*` |
| `tenant` | يرى عقده ودفعاته، يرفع طلبات صيانة، يحذف طلبات معلقة | `/tenant/*` |

### بيانات الدخول التجريبية (كلمة المرور: `password`)

| الدور | البريد الإلكتروني |
|---|---|
| Manager | manager@tharwa.com |
| Employee | employee1@tharwa.com |
| Accountant | accountant@tharwa.com |
| Tenant | tenant@tharwa.com |

---

## هيكل الملفات

### Models — `app/Models/`
```
User.php              — HasRoles, relationships: buildings, tenant, maintenanceRequests
Building.php          — belongsTo(User employee), hasMany(Unit)
Unit.php              — belongsTo(Building), hasMany(RentalContract)
Tenant.php            — belongsTo(User), hasMany(RentalContract, MaintenanceRequest)
RentalContract.php    — belongsTo(Unit, Tenant)
MaintenanceRequest.php — belongsTo(Tenant) + Arabic label helpers
Payment.php           — belongsTo(RentalContract) + Arabic month names
ContactMessage.php    — name, email, phone, subject, message, is_read
```

### Controllers — `app/Http/Controllers/`
```
DashboardController.php           — redirects user to role-specific dashboard
ContactController.php             — public contact form store (throttle: 5/min)

Manager/
  DashboardController.php         — stats + Charts data (monthly revenue, units status)
  BuildingController.php          — CRUD + transfer + search
  UnitController.php              — CRUD (nested under building)
  TenantController.php            — CRUD + search
  EmployeeController.php          — CRUD + search
  ReportController.php            — index + building PDF (?export=pdf)
  ContactController.php           — index/show/destroy contact messages

Employee/
  DashboardController.php         — stats for assigned buildings
  MaintenanceController.php       — index/show + updateStatus (PATCH)
  PaymentController.php           — index + confirm/markOverdue

Accountant/
  DashboardController.php
  PaymentController.php           — index + generateMonthlyPayments + confirm/overdue + exportPdf

Tenant/
  DashboardController.php         — contract + payments summary
  MaintenanceController.php       — index/create/store/show + destroy (pending only)
  PaymentController.php           — index/show
```

### Views — `resources/views/`
```
welcome.blade.php                 — Landing Page ثنائية اللغة AR/EN (Responsive)
layouts/app.blade.php             — RTL layout + dynamic sidebar + Notifications Bell + Mobile hamburger
layouts/guest.blade.php           — Breeze guest layout

manager/
  dashboard.blade.php             — Stats + Chart.js (revenue bar + units doughnut)
  buildings/  index (search), create, edit, show
  units/      create, edit
  employees/  index (search), create, edit, show
  tenants/    index (search), create, edit, show
  reports/    index, building, building-pdf
  contacts/   index, show

employee/
  dashboard.blade.php
  maintenance/  index, show
  payments/     index

accountant/
  dashboard.blade.php
  payments/     index (PDF export button), pdf

tenant/
  dashboard.blade.php
  maintenance/  index (delete pending), create, show
  payments/     index, show
```

---

## المسارات (Routes)

### عامة (بدون مصادقة)
```
GET  /                          → landing page
POST /contact (throttle:5,1)    → contact.store
```

### أمان
```
GET  /register                  → redirect to /login (معطّل للعموم)
```

### Manager — prefix: /manager, middleware: auth + role:manager
```
GET/POST   /manager/dashboard
CRUD       /manager/buildings           (?search=)
CRUD       /manager/buildings/{building}/units  (nested)
PATCH      /manager/buildings/{building}/transfer
CRUD       /manager/employees           (?search=)
CRUD       /manager/tenants             (?search=)
GET        /manager/reports
GET        /manager/reports/buildings/{building}  (?export=pdf)
GET/DELETE /manager/contacts
```

### Employee — prefix: /employee, middleware: auth + role:employee
```
GET        /employee/dashboard
GET        /employee/maintenance        (?status=)
GET        /employee/maintenance/{id}
PATCH      /employee/maintenance/{id}/status
GET        /employee/payments
PATCH      /employee/payments/{id}/confirm
PATCH      /employee/payments/{id}/overdue
```

### Accountant — prefix: /accountant, middleware: auth + role:accountant
```
GET        /accountant/dashboard
GET        /accountant/payments         (?status=&year=&month=)
POST       /accountant/payments/generate
GET        /accountant/payments/export  → PDF download
PATCH      /accountant/payments/{id}/confirm
PATCH      /accountant/payments/{id}/overdue
```

### Tenant — prefix: /tenant, middleware: auth + role:tenant
```
GET        /tenant/dashboard
GET/POST   /tenant/maintenance
GET        /tenant/maintenance/{id}
DELETE     /tenant/maintenance/{id}     (pending فقط)
GET        /tenant/payments
GET        /tenant/payments/{id}
```

---

## الصفحة الرئيسية (Landing Page)

**الملف:** `resources/views/welcome.blade.php`  
**النمط:** Standalone HTML (لا يستخدم app layout)  
**المكتبات:** Tailwind CDN + Google Fonts Tajawal + Font Awesome CDN

### الميزات:
- **ثنائية اللغة:** زر AR/EN في الـ navbar — يبدّل المحتوى والاتجاه (RTL/LTR) بـ JavaScript
- **Responsive:** يعمل على Mobile / Tablet / Desktop
- **حفظ اللغة:** localStorage

### الأقسام:
1. **Navbar** — ثابت، يتحول أبيض عند التمرير + زر اللغة + hamburger mobile
2. **Hero** — خلفية كحلي، عنوان + أرقام إحصائية + بطاقة عائمة
3. **Services** — 6 بطاقات خدمات
4. **Stats Bar** — 4 أرقام على خلفية كحلي
5. **Properties** — 3 بطاقات عقارات
6. **About** — نص تعريفي + 4 بطاقات قيم
7. **Contact** — معلومات التواصل + نموذج (يُحفظ في DB)
8. **Footer**

---

## Notifications Bell

في `layouts/app.blade.php` — يحسب عدد الإشعارات لكل دور:

| الدور | ما يُعرض |
|---|---|
| Manager | رسائل تواصل غير مقروءة + طلبات صيانة معلقة |
| Employee | طلبات صيانة معلقة في مبانيه |
| Tenant | دفعات متأخرة |

---

## Charts في Dashboard المدير

يستخدم **Chart.js 4.4 CDN** مع `@push('scripts')` / `@stack('scripts')`:
- **Bar Chart:** الإيرادات الشهرية لآخر 6 أشهر
- **Doughnut Chart:** نسبة الوحدات المشغولة / المتاحة

---

## إعداد Spatie Permission (Laravel 13)

في `bootstrap/app.php`:
```php
$middleware->alias([
    'role' => \Spatie\LaravelPermission\Middleware\RoleMiddleware::class,
    'permission' => \Spatie\LaravelPermission\Middleware\PermissionMiddleware::class,
]);
```

---

## أوامر مفيدة

```bash
# تشغيل السيرفر
php artisan serve --host=127.0.0.1 --port=8080

# إعادة تشغيل البيانات التجريبية
php artisan migrate:fresh --seed

# مسح الكاش
php artisan view:clear && php artisan cache:clear && php artisan config:clear

# بناء الأصول
npm run build   # أو للتطوير: npm run dev
```

---

## أخطاء تم إصلاحها

| الخطأ | السبب | الإصلاح |
|---|---|---|
| `Undefined variable $employees` في building show | Controller لم يمرر `$employees` | أضفنا `$employees` لـ `BuildingController::show()` |
| `hasRole() on null` في employees index/show | View يستخدم `$employee->user->name` لكن Controller يمرر `User` مباشرة | استبدلنا `$employee->user->*` بـ `$employee->*` |
| `Route [manager.buildings.units.create] not defined` | اسم الـ route خاطئ | صححنا إلى `manager.units.create` |
| `Missing parameter: unit` في units edit | Route يحتاج `[$building, $unit]` | مررنا array بدلاً من `$unit` فقط |
| `Undefined variable $request` في employee maintenance show | Controller يمرر `$maintenanceRequest` | استبدلنا `$request->` بـ `$maintenanceRequest->` في الـ view |
| `Route [employee.maintenance.update] not defined` | اسم الـ route خاطئ | صححنا إلى `employee.maintenance.update-status` |
| `Attempt to read property "name" on null` في employees edit | View يستخدم `$employee->user->name` | صححنا إلى `$employee->name` |
