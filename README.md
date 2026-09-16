# SKL GRAND ROOMS — Laravel 12 hotel website + admin panel

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

This creates all tables, the admin logins, 4 sample rooms, the 5 real facilities,
12 sample bookings, 5 contact messages and the site text.

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
| Main admin | `admin@sklgrandrooms.com` | `admin123` |
| Front desk | `desk@sklgrandrooms.com` | `desk123` |

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
   reference like `SKL-20260906-A1B2C3` on screen.
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
| Site Settings | Hotel name, tagline, home banner text, About text and points, phone, email, address, Google map embed, check-in/out times, footer text |

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
| `SettingSeeder.php` | All 14 site text values (name, banner, About, contact details, map, times, footer) |
| `RoomSeeder.php` | 4 **placeholder** room types — Standard Single, Standard Double, Deluxe Double, Family |
| `ServiceSeeder.php` | The 5 real facilities — AC, Geyser, Wi-Fi, TV, Parking |
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
DELETE FROM bookings WHERE reference LIKE 'SKL-SAMPLE-%';
```

### Testing the "fully booked" message

`Family Room` has only 3 rooms. Sample bookings `SKL-SAMPLE-007` (2 rooms) and
`SKL-SAMPLE-008` (1 room) overlap around day 9 to day 11 from your seed date,
which uses all 3. Try booking a Family Room for those dates on the website — it
should refuse politely instead of accepting.

### What is real and what is placeholder

| Data | Status |
|------|--------|
| Hotel name, address, phone | **Real** — from the Google Maps listing |
| The 5 facilities (AC, Geyser, Wi-Fi, TV, Parking) | **Real** — confirmed by the owner |
| Email address | Placeholder — no email on the listing yet |
| Check-in / check-out times | Best guess from booking sites — confirm these |
| Room types, rates, bed sizes, room counts | **Placeholder** — replace with the real ones |
| Bookings and contact messages | Sample data for testing |

Nothing on the site claims a facility the hotel does not have. There is no
restaurant, pool, gym, conference hall, airport pickup or room service anywhere
in the text.

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
App\Models\User::where('email','admin@sklgrandrooms.com')->update(['password' => bcrypt('YourNewPassword')]);
```

**Adding another admin:**

```php
App\Models\User::create(['name' => 'Manager', 'email' => 'manager@sklgrandrooms.com', 'password' => bcrypt('secret123'), 'is_admin' => true]);
```

Only users with `is_admin = 1` can open the panel. Anyone else is turned away
at the login screen.

---

## 11. About the name

The hotel is called **SKL GRAND ROOMS**. Three things keep the old `HNP` name
on purpose, and none of them are visible to your guests:

| Stays as HNP | Why |
|--------------|-----|
| The folder `C:\wamp64\www\HNP` | Renaming it changes every URL and breaks the link between this project and Claude. |
| The database `hnp_hotel` | Renaming means creating a new database and migrating again. Say the word and I will change it. |
| CSS class names like `btn-hnp` | Internal styling names only. Renaming them touches 40 files for no benefit. |

The hotel name shown on the site comes from **Admin → Site Settings → Hotel
name**, so you can change it any time without editing code. It is written as
`SKL GRAND ROOMS` in the seeder; change that one field if you prefer
`SKL Grand Rooms`.

---

## 12. Reaching the admin panel

There is **no Admin Login link on the website**. It was removed on purpose, so
guests never see a way into the panel. Reach it by typing the address:

```
http://localhost/HNP/public/admin/login
```

Bookmark that page in your browser. Nothing else on the public site links to it.

Social media icons have also been removed from the footer, along with their
fields in Site Settings. Say the word if you want them back later.

---

## 12b. Privacy policy and terms

Two legal pages ship with the site and are linked from the footer, both in the
Pages column and in the bar along the bottom:

| Page | Address |
|------|---------|
| Privacy Policy | `/privacy-policy` |
| Terms and Conditions | `/terms-and-conditions` |

