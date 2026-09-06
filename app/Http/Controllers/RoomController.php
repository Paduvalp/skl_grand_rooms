<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::where('is_active', true);

        if ($request->filled('guests')) {
            $query->where('capacity', '>=', (int) $request->input('guests'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->input('max_price'));
        }

        $rooms = $query->orderBy('price')->paginate(9)->withQueryString();

        return view('rooms.index', compact('rooms'));
    }

    public function show(Room $room)
    {
        abort_unless($room->is_active, 404);

        $otherRooms = Room::where('is_active', true)
            ->where('id', '!=', $room->id)
            ->orderBy('price')
            ->take(3)
            ->get();

        return view('rooms.show', compact('room', 'otherRooms'));
    }
}
