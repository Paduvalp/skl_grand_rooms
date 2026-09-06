<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::orderBy('name')->paginate(12);

        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('admin.rooms.create', ['room' => new Room(['total_rooms' => 1, 'capacity' => 2, 'is_active' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['image'] = $this->handleImage($request);

        Room::create($data);

        return redirect()->route('admin.rooms.index')->with('success', 'Room added.');
    }

    public function edit(Room $room)
    {
        return view('admin.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $data = $this->validated($request);

        if ($data['name'] !== $room->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $room->id);
        }

        if ($newImage = $this->handleImage($request)) {
            $this->deleteImage($room->image);
            $data['image'] = $newImage;
        }

        $room->update($data);

        return redirect()->route('admin.rooms.index')->with('success', 'Room updated.');
    }

    public function destroy(Room $room)
    {
        $this->deleteImage($room->image);
        $room->delete();

        return redirect()->route('admin.rooms.index')->with('success', 'Room deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'string', 'max:60'],
            'short_description' => ['nullable', 'string', 'max:300'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'bed_type' => ['nullable', 'string', 'max:80'],
            'size' => ['nullable', 'string', 'max:60'],
            'amenities' => ['nullable', 'string', 'max:800'],
            'total_rooms' => ['required', 'integer', 'min:1', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        unset($data['image']);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function handleImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $file = $request->file('image');
        $name = Str::random(20).'.'.$file->getClientOriginalExtension();
        $file->move(public_path('uploads/rooms'), $name);

        return $name;
    }

    private function deleteImage(?string $image): void
    {
        if ($image && file_exists(public_path('uploads/rooms/'.$image))) {
            @unlink(public_path('uploads/rooms/'.$image));
        }
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'room';
        $slug = $base;
        $i = 2;

        while (Room::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
