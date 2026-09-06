# HNP Hotel — Laravel 12 hotel website + admin panel

A small hotel website with a booking form, and an admin panel that controls
everything shown on the site.

---

## 1. What you need

* WAMP running (Apache + MySQL)
* PHP 8.2 or newer
* Composer installed (https://getcomposer.org)

---

## 2. Setup — five steps

Open a command prompt inside `C:\wamp64\www\HNP` and run these one by one.

### Step 1 — install the Laravel packages and create `.env`

```
composer install
copy .env.example .env
php artisan key:generate
```

`composer install` creates the `vendor` folder. It needs internet and takes a
few minutes. The `copy` command makes your `.env` settings file, and
`key:generate` fills in the app encryption key.

### Step 2 — create an empty database

Open http://localhost/phpmyadmin and create a database named:

```
hnp_hotel
```

Collation: `utf8mb4_unicode_ci`

If your MySQL root user has a password, open the `.env` file and put it on the
`DB_PASSWORD=` line.

### Step 3 — create the tables and the sample data

```
php artisan migrate --seed
```

This creates all tables, the admin login, 4 sample rooms, 6 services and the
site text.

### Step 4 — clear the caches (only needed if something looks stale)

```
php artisan optimize:clear
```

### Step 5 — open the site

| What | URL |
|------|-----|
| Website | http://localhost/HNP/public |
| Admin panel | http://localhost/HNP/public/admin/login |

**Admin logins** (both are created by the seeder)

| Role | Email | Password |
|------|-------|----------|
| Main admin | `admin@hnphotel.com` | `admin123` |
| Front desk | `desk@hnphotel.com` | `desk123` |

Both accounts have full access to the panel. See section 9 for how to change
the passwords.

---

## 3. Pages on the website

| Page | URL |
|------|-----|
| Home | `/` |
| About | `/about` |
| Rooms list | `/rooms` |
| Single room | `/rooms/{slug}` |
| Services | `/services` |
| Contact Us | `/contact` |
| Booking form | `/booking` |
| Booking received | `/booking/success/{reference}` |
| Check my booking | `/booking/status` |

---

## 4. How booking works

1. The customer fills the booking form and submits it.
2. The system checks the dates against how many rooms of that type exist. If
   they are all taken for those dates, it refuses politely.
3. A booking is saved with status **pending** and the customer gets a
   reference like `HNP-20260906-A1B2C3` on screen.
4. The booking appears in **Admin → Bookings**.
5. The admin presses **Confirm** (or opens the booking and changes the status,
   optionally adding a note for the guest).
6. The customer can check the status any time at `/booking/status` using their
   reference and email.

Statuses available: `pending`, `confirmed`, `cancelled`, `completed`.

---

## 5. What the admin panel controls

| Screen | What it does |
|--------|--------------|
| Dashboard | Counts of bookings, pending, confirmed, revenue, unread messages |
| Bookings | Search and filter, confirm / cancel / complete, add a note, delete |
| Rooms | Add, edit, delete rooms — name, type, price, capacity, bed, size, amenities, **photo upload**, how many rooms of that type, show/hide, feature on home page |
| Services | Add, edit, delete the service blocks with a Bootstrap icon |
| Messages | Every Contact Us submission, marked read when opened |
| Site Settings | Hotel name, tagline, home banner text, About text and points, phone, email, address, Google map embed, check-in/out times, social links, footer text |

Everything on the public site comes from the database, so nothing needs code
changes to update the content.

---

## 6. Room photos

Uploaded room photos are saved to:

```
public/uploads/rooms/
```

No `php artisan storage:link` is needed. Allowed types: jpg, jpeg, png, webp.
Maximum size 4 MB.

---

## 7. Database tables

| Table | Holds |
|-------|-------|
| `users` | Admin logins (`is_admin` = 1) |
| `rooms` | Room types shown on the site |
| `bookings` | Customer bookings with status |
| `services` | Service blocks |
| `contacts` | Contact Us messages |
| `settings` | Key/value site text edited from Site Settings |

---

## 8. If something goes wrong

**"Please provide a valid cache path" or a views error**
Make sure these folders exist and are writable:
`storage/framework/views`, `storage/framework/cache/data`, `storage/framework/sessions`, `bootstrap/cache`

**404 on every page except the home page**
Apache `mod_rewrite` is off. In WAMP: left-click the tray icon → Apache →
Apache modules → tick `rewrite_module`, then restart Apache.

**"No application encryption key has been specified"**
Run `php artisan key:generate`

**Blank page**
Set `APP_DEBUG=true` in `.env`, reload, and read the error. Also check
`storage/logs/laravel.log`.

**Want a cleaner URL (http://hotel.local instead of /HNP/public)**
Point an Apache virtual host document root at `C:\wamp64\www\HNP\public`, then
set `APP_URL=http://hotel.local` in `.env`.

---

## 9. Seeders — the sample data

All seed data lives in `database/seeders/`. There is one file per table, so you
can edit the data by hand without hunting through one long file.

| Seeder file | Creates |
|-------------|---------|
| `AdminUserSeeder.php` | The 2 admin logins |
| `SettingSeeder.php` | All 17 site text values (name, banner, About, contact, times, social) |
| `RoomSeeder.php` | 6 room types — Standard Single, Budget Twin, Deluxe Double, Family, Executive Suite, Presidential Suite |
| `ServiceSeeder.php` | 8 services with Bootstrap icons |
| `BookingSeeder.php` | 12 sample bookings — 3 completed, 2 confirmed, 6 pending, 1 cancelled |
| `ContactSeeder.php` | 5 Contact Us messages, 2 of them unread |

`DatabaseSeeder.php` just calls the six in the correct order.

### Run them

```
php artisan db:seed
```

Or one at a time, which is handy while you are editing the data:

```
php artisan db:seed --class=RoomSeeder
php artisan db:seed --class=ServiceSeeder
php artisan db:seed --class=BookingSeeder
```

Every seeder uses `updateOrCreate`, so running it twice does **not** create
duplicate rows — it just updates the same records.

### Start over completely

```
php artisan migrate:fresh --seed
```

This drops every table, rebuilds them, and seeds again. All your own data is
lost, so only use it while you are still building.

### Booking dates move with you

The sample bookings are dated relative to the day you run the seeder. So you
always get past stays, one stay happening right now, and upcoming ones — no
matter when you install the project.

### Remove only the sample bookings

```sql
DELETE FROM bookings WHERE reference LIKE 'HNP-SAMPLE-%';
```

### Testing the "fully booked" message

`Presidential Suite` has only 1 room and sample booking `HNP-SAMPLE-008` takes
it for 3 nights starting 9 days from your seed date. Try booking that suite for
those dates on the website — it should refuse politely instead of accepting.

---

## 10. Changing an admin password

**Easiest way** — edit `database/seeders/AdminUserSeeder.php`, change the
`password` value, then run:

```
php artisan db:seed --class=AdminUserSeeder
```

**Or from the command line:**

```
php artisan tinker
```

then paste:

```php
App\Models\User::where('email','admin@hnphotel.com')->update(['password' => bcrypt('YourNewPassword')]);
```

**Adding another admin:**

```php
App\Models\User::create(['name' => 'Manager', 'email' => 'manager@hnphotel.com', 'password' => bcrypt('secret123'), 'is_admin' => true]);
```

Only users with `is_admin = 1` can open the panel. Anyone else is turned away
at the login screen.
