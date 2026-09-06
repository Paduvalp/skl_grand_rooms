<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Service;

class PageController extends Controller
{
    public function about()
    {
        $roomCount = Room::where('is_active', true)->sum('total_rooms');

        return view('about', compact('roomCount'));
    }

    public function services()
    {
        $services = Service::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('services', compact('services'));
    }
}
