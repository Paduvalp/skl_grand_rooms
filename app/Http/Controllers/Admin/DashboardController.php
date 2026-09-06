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

        return view('admin.dashboard', compact('stats', 'recentBookings'));
    }
}
