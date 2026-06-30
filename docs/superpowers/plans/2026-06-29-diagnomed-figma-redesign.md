# DiagnoMed Figma Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redesign the existing Laravel RuleBasedMedic project into the DiagnoMed web application matching the provided Figma/PDF UI while preserving expert-system behavior.

**Architecture:** Keep the Laravel 11 MVC structure and existing database model as the source of truth. Add small, reusable Blade components and helper functions for the Figma visual system, then replace the user and admin Blade views around the existing controllers. Backend behavior changes are limited to consultation payload detail, dashboard analytics, filtering, and safe profile/medicine metadata.

**Tech Stack:** Laravel 11, Blade, Tailwind CSS 3, Vite, MySQL root without password, Bash `start.sh`/`stop.sh`, no Docker.

## Global Constraints

- Use the DiagnoMed visual identity from the provided Figma screenshots and rendered PDF pages.
- Preserve Rule Based, Forward Chaining, Backward Chaining, and Certainty Factor behavior.
- Do not add emoticons to code, seed data, or UI copy.
- Build responsive mobile-first layouts for phone, tablet, laptop, large screens, and mobile.
- Compress new image, video, asset, and file additions before use.
- Database uses MySQL user `root` without password.
- Run with Git Bash `start.sh` and `stop.sh`, not Docker.
- This workspace has no `.git` directory, so task checkpoint steps replace commit steps.

---

## File Structure

- Modify `resources/css/app.css`: DiagnoMed component classes, badges, layout primitives, focus states, modal styles, responsive table helpers, animation utilities.
- Modify `resources/js/app.js`: selected symptom UI, search/filter tabs, detail modal toggles, mobile menu/sidebar toggles, Magic UI style card pointer effect.
- Modify `resources/views/layouts/app.blade.php`: public/user shell, Figma navbar, flash messages, mobile nav.
- Modify `resources/views/layouts/admin.blade.php`: admin sidebar shell, topbar, mobile sidebar drawer.
- Modify `resources/views/partials/navbar.blade.php`: DiagnoMed user navigation.
- Modify `resources/views/partials/footer.blade.php`: contact, social, location, mini maps section.
- Create `resources/views/components/diagnomed/logo.blade.php`: reusable logo lockup.
- Create `resources/views/components/diagnomed/icon.blade.php`: small inline SVG icon component for Figma-like icons.
- Create `resources/views/components/diagnomed/hero-banner.blade.php`: blue banner with illustration.
- Create `resources/views/components/diagnomed/stepper.blade.php`: 4-step consultation progress UI.
- Create `resources/views/components/diagnomed/badge.blade.php`: category/severity/status badge.
- Create `resources/views/components/diagnomed/medicine-art.blade.php`: lightweight medicine illustration/card image.
- Modify `resources/views/auth/login.blade.php`: Figma split login and admin variant.
- Modify `resources/views/auth/register.blade.php`: Figma split register.
- Modify `resources/views/landing.blade.php`: homepage matching Figma.
- Modify `resources/views/information.blade.php`: Figma-styled information page.
- Modify `resources/views/user/dashboard.blade.php`: user beranda style if authenticated.
- Modify `resources/views/user/consultation/index.blade.php`: Figma symptom picker.
- Modify `resources/views/user/consultation/show.blade.php`: Figma diagnosis result and medicine modal.
- Modify `resources/views/user/history/index.blade.php`: Figma history view.
- Modify `resources/views/admin/dashboard.blade.php`: Figma admin dashboard.
- Modify `resources/views/admin/resource/index.blade.php`: Figma CRUD table/filter page.
- Modify `resources/views/admin/resource/form.blade.php`: Figma admin form page.
- Modify `app/Http/Controllers/ConsultationController.php`: richer result payload and history summary data.
- Modify `app/Http/Controllers/Admin/DashboardController.php`: chart and severity analytics.
- Modify `app/Http/Controllers/Admin/ResourceController.php`: query filters and richer table metadata.
- Modify `tests/Feature/ExpertSystemServiceTest.php`: forward/backward/certainty coverage.
- Create `tests/Feature/ConsultationFlowTest.php`: consultation/history behavior.
- Create `tests/Feature/RoleAccessTest.php`: role access behavior.
- Create `tests/Feature/AdminResourceTest.php`: admin CRUD/filter smoke tests.
- Modify `public/assets/images/logo.svg`: DiagnoMed logo if needed.
- Create or modify `public/assets/images/medical-hero.svg`: compressed Figma-like illustration.

