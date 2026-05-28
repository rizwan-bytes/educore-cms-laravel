# CLAUDE.md — EduCore CMS Laravel Edition
# Original PHP Repo: https://github.com/rizwan-bytes/educore-cms
# Laravel Repo: https://github.com/rizwan-bytes/educore-cms-laravel
# Developer: Rizwan — Wahsol Technologies
# Started: 26 May 2026

---

## ⚡ QUICK CONTEXT — READ THIS EVERY SESSION

> ⚠️ Claude Code: Yeh section SESSION END pe KHUD update karo.
> Rizwan ko manually update karne ki zaroorat NAHI padni chahiye.

**Project:** EduCore CMS — Full Laravel replica of PHP/MySQL original
**Reference:** `../educore-cms/` (original PHP project — logic reference)
**Status:** 🔴 Phase 2 IN PROGRESS — Students ✅ Teachers ✅ Classes ✅ Subjects ✅ Notices ✅ Attendance ✅ Teacher+Student Portals ✅ Git ✅
**Last Session:** Teacher+Student portals fully built — Teacher dashboard (real stats, charts), Teacher attendance (restricted to own subjects/classes, 403 guard), Teacher notices; Student dashboard (personal stats, 7-day chart, 75% threshold), Student attendance (DataTable + 6-month chart), Student notices; Subjects updated with teacher assignment FK + admin UI dropdown; DataTables spinner fix; SampleDataSeeder updated to assign teachers by specialization; lang files completed (en+ur subjects 4 new keys); Git initialized + pushed to GitHub (197 files)
**Next Task:** Phase 2 — Exams + Results module (create exam, enter marks per student, auto grade via AppHelpers::calcGrade)

### Current Phase:
- ✅ Phase 1 — Foundation (Laravel setup, auth, DB, layout)
- 🔴 Phase 2 — Core Academic (Students, Teachers, Attendance, Results)
- ⬜ Phase 3 — Communication (Diary, Notices, WhatsApp)
- ⬜ Phase 4 — Finance (Fee 4-phases)
- ⬜ Phase 5 — Advanced (Library, Payroll, Admissions, Parent Portal)
- ⬜ Phase 6 — SaaS Layer (Multi-tenancy, SuperAdmin, Landing, Demo)

> 🔴 = Current | ✅ = Done | ⬜ = Pending

---

## 🔴 MANDATORY SESSION PROTOCOL — NON-NEGOTIABLE

> ⚠️ Claude Code: These rules execute AUTOMATICALLY.
> You do NOT wait to be asked. You do NOT skip these steps.
> Violation = session context permanently lost = Rizwan has to repeat everything.

### ON EVERY SESSION START — Do This First:
```
1. Read this entire CLAUDE.md before writing a single line of code
2. Read SESSION NOTES section — understand what was done before
3. Read PROGRESS TRACKER — know exactly what's done vs pending
4. Confirm understanding by saying:
   "Session started. Last done: [X]. Current task: [Y]. Proceeding."
5. ONLY THEN start coding
```

### ON EVERY SESSION END — Do This Before Stopping:
```
STEP 1: Update PROGRESS TRACKER
   → Mark completed items with ✅
   → Update current phase status

STEP 2: Update SESSION NOTES
   → Append one line: [DATE] | Built: X, Y, Z | Bugs: A | Next: B

STEP 3: Update QUICK CONTEXT block at top:
   → Status: what phase/state project is in NOW
   → Last Session: what was just completed
   → Next Task: exact next thing to build

STEP 4: Update KNOWN BUGS if any found

STEP 5: Confirm by saying:
   "CLAUDE.md updated. Next session will continue with: [exact task]"
```

### IF SESSION ENDS UNEXPECTEDLY (context limit hit):
```
Before stopping, ALWAYS write at minimum:
─────────────────────────────────────────
## EMERGENCY SESSION NOTE
Date: [today]
Stopped at: [exact file/function being worked on]
Completed: [list]
Incomplete: [what was mid-way]
Next: [exact first thing to do next session]
─────────────────────────────────────────
```

### TRIGGER WORDS — When Rizwan says any of these, update CLAUDE.md:
- "band karo" / "kal karte hain" / "bas aaj ke liye"
- "good" / "done" / "next session"
- "context khatam" / "limit aa gayi"
- "save karo" / "commit karo"
- ANY goodbye or session-ending phrase

### SELF-CHECK — Before Every Response Ask Yourself:
```
□ Kya maine __() use kiya har string ke liye?
□ Kya maine Ajax use kiya form submit ke liye?
□ Kya maine DataTables use kiya listing ke liye?
□ Kya maine dono lang/en/ aur lang/ur/ files banaye?
□ Kya maine RTL check kiya CSS ke liye?
□ Kya color scheme same rakha?
□ Kya sidebar left pe hai?
□ Kya Chart.js head mein hai?
□ Kya local vendor files use ki (CDN nahi)?
```

---



**EduCore CMS** — A full-featured College Management System + SaaS platform.  
Laravel replica of the original PHP system at `../educore-cms/`

**Two modes:**
- **Single-school:** `https://educore.test` → `college_mgmt` DB
- **SaaS multi-tenant:** `https://educore.test?_tenant=lgs` → `educore_lgs` DB

**Portals:**
- `/admin/` → School admin
- `/teacher/` → Teacher portal
- `/student/` → Student portal
- `/superadmin/` → SaaS super admin (separate auth)
- Public pages → No login, token-authenticated

---

## 🛠️ TECH STACK

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 11.x |
| PHP | 8.3+ |
| Database | MySQL 8.4 / MariaDB 10.4+ |
| Frontend | Bootstrap 5.3 + Chart.js 4.4 + Font Awesome 6.5 |
| Tables | DataTables 2.x (Bootstrap 5 theme) + Buttons extension |
| Ajax | Axios (forms, CRUD) + SweetAlert2 (confirms/alerts) |
| Auth | Laravel Breeze (custom multi-role) |
| Multi-tenancy | Spatie Laravel-Multitenancy |
| Sessions | Laravel Sessions (file/DB) |
| Queue | Laravel Queue (for WhatsApp notifications) |
| Storage | Laravel Storage (avatars, logos, proofs) |
| Server | Apache / Laragon |
| Template | Blade (NOT Livewire, NOT Inertia — plain Blade + vanilla JS) |

**Local URL:** `https://educore-laravel.test`  
**Database:** `educore_laravel` (single) / `educore_central` + `educore_{sub}` (SaaS)

### Composer Packages:
```bash
composer require spatie/laravel-multitenancy
composer require spatie/laravel-permission
composer require barryvdh/laravel-dompdf
composer require simplesoftwareio/simple-qrcode
composer require yajra/laravel-datatables-oracle   # DataTables server-side
composer require laravel/breeze --dev
```

### NPM:
```bash
# No build tools — all frontend via CDN or local vendor files
# DO NOT use Vite/Mix for CSS — plain CSS files only
```

### 📦 Frontend Assets Strategy — LOCAL FILES (not CDN)
> All vendor JS/CSS downloaded locally into `public/assets/vendor/`
> Reason: offline development on Laragon, no internet dependency

```
public/assets/
├── css/
│   ├── style.css              ← custom (copied from original)
│   └── superadmin.css         ← superadmin custom (copied from original)
├── js/
│   ├── main.js                ← custom (copied from original)
│   └── app.js                 ← new: global Ajax helpers + SweetAlert defaults
└── vendor/
    ├── bootstrap/
    │   ├── bootstrap.min.css  ← v5.3.3
    │   └── bootstrap.bundle.min.js
    ├── fontawesome/
    │   ├── all.min.css        ← v6.5.2
    │   └── webfonts/          ← FA font files
    ├── chartjs/
    │   └── chart.umd.min.js   ← v4.4.1
    ├── datatables/
    │   ├── dataTables.min.css ← DataTables 2.x Bootstrap5 theme
    │   ├── dataTables.min.js
    │   ├── buttons.min.css    ← Export buttons extension
    │   ├── buttons.min.js
    │   ├── buttons.html5.min.js
    │   └── buttons.print.min.js
    ├── sweetalert2/
    │   ├── sweetalert2.min.css
    │   └── sweetalert2.min.js
    ├── axios/
    │   └── axios.min.js       ← v1.7.x
    └── qrcodejs/
        └── qrcode.min.js      ← for challan QR codes
```

**Download script** (run once, saves to vendor folder):
```bash
# Claude Code will create a bash script: scripts/download-vendors.sh
# that downloads all vendor files automatically
```

---

## ⚠️ STRICT UI RULES — NEVER OVERRIDE

> Claude: These are NON-NEGOTIABLE. Match the original PHP system exactly.
> Do NOT change layout, colors, or theme unless Rizwan explicitly says so.

### Layout (EXACT match to original)
- **Sidebar:** LEFT side, `250px` fixed width, dark background
- **Topbar:** TOP, `60px` fixed height, sticky
- **Main content:** fills remaining space, `padding: 24px`
- **Mobile (≤992px):** sidebar hidden, bottom nav `80px` height
- **Footer:** NOT a traditional footer — Bootstrap JS + main.js only

### ⚠️ COLOR SCHEME — DO NOT CHANGE
```css
--primary:      #6366f1;   /* indigo — buttons, active nav */
--primary-lt:   #818cf8;   /* lighter indigo */
--primary-grad: linear-gradient(135deg, #6366f1, #8b5cf6);
--green:        #10b981;   /* success/paid/present */
--yellow:       #f59e0b;   /* warning/pending */
--red:          #ef4444;   /* danger/absent/overdue */
--cyan:         #06b6d4;   /* info/teacher accent */
--orange:       #f97316;
--bg:           #070c18;   /* page background */
--surface:      #0c1221;
--card:         #111827;
--card-hover:   #151f2e;
--border:       rgba(255,255,255,.055);
--text:         #f1f5f9;
--text-2:       #94a3b8;
--muted:        #4b5563;
```

### Typography
- **Font:** Inter (Google Fonts) — same as original
- Load in layout: `<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">`

### CSS Classes (use exact same class names as original)
- Layout: `.app-wrapper`, `.main-content`, `.page-body`, `.glass-card`
- Buttons: `.btn-primary-custom`, `.btn-outline-custom`, `.btn-icon`
- Status: `.badge-status.active/.inactive/.paid/.pending/.overdue`
- Tables: `.table-dark-custom`
- Stats: `.stat-card`, `.stat-icon`, `.stat-value`, `.stat-label`

> Copy `assets/css/style.css` from original repo directly into `public/assets/css/style.css`

---

## 📁 LARAVEL PROJECT STRUCTURE

```
educore-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          ← Admin portal controllers
│   │   │   ├── Teacher/        ← Teacher portal controllers
│   │   │   ├── Student/        ← Student portal controllers
│   │   │   ├── SuperAdmin/     ← SuperAdmin controllers
│   │   │   └── Public/         ← Public pages (no auth)
│   │   └── Middleware/
│   │       ├── RoleMiddleware.php
│   │       └── TenantMiddleware.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Student.php
│   │   ├── Teacher.php
│   │   ├── Fee.php
│   │   └── [module models...]
│   ├── Services/
│   │   ├── TenantService.php   ← replaces includes/tenant.php
│   │   ├── WhatsAppService.php ← replaces includes/whatsapp_api.php
│   │   ├── FeeService.php
│   │   └── DiaryService.php
│   └── Helpers/
│       └── AppHelpers.php      ← sanitize(), calcGrade(), etc.
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php       ← main layout (sidebar + topbar)
│       │   ├── superadmin.blade.php
│       │   └── public.blade.php
│       ├── admin/
│       ├── teacher/
│       ├── student/
│       ├── superadmin/
│       └── public/
├── routes/
│   ├── web.php
│   ├── admin.php
│   ├── teacher.php
│   ├── student.php
│   └── superadmin.php
├── database/
│   └── migrations/             ← all 20 SQL files converted
├── public/
│   └── assets/
│       ├── css/
│       │   ├── style.css       ← copied from original
│       │   └── superadmin.css  ← copied from original
│       ├── js/
│       │   ├── main.js         ← copied from original
│       │   └── app.js          ← NEW: global Ajax + SweetAlert helpers
│       └── vendor/             ← ALL vendor files downloaded locally
│           ├── bootstrap/
│           ├── fontawesome/
│           ├── chartjs/
│           ├── datatables/
│           ├── sweetalert2/
│           ├── axios/
│           └── qrcodejs/
└── CLAUDE.md                   ← this file
├── scripts/
│   └── download-vendors.sh     ← one-time vendor download script
```

