<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with('room');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->input('room_id'));
        }

        if ($request->filled('q')) {
            $term = '%'.$request->input('q').'%';
            $query->where(function ($q) use ($term) {
                $q->where('reference', 'like', $term)
                    ->orWhere('customer_name', 'like', $term)
                    ->orWhere('customer_email', 'like', $term)
                    ->orWhere('customer_phone', 'like', $term);
            });
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();
        $rooms = Room::orderBy('name')->get();

        return view('admin.bookings.index', compact('bookings', 'rooms'));
    }

    public function show(Booking $booking)
    {
        $booking->load('room');

        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', Booking::STATUSES)],
            'admin_remark' => ['nullable', 'string', 'max:1000'],
        ]);

        $booking->update($data);

        return back()->with('success', 'Booking marked as '.$data['status'].'.');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted.');
    }
}