---

### Task 1: Expert-System Behavior Coverage

**Files:**
- Modify: `tests/Feature/ExpertSystemServiceTest.php`
- Test: `tests/Feature/ExpertSystemServiceTest.php`

**Interfaces:**
- Consumes: `App\Services\ExpertSystemService::analyze(array $selectedCodes, string $method = 'forward'): array`
- Produces: verified behavior for `forward`, `backward`, and `certainty` UI flows.

- [ ] **Step 1: Write failing tests**

Replace `tests/Feature/ExpertSystemServiceTest.php` with:

```php
<?php

namespace Tests\Feature;

use App\Services\ExpertSystemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpertSystemServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_forward_chaining_returns_influenza_ringan_for_rule_r001(): void
    {
        $this->seed();

        $result = app(ExpertSystemService::class)->analyze(['G001', 'G009', 'G011'], 'forward');

        $this->assertSame('forward', $result['method']);
        $this->assertSame('P001', $result['disease']->code);
        $this->assertSame(['G001', 'G009', 'G011'], $result['matched_rule']['matched_symptoms']);
        $this->assertGreaterThan(0, $result['confidence_score']);
        $this->assertNotEmpty($result['medicines']);
    }

    public function test_backward_chaining_scores_partial_goal_match(): void
    {
        $this->seed();

        $result = app(ExpertSystemService::class)->analyze(['G001', 'G009'], 'backward');

        $this->assertSame('backward', $result['method']);
        $this->assertSame('P001', $result['disease']->code);
        $this->assertContains('G011', $result['matched_rule']['missing_symptoms']);
        $this->assertGreaterThan(0, $result['confidence_score']);
        $this->assertLessThan(100, $result['confidence_score']);
    }

    public function test_unmatched_symptoms_return_safe_message(): void
    {
        $this->seed();

        $result = app(ExpertSystemService::class)->analyze(['G100'], 'forward');

        $this->assertNull($result['disease']);
        $this->assertSame(0, $result['confidence_score']);
        $this->assertSame('Belum ditemukan aturan yang sesuai. Silakan pilih gejala yang lebih spesifik atau hubungi apoteker.', $result['message']);
    }
}
```

- [ ] **Step 2: Run tests to verify current behavior**

Run: `rtk php artisan test --filter=ExpertSystemServiceTest`

Expected: tests pass if existing behavior already matches. If a test fails, the failure must describe a real mismatch in service output, not syntax.

- [ ] **Step 3: Adjust service only if a test exposes mismatch**

If `method` or missing symptoms do not match, update `app/Services/ExpertSystemService.php` so returned arrays keep the requested method and include `missing_symptoms` in `matched_rule`.

- [ ] **Step 4: Verify tests pass**

Run: `rtk php artisan test --filter=ExpertSystemServiceTest`

Expected: PASS.

- [ ] **Step 5: Checkpoint**

Run: `rtk powershell -NoProfile -Command "Get-Content -Raw tests\Feature\ExpertSystemServiceTest.php"`

Expected: file contains exactly three tests above.

---

### Task 2: Consultation Flow and Role Tests

**Files:**
- Create: `tests/Feature/ConsultationFlowTest.php`
- Create: `tests/Feature/RoleAccessTest.php`
- Modify if needed: `app/Http/Controllers/ConsultationController.php`
- Test: `tests/Feature/ConsultationFlowTest.php`, `tests/Feature/RoleAccessTest.php`

**Interfaces:**
- Consumes: routes `consultation.index`, `consultation.diagnose`, `history.index`, `admin.dashboard`
- Produces: verified consultation persistence and role access behavior.