---

## 🗄️ DATABASE — LARAVEL MIGRATIONS

> Convert all 20 SQL files to Laravel migrations. Run in exact order.

### Migration Order:
```
001_create_users_table
002_create_students_teachers_tables
003_create_classes_subjects_table
004_create_attendance_results_fees_table
005_create_settings_table
006_create_uploads_columns
007_create_diary_tables (phases 1,2,3)
008_create_fee_structures_tables (phases 1,2,4)
009_create_timetable_tables
010_create_leave_tables
011_create_syllabus_tables
012_create_library_tables
013_create_security_tables
014_create_at_risk_table
015_create_transcript_table
016_create_ptm_tables
017_create_admission_tables
018_create_student_profile_columns
019_create_payroll_tables
020_create_saas_central_tables (SaaS only)
```

### Key Models & Relationships:
```php
// User → Student/Teacher (polymorphic-style)
User hasOne Student
User hasOne Teacher
Student belongsTo ClassRoom
Student hasMany Fee
Student hasMany Attendance
Teacher hasMany Subject

// Cascade deletes — replicate original behavior
protected static function boot() {
    parent::boot();
    static::deleting(function($user) {
        $user->student()->delete();
        $user->teacher()->delete();
    });
}
```

### Grade Calculation (replicate exact logic):
```php
// In AppHelpers.php
function calcGrade(float $obtained, float $total): string {
    $pct = ($total > 0) ? ($obtained / $total) * 100 : 0;
    return match(true) {
        $pct >= 90 => 'A+',
        $pct >= 80 => 'A',
        $pct >= 70 => 'B',
        $pct >= 60 => 'C',
        $pct >= 50 => 'D',
        default    => 'F',
    };
}
```

---

## 🔐 AUTH & ROLES

### Multi-Role System:
```php
// 3 roles in same users table (same as original)
// roles: 'admin', 'teacher', 'student'
// SuperAdmin: separate table/guard

// Middleware
Route::middleware(['auth', 'role:admin'])->group(function() {
    // admin routes
});
```

### Route Files Structure:
```php
// routes/web.php
Route::get('/', [LandingController::class, 'index']);
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

require __DIR__.'/admin.php';
require __DIR__.'/teacher.php';
require __DIR__.'/student.php';
require __DIR__.'/superadmin.php';

// routes/admin.php
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function() {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index']);
    Route::resource('students', Admin\StudentController::class);
    // etc.
});
```

### CSRF — Laravel handles automatically:
```blade
{{-- In every POST form: --}}
<form method="POST" action="{{ route('admin.students.store') }}">
    @csrf
    {{-- fields --}}
</form>
```

### Output Sanitization:
```blade
{{-- Always use {{ }} NOT {!! !!} for user data --}}
{{ $student->name }}        ✅ auto-escaped
{!! $student->name !!}      ❌ only for trusted HTML
```

---

## 🧩 BLADE LAYOUT SYSTEM

### Head Partial (`layouts/partials/head.blade.php`):
```blade
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'EduCore') — {{ SettingService::get('college_name', 'EduCore') }}</title>

{{-- Google Fonts — Inter (CDN OK for fonts) --}}
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

{{-- Local Vendor CSS --}}
<link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/datatables/dataTables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/datatables/buttons.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/sweetalert2.min.css') }}">

{{-- Custom CSS --}}
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

{{-- Chart.js — must be in head (NOT footer) --}}
<script src="{{ asset('assets/vendor/chartjs/chart.umd.min.js') }}"></script>

@stack('styles')
```

### Footer Partial (`layouts/partials/footer.blade.php`):
```blade
{{-- Local Vendor JS --}}
<script src="{{ asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables/dataTables.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables/buttons.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables/buttons.print.min.js') }}"></script>
<script src="{{ asset('assets/vendor/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/vendor/axios/axios.min.js') }}"></script>

{{-- Custom JS --}}
<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>  {{-- Ajax helpers --}}

@stack('scripts')

{{-- Mobile Bottom Nav --}}
@auth
@include('layouts.partials.mobile-nav')
@endauth
</body>
</html>
```
```blade
<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.partials.head')
</head>
<body class="@yield('body-class')">
<div class="app-wrapper">
    @include('layouts.partials.sidebar')
    <div class="main-content">
        @include('layouts.partials.topbar')
        <div class="page-body">
            @include('layouts.partials.flash')
            @yield('content')
        </div>
        @include('layouts.partials.footer')
    </div>
</div>
</body>
</html>
```

### Page Template:
```blade
@extends('layouts.app')
@section('title', 'Page Name')
@section('content')
    <div class="glass-card">
        <div class="card-header">
            <h5>Page Title</h5>
        </div>
        <div class="card-body">
            {{-- content --}}
        </div>
    </div>
@endsection
@push('scripts')
    {{-- page-specific JS --}}
@endpush
```

### Sidebar Navigation:
```php
// In sidebar partial — role-aware like original
$menus = [
    'admin' => [
        ['icon' => 'fa-tachometer-alt', 'label' => 'Dashboard', 'route' => 'admin.dashboard'],
        ['icon' => 'fa-users', 'label' => 'Students', 'route' => 'admin.students.index'],
        // SaaS gating:
        ['icon' => 'fa-money-bill', 'label' => 'Fee Analytics', 'route' => 'admin.fee-analytics',
         'gate' => 'fee_analytics'],
    ],
];
```

---

## ⚙️ SETTINGS SYSTEM

```php
// app/Services/SettingService.php
class SettingService {
    public static function get(string $key, $default = null) {
        return cache()->remember("setting_{$key}", 3600, function() use ($key, $default) {
            return Setting::where('key', $key)->value('value') ?? $default;
        });
    }
    
    public static function set(string $key, $value): void {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        cache()->forget("setting_{$key}");
    }
}

// Usage in Blade:
{{ \App\Services\SettingService::get('college_name', 'EduCore') }}

// Dynamic theme — in layouts/partials/head.blade.php:
@php
    $primaryColor = \App\Services\SettingService::get('primary_color', '#6366f1');
    $cyanColor = \App\Services\SettingService::get('secondary_color', '#06b6d4');
    $useCustomColors = ($primaryColor !== '#6366f1' || $cyanColor !== '#06b6d4');
@endphp
@if($useCustomColors)
<style>
    :root {
        --primary: {{ $primaryColor }};
        --cyan: {{ $cyanColor }};
    }
</style>
@endif
```

---

## 🌐 PUBLIC PAGES (No Auth)

These pages use a separate layout and NO auth middleware:

| Route | Controller | Purpose |
|-------|-----------|---------|
| `/fee-portal` | Public\FeePortalController | Parent fee portal |
| `/acknowledge` | Public\AcknowledgeController | Diary acknowledgment |
| `/verify` | Public\VerifyController | Transcript QR verify |
| `/ptm-booking` | Public\PtmBookingController | PTM slot booking |
| `/apply` | Public\AdmissionController | Online admission form |
| `/forgot-password` | Auth\ForgotPasswordController | Password reset |

```php
// routes/web.php — public routes (no auth middleware)
Route::get('/fee-portal', [Public\FeePortalController::class, 'show']);
Route::post('/fee-portal/submit-proof', [Public\FeePortalController::class, 'submitProof']);
```

---

## 🏢 SAAS MULTI-TENANCY

Using **Spatie Laravel-Multitenancy** package.

### Architecture:
```
Central DB: educore_central
    → tenants table (subdomain, db_name, plan, status)
    → plans table (features JSON)

Per-tenant DB: educore_{subdomain}
    → complete copy of all migrations
```

### Tenant Detection:
```php
// app/Http/Middleware/TenantMiddleware.php
public function handle(Request $request, Closure $next) {
    $subdomain = $request->query('_tenant') 
                ?? session('_dev_tenant')
                ?? $this->extractSubdomain($request->getHost());
    
    if ($subdomain) {
        $tenant = Tenant::where('subdomain', $subdomain)->firstOrFail();
        app(CurrentTenant::class)->set($tenant);
        // switch DB connection
    }
    return $next($request);
}
```

### Feature Gating:
```php
// Replaces canUse() from original
// app/Services/TenantService.php
public static function canUse(string $feature): bool {
    $tenant = app(CurrentTenant::class)->get();
    if (!$tenant) return true; // single-school = full access
    return in_array($feature, $tenant->plan->features ?? []);
}

// In controllers:
if (!TenantService::canUse('fee_analytics')) {
    return redirect()->route('upgrade');
}

// In Blade:
@if(TenantService::canUse('diary_module'))
    {{-- show diary --}}
@endif
```

### Plans:
| Plan | Price | Features |
|------|-------|---------|
| Trial | Free | core_academic, attendance, basic_fees |
| Basic | ₨5,000/mo | + user_management, csv_export |
| Pro | ₨15,000/mo | + diary, whatsapp, fee_structures, timetable, library, payroll |
| Elite | ₨50,000/mo | + whatsapp_api, payment_proofs, at_risk_detection, analytics |

---

## 📋 DATATABLES — STANDARD PATTERN

> ⚠️ Claude Code: EVERY table MUST follow this EXACT pattern. No exceptions.
> This is the final tested and approved pattern — do not deviate.

### ✅ MANDATORY RULES FOR EVERY TABLE:
1. **DOM string ALWAYS:** `"<'dt-toolbar'<'dt-left'f><'dt-right'Bl>><'dt-table't><'dt-footer'ip>"`
2. **Search on LEFT, buttons (CSV/Print) + length on RIGHT**
3. **All toolbar elements same height (34px)**
4. **lengthMenu ALWAYS:** `[[10, 20, 50, 100, -1], [10, 20, 50, 100, 'All']]`
5. **Print button ALWAYS has `customize` function for white professional layout**
6. **CSV/Print exportOptions ALWAYS:** `{ columns: ':not(:last-child)' }` (exclude Actions column)
7. **Pagination dark via CSS variables** — see style.css DataTables section
8. **language.info ALWAYS:** `'Showing _START_–_END_ of _TOTAL_'` (NO `__()` keys here)
9. **Pagination icons:** `'<i class="fas fa-chevron-left"></i>'` etc.
10. **Sample data ALWAYS seeded** alongside new module

