<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $rooms = Room::where('is_active', true)->orderBy('price')->get();

        $selectedRoomId = null;

        if ($request->filled('room')) {
            $selectedRoomId = optional(
                Room::where('slug', $request->input('room'))->first()
            )->id;
        }

        return view('booking.create', compact('rooms', 'selectedRoomId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:190'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1', 'max:20'],
            'rooms_count' => ['required', 'integer', 'min:1', 'max:10'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [], [
            'room_id' => 'room',
            'customer_name' => 'name',
            'customer_email' => 'email',
            'customer_phone' => 'phone number',
            'rooms_count' => 'number of rooms',
        ]);

        $room = Room::where('is_active', true)->findOrFail($data['room_id']);

        $checkIn = Carbon::parse($data['check_in'])->startOfDay();
        $checkOut = Carbon::parse($data['check_out'])->startOfDay();
        $nights = (int) $checkIn->diffInDays($checkOut);

        if ($nights < 1) {
            throw ValidationException::withMessages([
                'check_out' => 'Your stay must be at least one night.',
            ]);
        }

        if ($nights > 60) {
            throw ValidationException::withMessages([
                'check_out' => 'For stays longer than 60 nights please call us directly.',
            ]);
        }

        $maxGuests = $room->capacity * $data['rooms_count'];

        if ($data['guests'] > $maxGuests) {
            throw ValidationException::withMessages([
                'guests' => "This room holds {$room->capacity} guest(s) each, so {$data['rooms_count']} room(s) can take up to {$maxGuests} guest(s).",
            ]);
        }

        $available = $room->availableCount($checkIn->toDateString(), $checkOut->toDateString());

        if ($available < $data['rooms_count']) {
            throw ValidationException::withMessages([
                'rooms_count' => $available > 0
                    ? "Only {$available} room(s) of this type are free for those dates."
                    : 'Sorry, this room is fully booked for those dates. Please pick other dates or another room.',
            ]);
        }

        $booking = Booking::create([
            'reference' => Booking::makeReference(),
            'room_id' => $room->id,
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'],
            'check_in' => $checkIn->toDateString(),
            'check_out' => $checkOut->toDateString(),
            'nights' => $nights,
            'guests' => $data['guests'],
            'rooms_count' => $data['rooms_count'],
            'total_price' => $room->price * $nights * $data['rooms_count'],
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('booking.success', $booking->reference);
    }

    public function success(string $reference)
    {
        $booking = Booking::with('room')->where('reference', $reference)->firstOrFail();

        return view('booking.success', compact('booking'));
    }

    public function statusForm()
    {
        return view('booking.status', ['booking' => null]);
    }

    public function statusLookup(Request $request)
    {
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:32'],
            'customer_email' => ['required', 'email', 'max:190'],
        ], [], ['customer_email' => 'email']);

        $booking = Booking::with('room')
            ->where('reference', trim($data['reference']))
            ->where('customer_email', trim($data['customer_email']))
            ->first();

        if (! $booking) {
            return back()->withInput()
                ->with('error', 'No booking found for that reference and email. Please check and try again.');
        }

        return view('booking.status', compact('booking'));
    }
}