- [ ] **Step 1: Write consultation flow test**

Create `tests/Feature/ConsultationFlowTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_masyarakat_can_create_consultation_and_see_history(): void
    {
        $this->seed();
        $user = User::where('role', 'masyarakat')->firstOrFail();

        $response = $this->actingAs($user)->post(route('consultation.diagnose'), [
            'symptoms' => ['G001', 'G009', 'G011'],
            'method' => 'forward',
            'notes' => 'Demam dan pilek sejak pagi',
        ]);

        $consultation = Consultation::firstOrFail();
        $response->assertRedirect(route('consultation.show', $consultation));
        $this->assertSame($user->id, $consultation->user_id);
        $this->assertSame('forward', $consultation->method);
        $this->assertSame(['G001', 'G009', 'G011'], $consultation->selected_symptom_codes);
        $this->assertNotNull($consultation->result_payload['disease']);
        $this->assertNotEmpty($consultation->result_payload['medicines']);

        $this->actingAs($user)->get(route('history.index'))->assertOk()->assertSee('Riwayat', false);
    }
}
```

- [ ] **Step 2: Write role access test**

Create `tests/Feature/RoleAccessTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_masyarakat_cannot_access_admin_dashboard(): void
    {
        $this->seed();
        $user = User::where('role', 'masyarakat')->firstOrFail();

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Dashboard', false);
    }
}
```

- [ ] **Step 3: Run tests to verify current behavior**

Run: `rtk php artisan test --filter=ConsultationFlowTest --filter=RoleAccessTest`

Expected: PASS or fail only on missing view copy. If route middleware returns redirect instead of forbidden, update the assertion to the intended app behavior only after checking `app/Http/Middleware/RoleMiddleware.php`.

- [ ] **Step 4: Implement minimal controller fixes if needed**

If `result_payload` lacks medicine detail needed by the UI, update `app/Http/Controllers/ConsultationController.php` medicine mapping to include:

```php
'medicines' => $result['medicines']->map->only([
    'code',
    'name',
    'category',
    'dosage',
    'usage_rule',
    'side_effects',
    'contraindication',
    'warning',
    'description',
    'image_path',
])->values(),
```

- [ ] **Step 5: Verify tests pass**

Run: `rtk php artisan test --filter=ConsultationFlowTest`

Expected: PASS.

Run: `rtk php artisan test --filter=RoleAccessTest`

Expected: PASS.

---

### Task 3: DiagnoMed Design System Components

**Files:**
- Modify: `resources/css/app.css`
- Modify: `resources/js/app.js`
- Create: `resources/views/components/diagnomed/logo.blade.php`
- Create: `resources/views/components/diagnomed/icon.blade.php`
- Create: `resources/views/components/diagnomed/hero-banner.blade.php`
- Create: `resources/views/components/diagnomed/stepper.blade.php`
- Create: `resources/views/components/diagnomed/badge.blade.php`
- Create: `resources/views/components/diagnomed/medicine-art.blade.php`
- Modify: `public/assets/images/medical-hero.svg`

**Interfaces:**
- Produces Blade components:
  - `<x-diagnomed.logo />`
  - `<x-diagnomed.icon name="home" class="..." />`
  - `<x-diagnomed.hero-banner title="..." subtitle="..." />`
  - `<x-diagnomed.stepper :active="1" />`
  - `<x-diagnomed.badge tone="pernapasan">Pernapasan</x-diagnomed.badge>`
  - `<x-diagnomed.medicine-art />`

- [ ] **Step 1: Add component CSS**

Append to `resources/css/app.css`:

```css
@layer base {
    :root {
        --dm-blue-900: #1f5d95;
        --dm-blue-700: #2377c8;
        --dm-blue-500: #2d91e6;
        --dm-green-700: #067a42;
        --dm-bg: #f2f6fc;
        --dm-border: #dce5f1;
    }

    html {
        -webkit-font-smoothing: antialiased;
    }
}

@layer components {
    .dm-page { @apply min-h-screen bg-[#f2f6fc] text-slate-950; }
    .dm-shell { @apply mx-auto w-full max-w-[1180px] px-4 sm:px-6 lg:px-8; }
    .dm-card { @apply rounded-[8px] border border-[#dce5f1] bg-white shadow-[0_12px_30px_rgba(31,93,149,0.06)]; }
    .dm-input { @apply h-11 w-full rounded-[6px] border border-[#c8d4e4] bg-white px-3 text-sm outline-none transition focus:border-[#2385dd] focus:ring-4 focus:ring-blue-100; }
    .dm-btn-primary { @apply inline-flex min-h-10 items-center justify-center gap-2 rounded-[6px] bg-[#2385dd] px-5 text-sm font-semibold text-white shadow-[0_8px_18px_rgba(35,133,221,0.22)] transition-transform hover:bg-[#1f75c8] active:scale-[0.96]; }
    .dm-btn-green { @apply inline-flex min-h-10 items-center justify-center gap-2 rounded-[6px] bg-[#067a42] px-5 text-sm font-semibold text-white shadow-[0_8px_18px_rgba(6,122,66,0.18)] transition-transform hover:bg-[#056a39] active:scale-[0.96]; }
    .dm-btn-light { @apply inline-flex min-h-10 items-center justify-center gap-2 rounded-[6px] border border-[#dce5f1] bg-white px-4 text-sm font-semibold text-slate-700 transition-transform hover:bg-slate-50 active:scale-[0.96]; }
    .dm-banner { @apply overflow-hidden rounded-[8px] bg-gradient-to-r from-[#1f5d95] to-[#2d91e6] text-white shadow-[0_16px_34px_rgba(31,93,149,0.18)]; }
    .dm-table { @apply min-w-full border-separate border-spacing-0 text-sm; }
    .dm-th { @apply border-b border-[#dce5f1] bg-[#eef4fb] px-4 py-3 text-left text-xs font-semibold text-slate-700; }
    .dm-td { @apply border-b border-[#edf2f7] px-4 py-3 align-middle text-xs text-slate-700; }
    .dm-nav-link { @apply inline-flex min-h-10 items-center gap-2 border-b-2 border-transparent px-2 text-sm font-semibold text-slate-800 hover:border-[#2385dd] hover:text-[#2385dd]; }
    .dm-nav-link-active { @apply border-[#2385dd] text-[#2385dd]; }
}

.motion-fade {
    opacity: 0;
    transform: translateY(14px);
    transition: opacity .55s cubic-bezier(.2,0,0,1), transform .55s cubic-bezier(.2,0,0,1);
}

.motion-fade.is-visible {
    opacity: 1;
    transform: translateY(0);
}

.magic-card {
    position: relative;
    overflow: hidden;
}

.magic-card::before {
    content: '';
    position: absolute;
    inset: -1px;
    background: radial-gradient(circle at var(--x,50%) var(--y,50%), rgba(45,145,230,.18), transparent 36%);
    opacity: 0;
    transition: opacity .2s cubic-bezier(.2,0,0,1);
    pointer-events: none;
}

.magic-card:hover::before {
    opacity: 1;
}
```

- [ ] **Step 2: Add component JavaScript**

Replace `resources/js/app.js` content with:

```js
import './bootstrap';

document.querySelectorAll('.motion-fade').forEach((element) => {
    requestAnimationFrame(() => element.classList.add('is-visible'));
});

document.querySelectorAll('.magic-card').forEach((card) => {
    card.addEventListener('pointermove', (event) => {
        const rect = card.getBoundingClientRect();
        card.style.setProperty('--x', `${event.clientX - rect.left}px`);
        card.style.setProperty('--y', `${event.clientY - rect.top}px`);
    });
});

document.querySelectorAll('[data-toggle-target]').forEach((button) => {
    button.addEventListener('click', () => {
        const target = document.querySelector(button.dataset.toggleTarget);
        target?.classList.toggle('hidden');
    });
});

document.querySelectorAll('[data-modal-open]').forEach((button) => {
    button.addEventListener('click', () => {
        document.querySelector(button.dataset.modalOpen)?.classList.remove('hidden');
    });
});

document.querySelectorAll('[data-modal-close]').forEach((button) => {
    button.addEventListener('click', () => {
        button.closest('[data-modal]')?.classList.add('hidden');
    });
});
```