### Standard DataTable JS (copy-paste template):
```javascript
$('#xyzTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{ route("admin.xyz.data") }}',
        type: 'GET',
        data: function(d) { /* add any custom filters */ }
    },
    columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'name',    name: 'name' },
        // ... more columns ...
        { data: 'status',  name: 'status',  orderable: false, searchable: false },
        { data: 'actions', name: 'actions', orderable: false, searchable: false },
    ],
    dom: "<'dt-toolbar'<'dt-left'f><'dt-right'Bl>><'dt-table't><'dt-footer'ip>",
    buttons: [
        {
            extend: 'csv',
            className: 'btn-dt-export',
            text: '<i class="fas fa-file-csv me-1"></i>CSV',
            exportOptions: { columns: ':not(:last-child)' }
        },
        {
            extend: 'print',
            className: 'btn-dt-export',
            text: '<i class="fas fa-print me-1"></i>Print',
            exportOptions: { columns: ':not(:last-child)' },
            customize: function(win) {
                var style = win.document.createElement('style');
                style.innerHTML = [
                    'body { font-family:Arial,sans-serif; background:#fff; color:#111; padding:20px; margin:0; }',
                    'h1   { font-size:16px; font-weight:700; color:#111; margin:0 0 2px; }',
                    '.print-meta { font-size:11px; color:#6b7280; margin-bottom:16px; border-bottom:2px solid #e5e7eb; padding-bottom:10px; }',
                    'table { border-collapse:collapse; width:100%; }',
                    'thead th { background:#f3f4f6 !important; color:#374151 !important; font-weight:700;',
                    '           font-size:10px; text-transform:uppercase; padding:9px 10px; border:1px solid #e5e7eb; }',
                    'tbody td { padding:8px 10px; border:1px solid #e5e7eb; color:#111; font-size:12px; background:#fff; }',
                    'tbody tr:nth-child(even) td { background:#f9fafb; }',
                    '@page { margin:15mm; }'
                ].join(' ');
                win.document.head.appendChild(style);
                var h1 = win.document.querySelector('h1');
                if (h1) {
                    h1.outerHTML = '<h1>EduCore CMS</h1>' +
                        '<div class="print-meta">MODULE_NAME | Printed: ' +
                        new Date().toLocaleDateString("en-PK",{day:"2-digit",month:"short",year:"numeric"}) + '</div>';
                }
            }
        }
    ],
    pageLength: 20,
    lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, 'All']],
    language: {
        search: '',
        searchPlaceholder: 'Search...',
        processing:   '<div class="dt-spinner-card"><i class="fas fa-spinner fa-spin"></i><span>Loading...</span></div>',
        emptyTable:   '<div class="dt-empty">No records found.</div>',
        zeroRecords:  '<div class="dt-empty">No records found.</div>',
        info:         'Showing _START_–_END_ of _TOTAL_',
        infoEmpty:    'Showing 0 of 0',
        infoFiltered: '(filtered from _MAX_)',
        paginate: {
            first:    '<i class="fas fa-angles-left"></i>',
            last:     '<i class="fas fa-angles-right"></i>',
            previous: '<i class="fas fa-chevron-left"></i>',
            next:     '<i class="fas fa-chevron-right"></i>',
        },
    },
    drawCallback: function(settings) {
        var info = this.api().page.info();
        $('#totalCount').text(info.recordsTotal);
    }
});
```

### Page-level CSS (add in @push('styles') of every table page):
```css
/* Same height for all toolbar elements */
.dt-toolbar .dataTables_filter input,
.dt-toolbar .dataTables_length select,
.dt-toolbar .btn-dt-export,
.dt-toolbar .dt-button { height: 34px !important; line-height: 1 !important; box-sizing: border-box !important; }
/* Table wrapper */
.dt-table { overflow-x: auto; }
```

### Server-Side DataTable Controller Method:
```php
public function data(Request $request) {
    $query = Student::with(['user', 'class'])->select('students.*');

    return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('name', fn($s) => '<div class="dt-name-cell">...</div>')
        ->addColumn('status', fn($s) =>
            '<button class="badge-status '.($s->status?'active':'inactive').'" onclick="toggleStatus('.$s->id.')">'.
            ($s->status ? __('common.active') : __('common.inactive')).'</button>'
        )
        ->addColumn('actions', fn($s) =>
            '<div class="dt-actions">'.
            '<button class="btn-icon edit" onclick="editXyz('.$s->id.')"><i class="fas fa-pen"></i></button>'.
            '<button class="btn-icon delete" onclick="deleteXyz('.$s->id.')"><i class="fas fa-trash"></i></button>'.
            '</div>'
        )
        ->rawColumns(['name','status','actions'])
        ->make(true);
}
```

### Pagination Dark Fix (already in style.css — DO NOT change):
```css
/* Bootstrap 5 CSS variable override — this is the correct approach */
.dataTables_paginate {
    --bs-pagination-bg:                   var(--surface);
    --bs-pagination-color:                var(--text-2);
    --bs-pagination-border-color:         var(--border);
    --bs-pagination-active-bg:            var(--primary);
    --bs-pagination-active-color:         #fff;
    /* ... full list in style.css ... */
}
```

---

## ⚡ AJAX SYSTEM — STANDARD PATTERNS

> ALL form submissions = Ajax. NO full page refresh on CRUD operations.
> Use Axios for requests. SweetAlert2 for confirms and success/error toasts.

### Global App.js (public/assets/js/app.js):
```javascript
// ============================================
// EDUCORE — Global Ajax + SweetAlert Helpers
// ============================================

// Axios defaults
axios.defaults.headers.common['X-CSRF-TOKEN'] = 
    document.querySelector('meta[name="csrf-token"]')?.content;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

// SweetAlert2 dark theme defaults
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    background: '#111827',
    color: '#f1f5f9',
    iconColor: '#10b981'
});

// Global success toast
window.toastSuccess = (msg) => Toast.fire({ icon: 'success', title: msg });
window.toastError   = (msg) => Toast.fire({ icon: 'error', title: msg, iconColor: '#ef4444' });
window.toastWarning = (msg) => Toast.fire({ icon: 'warning', title: msg, iconColor: '#f59e0b' });

// Delete confirm dialog
window.confirmDelete = (url, onSuccess) => {
    Swal.fire({
        title: 'Delete?',
        text: 'This cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#374151',
        confirmButtonText: 'Yes, delete',
        background: '#111827',
        color: '#f1f5f9'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(url)
                .then(res => {
                    toastSuccess(res.data.message || 'Deleted!');
                    if (onSuccess) onSuccess(res);
                })
                .catch(err => toastError(err.response?.data?.message || 'Error!'));
        }
    });
};

// Generic Ajax form submit
window.ajaxSubmit = (formEl, options = {}) => {
    const form = formEl instanceof HTMLElement ? formEl : document.querySelector(formEl);
    const btn  = form.querySelector('[type=submit]');
    const url  = options.url  || form.action;
    const method = options.method || form.method || 'POST';
    
    // Show loading state
    const origText = btn?.innerHTML;
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...'; }
    
    const data = new FormData(form);
    
    axios({ method, url, data,
        headers: { 'Content-Type': 'multipart/form-data' }
    })
    .then(res => {
        toastSuccess(res.data.message || 'Saved successfully!');
        if (options.onSuccess) options.onSuccess(res);
        if (options.resetForm) form.reset();
        if (options.closeModal) bootstrap.Modal.getInstance(document.querySelector(options.closeModal))?.hide();
        if (options.reloadTable) window[options.reloadTable]?.ajax.reload();
    })
    .catch(err => {
        const errors = err.response?.data?.errors;
        if (errors) {
            // Show Laravel validation errors
            Object.keys(errors).forEach(field => {
                const input = form.querySelector(`[name="${field}"]`);
                if (input) {
                    input.classList.add('is-invalid');
                    let fb = input.nextElementSibling;
                    if (!fb || !fb.classList.contains('invalid-feedback')) {
                        fb = document.createElement('div');
                        fb.className = 'invalid-feedback';
                        input.after(fb);
                    }
                    fb.textContent = errors[field][0];
                }
            });
        }
        toastError(err.response?.data?.message || 'Something went wrong!');
    })
    .finally(() => {
        if (btn) { btn.disabled = false; btn.innerHTML = origText; }
    });
};

// Clear validation errors on input focus
document.addEventListener('focusin', e => {
    if (e.target.classList.contains('is-invalid')) {
        e.target.classList.remove('is-invalid');
        e.target.nextElementSibling?.remove();
    }
});
```

### Laravel Controller — Ajax Response Pattern:
```php
// ALWAYS return JSON for Ajax requests
// Success:
return response()->json([
    'success' => true,
    'message' => 'Student saved successfully!',
    'data'    => $student
]);

// Validation error (automatic via Laravel):
// $request->validate([...]) → Laravel returns 422 with errors JSON

// Error:
return response()->json([
    'success' => false,
    'message' => 'Something went wrong.'
], 500);

// Delete:
$student->delete();
return response()->json(['message' => 'Student deleted.']);
```

### Modal Form Pattern (Add/Edit without page refresh):
```blade
{{-- Trigger button --}}
<button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addStudentModal">
    <i class="fas fa-plus"></i> Add Student
</button>

{{-- Edit button (loads data via Ajax) --}}
<button class="btn-icon edit" onclick="editStudent({{ $student->id }})">
    <i class="fas fa-edit"></i>
</button>

{{-- Modal --}}
<div class="modal fade" id="addStudentModal">
    <div class="modal-dialog">
        <div class="modal-content" style="background: var(--card);">
            <div class="modal-header" style="border-color: var(--border);">
                <h5 class="modal-title" style="color: var(--text);" id="modalTitle">Add Student</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="studentForm">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="student_id" id="studentId">
                <div class="modal-body">
                    {{-- form fields --}}
                </div>
                <div class="modal-footer" style="border-color: var(--border);">
                    <button type="button" class="btn-outline-custom" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-primary-custom">Save Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Form submit via Ajax
document.getElementById('studentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    ajaxSubmit(this, {
        url: this.id === 'studentForm' && document.getElementById('studentId').value
             ? '{{ route("admin.students.update", ":id") }}'.replace(':id', document.getElementById('studentId').value)
             : '{{ route("admin.students.store") }}',
        onSuccess: () => {
            studentsTable.ajax.reload();  // reload DataTable
        },
        closeModal: '#addStudentModal',
        resetForm: true
    });
});

// Load data for edit
function editStudent(id) {
    axios.get(`/admin/students/${id}/edit`)
        .then(res => {
            const s = res.data.student;
            document.getElementById('modalTitle').textContent = 'Edit Student';
            document.getElementById('studentId').value = s.id;
            document.getElementById('formMethod').value = 'PUT';
            // populate fields...
            new bootstrap.Modal(document.getElementById('addStudentModal')).show();
        });
}

// Delete
function deleteStudent(id) {
    confirmDelete(`/admin/students/${id}`, () => studentsTable.ajax.reload());
}
</script>
@endpush
```

### AJAX Rules — DO NOT BREAK:
1. **Every listing page** → DataTable with server-side processing
2. **Every CRUD form** → Ajax submit, NO page refresh
3. **Every delete** → SweetAlert2 confirm first, then Ajax DELETE
4. **Every success/error** → SweetAlert2 toast (top-right)
5. **Laravel validation errors** → show inline under each field
6. **Loading state** → button shows spinner while request pending
7. **DataTable reload** after create/update/delete → `table.ajax.reload()`

---

## 📊 CHARTS (Chart.js)

> ⚠️ Load Chart.js in `<head>` NOT before `</body>` — same rule as original.

```blade
{{-- In layouts/partials/head.blade.php --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

{{-- In page @push('scripts') --}}
@push('scripts')
<script>
(function() {
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
    Chart.defaults.font.size = 11;
    
    // Always check empty:
    // if (!labels.length) { emptyMsg('chartId', 'No data'); return; }
    
    new Chart(document.getElementById('myChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                data: {!! json_encode($data) !!},
                backgroundColor: 'rgba(99,102,241,0.7)'
            }]
        }
    });
})();
</script>
@endpush
```

**Color convention (same as original):**
- Green `rgba(16,185,129,...)` → present/paid/success
- Red `rgba(239,68,68,...)` → absent/overdue/danger
- Yellow `rgba(245,158,11,...)` → pending/warning
- Indigo `rgba(99,102,241,...)` → primary metric
- Cyan `rgba(6,182,212,...)` → secondary/trend

---

## 📱 MOBILE LAYOUT

```css
/* Same breakpoints as original — DO NOT CHANGE */
@media (max-width: 992px) {
    .sidebar { display: none; }
    .mobile-bottom-nav { display: flex; }
    .page-body { padding-bottom: 80px; }
}
@media (max-width: 576px) {
    /* single column, smaller cards */
}
```

Bottom nav in `layouts/partials/footer.blade.php` — role-specific 5 links.

