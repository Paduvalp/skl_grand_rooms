<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\Room;
use App\Models\Service;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'rooms' => Room::count(),
            'services' => Service::count(),
            'bookings' => Booking::count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'unread_messages' => Contact::where('is_read', false)->count(),
            'revenue' => Booking::whereIn('status', ['confirmed', 'completed'])->sum('total_price'),
        ];

        $recentBookings = Booking::with('room')->latest()->take(8)->get();

        return view('admin.dashboard', array_merge(
            compact('stats', 'recentBookings'),
            $this->marketing()
        ));
    }

    /**
     * Where bookings are coming from.
     *
     * Cancelled bookings are left out of the value column, because a campaign
     * should not get credit for money that never arrived. They are still
     * counted as bookings, so you can see a source that brings enquiries which
     * then fall through.
     */
    private function marketing(): array
    {
        $since = now()->subDays(30);

        $channels = Booking::selectRaw('utm_source, utm_medium, referrer_host, COUNT(*) as bookings, SUM(CASE WHEN status <> ? THEN total_price ELSE 0 END) as value', ['cancelled'])
            ->groupBy('utm_source', 'utm_medium', 'referrer_host')
            ->get()
            ->groupBy(fn ($row) => \App\Support\Attribution::channel($row->utm_source, $row->utm_medium, $row->referrer_host)[0])
            ->map(fn ($rows, $name) => [
                'name' => $name,
                'colour' => \App\Support\Attribution::channel(
                    $rows->first()->utm_source,
                    $rows->first()->utm_medium,
                    $rows->first()->referrer_host
                )[1],
                'bookings' => (int) $rows->sum('bookings'),
                'value' => (float) $rows->sum('value'),
            ])
            ->sortByDesc('bookings')
            ->values();

        $campaigns = Booking::selectRaw('utm_campaign, utm_source, utm_medium, COUNT(*) as bookings, SUM(CASE WHEN status <> ? THEN total_price ELSE 0 END) as value, MAX(created_at) as last_booking', ['cancelled'])
            ->whereNotNull('utm_campaign')
            ->groupBy('utm_campaign', 'utm_source', 'utm_medium')
            ->orderByDesc('bookings')
            ->take(10)
            ->get();

        return [
            'channels' => $channels,
            'campaigns' => $campaigns,
            'totalTracked' => Booking::count(),
            'taggedCount' => Booking::whereNotNull('utm_source')->count(),
            'recentTagged' => Booking::whereNotNull('utm_source')->where('created_at', '>=', $since)->count(),
        ];
    }
}
