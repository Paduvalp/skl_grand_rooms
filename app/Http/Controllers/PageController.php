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

    /**
     * The date shown at the top of the legal pages.
     *
     * The admin can set it in Site Settings whenever the wording changes.
     * Until then we fall back to the date the pages were written, rather
     * than today's date, which would claim a review that never happened.
     */
    private const LEGAL_WRITTEN_ON = '2026-09-16';

    public function privacy()
    {
        return view('legal.privacy', ['updated' => $this->legalUpdatedAt()]);
    }

    public function terms()
    {
        return view('legal.terms', ['updated' => $this->legalUpdatedAt()]);
    }

    private function legalUpdatedAt(): string
    {
        $set = trim((string) \App\Models\Setting::get('legal_updated_at', ''));

        try {
            return \Carbon\Carbon::parse($set !== '' ? $set : self::LEGAL_WRITTEN_ON)
                ->format('j F Y');
        } catch (\Throwable $e) {
            return \Carbon\Carbon::parse(self::LEGAL_WRITTEN_ON)->format('j F Y');
        }
    }
}
