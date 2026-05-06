# Dwellcasa — Codebase Context Document

> Generated: 2026-05-06. Covers the `main` branch as of commit `ec8dfcb`.

---

## 1. Project Overview

Dwellcasa is a **multi-location hotel and short-stay property management system** built for internal property operators. It solves two problems simultaneously:

1. **Operations management** — admin staff manage bookings, room assignments, check-in/check-out workflows, payments, inventory, and guests across one or more physical properties.
2. **Public-facing website** — guests browse rooms, view availability, and submit booking inquiries per location.

The system is designed for a small hotel group where a single **super admin** oversees multiple properties, each staffed by their own **admins** and **staff** members.

---

## 2. Tech Stack

| Layer | Technology |
|---|---|
| Language | PHP 8.2+ |
| Framework | Laravel 12.0 |
| Frontend build | Vite 7 + Laravel Vite Plugin |
| CSS | Tailwind CSS 4.2 |
| HTTP client (frontend) | Axios 1.11 |
| Auth (API) | Laravel Sanctum 4.0 |
| RBAC | Spatie Laravel Permission 7.3 |
| Audit logging | Spatie Laravel Activity Log 5.0 |
| Database | MySQL (database `dwellcasa`) |
| Session / Cache / Queue | All database-driven |
| Mail | SMTP (`noreply@dwellcasa.com.np`) |
| Templating | Blade |

---

## 3. Architecture

### Overview

The app is a **Laravel monolith** with two distinct request surfaces:

- **Admin panel** — authenticated Blade views that communicate with an internal JSON API (`/api/*`) via Axios. All admin UI calls go through `routes/api.php` under `web` + `auth` middleware.
- **Public website** — traditional server-rendered Blade pages served from `routes/web.php`. Public booking submissions are processed by a dedicated `Web\BookingController`.

The admin UI uses Blade for the shell/layout but fetches/posts data via Axios to the API layer, making it a hybrid MPA (multi-page application with JS-powered data operations).

### Data Isolation — Multi-Tenancy

Every tenant-specific model carries a `location_id` column. A **global Eloquent scope** (`LocationScope`) is registered on all such models and automatically filters queries by the currently selected location. Super admins can switch their active location; regular admins and staff are pinned to a single location.

### Directory Tree

