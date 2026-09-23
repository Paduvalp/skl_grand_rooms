<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SmsAccessController as AdminSmsAccessController;
use App\Http\Controllers\Admin\SmsContactController as AdminSmsContactController;
use App\Http\Controllers\Admin\SmsCredentialController as AdminSmsCredentialController;
use App\Http\Controllers\Admin\SmsController as AdminSmsController;
use App\Http\Controllers\Admin\SmsTemplateController as AdminSmsTemplateController;
use App\Http\Controllers\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/services', [PageController::class, 'services'])->name('services');

Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');

Route::get('/location', [PageController::class, 'location'])->name('location');
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');

Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms-and-conditions', [PageController::class, 'terms'])->name('terms');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/booking', [BookingController::class, 'create'])->name('booking');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/success/{reference}', [BookingController::class, 'success'])->name('booking.success');
Route::get('/booking/status', [BookingController::class, 'statusForm'])->name('booking.status');
Route::post('/booking/status', [BookingController::class, 'statusLookup'])->name('booking.status.lookup');

/*
|--------------------------------------------------------------------------
| Short links for campaigns
|--------------------------------------------------------------------------
|
| sklgrandrooms.com/stay is short enough for a text message and lands the
| guest straight on the booking form. The tags mean any booking that
| follows is credited to the SMS campaign on the admin dashboard.
|
*/

Route::redirect('/stay', '/booking?utm_source=sms&utm_medium=sms&utm_campaign=shashi');

/*
|--------------------------------------------------------------------------
| SEO
|--------------------------------------------------------------------------
*/

// robots.txt is the static file public/robots.txt.
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('rooms', AdminRoomController::class)->except('show');

        Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
        Route::patch('bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');
        Route::delete('bookings/{booking}', [AdminBookingController::class, 'destroy'])->name('bookings.destroy');

        Route::resource('services', AdminServiceController::class)->except('show');

        Route::get('gallery', [AdminGalleryController::class, 'index'])->name('gallery.index');
        Route::post('gallery', [AdminGalleryController::class, 'store'])->name('gallery.store');
        Route::put('gallery/{gallery}', [AdminGalleryController::class, 'update'])->name('gallery.update');
        Route::patch('gallery/{gallery}/toggle', [AdminGalleryController::class, 'toggle'])->name('gallery.toggle');
        Route::delete('gallery/{gallery}', [AdminGalleryController::class, 'destroy'])->name('gallery.destroy');

        Route::resource('reviews', AdminReviewController::class)->except('show');

        Route::get('messages', [AdminContactController::class, 'index'])->name('messages.index');
        Route::get('messages/{contact}', [AdminContactController::class, 'show'])->name('messages.show');
        Route::delete('messages/{contact}', [AdminContactController::class, 'destroy'])->name('messages.destroy');

        // Texting guests needs the access code as well as an admin login.
        Route::get('sms/unlock', [AdminSmsAccessController::class, 'show'])->name('sms.unlock');
        Route::post('sms/unlock', [AdminSmsAccessController::class, 'unlock'])->name('sms.unlock.submit');
        Route::post('sms/lock', [AdminSmsAccessController::class, 'lock'])->name('sms.lock');

        Route::middleware('sms.code')->group(function () {
            Route::get('sms', [AdminSmsController::class, 'create'])->name('sms.create');
            Route::post('sms', [AdminSmsController::class, 'send'])->name('sms.send');

            Route::get('sms/credentials', [AdminSmsCredentialController::class, 'edit'])->name('sms.credentials');
            Route::put('sms/credentials', [AdminSmsCredentialController::class, 'update'])->name('sms.credentials.update');
            Route::delete('sms/credentials', [AdminSmsCredentialController::class, 'destroy'])->name('sms.credentials.destroy');
            Route::post('sms/credentials/test', [AdminSmsCredentialController::class, 'test'])->name('sms.credentials.test');
            Route::put('sms/credentials/code', [AdminSmsCredentialController::class, 'updateCode'])->name('sms.credentials.code');
            Route::delete('sms/credentials/code', [AdminSmsCredentialController::class, 'destroyCode'])->name('sms.credentials.code.destroy');

            Route::get('sms/templates', [AdminSmsTemplateController::class, 'index'])->name('sms.templates');
            Route::post('sms/templates', [AdminSmsTemplateController::class, 'store'])->name('sms.templates.store');
            Route::put('sms/templates/{template}', [AdminSmsTemplateController::class, 'update'])->name('sms.templates.update');
            Route::patch('sms/templates/{template}/toggle', [AdminSmsTemplateController::class, 'toggle'])->name('sms.templates.toggle');
            Route::delete('sms/templates/{template}', [AdminSmsTemplateController::class, 'destroy'])->name('sms.templates.destroy');

            Route::get('sms/contacts', [AdminSmsContactController::class, 'index'])->name('sms.contacts');
            Route::post('sms/contacts', [AdminSmsContactController::class, 'store'])->name('sms.contacts.store');
            Route::post('sms/contacts/import', [AdminSmsContactController::class, 'import'])->name('sms.contacts.import');
            Route::get('sms/contacts/sample', [AdminSmsContactController::class, 'sample'])->name('sms.contacts.sample');
            Route::post('sms/contacts/use', [AdminSmsContactController::class, 'useForSms'])->name('sms.contacts.use');
            Route::patch('sms/contacts/{contact}/toggle', [AdminSmsContactController::class, 'toggle'])->name('sms.contacts.toggle');
            Route::delete('sms/contacts/{contact}', [AdminSmsContactController::class, 'destroy'])->name('sms.contacts.destroy');

        });

        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
