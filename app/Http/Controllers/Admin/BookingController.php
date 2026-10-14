<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDocument;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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

    public function create()
    {
        $booking = new Booking([
            'check_in' => now()->startOfDay(),
            'check_out' => now()->addDay()->startOfDay(),
            'guests' => 1,
            'rooms_count' => 1,
            'status' => 'confirmed',
        ]);

        return view('admin.bookings.create', [
            'booking' => $booking,
            'rooms' => Room::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $booking = Booking::create([
            ...$data,
            'reference' => Booking::makeReference(),
        ]);

        $this->storeDocuments($request, $booking);

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Booking added.');
    }

    public function show(Booking $booking)
    {
        $booking->load('room', 'documents');

        return view('admin.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $booking->load('documents');

        return view('admin.bookings.edit', [
            'booking' => $booking,
            'rooms' => Room::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Booking $booking)
    {
        $booking->update($this->validated($request, $booking));

        // Only this booking's own files, whatever ids were posted.
        $booking->documents()
            ->whereIn('id', (array) $request->input('remove_documents', []))
            ->get()
            ->each->delete();

        $this->storeDocuments($request, $booking);

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Booking updated.');
    }

    /** Opens the file in the browser rather than downloading it. */
    public function document(Booking $booking, BookingDocument $document)
    {
        abort_unless($document->booking_id === $booking->id, 404);
        abort_unless(Storage::disk('local')->exists($document->path), 404);

        return response()->file(Storage::disk('local')->path($document->path), [
            'Content-Type' => $document->mime_type,
            'Content-Disposition' => 'inline; filename="'.addslashes($document->original_name).'"',
        ]);
    }

    public function destroyDocument(Booking $booking, BookingDocument $document)
    {
        abort_unless($document->booking_id === $booking->id, 404);

        $document->delete();

        return back()->with('success', 'Document removed.');
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

    /**
     * Same rules as the public booking form, except the admin may record a
     * past stay, use a hidden room, and set the status and price directly.
     */
    private function validated(Request $request, ?Booking $booking = null): array
    {
        $data = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:190'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1', 'max:100'],
            'rooms_count' => ['required', 'integer', 'min:1', 'max:50'],
            'total_price' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:'.implode(',', Booking::STATUSES)],
            'admin_remark' => ['nullable', 'string', 'max:1000'],
            'additional_guests' => ['nullable', 'array', 'max:50'],
            'additional_guests.*.name' => ['nullable', 'string', 'max:120'],
            'additional_guests.*.phone' => ['nullable', 'string', 'max:30'],
            'documents' => ['nullable', 'array'],
            'documents.*.label' => ['nullable', 'string', 'max:100'],
            'documents.*.files' => ['nullable', 'array'],
            'documents.*.files.*' => ['file', 'mimes:'.implode(',', BookingDocument::MIMES), 'max:5120'],
        ], [
            'documents.*.files.*.mimes' => 'Each document must be an image (JPG, PNG, WEBP) or a PDF.',
            'documents.*.files.*.max' => 'Each document must be 5 MB or smaller.',
        ], [
            'room_id' => 'room',
            'customer_name' => 'name',
            'customer_email' => 'email',
            'customer_phone' => 'phone number',
            'rooms_count' => 'number of rooms',
        ]);

        unset($data['documents']);

        $fileCount = collect($request->file('documents', []))->sum(fn ($row) => count($row['files'] ?? []));

        if ($fileCount > BookingDocument::MAX_PER_SAVE) {
            throw ValidationException::withMessages([
                'documents' => 'Upload up to '.BookingDocument::MAX_PER_SAVE.' files at a time. Save, then add the rest.',
            ]);
        }

        // Rows left empty in the form are dropped rather than stored.
        $guests = collect($data['additional_guests'] ?? [])
            ->map(fn ($g) => ['name' => trim($g['name'] ?? ''), 'phone' => trim($g['phone'] ?? '')])
            ->filter(fn ($g) => $g['name'] !== '' || $g['phone'] !== '')
            ->values()
            ->all();

        $data['additional_guests'] = $guests ?: null;

        $room = Room::findOrFail($data['room_id']);
        $checkIn = Carbon::parse($data['check_in'])->startOfDay();
        $checkOut = Carbon::parse($data['check_out'])->startOfDay();
        $nights = (int) $checkIn->diffInDays($checkOut);

        // Cancelled and completed stays no longer hold a room, so only
        // live bookings are checked against what is free.
        if (in_array($data['status'], ['pending', 'confirmed'], true)) {
            $available = $room->availableCount($checkIn->toDateString(), $checkOut->toDateString(), $booking?->id);

            if ($available < $data['rooms_count']) {
                throw ValidationException::withMessages([
                    'rooms_count' => $available > 0
                        ? "Only {$available} room(s) of this type are free for those dates."
                        : 'This room is fully booked for those dates.',
                ]);
            }
        }

        $data['check_in'] = $checkIn->toDateString();
        $data['check_out'] = $checkOut->toDateString();
        $data['nights'] = $nights;

        // Left blank, the price is worked out from the room rate.
        if ($request->input('total_price') === null || $request->input('total_price') === '') {
            $data['total_price'] = $room->price * $nights * $data['rooms_count'];
        }

        return $data;
    }

    private function storeDocuments(Request $request, Booking $booking): void
    {
        // Each row has an optional label shared by every file picked in it.
        foreach ($request->file('documents', []) as $index => $row) {
            $label = trim((string) $request->input("documents.$index.label")) ?: null;

            foreach ($row['files'] ?? [] as $file) {
                $booking->documents()->create([
                    'label' => $label,
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $file->store('booking-documents/'.$booking->id, 'local'),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }
    }
}