```
/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php          # Dashboard, revenue, activity log, profile, settings
│   │   │   ├── BookingController.php         # API: full CRUD + soft delete + availability check
│   │   │   ├── GuestController.php           # API: guest CRUD + soft delete
│   │   │   ├── CheckInController.php         # API: check-in CRUD
│   │   │   ├── CheckOutController.php        # API: check-out + triggers review email + room reset
│   │   │   ├── UserController.php            # API: user + role + permission management
│   │   │   ├── RoomController.php            # API: room CRUD + soft delete
│   │   │   ├── RoomTypeController.php        # API: room type CRUD + soft delete
│   │   │   ├── AmenityController.php         # API: amenity CRUD
│   │   │   ├── PaymentController.php         # API: payment CRUD
│   │   │   ├── InventoryController.php       # API: inventory CRUD
│   │   │   ├── InquiryController.php         # API: inquiry CRUD + reply
│   │   │   ├── ReviewController.php          # API: review CRUD + status toggle
│   │   │   ├── LocationController.php        # API: location CRUD (super_admin only)
│   │   │   ├── WebsiteInfoController.php     # API: per-location website content
│   │   │   ├── GalleryImageController.php    # API: gallery images (polymorphic)
│   │   │   ├── ServiceController.php         # API: property services
│   │   │   ├── HouseRuleController.php       # API: house rules
│   │   │   └── Web/
│   │   │       ├── AuthController.php        # Login/logout + location selection for super_admin
│   │   │       ├── BookingController.php     # Public booking form (GET) + submission (POST)
│   │   │       ├── HomeController.php        # Public homepage
│   │   │       ├── RoomController.php        # Public room listing / detail
│   │   │       ├── GalleryController.php     # Public gallery
│   │   │       ├── AboutController.php       # Public about page
│   │   │       ├── ContactController.php     # Public contact page
│   │   │       ├── ReviewController.php      # Public review submission (token-gated)
│   │   │       ├── SitemapController.php     # Generates sitemap.xml
│   │   │       └── RobotsController.php      # Serves robots.txt
│   │   ├── Middleware/
│   │   │   └── EnsureLocationSelected.php   # Blocks super_admin without active location
│   │   └── Requests/                        # Form request validation classes (Store/Update per model)
│   ├── Models/
│   │   ├── User.php                          # Authenticatable; HasRoles (Spatie)
│   │   ├── Location.php                      # Root multi-tenancy entity
│   │   ├── RoomType.php                      # Room category with pricing; SoftDeletes
│   │   ├── Room.php                          # Physical room; status enum; SoftDeletes
│   │   ├── Booking.php                       # Core reservation; SoftDeletes; Prunable (90 days)
│   │   ├── Guest.php                         # Guest profile; SoftDeletes; Prunable
│   │   ├── CheckIn.php                       # Check-in record linked to Booking
│   │   ├── CheckOut.php                      # Check-out record with room condition
│   │   ├── Payment.php                       # Payment transaction records
│   │   ├── Review.php                        # Guest review with approval workflow
│   │   ├── Inquiry.php                       # Contact / booking inquiry
│   │   ├── Amenity.php                       # Room amenity (many-to-many with Room, RoomType)
│   │   ├── GalleryImage.php                  # Polymorphic gallery image; SoftDeletes
│   │   ├── Inventory.php                     # Property inventory stock
│   │   ├── WebsiteInfo.php                   # Per-location website content block
│   │   ├── PropertySetting.php               # Global key-value settings
│   │   ├── Activity.php                      # Extended Spatie Activity (adds location_id)
│   │   ├── Service.php                       # Property service listing
│   │   └── HouseRule.php                     # Property house rules
│   │   └── Scopes/
│   │       └── LocationScope.php             # Global scope: filters by active location_id
│   ├── Repositories/                         # Concrete repository implementations
│   ├── Contracts/                            # Repository interfaces
│   └── Mail/
│       ├── BookingConfirmationMail.php       # Sent on booking status → confirmed
│       ├── ReviewRequestMail.php             # Sent post-checkout to request review
│       └── InquiryReplyMail.php             # Sent when admin replies to inquiry
├── database/
│   ├── migrations/                           # 40+ ordered migrations
│   └── seeders/
│       └── RolesAndPermissionsSeeder.php     # Creates all roles and permissions
├── resources/
│   └── views/
│       ├── admin/                            # Admin Blade shells (data loaded via Axios)
│       │   ├── home.blade.php                # Dashboard
│       │   ├── bookings/                     # Booking list, add, edit views
│       │   ├── room_type/                    # Room type + room views
│       │   ├── guests.blade.php
│       │   ├── revenue.blade.php
│       │   ├── users.blade.php
│       │   ├── amenities.blade.php
│       │   ├── gallery.blade.php
│       │   ├── reviews.blade.php
│       │   ├── inquiry.blade.php
│       │   ├── inventory.blade.php
│       │   ├── info.blade.php               # Website info editor
│       │   ├── location.blade.php
│       │   └── activity-log.blade.php
│       ├── web/                              # Public website Blade views
│       │   ├── home.blade.php, location.blade.php
│       │   ├── rooms.blade.php, room.blade.php
│       │   ├── booking.blade.php
│       │   ├── gallery.blade.php, about.blade.php, contact.blade.php
│       │   └── review.blade.php, hotel-review.blade.php
│       ├── emails/                           # Email templates
│       │   ├── booking-confirmation.blade.php
│       │   ├── review-request.blade.php
│       │   └── inquiry-reply.blade.php
│       ├── layouts/
│       │   ├── admin.blade.php               # Admin shell layout
│       │   └── app.blade.php                 # Public site layout
│       ├── components/
│       │   ├── property-map.blade.php
│       │   └── schema-lodging.blade.php     # JSON-LD structured data for SEO
│       └── partials/
│           └── seo-head.blade.php
├── routes/
│   ├── web.php                               # Public routes + admin Blade pages
│   └── api.php                               # All data API routes (admin operations)
└── config/
    ├── permission.php                        # Spatie RBAC config
    ├── activitylog.php                       # Spatie audit log config
    └── ... (standard Laravel configs)
```

