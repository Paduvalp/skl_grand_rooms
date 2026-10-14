<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * A guest ID or other paper attached to a booking. Files sit on the private
 * disk, so they can only be opened through the admin panel.
 */
class BookingDocument extends Model
{
    public const MIMES = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];

    /** PHP's max_file_uploads drops anything past this in one request. */
    public const MAX_PER_SAVE = 20;

    protected $fillable = [
        'booking_id',
        'label',
        'original_name',
        'path',
        'mime_type',
        'size',
    ];

    protected static function booted(): void
    {
        static::deleted(fn (self $document) => Storage::disk('local')->delete($document->path));
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function sizeLabel(): string
    {
        return $this->size >= 1048576
            ? round($this->size / 1048576, 1).' MB'
            : max(1, round($this->size / 1024)).' KB';
    }
}
