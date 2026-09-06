<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'short_description',
        'description',
        'price',
        'capacity',
        'bed_type',
        'size',
        'image',
        'amenities',
        'total_rooms',
        'is_featured',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Amenities are stored as one line of comma separated text. */
    public function amenityList(): array
    {
        if (blank($this->amenities)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $this->amenities))));
    }

    public function imageUrl(): string
    {
        if ($this->image && file_exists(public_path('uploads/rooms/'.$this->image))) {
            return asset('uploads/rooms/'.$this->image);
        }

        return asset('images/room-placeholder.svg');
    }

    /**
     * How many of this room type are still free for the given dates.
     */
    public function availableCount(string $checkIn, string $checkOut, ?int $ignoreBookingId = null): int
    {
        $booked = $this->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->sum('rooms_count');

        return max(0, $this->total_rooms - (int) $booked);
    }
}