---

## 4. Core Data Models

### Location
The root multi-tenancy entity. Every other model below ties back to a `location_id`. Fields: `name`, `slug` (URL key), `description`, `hero_image`, `address`, `phone`, `email`, `is_active`.

### RoomType
Represents a category of rooms (e.g., "Deluxe Double"). Carries pricing: `price_per_night`, `price_per_month`. Has `is_standalone` flag for single-room types. Many-to-many with `Amenity`. Has many `Room`.

### Room
A physical, bookable room. Key field: `status` (enum: `available`, `occupied`, `maintenance`, `reserved`, `out_of_service`). Belongs to `RoomType`. The `room_number` is unique globally.

### Booking
The central entity. Key fields:
- `booking_ref` — unique human-readable reference
- `guest_id`, `room_type_id`, `room_id` (nullable — room assigned at check-in)
- `check_in_date`, `check_out_date`
- `stay_type` — `short_term` | `long_term`
- `rate_per_night`, `rate_per_month`, `total_amount`, `discount`, `amount_paid`, `deposit_amount`
- `status` — `pending` | `confirmed` | `checked_in` | `checked_out` | `cancelled` | `no_show`
- `payment_status` — `unpaid` | `deposit_paid` | `partially_paid` | `fully_paid` | `refunded`
- `checked_in_at`, `checked_out_at` timestamps
- SoftDeletes + Prunable (auto-purge after 90 days in trash)

### Guest
Guest profile. Key: `email` + `location_id` (unique together). Fields: `full_name`, `phone`, `nationality`, `id_type`, `id_number`, `address`. SoftDeletes + Prunable.

### CheckIn / CheckOut
Records the physical check-in/check-out event. `CheckOut` additionally tracks: `room_condition` (enum: `good`, `damaged`, `needs_cleaning`), `damage_notes`, `extra_charges`. Both reference `booking_id` and optionally `room_id` and a `checked_in_by` / `checked_out_by` user.

### Review
Token-gated guest review. Fields: `review_token` (UUID), `token_used` (boolean), `rating`, `title`, `body`, `type` (`room_type` | `hotel`), `status` (`pending` | `approved` | `rejected`). The token is emailed post-checkout; the guest submits via `/review/{token}` without needing an account.

---

## 5. Key Modules and Their Responsibilities

| File | Responsibility |
|---|---|
| `app/Models/Scopes/LocationScope.php` | Global Eloquent scope that silently applies `WHERE location_id = ?` to all tenant models — the heart of multi-tenancy |
| `app/Http/Controllers/AdminController.php` | Dashboard KPIs, revenue report, activity log page, profile/password management, super admin location switching |
| `app/Http/Controllers/BookingController.php` | All booking API operations including the pessimistic-lock availability check (`checkAvailability`) |
| `app/Http/Controllers/CheckOutController.php` | Check-out flow: records checkout, resets room status to `available`, creates Review record, sends `ReviewRequestMail` |
| `app/Http/Controllers/UserController.php` | User CRUD + role/permission management; enforces that `super_admin` role cannot be granted by admins |
| `app/Http/Controllers/Web/BookingController.php` | Public booking form — wraps creation in `DB::transaction` with `lockForUpdate()` to prevent race conditions |
| `app/Http/Controllers/Web/AuthController.php` | Login flow; super admin gets a location-selection step before proceeding to the dashboard |
| `app/Http/Requests/StoreBookingRequest.php` | Validation + `prepareForValidation()` hook that auto-creates or retrieves the Guest record by email before the booking is stored |
| `database/seeders/RolesAndPermissionsSeeder.php` | Defines all 20 permissions and the three roles (super_admin, admin, staff) with their permission sets |
| `app/Http/Middleware/EnsureLocationSelected.php` | Guards all admin routes — redirects super_admin to location selection if no location is active in the session |
| `app/Models/Activity.php` | Extends Spatie's Activity model to add `location_id` (denormalized for indexed queries on the audit log page) |

