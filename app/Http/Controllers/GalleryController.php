<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;

class GalleryController extends Controller
{
    public function index()
    {
        $images = GalleryImage::shown()->get();

        // Only offer tabs for categories that actually have photos.
        $categories = array_intersect_key(
            GalleryImage::CATEGORIES,
            $images->pluck('category')->unique()->flip()->all()
        );

        return view('gallery', compact('images', 'categories'));
    }
}