---

## 🔒 SECURITY RULES

### Rate Limiting:
```php
// routes/web.php
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,15'); // 5 attempts per 15 min

// Custom: also lock by email (replicate dual rate-limit from original)
// In AuthController — check failed_logins table
```

### File Uploads:
```php
// Always validate MIME, never trust extension
$request->validate([
    'photo' => 'required|file|mimes:jpeg,png,webp|max:3072',
]);
// Store in storage/app/public/avatars/ with symlink
```

### Password Strength:
```php
// In validation rules
'password' => ['required', 'min:8', 'regex:/[a-zA-Z]/', 'regex:/[0-9]/']
```

---

## 🚀 PHP → LARAVEL CONVERSION PATTERNS

### Original PHP → Laravel equivalent:

| Original PHP | Laravel |
|-------------|---------|
| `requireLogin('admin')` | `middleware('auth', 'role:admin')` |
| `csrfInput()` | `@csrf` |
| `sanitize($var)` | `{{ $var }}` (auto-escaped) |
| `$_POST['field']` | `$request->input('field')` |
| `$_SESSION['flash']` | `session()->flash('success', 'msg')` |
| `showFlash()` | `@include('partials.flash')` |
| `getSetting($pdo, 'key')` | `SettingService::get('key')` |
| `PDO prepared statement` | `DB::select()` or Eloquent |
| `header('Location: ...')` | `return redirect()->route('name')` |
| `include 'header.php'` | `@extends('layouts.app')` |
| `canUse('feature')` | `TenantService::canUse('feature')` |
| `requireFeature('x')` | `abort_unless(TenantService::canUse('x'), 403)` |
| `calcGrade($o, $t)` | `AppHelpers::calcGrade($o, $t)` |
| `sanitize($v)` | `{{ $v }}` in Blade |
| `uploadPhoto($_FILES)` | `$request->file('photo')->store()` |
| `BASE_URL . '/admin/page'` | `route('admin.page')` |

### POST-Redirect-GET in Laravel:
```php
public function store(Request $request) {
    $validated = $request->validate([...]);
    Student::create($validated);
    return redirect()->route('admin.students.index')
                     ->with('success', 'Student added!');
}
```

### Flash Messages in Blade:
```blade
@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert-danger">{{ session('error') }}</div>
@endif
```

---

## ✅ PROGRESS TRACKER

> Update after every session!

### Phase 1 — Foundation
- [x] Laravel install + Laragon setup — Laravel 11.53.1 on PHP 8.3.30
- [x] `.env` config + DB connection — MySQL, Asia/Karachi, educore_laravel
- [x] `scripts/download-vendors.sh` + `scripts/download-vendors.ps1` — both created + executed
- [x] CSS + JS assets copied from original (`style.css`, `main.js`)
- [x] `public/assets/js/app.js` — global Ajax + SweetAlert helpers (full implementation)
- [x] `layouts/app.blade.php` with sidebar + topbar + partials (local vendor files)
- [x] DataTables dark theme CSS added to `style.css`
- [x] Auth (login/logout, 3 roles) — AuthController + login.blade.php
- [x] Role middleware — RoleMiddleware registered in bootstrap/app.php
- [x] DB migrations — users (role/status/phone/avatar), settings, spatie permissions
- [x] `yajra/laravel-datatables-oracle` composer package
- [x] Settings system + SettingService
- [x] Dynamic theme injection (primary_color, secondary_color)
- [x] Mobile bottom nav (role-aware, 5 links each)
- [x] Lang files — en/ + ur/ (common, auth, dashboard, students, teachers, classes, subjects, attendance, fees, exams, diary, notices, settings)
- [x] AppHelpers (calcGrade, rollNo, formatPhone, attendancePercent)
- [x] Admin/Teacher/Student routes + placeholder controllers
- [x] Locale switcher (EN/UR) with RTL body class
- [x] Admin created: admin@educore.test / admin123

### Phase 2 — Core Academic
- [x] UI matched to original PHP — sidebar, topbar, Inter font, all nav items/icons
- [x] Inter font vendor files copied locally (public/assets/vendor/inter/)
- [x] sidebar-overlay div added to app.blade.php
- [x] Sidebar: .sidebar-brand, user at top (between brand+nav), .badge-role, pen icon, logout in footer
- [x] Topbar: div (not header), .topbar-breadcrumb, .topbar-actions, date chip, bell, logout icon
- [x] Admin sidebar: all menu items from original (34 items, 8 sections, correct FA6 icons)
- [x] Teacher sidebar: all menu items from original (15 items, 6 sections)
- [x] Student sidebar: all menu items from original (13 items, 6 sections)
- [x] Models: Student, Teacher, Fee, Notice, ClassRoom, Attendance, Result, Subject
- [x] Migrations: classes, students, teachers, fees, notices, attendance, results, subjects (all migrated)
- [x] Routes: 69 total — admin (34 routes), teacher (15), student (13) all registered
- [x] Admin Dashboard: real stats (8 cards in 2 rows) + 5 charts + recent students table + notices panel
- [x] Lang files updated: dashboard.php, fees.php, exams.php, diary.php, common.php (en+ur)
- [x] Students CRUD (DataTables server-side, Ajax modal, photo upload) — StudentController + 7 routes + students/index.blade.php + address migration
- [x] Teachers CRUD — TeacherController + 7 routes + teachers/index.blade.php (photo upload, phone, qualification, specialization, joining date)
- [x] Classes CRUD — ClassController + 7 routes + classes/index.blade.php (student count shown, cannot-delete guard if students enrolled)
- [x] Subjects CRUD — SubjectController + 7 routes + subjects/index.blade.php (filter by class, code badge, teacher assignment)
- [x] Notices CRUD — NoticeController + 7 routes + notices/index.blade.php (target role filter, content preview in table)
- [x] Attendance (mark by class+date, Present/Absent/Late, bulk mark, history DataTable, AttendanceSeeder 2130 records)
- [x] Teacher Portal — Dashboard (real stats + charts), Attendance (restricted to own subjects/classes, 403 guard), Notices
- [x] Student Portal — Dashboard (personal stats + 7-day chart), Attendance (DataTable + 6-month chart, 75% threshold warning), Notices
- [x] subjects.teacher_id migration — FK to teachers, Teacher model assignedClasses() helper
- [x] SampleDataSeeder updated — teachers assigned to subjects by specialization
- [x] Git initialized + pushed to GitHub (197 files, fe8c927)
- [ ] Exams + Results
- [ ] Report Cards (print)

### Phase 3 — Communication
- [ ] School Diary Phase 1 (entries, read receipts)
- [ ] School Diary Phase 2 (WhatsApp API, parent ack)
- [ ] School Diary Phase 3 (HW submissions, analytics, streaks)

### Phase 4 — Finance
- [ ] Fee CRUD (basic)
- [ ] Fee Structures + Batch Generate
- [ ] Fee Challan (3-copy print + QR)
- [ ] Scholarships
- [ ] Late fees + installments
- [ ] Fee Analytics + Reports
- [ ] Parent Portal + Payment Proofs

### Phase 5 — Advanced Modules
- [ ] Timetable Builder
- [ ] Leave Management
- [ ] Syllabus Tracker
- [ ] Library Management
- [ ] Security Dashboard
- [ ] At-Risk Students (Elite)
- [ ] Digital Transcripts
- [ ] PTM Scheduler
- [ ] Online Admissions
- [ ] Staff Payroll
- [ ] HEC Reports
- [ ] **Parent Portal** (hybrid token + optional password)
- [ ] Parents management in Admin panel

### Phase 6 — SaaS Layer
- [ ] Spatie multitenancy setup
- [ ] Central DB (plans, tenants, `is_demo` flag)
- [ ] Tenant detection middleware
- [ ] Feature gating (canUse / requireFeature)
- [ ] SuperAdmin panel
- [ ] School onboarding (auto-provision DB)
- [ ] Plan management
- [ ] Tenant-aware logout
- [ ] **Professional Landing Page** (all 11 sections)
- [ ] Pricing toggle (monthly/annual)
- [ ] **Demo tenant** (educore_demo DB, Elite plan)
- [ ] `DemoSchoolSeeder` — 80 students, full Pakistani data, `is_seeded=1`
- [ ] `demo:seed` / `demo:reset` / `demo:cleanup` artisan commands
- [ ] Scheduled nightly cleanup (2 AM)
- [ ] Demo banner in topbar
- [ ] `TenantService::isDemo()` helper
- [ ] SuperAdmin demo reset button
- [ ] Demo credentials shown on landing page

### 🐛 Known Issues / Bugs
*(Add as discovered)*

---

## 📝 SESSION NOTES — APPEND EVERY SESSION

> ⚠️ Claude Code: Yeh section EVERY session end pe update HOGA.
> Format strict hai — follow karo exactly.
> Rizwan ko manually update karne ki zaroorat KABHI nahi padni chahiye.

### Format:
```
[DATE] | ✅ Built: [list] | 🐛 Bugs: [list or 'none'] | ⏭️ Next: [exact task]
```

### Log:
```
[START]      | Project initialized | Next: Laravel setup + Phase 1
[2026-05-26] | Built: Laravel 11.53.1 install (PHP 8.3.30), .env configured (MySQL/Karachi), vendor downloads (Bootstrap 5.3.3, FA 6.5.2, Chart.js 4.4.1, DataTables 2.x, SweetAlert2, Axios, jQuery, QRCode.js), scripts/download-vendors.ps1 + .sh created | Bugs: none | Next: Install composer packages
[2026-05-26] | ✅ PHASE 1 COMPLETE | Built: Composer packages (Spatie Permission, DataTables, DomPDF, QRCode, Breeze), RoleMiddleware, AuthController, login page, layouts/app.blade.php + all partials (head, sidebar, topbar, flash, footer, mobile-nav, sidebar-admin/teacher/student), SettingService, AppHelpers, lang en/+ur/ (13 files each), all routes (admin/teacher/student), Admin+Teacher+Student DashboardControllers, placeholder views, DataTables dark CSS + Login CSS, default admin user (admin@educore.test/admin123), default settings seeded, 27 routes registered | Bugs: none | Next: Phase 2 — Students CRUD + real dashboard stats
[2026-05-26] | ✅ UI FIXES + PHASE 2 DASHBOARD | Built: Inter font vendor files (local), sidebar-overlay, sidebar rewrite (.sidebar-brand, user at top with pen icon, flat nav, correct FA6 icons, all 34 admin items), topbar rewrite (breadcrumb, date chip, bell, logout), 8 new models (Student/Teacher/Fee/Notice/ClassRoom/Attendance/Result/Subject), 8 migrations (all run), 69 routes, admin dashboard with 8 stat cards + 5 charts (attendance trend, fee doughnut, monthly fee line, grade distribution, students by class) + recent students table + notices panel, lang files updated (en+ur) | Bugs: none | Next: Phase 2 — Students CRUD module (DataTables server-side + Ajax modal + photo upload)
[2026-05-26] | ✅ STUDENTS CRUD | Built: StudentController (7 methods: index/data/store/edit/update/destroy/toggleStatus), address migration added to students table, 7 student routes registered, resources/views/admin/students/index.blade.php (DataTables server-side + Ajax modal add/edit + photo upload + status toggle + delete confirm), lang/en/students.php + lang/ur/students.php updated (30+ keys), common.php click_to_toggle key added, Attendance model $table fix | Bugs: attendances table bug fixed (Attendance model needed protected $table = 'attendance') | Next: Phase 2 — Teachers CRUD
[2026-05-28] | ✅ TEACHERS + CLASSES + SUBJECTS + NOTICES CRUD | Built: TeacherController+ClassController+SubjectController+NoticeController (28 routes total, 7 each), views: teachers/index+classes/index+subjects/index+notices/index (all DataTables server-side, Ajax modals, status toggle, SweetAlert2 delete), lang files: en+ur for all 4 modules (teachers/classes/subjects/notices — 8 files, 15+ keys each), Teachers avatar placeholder cyan gradient, Classes shows student count badge + cannot-delete guard, Subjects has class filter + code badge styling, Notices has target-role filter + content preview in table | Bugs: none | Next: Phase 2 — Attendance module (mark by class+date, bulk mark, calendar view, summary chart)
[2026-05-28] | ✅ ATTENDANCE MODULE + SPINNER FIX | Built: AttendanceController (5 routes: index/data/mark/students/store), history page (DataTable with class+date+status filters, date-desc order), mark page (class+date selector → Ajax load students → P/A/L buttons per row → live counters → bulk save via updateOrCreate), AttendanceSeeder (2130 records, 30 past working days, 78%P/14%A/8%L), AttendanceSeeder.php standalone, Attendance model relations added (class+marker), lang/en/attendance.php + lang/ur/attendance.php (30+ keys), common.and key en+ur | Bugs: DataTables spinner stuck fixed (removed display:flex!important — was overriding DataTables display:none), spinner now centers via position:absolute+transform | Next: Phase 2 — Exams + Results module
[2026-05-28] | ✅ TEACHER+STUDENT PORTALS + TEACHER-SUBJECT RESTRICTION + GIT | Built: Teacher\DashboardController (real stats: totalStudents/Classes/todayAttPct/noticesCount, trend line chart 7d, today breakdown doughnut), Teacher\AttendanceController (restricted to assignedClasses() only, 403 on unauthorized class_id), Teacher\NoticeController, Teacher views: dashboard+attendance+notices; Student\DashboardController (personal stats, 7-day bar chart P/L/A colors, 75% threshold warning), Student\AttendanceController (own DataTable + 6-month grouped bar chart), Student\NoticeController, Student views: dashboard+attendance+notices; subjects.teacher_id migration (FK nullable, nullOnDelete), Subject model teacher() relation, Teacher model subjects() + assignedClasses() helpers; Admin SubjectController+view updated with teacher assignment dropdown; SampleDataSeeder assigns teachers by spec (Mathematics→Imran/English→Sana/Science→Tariq/Urdu→Fatima/SocialStudies→Usman/Islamiyat→Ayesha); lang/en+ur subjects.php 4 new keys (assigned_teacher, assign_teacher, optional, no_teacher); common.php unauthorized+no_classes_assigned; dashboard.php total_classes+today_breakdown etc; Git initialized, 197 files, pushed to https://github.com/rizwan-bytes/educore-cms-laravel | Bugs: SampleDataSeeder roll_no collision on re-run (existing students) — non-critical, teacher assignment completed before error | Next: Phase 2 — Exams + Results module (exams CRUD, enter marks per student, auto grade calcGrade)
```