Each one has a sticky contents list down the side, a plain-English summary box
at the top, numbered sections, and a "last updated" date. The hotel name,
address, phone, email and the check-in and check-out times are pulled from Site
Settings, so they are never out of step with the rest of the site.

The wording describes what this website genuinely does: the booking form takes a
request rather than a payment, the only details collected are the ones on the
booking and contact forms, and the analytics paragraph changes on its own
depending on whether you have entered a Google Analytics ID.

### Two boxes only you can fill in

Go to **Admin → Site Settings → Privacy policy and terms**:

- **Your cancellation policy** — printed as section 5 of the Terms page, one
  paragraph per line. Leave it empty and a fair general wording appears instead,
  but put your real policy in. It is the clause guests argue about.
- **Legal pages last updated** — the date at the top of both pages. Change it
  whenever you edit the wording.

### Please read them before you go live

These pages are a solid, honest starting point written around how this
particular site works, and they are far better than the copied-and-pasted
template most small hotels use. They are **not legal advice**, and nobody has
checked them against your specific business. Before you launch:

1. Read both pages through as if you were a guest.
2. Fix anything that is not true of your hotel — the house rules, the quiet
   hours, whether you take pets, how parking works.
3. Put your real cancellation policy in the box above.
4. If you handle a lot of bookings, or take payments online later, have a lawyer
   look them over. The rules that apply to personal data in India changed with
   the Digital Personal Data Protection Act, and a short review is cheap
   compared with getting it wrong.

---

## 13. SEO

Everything below is already built. Most of it needs nothing from you.

### What is automatic

| Feature | Where |
|---------|-------|
| A unique page title and Google description on every page | Each Blade view |
| Canonical link, so Google never sees duplicate pages | Layout |
| Open Graph and Twitter cards, so WhatsApp and Facebook shares look right | Layout |
| Hotel structured data — address, phone, price range, facilities, check-in times | `app/Support/Seo.php` |
| Room structured data — price, currency, bed, how many guests | Room detail pages |
| Breadcrumb structured data | Every inner page |
| `sitemap.xml`, rebuilt on every request from your live rooms | `/sitemap.xml` |
| `robots.txt` that blocks the admin panel | `/robots.txt` |
| Booking confirmation, booking status, 404 and the whole admin panel marked "do not index" | Those views |
| Lazy-loaded room photos with real alt text | Room views |
| Favicon | `public/favicon.svg` |

The price range Google sees comes from your real room rates. The facilities list
comes from Admin → Services. Change either one and the search data follows.

### Being found by people searching nearby

This is a different job from ranking for "hotel in Bengaluru", and most of it is
not code. When somebody searches **hotels near me**, Google answers from the
map, not from the website. Three things decide who appears: how close the hotel
is to whoever is searching, how complete and active its **Google Business
Profile** is, and how many recent reviews it has. Nothing on this website
outranks those.

What the site does is back the listing up, so Google can see the pin and the
website are the same business and can match the hotel to the places people
search near.

| Feature | Where |
|---------|-------|
| Business Profile link published as `sameAs` and `hasMap` | Site Settings → Local search |
| `geo.position`, `ICBM`, `geo.region` meta tags | Layout |
| Nearby landmarks printed as readable text, with distances | Home and Contact pages |
| Areas served, listed as `areaServed` | Hotel structured data |
| "Open 24 hours", payment methods, currency | Hotel structured data |
| Common questions, answered on the page and as `FAQPage` data | Contact page |

Fill these in at **Admin → Site Settings → Local search**. Everything is
optional, and any box left empty is simply left off the page.

The **nearby landmarks** box is the one that matters most. Google can only
connect the hotel to a landmark if that landmark is written on the site in
plain text, so a search for *"lodge near Bangalore University"* has something to
match. Write one per line as `Place | distance`:

```
Bangalore University | 2 km
RR Nagar Metro Station | 3 km
Global Village Tech Park | 5 km
```

Use the names people actually type: stations, colleges, hospitals, tech parks,
bus stands, temples. Keep the distances honest — a guest who finds the real
distance is longer than promised leaves the review that undoes the work.

One caveat on the questions: Google now shows FAQ rich results only for
government and health sites, so do not expect the drop-downs to appear in search
results. The answers still earn their place, because they are the text Google
reads when someone asks how far the hotel is from somewhere.

**The part that actually decides it.** Claim and finish the Google Business
Profile at [business.google.com](https://business.google.com): exact pin, the
same name, address and phone as this site, the hotel category, real photos,
opening hours, and this website's address. Then ask departing guests for
reviews, steadily. A complete profile with recent reviews beats a perfect
website every time.

### Three things to do yourself

**1. Set your domain.** Everything reads `APP_URL` from `.env`. When you go
live, change it:

```
APP_URL=https://yourdomain.com
```

The canonical tags, sitemap and robots file all update on their own. Nothing is
hard-coded.

**2. Delete `public/robots.txt`.** That static file is only a fallback. Once it
is gone, Laravel serves `/robots.txt` instead, which adds a `Sitemap:` line
pointing at your live domain automatically.

**3. Fill in the SEO fields.** Go to **Admin → Site Settings → Search engines**:

- **Google description** — the grey text under your title in search results
- **Address for Google** — the address split into street, city, state and PIN
- **Map latitude and longitude** — right click the hotel on Google Maps and click the numbers at the top of the menu
- **Google Analytics 4 ID** — from analytics.google.com, starts with `G-`
- **Search Console verification code** — from search.google.com/search-console, HTML tag method, paste only the long code

Analytics and Search Console stay completely switched off while those boxes are
empty. No script is loaded and nothing is sent to Google.

### After you go live

1. Add the site at [search.google.com/search-console](https://search.google.com/search-console)
2. Verify it with the HTML tag method, using the field above
3. Submit `https://yourdomain.com/sitemap.xml`
4. Test the structured data at [search.google.com/test/rich-results](https://search.google.com/test/rich-results)
5. Claim your Google Business Profile and add the website link, so the hotel listing and the site reinforce each other

### The biggest win is not code

Your Google listing has **no photos**. For a hotel, photos drive more clicks
than anything on this list. Upload them in Admin → Rooms and on your Google
Business Profile. The first room photo you upload also becomes the picture
people see when the site is shared on WhatsApp.

---

## 14. Messages and the map

### SweetAlert

Every message on the site uses SweetAlert, not a plain browser box.

| Where | What you see |
|-------|--------------|
| Public site, success | A centred box, for example after a contact message is sent |
| Public site, error | A centred box with the reason |
| Form with mistakes | One box listing every problem, plus the usual red text under each field |
| Admin panel, success | A small green toast in the top right that fades after 3 seconds |
| Admin panel, delete | A red "Are you sure?" box with Cancel and Yes, delete it |

Nothing else needs doing. Any form you add later gets a SweetAlert confirmation
just by adding one attribute:

```html
<form ... data-confirm="Delete this thing?">
```

The old browser `confirm()` popups are gone.

### The map

Go to **Admin → Site Settings → Google Map**. You can paste **either**:

- a plain Google Maps link — open the hotel on Google Maps, press **Share**, copy the link, paste it
- or the full `<iframe>` embed code

Both work. If the link has coordinates in it, the map drops a pin on the exact
spot. If you leave the box empty, the map still appears using the address.

Those coordinates are also used for your search-engine data, so pasting a link
with an `@12.93...` in it fills in your map pin for Google at the same time.

**A note on safety.** Only the map address is ever kept from what you paste.
Any other HTML, script or attribute is thrown away, and a link to anywhere
other than Google Maps is rejected. So a bad copy and paste cannot put
anything harmful on your site.