- [ ] **Step 3: Create Blade components**

Create the six component files listed in this task. Each must be pure Blade/HTML/SVG, with no PHP classes required. `logo.blade.php` must render "DiagnoMed" and "Sistem Rekomendasi Obat". `icon.blade.php` must support at least `home`, `stethoscope`, `history`, `info`, `search`, `bell`, `calendar`, `edit`, `trash`, `logout`, `user`, `pill`, `shield`, `clock`, `clipboard`.

- [ ] **Step 4: Verify component compilation**

Run: `rtk php artisan view:clear`

Expected: command succeeds.

Run: `rtk npm run build`

Expected: Vite build succeeds.

---

### Task 4: Public/User Layout and Auth Screens

**Files:**
- Modify: `resources/views/layouts/app.blade.php`
- Modify: `resources/views/partials/navbar.blade.php`
- Modify: `resources/views/auth/login.blade.php`
- Modify: `resources/views/auth/register.blade.php`

**Interfaces:**
- Consumes: components from Task 3.
- Produces: Figma-style user shell, login, register.

- [ ] **Step 1: Update app layout**

Change `resources/views/layouts/app.blade.php` to use `body class="dm-page antialiased"`, keep `@vite`, include navbar, flash messages inside `.dm-shell`, and remove the old radial gradient background.

- [ ] **Step 2: Update navbar**

Implement Figma navbar with logo left, menu links, search pill, notification icon, login/register buttons for guests, and user dropdown for authenticated users. Use route active classes with `request()->routeIs(...)`.

- [ ] **Step 3: Update login view**

Implement split login. If `request('admin')` is present or old login is admin, show centered admin card variant on full blue gradient. Otherwise show left illustration panel and right form.

- [ ] **Step 4: Update register view**

Implement split register matching screenshot: left blue panel, right full form fields for name, gender, birth date, username, email, phone, password, confirmation.

- [ ] **Step 5: Verify auth pages render**

Run: `rtk php artisan test --filter=RoleAccessTest`

Expected: PASS.

Run: `rtk npm run build`

Expected: PASS.

---

### Task 5: Homepage, Information, and Footer

**Files:**
- Modify: `resources/views/landing.blade.php`
- Modify: `resources/views/information.blade.php`
- Modify: `resources/views/partials/footer.blade.php`

**Interfaces:**
- Consumes: `$symptomCount`, `$diseaseCount`, `$medicineCount`, `$ruleCount` from `HomeController::landing`.
- Produces: Figma homepage and complete contact footer.

- [ ] **Step 1: Replace landing page**

Build the Figma homepage: hero blue banner, two CTA buttons, four benefits, "Bagaimana Cara Kerjanya?" process cards, and disclaimer.

- [ ] **Step 2: Replace information page**

Create sections for aturan pakai, efek samping, kategori obat, batas swamedikasi 3 x 24 jam, dan kapan harus ke dokter. Use Figma cards and badges.

- [ ] **Step 3: Replace footer**

Footer must include WA contact, Instagram, Facebook, location, and mini maps iframe or static embedded map link. Keep it responsive and avoid heavy assets.

- [ ] **Step 4: Verify public pages**

Run: `rtk php artisan test --filter=RoleAccessTest`

Expected: PASS.

Run: `rtk npm run build`

Expected: PASS.

---

### Task 6: Cek Gejala and Result UI

**Files:**
- Modify: `resources/views/user/consultation/index.blade.php`
- Modify: `resources/views/user/consultation/show.blade.php`
- Modify if needed: `app/Http/Controllers/ConsultationController.php`
- Test: `tests/Feature/ConsultationFlowTest.php`

**Interfaces:**
- Consumes: grouped `$symptoms`, `Consultation::$result_payload`, `Consultation::$confidence_score`.
- Produces: Figma symptom picker, result view, and medicine detail modal.