---

## 🚫 DO NOT DO — CRITICAL RULES

> Claude: These mistakes must NEVER happen.

1. **Never use `{!! !!}`** for user data — always `{{ }}` (XSS protection)
2. **Never use raw `DB::statement()`** with user input — always Eloquent/query builder bindings
3. **Never re-run migrations** that drop existing tables without checking
4. **Never use Livewire or Inertia** — plain Blade + vanilla JS only
5. **Never change the dark color scheme** — `#070c18` background is fixed
6. **Never change sidebar position** — always LEFT, always 250px
7. **Never move Chart.js to footer** — must be in `<head>`
8. **Never use `.php` extension in routes** — use named routes only
9. **Never load Bootstrap JS in `<head>`** — only in footer partial
10. **Never change font from Inter** without explicit instruction
11. **Never use CDN links** for vendor JS/CSS — always local `vendor/` folder
12. **Never submit forms with full page refresh** — always Ajax via `ajaxSubmit()`
13. **Never use plain `confirm()` for delete** — always `confirmDelete()` SweetAlert2
14. **Never use plain HTML tables for listings** — always DataTables
15. **Never return HTML from Ajax controllers** — always JSON response
16. **⚠️ NEVER hardcode any user-facing string** — always `__('file.key')` — NO EXCEPTIONS
17. **Never create a module** without simultaneously creating both `lang/en/` AND `lang/ur/` files
18. **Never write CSS** without checking it works in both LTR and RTL modes

---

## 🔧 CONFIGURATION

```env
# .env
APP_NAME="EduCore CMS"
APP_URL=https://educore-laravel.test
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=educore_laravel
DB_USERNAME=root
DB_PASSWORD=

# SaaS Central DB (when Phase 6)
CENTRAL_DB_DATABASE=educore_central

# WhatsApp (UltraMsg)
ULTRAMSG_INSTANCE=
ULTRAMSG_TOKEN=

# Queue (for WhatsApp async)
QUEUE_CONNECTION=database
```

```php
// config/app.php additions
'roll_no_prefix' => env('ROLL_NO_PREFIX', 'STU'),
'session_timeout' => env('SESSION_TIMEOUT', 3600),
```

---

## 👨‍👩‍👧 PARENT PORTAL — HYBRID APPROACH

> Token link → Auto-login → Full Parent Dashboard
> Password optional. Zero friction. Professional experience.

### Architecture:

```
WhatsApp Message
    ↓
"Click here to view Ali's fees"
https://educore.app/parent?token=abc123xyz
    ↓
ParentAuthController::tokenLogin()
    ↓
Auto-login → $_SESSION parent set
    ↓
Parent Dashboard (all children visible)
    ├── 📊 Overview (all children summary)
    ├── 💰 Fees & Payments
    ├── 📖 Diary & Homework
    ├── 📅 Attendance
    ├── 📝 Results & Report Cards
    ├── 📅 PTM Booking
    └── ⚙️ Profile (set password for direct login)
```

### Database — Parent Tables:

```php
// Migration: create_parents_table
Schema::create('parents', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('phone', 20)->unique();      // 03xx format
    $table->string('email')->nullable()->unique();
    $table->string('password')->nullable();      // optional
    $table->string('portal_token', 64)->unique(); // main auto-login token
    $table->timestamp('token_last_used')->nullable();
    $table->boolean('password_set')->default(false);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// Migration: create_parent_children_table
Schema::create('parent_children', function (Blueprint $table) {
    $table->id();
    $table->foreignId('parent_id')->constrained()->cascadeOnDelete();
    $table->foreignId('student_id')->constrained()->cascadeOnDelete();
    $table->enum('relation', ['father','mother','guardian'])->default('father');
    $table->boolean('is_primary')->default(true); // primary contact
    $table->timestamps();
    $table->unique(['parent_id', 'student_id']);
});
```

### Routes (`routes/parent.php`):

```php
// Public — token auto-login (no auth needed)
Route::get('/parent', [ParentAuthController::class, 'tokenLogin'])
    ->name('parent.token-login');

// Optional direct login
Route::get('/parent/login',  [ParentAuthController::class, 'showLogin'])
    ->name('parent.login');
Route::post('/parent/login', [ParentAuthController::class, 'login']);
Route::post('/parent/logout',[ParentAuthController::class, 'logout'])
    ->name('parent.logout');

// Protected parent routes
Route::prefix('parent')
    ->middleware(['parent.auth'])
    ->name('parent.')
    ->group(function () {
        Route::get('/dashboard',    [ParentController::class, 'dashboard'])->name('dashboard');
        Route::get('/fees',         [ParentController::class, 'fees'])->name('fees');
        Route::post('/fees/proof',  [ParentController::class, 'submitProof'])->name('fees.proof');
        Route::get('/diary',        [ParentController::class, 'diary'])->name('diary');
        Route::post('/diary/ack',   [ParentController::class, 'acknowledge'])->name('diary.ack');
        Route::get('/attendance',   [ParentController::class, 'attendance'])->name('attendance');
        Route::get('/results',      [ParentController::class, 'results'])->name('results');
        Route::get('/ptm',          [ParentController::class, 'ptm'])->name('ptm');
        Route::post('/ptm/book',    [ParentController::class, 'bookSlot'])->name('ptm.book');
        Route::get('/profile',      [ParentController::class, 'profile'])->name('profile');
        Route::post('/profile/set-password', [ParentController::class, 'setPassword'])
            ->name('profile.set-password');
        // Switch active child (if multiple children)
        Route::post('/switch-child/{student}', [ParentController::class, 'switchChild'])
            ->name('switch-child');
    });
```

### Token Auto-Login Controller:

```php
// app/Http/Controllers/ParentAuthController.php
class ParentAuthController extends Controller
{
    public function tokenLogin(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect()->route('parent.login');
        }

        $parent = ParentModel::where('portal_token', $token)
                             ->where('is_active', true)
                             ->first();

        if (!$parent) {
            return view('parent.invalid-token'); // friendly error page
        }

        // Log them in
        $parent->update(['token_last_used' => now()]);
        session(['parent_id' => $parent->id, 'parent_auth' => true]);

        // Remember which child context (from token source if provided)
        if ($studentId = $request->query('student')) {
            session(['active_child_id' => $studentId]);
        } else {
            // Default to first child
            $firstChild = $parent->children()->first();
            session(['active_child_id' => $firstChild?->id]);
        }

        // First time? Show "Save this link / Set password" prompt
        if (!$parent->password_set) {
            session(['show_save_prompt' => true]);
        }

        return redirect()->route('parent.dashboard');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone'    => 'required',
            'password' => 'required',
        ]);

        $parent = ParentModel::where('phone', $request->phone)
                             ->where('password_set', true)
                             ->first();

        if (!$parent || !Hash::check($request->password, $parent->password)) {
            return back()->withErrors(['phone' => 'Invalid credentials.']);
        }

        session(['parent_id' => $parent->id, 'parent_auth' => true]);
        $firstChild = $parent->children()->first();
        session(['active_child_id' => $firstChild?->id]);

        return redirect()->route('parent.dashboard');
    }
}
```

### Parent Dashboard Controller:

```php
// app/Http/Controllers/ParentController.php
class ParentController extends Controller
{
    protected function parent(): ParentModel
    {
        return ParentModel::findOrFail(session('parent_id'));
    }

    protected function activeChild(): Student
    {
        return Student::findOrFail(session('active_child_id'));
    }

    public function dashboard()
    {
        $parent   = $this->parent();
        $children = $parent->children()->with('class')->get();
        $child    = $this->activeChild();

        // Summary cards data
        $pendingFees  = Fee::where('student_id', $child->id)
                           ->whereIn('status', ['Pending','Overdue'])
                           ->sum('amount');
        $attendancePct = $this->calcAttendancePct($child->id);
        $unreadDiary  = DiaryRead::where('student_id', $child->id)
                                  ->where('is_read', false)->count();
        $lastResult   = Result::where('student_id', $child->id)
                               ->latest()->first();

        return view('parent.dashboard', compact(
            'parent','children','child',
            'pendingFees','attendancePct','unreadDiary','lastResult'
        ));
    }

    // Similar methods for fees, diary, attendance, results, ptm...
}
```

### Parent Dashboard View (`resources/views/parent/dashboard.blade.php`):