---

## 6. Data Flow

### Typical Admin Booking Creation

```
Browser (admin Blade view)
  │  POST /api/bookings (Axios, JSON)
  ▼
routes/api.php  →  BookingController@store
  │  validates via StoreBookingRequest
  │    └─ prepareForValidation(): Guest::firstOrCreate(email + location_id)
  │  checks availability: RoomType::lockForUpdate() + CarbonPeriod date iteration
  │  DB::transaction:
  │    Booking::create(...)
  │    activity()->log('Booking created')
  ▼
JSON response  →  Axios callback  →  UI update
```

### Public Booking Submission

```
Browser (public /booking page)
  │  POST /{location:slug}/booking (form submit)
  ▼
routes/web.php  →  Web\BookingController@store
  │  validates via StoreBookingRequest
  │    └─ prepareForValidation(): Guest::firstOrCreate(...)
  │  availability check with lockForUpdate()
  │  DB::transaction:
  │    Booking::create(status='pending')
  ▼
redirect → /{location:slug}/ with flash 'success'
```

### Check-out Flow

```
Admin clicks "Check Out"
  │  POST /api/check-outs (Axios)
  ▼
CheckOutController@store
  │  creates CheckOut record
  │  updates Booking: status='checked_out', checked_out_at=now()
  │  updates Room: status='available'
  │  creates Review record with UUID token
  │  dispatches ReviewRequestMail (SMTP, queued)
  │  activity()->log('Guest checked out')
  ▼
JSON response
```

### Admin Login (super_admin path)

```
POST /admin/login
  ▼
Web\AuthController@login
  │  Auth::attempt(credentials)
  │  if role == super_admin → redirect to location selection page
  │  else → redirect to /admin/
  ▼
Location selection → session('selected_location_id') set
  ▼
EnsureLocationSelected middleware passes → /admin/ dashboard
```

---

## 7. Entry Points

### Application Start

```bash
# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed roles and permissions
php artisan db:seed --class=RolesAndPermissionsSeeder

# Build frontend assets
npm run build

# Serve (development)
php artisan serve

# Queue worker (required for mail sending)
php artisan queue:work

# Frontend dev mode with HMR
npm run dev
```

### Main HTTP Entry

- `public/index.php` — standard Laravel front controller (all requests route through here)
- `routes/web.php` — public + admin Blade routes
- `routes/api.php` — admin data API routes

### Artisan Commands

No custom Artisan commands were found. Standard Laravel commands apply (`migrate`, `db:seed`, `queue:work`, `tinker`, `pail`).

---

## 8. External Dependencies

| Dependency | Purpose |
|---|---|
| **MySQL** | Primary data store (`DB_HOST`, `DB_DATABASE=dwellcasa`) |
| **SMTP mail server** (`box.rajesh-maharjan.com.np`) | Transactional email delivery (confirmations, review requests, inquiry replies) |
| **Spatie Laravel Permission** | Role and permission storage in DB (`roles`, `permissions`, `model_has_roles`, etc.) |
| **Spatie Laravel Activity Log** | Audit trail in `activity_log` table |

No external queues (SQS, Redis), object storage (S3), or third-party payment gateways are configured — queues and file storage are local/database-backed.

---

## 9. Known Patterns and Conventions

### Repository Pattern
All data access is abstracted behind interfaces in `app/Contracts/` with implementations in `app/Repositories/`. Controllers inject repository interfaces. This decouples the controllers from Eloquent directly, though in practice most complex queries still live on the repositories.

### LocationScope (Multi-Tenancy via Global Scope)
`LocationScope` is applied as a **global scope** on tenant models (Booking, Guest, Room, RoomType, Amenity, GalleryImage, Review, Inquiry, Inventory, WebsiteInfo). This means every query on these models is automatically filtered by the session's active `location_id`. You must call `->withoutGlobalScope(LocationScope::class)` when you need to query across all locations (e.g., the super admin's location list).

### API-Driven Admin UI
Admin Blade views are shells only. The actual data is loaded and mutated via Axios calls to `/api/*` routes. This means:
- `routes/api.php` uses `middleware(['web', 'auth'])` (not `sanctum`) for admin API calls — it relies on the session cookie, not tokens.
- JSON responses from API controllers are consumed by inline `<script>` blocks or separate JS in the Blade views.

