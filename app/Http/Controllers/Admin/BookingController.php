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

        // "Direct" means the guest arrived with no campaign tag and no
        // referring site, so it has to be matched as an absence, not a value.
        if ($request->filled('source')) {
            $source = $request->input('source');

            if ($source === '__direct') {
                $query->whereNull('utm_source')->whereNull('referrer_host');
            } else {
                $query->where('utm_source', $source);
            }
        }

        if ($request->filled('campaign')) {
            $query->where('utm_campaign', $request->input('campaign'));
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

        // Only the sources and campaigns that actually exist are offered as
        // filters, so the dropdowns never list something with no bookings.
        $sources = Booking::whereNotNull('utm_source')
            ->distinct()->orderBy('utm_source')->pluck('utm_source');

        $campaigns = Booking::whereNotNull('utm_campaign')
            ->distinct()->orderBy('utm_campaign')->pluck('utm_campaign');

        return view('admin.bookings.index', compact('bookings', 'rooms', 'sources', 'campaigns'));
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
