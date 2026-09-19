<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use App\Models\Review;
use App\Models\Room;
use App\Models\Service;
use App\Support\Seo;

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

        // Shown in the page title, so the cheapest rate appears in Google
        // results. People searching "rooms under 1500" can see it without
        // clicking, which is what earns the click.
        $fromPrice = Seo::fromPrice();

        // "See the place": the six newest photos that are switched on.
        $galleryImages = GalleryImage::where('is_active', true)->latest('id')->take(6)->get();

        $reviews = Review::shown()->take(3)->get();

        return view('home', compact('featuredRooms', 'services', 'fromPrice', 'galleryImages', 'reviews'));
    }
}