### Availability Checking with Pessimistic Locking
`BookingController::checkAvailability()` uses `RoomType::lockForUpdate()` inside a transaction to prevent double-booking under concurrent requests. The check iterates dates using `CarbonPeriod` and compares against existing overlapping bookings per room type.

### Soft Deletes + Prunable
Booking and Guest use both `SoftDeletes` and Laravel's `Prunable` trait. Prunable is configured to auto-permanently-delete records that have been soft-deleted for 90 days. The `prunable()` query method on each model defines the eligible records.

### Form Request `prepareForValidation()`
`StoreBookingRequest` uses `prepareForValidation()` to run a `Guest::firstOrCreate(email + location_id)` before validation runs. This means **guest creation is a side effect of booking validation** — if validation subsequently fails, the guest may still be created.

### Activity Logging
Controllers call `activity()->causedBy(auth()->user())->performedOn($model)->log('...')` directly. The custom `Activity` model extends Spatie's to add a `location_id` column (populated from `properties['location_id']` or the model's own `location_id`), enabling fast per-location filtering on the audit log page.

### Role Naming Convention
- `super_admin` — platform owner; all permissions, all locations
- `admin` — property manager; all operational permissions for their location
- `staff` — front desk; booking, check-in/out, guest, inquiry, inventory

Permission names use **lowercase with spaces**: `"view bookings"`, `"manage room types"`, `"manage logs"`.

### Route Model Binding
Public web routes use `{location:slug}` to resolve the `Location` model by its `slug` column. Admin routes use `{id}` (plain integer) and resolve manually in controllers.

---

## 10. Gotchas and Non-Obvious Things

1. **Guest creation in validation**: `StoreBookingRequest::prepareForValidation()` calls `Guest::firstOrCreate()`. If the booking form submission then fails for another reason (e.g., room unavailable), the guest record is already in the database. This is intentional but can leave orphaned guests.

2. **Admin API uses session auth, not tokens**: Despite living in `routes/api.php`, admin routes use `middleware(['web', 'auth'])`, not `auth:sanctum`. This means the Sanctum token flow is not used internally — Sanctum is installed but appears to be for potential future mobile/external API use.

3. **LocationScope is bypassed on public routes**: The public web controllers (`Web\*`) do not go through `EnsureLocationSelected` middleware and resolve their location from the `{location:slug}` route parameter instead. Queries on those controllers must manually scope to the location — `LocationScope` may not apply correctly unless the session location is set.

4. **`amount_paid` is nullable**: Migration `2026_05_05_102243_make_amount_paid_nullable_in_bookings_table.php` makes `amount_paid` nullable — it was non-nullable originally. This means `$booking->amount_paid` can be `null` (not `0`) for bookings created before payment.

5. **`discount` column is a recent addition**: Migration `2026_05_05_120000_add_discount_to_bookings_table.php` adds `discount` to bookings. Older bookings will have `discount = null` or `0` — check for nullability before arithmetic.

6. **`is_standalone` on RoomType**: This flag is not well-documented in code. It appears to signal a room type that corresponds to exactly one physical room (no multiple rooms under it).

7. **`activity_log.location_id` is denormalized**: The Spatie `activity_log` table has a custom `location_id` column (added in a migration) populated at log time by the custom `Activity` model. This is non-standard Spatie usage — it breaks if the Spatie package's table is recreated from scratch without re-applying the custom migration.

8. **Queue is synchronous by default in development**: `QUEUE_CONNECTION=database` means mail jobs are queued in the `jobs` table. Without running `php artisan queue:work`, no emails will be sent. There is no fallback to synchronous mail.

9. **`EnsureLocationSelected` only guards super_admin**: Regular admins and staff have a fixed `location_id` on their `User` record and never need location selection. The middleware checks the role and skips non-super-admins.

10. **No automated test suite found**: There are no test files in `tests/Feature/` or `tests/Unit/` beyond the default Laravel placeholders. All testing appears to be manual.