```blade
@extends('layouts.parent')

@section('content')
{{-- Child Switcher (if multiple children) --}}
@if($children->count() > 1)
<div class="child-switcher">
    @foreach($children as $ch)
    <button class="child-tab {{ $ch->id == $child->id ? 'active' : '' }}"
            onclick="switchChild({{ $ch->id }})">
        <div class="child-avatar">{{ substr($ch->user->name, 0, 1) }}</div>
        <span>{{ $ch->user->name }}</span>
        <small>{{ $ch->class->name }}</small>
    </button>
    @endforeach
</div>
@endif

{{-- Save Link / Set Password Prompt (first visit) --}}
@if(session('show_save_prompt'))
<div class="save-prompt-banner">
    <i class="fas fa-bookmark"></i>
    <div>
        <strong>Save this link for easy access!</strong>
        <p>Bookmark this page, or set a password to login directly anytime.</p>
    </div>
    <a href="{{ route('parent.profile') }}" class="btn-primary-custom btn-sm">
        Set Password
    </a>
    <button onclick="this.parentElement.remove()" class="btn-close-white"></button>
</div>
@endif

{{-- Summary Cards --}}
<div class="parent-stats-grid">
    <div class="parent-stat-card fees {{ $pendingFees > 0 ? 'alert' : 'clear' }}">
        <i class="fas fa-money-bill-wave"></i>
        <div>
            <span class="label">Pending Fees</span>
            <span class="value">{{ $pendingFees > 0 ? '₨'.number_format($pendingFees) : 'All Clear ✓' }}</span>
        </div>
        <a href="{{ route('parent.fees') }}">View →</a>
    </div>

    <div class="parent-stat-card attendance">
        <i class="fas fa-calendar-check"></i>
        <div>
            <span class="label">Attendance</span>
            <span class="value">{{ $attendancePct }}%</span>
        </div>
        <a href="{{ route('parent.attendance') }}">View →</a>
    </div>

    <div class="parent-stat-card diary {{ $unreadDiary > 0 ? 'alert' : '' }}">
        <i class="fas fa-book-open"></i>
        <div>
            <span class="label">Unread Diary</span>
            <span class="value">{{ $unreadDiary }} entries</span>
        </div>
        <a href="{{ route('parent.diary') }}">View →</a>
    </div>

    <div class="parent-stat-card results">
        <i class="fas fa-graduation-cap"></i>
        <div>
            <span class="label">Latest Result</span>
            <span class="value">{{ $lastResult?->percentage ?? 'N/A' }}%</span>
        </div>
        <a href="{{ route('parent.results') }}">View →</a>
    </div>
</div>

{{-- Quick Actions --}}
<div class="quick-actions">
    <a href="{{ route('parent.fees') }}"       class="qa-btn"><i class="fas fa-money-bill"></i> Pay Fee</a>
    <a href="{{ route('parent.diary') }}"      class="qa-btn"><i class="fas fa-book"></i> Diary</a>
    <a href="{{ route('parent.attendance') }}" class="qa-btn"><i class="fas fa-calendar"></i> Attendance</a>
    <a href="{{ route('parent.results') }}"    class="qa-btn"><i class="fas fa-chart-bar"></i> Results</a>
    <a href="{{ route('parent.ptm') }}"        class="qa-btn"><i class="fas fa-handshake"></i> Book PTM</a>
</div>
@endsection
```

### Parent Layout (`resources/views/layouts/parent.blade.php`):

```blade
{{-- Separate layout — lighter than admin app --}}
{{-- Mobile-first, clean, parent-friendly --}}
<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.partials.head')
    <style>
        /* Parent portal overrides — lighter sidebar */
        .app-wrapper { --sidebar-width: 220px; }
    </style>
</head>
<body>
<div class="app-wrapper">
    @include('parent.partials.sidebar')  {{-- Simplified sidebar --}}
    <div class="main-content">
        @include('parent.partials.topbar') {{-- Child name + school logo --}}
        <div class="page-body">
            @yield('content')
        </div>
    </div>
</div>
</body>
</html>
```

### Parent Middleware (`app/Http/Middleware/ParentAuth.php`):

```php
class ParentAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('parent_auth') || !session('parent_id')) {
            // Save intended URL for redirect after login
            session(['parent_intended' => $request->url()]);
            return redirect()->route('parent.login')
                             ->with('info', 'Please login to access parent portal.');
        }
        return $next($request);
    }
}
```

### WhatsApp Message Updates — Include Token:

```php
// In WhatsAppService.php — ALL parent messages include portal link
private function parentPortalLink(int $studentId): string
{
    $student = Student::find($studentId);
    $parent  = $student->primaryParent();
    $token   = $parent->portal_token;
    
    return route('parent.token-login', [
        'token'   => $token,
        'student' => $studentId,
        '_tenant' => TenantService::subdomain()
    ]);
}

// Fee alert message:
public function feeAlertMsg(Fee $fee): string
{
    return "💰 *Fee Due — {$fee->student->name}*\n"
         . "Amount: ₨{$fee->amount}\n"
         . "Due: {$fee->due_date}\n\n"
         . "👆 View & Pay: {$this->parentPortalLink($fee->student_id)}";
}

// Diary entry message:
public function diaryMsg(DiaryEntry $entry): string  
{
    return "📚 *Diary — {$entry->class->name}*\n"
         . "{$entry->title}\n\n"
         . "👆 View & Acknowledge: {$this->parentPortalLink($entry->student_id)}";
}
```

### Admin — Parent Management (`admin/parents.php`):

```
Admin Panel → Parents section:
- View all parents + their children
- Generate/regenerate portal token
- Send portal link via WhatsApp
- See last login date
- Link/unlink children from parent
- Enable/disable portal access
```

### Parent Portal CSS (add to style.css):

```css
/* Child Switcher */
.child-switcher {
    display: flex; gap: 12px; margin-bottom: 24px;
    overflow-x: auto; padding-bottom: 4px;
}
.child-tab {
    display: flex; align-items: center; gap: 10px;
    background: var(--card); border: 1px solid var(--border);
    border-radius: 12px; padding: 10px 16px; cursor: pointer;
    transition: all 0.2s; white-space: nowrap;
    color: var(--text-2);
}
.child-tab.active {
    border-color: var(--primary);
    background: rgba(99,102,241,0.1);
    color: var(--text);
}
.child-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    background: var(--primary-grad);
    display: flex; align-items: center; justify-content: center;
    font-weight: 600; color: white; font-size: 14px;
}

/* Summary Cards */
.parent-stats-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr));
    gap: 16px; margin-bottom: 24px;
}
.parent-stat-card {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 12px; padding: 20px;
    display: flex; align-items: center; gap: 16px;
}
.parent-stat-card.alert { border-left: 3px solid var(--red); }
.parent-stat-card.clear { border-left: 3px solid var(--green); }

/* Save Prompt Banner */
.save-prompt-banner {
    background: rgba(99,102,241,0.1);
    border: 1px solid rgba(99,102,241,0.3);
    border-radius: 12px; padding: 16px 20px;
    display: flex; align-items: center; gap: 16px;
    margin-bottom: 20px;
}

/* Quick Actions */
.quick-actions {
    display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 24px;
}
.qa-btn {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 10px; padding: 10px 18px;
    color: var(--text-2); text-decoration: none;
    display: flex; align-items: center; gap: 8px;
    transition: all 0.2s; font-size: 14px;
}
.qa-btn:hover {
    border-color: var(--primary); color: var(--text);
    background: rgba(99,102,241,0.08);
}
```

### Progress Tracker — Phase 5 additions:
- [ ] `parents` table migration
- [ ] `parent_children` table migration
- [ ] `ParentModel` with `children()` relationship
- [ ] `Student` → `primaryParent()` relationship
- [ ] `ParentAuth` middleware
- [ ] `ParentAuthController` (token login + direct login)
- [ ] `ParentController` (dashboard, fees, diary, attendance, results, ptm)
- [ ] Parent layout + sidebar + topbar (simplified)
- [ ] Child switcher component
- [ ] "Save link / Set password" first-visit prompt
- [ ] `routes/parent.php`
- [ ] WhatsApp messages updated to include token portal link
- [ ] Admin → Parents management page
- [ ] `portal_token` generation on parent create

---

## 🌐 SAAS LANDING PAGE

> Professional marketing page at root URL when no tenant detected.
> This is the FIRST thing a potential customer sees. Must be world-class.

### Route:
```php
// routes/web.php
// If no tenant + no ?_tenant= + no ?direct=1 → show landing page
Route::get('/', function(Request $request) {
    if (!$request->query('_tenant') && !session('_dev_tenant') && !$request->query('direct')) {
        return view('landing');
    }
    return redirect()->route('login');
});
```

### Landing Page Design Requirements (`resources/views/landing.blade.php`):

**Overall Style:**
- Dark theme — same CSS variables as app (`#070c18` background)
- Font: Inter — same as app
- Fully responsive (mobile-first)
- Smooth scroll, subtle animations (CSS only — no heavy JS libs)
- Professional, modern — NOT a generic Bootstrap template

**Sections (in order):**

#### 1. NAVBAR
```
Logo (EduCore)  |  Features  Pricing  Demo  |  [Login →]  [Start Free Trial]
```
- Sticky on scroll with blur backdrop
- Mobile: hamburger menu
- "Start Free Trial" = primary indigo button

#### 2. HERO SECTION
```
Badge: "🇵🇰 Built for Pakistani Institutions"

H1: "The Modern School Management System
     Pakistan Deserves"

Subtext: "From fee collection to WhatsApp parent alerts —
          EduCore handles everything. No IT team required."

[Start Free Trial]  [View Live Demo →]

Hero image: App screenshot / mockup (dark UI)
```
- Gradient mesh background (indigo/purple glows)
- Animated floating cards showing mini stats

#### 3. STATS BAR
```
500+  Schools  |  50,000+  Students  |  ₨2Cr+  Fees Collected  |  99.9%  Uptime
```
- Full width, subtle dark card
- Numbers animate on scroll (CountUp)

#### 4. FEATURES GRID — "Everything Your School Needs"
```
Feature cards in 3-col grid (2-col mobile):

📊 Smart Dashboard      📚 Academic Management   👨‍🏫 Multi-Role Access
💰 Fee Management       📱 WhatsApp Integration  🔒 Enterprise Security  
📖 School Diary         📅 Timetable Builder     🏆 At-Risk Detection
📚 Library System       💼 Staff Payroll         🌐 Online Admissions
📄 Digital Transcripts  🎓 Report Cards          ☁️ SaaS Multi-Tenant
```
- Each card: icon + title + 1-line description
- Hover: slight glow + lift effect
- "Elite" / "Pro" badge on premium features

#### 5. FEATURE DEEP-DIVE — Tabbed Section
```
Tabs: Academic | Finance | Communication | SaaS

Each tab shows:
- Left: feature description + bullet points
- Right: app screenshot/mockup
```

#### 6. WHATSAPP SECTION — "Parents Stay Informed"
```
"Send automated homework reminders, fee alerts,
 and diary entries directly to parents on WhatsApp"

[WhatsApp mock conversation screenshot]
✓ Homework reminders
✓ Fee overdue alerts  
✓ Parent acknowledgments
✓ Auto evening digest
```
- Green WhatsApp accent color for this section
- Distinctive from rest of page

#### 7. PRICING SECTION
```
[Monthly / Annual toggle — Annual = 20% off]

Trial    Basic        Pro          Elite
Free     ₨5,000/mo   ₨15,000/mo  ₨50,000/mo
─────    ─────────   ──────────  ──────────
500 std  500 std     2,000 std   Unlimited
Core     + Exports   + Diary     + AI Features
         + WhatsApp  + Payroll   + At-Risk
                     + Library   + Analytics

[Start Free]  [Get Basic]  [★ Most Popular]  [Contact Us]
```
- "Pro" card highlighted with indigo border + "Most Popular" badge
- Annual pricing auto-calculated on toggle
- Each plan: checkmark feature list

#### 8. DEMO CTA — "See It Live in 30 Seconds"
```
"No signup required. Explore a fully loaded school
 with real data — students, fees, diary, everything."

[🏫 Open Live Demo →]   ← links to demo.educore.app

Demo credentials shown:
Admin: admin@demo.edu.pk / demo@2025
Teacher: teacher@demo.edu.pk / teacher@123
Student: student@demo.edu.pk / student@123
```
- Distinct section with subtle background
- Demo resets every 24 hours note

#### 9. TESTIMONIALS (Placeholder — fill later)
```
"EduCore replaced our 3 separate systems." — Principal, LGS Jaranwala
"Fee collection went from 3 days to 3 hours." — Bursar, City School
```