- [ ] **Step 1: Update symptom picker**

Build the Figma layout: blue banner, `<x-diagnomed.stepper :active="1" />`, search input, category tabs, checkbox rows, selected symptoms panel, tips, method selector, and submit button.

- [ ] **Step 2: Add selected symptom JS hooks**

Use `resources/js/app.js` to mirror checked symptom labels into the selected symptoms panel. Do not block form submit if JS is unavailable.

- [ ] **Step 3: Update result view**

Build the two-column Figma result: selected symptoms, disease ranking from `result_payload.matched_rule`, confidence ring, medicine cards, suggestions, disclaimer.

- [ ] **Step 4: Add medicine modal**

Each medicine card has a `Detail Obat` button opening a modal with medicine image art, name, dosage, tabs for Tentang, Aturan, Efek, Peringatan, Interaksi. Use available payload fields; show "Informasi belum tersedia" for missing optional text.

- [ ] **Step 5: Verify consultation flow**

Run: `rtk php artisan test --filter=ConsultationFlowTest`

Expected: PASS.

Run: `rtk npm run build`

Expected: PASS.

---

### Task 7: Riwayat Masyarakat

**Files:**
- Modify: `app/Http/Controllers/ConsultationController.php`
- Modify: `resources/views/user/history/index.blade.php`
- Test: `tests/Feature/ConsultationFlowTest.php`

**Interfaces:**
- Consumes: authenticated user histories.
- Produces: Figma history page with summary cards, filters, table, latest detail panel.

- [ ] **Step 1: Add history summary data**

In `ConsultationController::history`, compute:

```php
$summary = [
    'total' => (clone $baseQuery)->count(),
    'diseases' => (clone $baseQuery)->whereNotNull('disease_id')->distinct('disease_id')->count('disease_id'),
    'medicines' => (clone $baseQuery)->get()->sum(fn ($item) => count(data_get($item->result_payload, 'medicines', []))),
    'latest' => optional((clone $baseQuery)->latest()->first())->created_at,
];
```

Pass `$summary` and `$latestHistory` to the view.

- [ ] **Step 2: Add GET filters**

Support `q`, `date_from`, `date_to`, and `sort` in history query. Keep default latest first.

- [ ] **Step 3: Update history view**

Build banner, four summary cards, filter row, table, pagination, and detail panel matching Figma.

- [ ] **Step 4: Verify history**

Run: `rtk php artisan test --filter=ConsultationFlowTest`

Expected: PASS.

---

### Task 8: Admin Dashboard Analytics

**Files:**
- Modify: `app/Http/Controllers/Admin/DashboardController.php`
- Modify: `resources/views/admin/dashboard.blade.php`
- Test: `tests/Feature/RoleAccessTest.php`

**Interfaces:**
- Produces view data: `$counts`, `$latest`, `$severityDistribution`, `$dailyConsultations`, `$activities`.

- [ ] **Step 1: Add dashboard analytics**

In `DashboardController::__invoke`, compute daily consultation counts for the last 7 days and severity distribution from diseases linked to consultations.

- [ ] **Step 2: Update admin dashboard view**

Build Figma admin dashboard with blue sidebar layout from Task 9, top search/date area, banner, stat cards, line chart using CSS/SVG, donut chart using conic-gradient, latest table, and activity list.

- [ ] **Step 3: Verify admin dashboard**

Run: `rtk php artisan test --filter=RoleAccessTest`

Expected: PASS.

Run: `rtk npm run build`

Expected: PASS.

---

### Task 9: Admin Layout and CRUD UI

**Files:**
- Modify: `resources/views/layouts/admin.blade.php`
- Modify: `app/Http/Controllers/Admin/ResourceController.php`
- Modify: `resources/views/admin/resource/index.blade.php`
- Modify: `resources/views/admin/resource/form.blade.php`
- Create: `tests/Feature/AdminResourceTest.php`

**Interfaces:**
- Consumes: ResourceController config arrays.
- Produces: Figma sidebar, filters, tables, forms.

