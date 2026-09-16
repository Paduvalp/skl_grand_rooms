<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /** Everything the admin can edit from the Site Settings screen. */
    public const KEYS = [
        'site_name', 'tagline', 'hero_title', 'hero_subtitle',
        'phone', 'email', 'address', 'map_embed',
        'about_heading', 'about_text', 'about_points',
        'checkin_time', 'checkout_time', 'footer_text',
        // SEO
        'meta_description',
        'street_address', 'address_locality', 'address_region', 'postal_code',
        'geo_lat', 'geo_lng',
        'ga_measurement_id', 'search_console_code',
        // Local search ("hotels near me")
        'google_business_url', 'nearby_landmarks', 'service_areas',
        'payment_accepted', 'open_24_hours',
        // Privacy policy and terms pages
        'cancellation_policy', 'legal_updated_at',
    ];

    public function index()
    {
        $values = Setting::all()->pluck('value', 'key')->toArray();

        return view('admin.settings.index', compact('values'));
    }

    public function update(Request $request)
    {
        $rules = [];

        foreach (self::KEYS as $key) {
            $rules[$key] = ['nullable', 'string', 'max:5000'];
        }

        $rules['email'] = ['nullable', 'email', 'max:190'];
        $rules['google_business_url'] = ['nullable', 'url', 'max:500'];

        $data = $request->validate($rules);

        foreach (self::KEYS as $key) {
            Setting::put($key, $data[$key] ?? '');
        }

        return back()->with('success', 'Site settings saved.');
    }
}