#### 10. FAQ ACCORDION
```
Q: Can I migrate my existing data?
Q: Is WhatsApp integration really free?
Q: How does multi-school work?
Q: Is data secure?
Q: What happens after trial ends?
```

#### 11. FOOTER
```
EduCore CMS | By Wahsol Technologies

Links: Features | Pricing | Demo | Login | Contact

"Built with ❤️ for Pakistani Schools"
© 2026 Wahsol Technologies
```

### Landing Page CSS Rules:
```css
/* Landing page uses same CSS variables but adds: */
.landing-hero {
    background: radial-gradient(ellipse at 20% 50%, rgba(99,102,241,0.15) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(139,92,246,0.1) 0%, transparent 50%),
                var(--bg);
}
.feature-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 24px;
    transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
}
.feature-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(99,102,241,0.15);
    border-color: rgba(99,102,241,0.4);
}
.pricing-card.popular {
    border: 2px solid var(--primary);
    position: relative;
}
.navbar-landing {
    backdrop-filter: blur(20px);
    background: rgba(7,12,24,0.8);
    border-bottom: 1px solid var(--border);
}
```

---

## 🎭 DEMO SYSTEM — "Try Before You Buy"

> Full working demo with real Pakistani school data.
> New test entries auto-delete after 4 days.
> No signup needed — instant access.

### Demo Architecture:

```
Demo URL: https://demo.educore.app  (OR  https://educore.app?_tenant=demo)

Database: educore_demo  (separate tenant DB)
Plan: Elite (all features unlocked for demo)
Reset: Scheduled command runs nightly — deletes entries older than 4 days
       BUT preserves seeded/baseline data
```

### Demo Credentials (shown on landing page):
```
Admin:   admin@demo.edu.pk   / demo@2025
Teacher: teacher@demo.edu.pk / teacher@123  
Student: student@demo.edu.pk / student@123
```

### Demo Seeder (`database/seeders/DemoSchoolSeeder.php`):

```php
// Seed realistic Pakistani school data:
// School: "Al-Noor Grammar School, Lahore"

// Classes: 8 (Nursery → Class 8, or Class 1 → 10)
// Students: 80 (10 per class)
// Teachers: 12
// Subjects: 6 per class (English, Urdu, Math, Science, Social Studies, Islamiyat)

// Data volume:
// Attendance: 60 days × all students × all subjects
// Fees: 6 months × all students
// Diary entries: 5 per class (2 homework, 1 test, 1 notice, 1 praise)
// Exam results: 3 exams × all students
// Library books: 50 books, 20 issued
// Leave applications: 8 (mix of approved/pending)
// Timetable: full weekly grid for all classes
// Notices: 10

// Pakistani-realistic names:
// Students: Muhammad Ali, Fatima Malik, Ahmed Hassan, Ayesha Raza...
// Teachers: Sir Imran, Miss Sana, Sir Tariq...
// School: Pakistani curriculum (Urdu + English subjects)
```

### Demo Data Seeder Command:
```bash
php artisan demo:seed          # seed fresh demo data
php artisan demo:reset         # wipe + reseed (full reset)
php artisan demo:cleanup       # delete only user-created entries > 4 days
```

### Demo Cleanup Command (`app/Console/Commands/DemoCleanup.php`):

```php
<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DemoCleanup extends Command
{
    protected $signature   = 'demo:cleanup';
    protected $description = 'Delete user-created demo entries older than 4 days';

    // Tables to clean + their timestamp column
    protected array $tables = [
        'diary_entries'    => 'created_at',
        'fees'             => 'created_at',
        'attendance'       => 'created_at',
        'results'          => 'created_at',
        'notices'          => 'created_at',
        'leaves'           => 'created_at',
        'book_issues'      => 'created_at',
        'admissions'       => 'created_at',
        'payment_proofs'   => 'created_at',
        'diary_reads'      => 'created_at',
        'diary_submissions'=> 'created_at',
    ];

    // Tables where seeded rows are protected by is_seeded=1 flag
    protected array $protectedTables = [
        'students', 'teachers', 'users', 'classes',
        'subjects', 'books', 'fee_structures', 'timetable_slots',
        'syllabus_topics', 'exams',
    ];

    public function handle(): void
    {
        // Switch to demo DB
        config(['database.default' => 'demo']);
        
        $cutoff = Carbon::now()->subDays(4);
        $total  = 0;

        foreach ($this->tables as $table => $col) {
            // Never delete rows with is_seeded = 1
            $deleted = DB::table($table)
                ->where($col, '<', $cutoff)
                ->where(function($q) {
                    $q->whereNull('is_seeded')->orWhere('is_seeded', 0);
                })
                ->delete();
            
            if ($deleted) {
                $this->info("  {$table}: deleted {$deleted} rows");
                $total += $deleted;
            }
        }

        $this->info("Demo cleanup done. Total deleted: {$total}");
    }
}
```

### Schedule in `routes/console.php`:
```php
use Illuminate\Support\Facades\Schedule;

// Run cleanup every night at 2 AM
Schedule::command('demo:cleanup')->dailyAt('02:00');

// Full reset every Sunday at 3 AM (optional)
Schedule::command('demo:reset')->weeklyOn(0, '03:00');
```

### `is_seeded` Column Convention:
```php
// Add to ALL demo-seeded tables in migrations:
$table->boolean('is_seeded')->default(0);

// DemoSchoolSeeder sets is_seeded=1 on all baseline rows:
Student::create([...baseline data..., 'is_seeded' => 1]);

// User-created rows never set is_seeded → defaults to 0 → gets cleaned up
```

### Demo Banner (shown inside demo app):
```blade
{{-- In layouts/partials/topbar.blade.php --}}
@if(TenantService::isDemo())
<div class="demo-banner">
    <i class="fas fa-flask"></i>
    <strong>Demo Mode</strong> — You're exploring a live demo.
    New entries auto-delete after 4 days.
    <a href="{{ route('landing') }}#pricing">Upgrade to keep your data →</a>
</div>
@endif
```
```css
.demo-banner {
    background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(139,92,246,0.15));
    border-bottom: 1px solid rgba(99,102,241,0.3);
    padding: 8px 20px;
    font-size: 13px;
    color: var(--text-2);
    text-align: center;
}
.demo-banner a { color: var(--primary-lt); text-decoration: none; }
.demo-banner a:hover { text-decoration: underline; }
```

### TenantService — Demo Detection:
```php
// In app/Services/TenantService.php
public static function isDemo(): bool {
    $tenant = app(CurrentTenant::class)->get();
    return $tenant && $tenant->subdomain === 'demo';
}
```

### Demo Tenant in Central DB:
```sql
INSERT INTO tenants (name, subdomain, db_name, plan_id, status, is_demo) 
VALUES ('Al-Noor Grammar School (Demo)', 'demo', 'educore_demo', 
        (SELECT id FROM plans WHERE slug='elite'), 'active', 1);
```

### SuperAdmin — Demo Controls:
```
SuperAdmin → Schools → Demo School
  [Reset Demo Data]  ← runs demo:reset command via artisan call
  [View Demo]        ← opens demo URL
  Last Reset: 2 days ago
  User-created entries: 47
```

### Phase 6 Progress Tracker additions:
- [ ] `DemoSchoolSeeder` with 80 students + full Pakistani data
- [ ] `is_seeded` column on all relevant tables
- [ ] `demo:seed`, `demo:reset`, `demo:cleanup` artisan commands
- [ ] Scheduled task (nightly cleanup at 2 AM)
- [ ] Demo banner in topbar
- [ ] `TenantService::isDemo()` helper
- [ ] `is_demo` flag in tenants table
- [ ] SuperAdmin demo reset button
- [ ] Demo credentials on landing page

---

## 🌍 INTERNATIONALIZATION (i18n) — URDU + ENGLISH

> ⚠️ Claude Code: NEVER hardcode any user-facing string.
> ALWAYS use __('file.key') — from day one, every file, every line.
> This rule has NO exceptions.

### Why Shuru Se:
- Baad mein 200+ files refactor = 3-4 sessions waste
- Laravel built-in — zero extra package needed
- Per-tenant language (Elite school = English, Govt school = Urdu)
- Pakistan ka BIGGEST market differentiator — no competitor does this

---

### Language Files Structure:

```
lang/
├── en/                          ← English (default)
│   ├── common.php               ← shared across all portals
│   ├── auth.php                 ← login, logout, password
│   ├── dashboard.php            ← dashboard strings
│   ├── students.php             ← students module
│   ├── teachers.php
│   ├── classes.php
│   ├── attendance.php
│   ├── fees.php
│   ├── diary.php
│   ├── library.php
│   ├── timetable.php
│   ├── leaves.php
│   ├── syllabus.php
│   ├── results.php
│   ├── admissions.php
│   ├── payroll.php
│   ├── parent.php               ← parent portal strings
│   ├── notifications.php
│   └── settings.php
└── ur/                          ← Urdu (mirror of en/)
    ├── common.php
    ├── auth.php
    ├── dashboard.php
    └── ... (same files)
```

---

### Sample Language Files:

```php
// lang/en/common.php
return [
    'dashboard'    => 'Dashboard',
    'save'         => 'Save',
    'cancel'       => 'Cancel',
    'delete'       => 'Delete',
    'edit'         => 'Edit',
    'add'          => 'Add',
    'search'       => 'Search',
    'actions'      => 'Actions',
    'status'       => 'Status',
    'active'       => 'Active',
    'inactive'     => 'Inactive',
    'yes'          => 'Yes',
    'no'           => 'No',
    'loading'      => 'Loading...',
    'no_data'      => 'No records found',
    'confirm_delete' => 'Are you sure you want to delete this?',
    'saved'        => 'Saved successfully!',
    'deleted'      => 'Deleted successfully!',
    'error'        => 'Something went wrong. Please try again.',
    'welcome'      => 'Welcome',
    'logout'       => 'Logout',
    'settings'     => 'Settings',
    'profile'      => 'Profile',
    'back'         => 'Back',
    'print'        => 'Print',
    'export_csv'   => 'Export CSV',
    'filter'       => 'Filter',
    'reset'        => 'Reset',
    'total'        => 'Total',
    'date'         => 'Date',
    'name'         => 'Name',
    'email'        => 'Email',
    'phone'        => 'Phone',
    'address'      => 'Address',
    'created_at'   => 'Created',
];

// lang/ur/common.php
return [
    'dashboard'    => 'ڈیش بورڈ',
    'save'         => 'محفوظ کریں',
    'cancel'       => 'منسوخ',
    'delete'       => 'حذف کریں',
    'edit'         => 'ترمیم',
    'add'          => 'شامل کریں',
    'search'       => 'تلاش کریں',
    'actions'      => 'اعمال',
    'status'       => 'حیثیت',
    'active'       => 'فعال',
    'inactive'     => 'غیر فعال',
    'yes'          => 'ہاں',
    'no'           => 'نہیں',
    'loading'      => 'لوڈ ہو رہا ہے...',
    'no_data'      => 'کوئی ریکارڈ نہیں ملا',
    'confirm_delete' => 'کیا آپ واقعی یہ حذف کرنا چاہتے ہیں؟',
    'saved'        => 'کامیابی سے محفوظ ہو گیا!',
    'deleted'      => 'کامیابی سے حذف ہو گیا!',
    'error'        => 'کچھ غلط ہو گیا۔ دوبارہ کوشش کریں۔',
    'welcome'      => 'خوش آمدید',
    'logout'       => 'لاگ آؤٹ',
    'settings'     => 'ترتیبات',
    'profile'      => 'پروفائل',
    'back'         => 'واپس',
    'print'        => 'پرنٹ کریں',
    'export_csv'   => 'CSV برآمد کریں',
    'filter'       => 'فلٹر',
    'reset'        => 'ری سیٹ',
    'total'        => 'کل',
    'date'         => 'تاریخ',
    'name'         => 'نام',
    'email'        => 'ای میل',
    'phone'        => 'فون',
    'address'      => 'پتہ',
    'created_at'   => 'تاریخ اندراج',
];

// lang/en/students.php
return [
    'title'           => 'Students',
    'add_new'         => 'Add New Student',
    'edit'            => 'Edit Student',
    'total'           => 'Total Students',
    'roll_no'         => 'Roll No',
    'guardian'        => 'Guardian Name',
    'guardian_phone'  => 'Guardian Phone',
    'date_of_birth'   => 'Date of Birth',
    'gender'          => 'Gender',
    'male'            => 'Male',
    'female'          => 'Female',
    'class'           => 'Class',
    'section'         => 'Section',
    'admission_date'  => 'Admission Date',
    'added_success'   => 'Student added successfully!',
    'updated_success' => 'Student updated successfully!',
    'deleted_success' => 'Student deleted successfully!',
    'not_found'       => 'Student not found.',
];

// lang/ur/students.php
return [
    'title'           => 'طلباء',
    'add_new'         => 'نیا طالب علم شامل کریں',
    'edit'            => 'طالب علم میں ترمیم',
    'total'           => 'کل طلباء',
    'roll_no'         => 'رول نمبر',
    'guardian'        => 'سرپرست کا نام',
    'guardian_phone'  => 'سرپرست کا فون',
    'date_of_birth'   => 'تاریخ پیدائش',
    'gender'          => 'جنس',
    'male'            => 'مرد',
    'female'          => 'عورت',
    'class'           => 'جماعت',
    'section'         => 'سیکشن',
    'admission_date'  => 'داخلہ کی تاریخ',
    'added_success'   => 'طالب علم کامیابی سے شامل ہو گیا!',
    'updated_success' => 'طالب علم کامیابی سے اپڈیٹ ہو گیا!',
    'deleted_success' => 'طالب علم کامیابی سے حذف ہو گیا!',
    'not_found'       => 'طالب علم نہیں ملا۔',
];
```