- [ ] **Step 1: Write admin resource smoke test**

Create `tests/Feature/AdminResourceTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_gejala_resource(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.resource.index', 'gejala'))
            ->assertOk()
            ->assertSee('Data Gejala', false)
            ->assertSee('G001', false);
    }

    public function test_admin_can_filter_gejala_by_search_query(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.resource.index', ['resource' => 'gejala', 'q' => 'Demam']))
            ->assertOk()
            ->assertSee('Demam', false);
    }
}
```

- [ ] **Step 2: Run test to verify filter behavior**

Run: `rtk php artisan test --filter=AdminResourceTest`

Expected: second test may fail until ResourceController supports `q`.

- [ ] **Step 3: Add ResourceController filters**

In `ResourceController::index`, apply request filters:

```php
if ($search = request('q')) {
    $query->where(function ($inner) use ($search, $config) {
        foreach (array_keys($config['columns']) as $column) {
            if (in_array($column, ['code', 'name', 'category', 'severity', 'status', 'email', 'username'], true)) {
                $inner->orWhere($column, 'like', "%{$search}%");
            }
        }
    });
}

if ($category = request('category')) {
    if (in_array('category', $config['fields'] ?? [], true)) {
        $query->where('category', $category);
    }
}
```

Append query string to pagination with `$items->withQueryString()`.

- [ ] **Step 4: Update admin layout**

Replace dark slate layout with Figma blue sidebar, white content area, topbar search/date, mobile drawer, and profile picture at sidebar bottom.

- [ ] **Step 5: Update resource index**

Build info alert row, search/filter toolbar, Figma table, badge rendering, icon action buttons, pagination.

- [ ] **Step 6: Update resource form**

Build Figma white card form with `dm-input`, compact labels, save/cancel buttons, and responsive grid.

- [ ] **Step 7: Verify admin resources**

Run: `rtk php artisan test --filter=AdminResourceTest`

Expected: PASS.

Run: `rtk npm run build`

Expected: PASS.

---

### Task 10: Assets, Start Scripts, and Final Verification

**Files:**
- Modify: `.env.example`
- Modify: `start.sh`
- Modify: `stop.sh`
- Modify: `README.md`
- Modify: `docs/VERIFICATION.md`
- Verify: all changed files.

**Interfaces:**
- Produces: Git Bash run workflow with MySQL root no password.

- [ ] **Step 1: Verify `.env.example` database defaults**

Ensure `.env.example` contains:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rulebasedmedic
DB_USERNAME=root
DB_PASSWORD=
```

- [ ] **Step 2: Verify `start.sh`**

Ensure `start.sh` creates database `rulebasedmedic` if missing, installs Composer and NPM dependencies, runs key generation, migrations, seeding, Vite, and Laravel server without Docker.

- [ ] **Step 3: Verify `stop.sh`**

Ensure `stop.sh` stops the Laravel and Vite processes started by `start.sh` without deleting user files or database data.

- [ ] **Step 4: Run full automated verification**

Run: `rtk php artisan test`

Expected: PASS.

Run: `rtk npm run build`

Expected: PASS.

- [ ] **Step 5: Run local smoke test**

Run in Git Bash: `bash start.sh`

Expected:

```text
Laravel development server started
Vite development server started
Application URL: http://127.0.0.1:8000
```

- [ ] **Step 6: Stop local servers**

Run in Git Bash: `bash stop.sh`

Expected: no Laravel/Vite process from this project remains running.

- [ ] **Step 7: Update verification docs**

Record exact commands and outcomes in `docs/VERIFICATION.md`.

---

## Self-Review

- Spec coverage: user auth, homepage, cek gejala, result modal, riwayat, admin dashboard, CRUD, role access, MySQL root no password, no Docker, responsive layout, no emoticons, compressed assets, and verification are covered.
- Empty-marker scan: no deferred-work markers remain in this plan.
- Type consistency: route names and service signatures match the existing Laravel code discovered before writing this plan.
- Known constraint: `.git` is absent in this workspace, so commit steps are intentionally replaced with checkpoints.
