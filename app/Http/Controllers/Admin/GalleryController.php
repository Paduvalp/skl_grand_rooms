<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use App\Support\ImageResizer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GalleryController extends Controller
{
    public function index()
    {
        $images = GalleryImage::orderBy('sort_order')->orderByDesc('id')->paginate(30);

        return view('admin.gallery.index', compact('images'));
    }

    /** Several photos at once, all given the same category and caption. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'images' => ['required', 'array', 'max:20'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'category' => ['required', Rule::in(array_keys(GalleryImage::CATEGORIES))],
            'caption' => ['nullable', 'string', 'max:190'],
        ], [
            'images.*.max' => 'Each photo must be 4 MB or smaller.',
            'images.*.mimes' => 'Photos must be JPG, PNG or WEBP.',
        ], [
            'images' => 'photos',
            'images.*' => 'photo',
        ]);

        // New photos go after the ones already there.
        $order = (int) GalleryImage::max('sort_order');

        foreach ($request->file('images') as $file) {
            GalleryImage::create([
                'image_path' => ImageResizer::store($file, GalleryImage::DIR),
                'caption' => $data['caption'] ?? null,
                'category' => $data['category'],
                'sort_order' => ++$order,
                'is_active' => true,
            ]);
        }

        $count = count($request->file('images'));

        return redirect()->route('admin.gallery.index')
            ->with('success', $count === 1 ? 'Photo uploaded.' : "{$count} photos uploaded.");
    }

    public function update(Request $request, GalleryImage $gallery)
    {
        $data = $request->validate([
            'caption' => ['nullable', 'string', 'max:190'],
            'category' => ['required', Rule::in(array_keys(GalleryImage::CATEGORIES))],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ]);

        $gallery->update($data + ['is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Photo updated.');
    }

    public function toggle(GalleryImage $gallery)
    {
        $gallery->update(['is_active' => ! $gallery->is_active]);

        return back()->with('success', $gallery->is_active ? 'Photo is now shown.' : 'Photo hidden.');
    }

    public function destroy(GalleryImage $gallery)
    {
        ImageResizer::delete($gallery->image_path, GalleryImage::DIR);
        $gallery->delete();

        return back()->with('success', 'Photo deleted.');
    }
}