---

### Usage in Blade — MANDATORY Pattern:

```blade
{{-- ✅ CORRECT — always --}}
{{ __('students.title') }}
{{ __('common.save') }}
{{ __('fees.pending') }}

{{-- ❌ WRONG — never hardcode --}}
Students
Save
Pending Fees

{{-- With variables: --}}
{{ __('students.count', ['count' => $total]) }}
// lang/en: 'count' => ':count Students'
// lang/ur: 'count' => ':count طلباء'

{{-- Conditional plural: --}}
{{ trans_choice('students.found', $count) }}
// lang/en: 'found' => '{0} No students|{1} :count student|[2,*] :count students'
```

---

### Usage in Controllers:

```php
// Flash messages
return redirect()->back()->with('success', __('students.added_success'));
return redirect()->back()->with('error',   __('common.error'));

// Validation messages
$request->validate([
    'name' => 'required|string|max:100',
], [
    'name.required' => __('validation.name_required'),
]);

// JSON responses (Ajax)
return response()->json([
    'message' => __('common.saved')
]);
```

---

### Language Detection & Switching:

```php
// app/Services/LocaleService.php
class LocaleService
{
    public static function set(): void
    {
        // Priority order:
        // 1. Per-tenant setting (Elite: en, Govt school: ur)
        // 2. User preference (saved in session)
        // 3. App default (en)

        $locale = 'en'; // default

        // Tenant setting
        if (TenantService::isLoaded()) {
            $tenantLocale = SettingService::get('app_locale', 'en');
            $locale = $tenantLocale;
        }

        // User override (from session)
        if (session('locale')) {
            $locale = session('locale');
        }

        app()->setLocale($locale);
        session(['locale' => $locale]);
    }
}

// In AppServiceProvider::boot():
LocaleService::set();
```

### Language Toggle in Topbar:

```blade
{{-- In layouts/partials/topbar.blade.php --}}
<div class="lang-toggle">
    <button onclick="switchLang('en')"
            class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}">
        EN
    </button>
    <button onclick="switchLang('ur')"
            class="lang-btn {{ app()->getLocale() === 'ur' ? 'active' : '' }}">
        اردو
    </button>
</div>

<script>
function switchLang(locale) {
    axios.post('{{ route("locale.switch") }}', { locale })
         .then(() => location.reload());
}
</script>
```

```php
// routes/web.php
Route::post('/locale/switch', function(Request $request) {
    $locale = in_array($request->locale, ['en', 'ur']) ? $request->locale : 'en';
    session(['locale' => $locale]);
    
    // If admin — save as tenant setting too
    if (auth()->check() && auth()->user()->role === 'admin') {
        SettingService::set('app_locale', $locale);
    }
    
    return response()->json(['switched' => true]);
})->name('locale.switch')->middleware('auth');
```

---

### RTL Support — Full CSS System:

```css
/* assets/css/style.css — add RTL section at bottom */

/* ============================================
   RTL SUPPORT — Urdu / Arabic
   ============================================ */

/* Urdu font */
@import url('https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;500;600;700&display=swap');

body.rtl {
    direction: rtl;
    text-align: right;
    font-family: 'Noto Nastaliq Urdu', 'Inter', serif;
    font-size: 16px;      /* Nastaliq needs slightly bigger size */
    line-height: 2;       /* Nastaliq needs more line height */
}

/* Layout flip */
body.rtl .app-wrapper       { flex-direction: row-reverse; }
body.rtl .sidebar           { right: 0; left: auto; border-right: none; border-left: 1px solid var(--border); }
body.rtl .main-content      { margin-right: 250px; margin-left: 0; }
body.rtl .topbar            { padding-right: 274px; padding-left: 20px; }

/* Sidebar nav items */
body.rtl .nav-item          { flex-direction: row-reverse; }
body.rtl .nav-item .nav-icon{ margin-right: 0; margin-left: 12px; }
body.rtl .sidebar-active-bar{ right: 0; left: auto; border-radius: 4px 0 0 4px; }

/* Cards + content */
body.rtl .card-header       { flex-direction: row-reverse; }
body.rtl .stat-card         { flex-direction: row-reverse; }
body.rtl .stat-icon         { margin-right: 0; margin-left: 16px; }

/* Tables */
body.rtl table              { direction: rtl; }
body.rtl .table th,
body.rtl .table td          { text-align: right; }

/* Forms */
body.rtl .form-label        { text-align: right; }
body.rtl .input-with-icon .icon { right: auto; left: 12px; }
body.rtl .input-with-icon input { padding-right: 16px; padding-left: 40px; }

/* Buttons */
body.rtl .btn-primary-custom i,
body.rtl .btn-outline-custom i { margin-right: 0; margin-left: 8px; }

/* Breadcrumb */
body.rtl .breadcrumb-item + .breadcrumb-item::before { content: "\\"; }

/* DataTables */
body.rtl .dataTables_filter { float: left; }
body.rtl .dataTables_length { float: right; }
body.rtl .dataTables_paginate { float: left; }

/* Mobile bottom nav */
body.rtl .mobile-bottom-nav { direction: rtl; }

/* Badges */
body.rtl .badge-status      { direction: rtl; }

/* Charts — labels stay LTR even in RTL mode */
body.rtl canvas             { direction: ltr; }
```

### RTL Body Class — Applied in Layout:

```blade
{{-- layouts/app.blade.php --}}
<body class="{{ app()->getLocale() === 'ur' ? 'rtl' : 'ltr' }}">
```

---

### Per-Tenant Language (SaaS):

```php
// In TenantMiddleware — load tenant locale
public function handle(Request $request, Closure $next)
{
    // ... tenant loading ...

    // Set locale from tenant settings
    $locale = SettingService::get('app_locale', 'en');
    app()->setLocale($locale);
    
    return $next($request);
}
```

```
SuperAdmin → Schools → LGS
    Language: [English ▼]   ← English for private schools

SuperAdmin → Schools → Govt High School Jaranwala
    Language: [اردو ▼]      ← Urdu for government schools
```

---

### Urdu Report Cards + Challans:

```blade
{{-- views/admin/report_card.blade.php --}}
<div class="report-card {{ app()->getLocale() === 'ur' ? 'rtl' : '' }}">
    <h2>{{ __('results.report_card') }}</h2>
    {{-- "رپورٹ کارڈ" in Urdu --}}

    <table>
        <thead>
            <tr>
                <th>{{ __('subjects.title') }}</th>   {{-- مضامین --}}
                <th>{{ __('results.marks') }}</th>     {{-- نمبر --}}
                <th>{{ __('results.grade') }}</th>     {{-- گریڈ --}}
            </tr>
        </thead>
    </table>
</div>
```

---

### Claude Code — i18n Strict Rules:

> ⚠️ These rules apply to EVERY file Claude Code writes. No exceptions.

1. **Every user-visible string** → `__('file.key')` — never raw text
2. **Every new module** → create both `lang/en/module.php` AND `lang/ur/module.php` simultaneously
3. **Every success/error message** → `__('common.saved')` etc.
4. **Every page title** → `__('module.title')`
5. **Every button label** → `__('common.save')` etc.
6. **Every table header** → `__('common.name')` etc.
7. **Every validation message** → use lang file keys
8. **Every Ajax JSON response** → `__()` wrapped messages
9. **RTL CSS** → already in style.css, just add `body.rtl` class
10. **New CSS classes** → always test both LTR and RTL layout

### Naming Convention:
```
Format: __('module.key')

✅ __('students.add_new')
✅ __('common.save')
✅ __('fees.overdue_amount')

❌ __('Add New Student')      ← never use sentence as key
❌ __('save')                 ← too vague, missing module prefix
❌ 'Add New Student'          ← hardcoded — FORBIDDEN
```

---

### Phase 1 — i18n Foundation Checklist:
- [ ] `lang/en/common.php` + `lang/ur/common.php`
- [ ] `lang/en/auth.php` + `lang/ur/auth.php`
- [ ] `LocaleService::set()` in AppServiceProvider
- [ ] RTL CSS block added to `style.css`
- [ ] Noto Nastaliq Urdu font loaded in head partial
- [ ] `body.rtl` / `body.ltr` class on `<body>` tag
- [ ] Language toggle buttons in topbar
- [ ] `/locale/switch` route
- [ ] `app_locale` setting in settings table
- [ ] Per-tenant locale loading in TenantMiddleware

---



| Original File | Laravel Equivalent |
|--------------|-------------------|
| `includes/functions.php` | `app/Helpers/AppHelpers.php` + middleware |
| `includes/tenant.php` | `app/Services/TenantService.php` + middleware |
| `includes/settings_helper.php` | `app/Services/SettingService.php` |
| `includes/fee_helpers.php` | `app/Services/FeeService.php` |
| `includes/diary_helpers.php` | `app/Services/DiaryService.php` |
| `includes/whatsapp_api.php` | `app/Services/WhatsAppService.php` |
| `includes/sidebar.php` | `resources/views/layouts/partials/sidebar.blade.php` |
| `includes/header.php` | `resources/views/layouts/partials/head.blade.php` |
| `includes/footer.php` | `resources/views/layouts/partials/footer.blade.php` |
| `assets/css/style.css` | `public/assets/css/style.css` (copy directly) |
| `assets/js/main.js` | `public/assets/js/main.js` (copy directly) |
| `config/db.php` | `.env` + `config/database.php` |
| `admin/dashboard.php` | `Admin\DashboardController@index` + `admin/dashboard.blade.php` |
| `superadmin/layout.php` | `resources/views/layouts/superadmin.blade.php` |
| `superadmin/style.css` | `public/assets/css/superadmin.css` (copy directly) |
| `fee_portal.php` | `Public\FeePortalController` + `public/fee-portal.blade.php` |
| `acknowledge.php` | `Public\AcknowledgeController` + `public/acknowledge.blade.php` |

---

*EduCore Laravel — Built with Claude Code*
*Wahsol Technologies — Rizwan*
*Original PHP: https://github.com/rizwan-bytes/educore-cms*
