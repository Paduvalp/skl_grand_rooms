<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Service;

class HomeController extends Controller
{
    public function index()
    {
        $featuredRooms = Room::where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('price')
            ->take(3)
            ->get();

        $services = Service::where('is_active', true)
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        return view('home', compact('featuredRooms', 'services'));
    }
}
