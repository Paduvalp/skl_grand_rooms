<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public const STATUSES = ['pending', 'confirmed', 'cancelled', 'completed'];

    protected $fillable = [
        'reference',
        'room_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'check_in',
        'check_out',
        'nights',
        'guests',
        'rooms_count',
        'total_price',
        'notes',
        'status',
        'admin_remark',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'referrer_host',
        'landing_page',
        'first_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'total_price' => 'decimal:2',
            'first_seen_at' => 'datetime',
        ];
    }

    /** Where this booking came from, in words. "Direct" when nothing is known. */
    public function sourceLabel(): string
    {
        return \App\Support\Attribution::label(
            $this->utm_source,
            $this->utm_medium,
            $this->referrer_host
        );
    }

    /**
     * The broad channel this booking belongs to, with a colour for its badge.
     *
     * @return array{0: string, 1: string}
     */
    public function channel(): array
    {
        return \App\Support\Attribution::channel(
            $this->utm_source,
            $this->utm_medium,
            $this->referrer_host
        );
    }

    /** True when this booking carries campaign tags we can report on. */
    public function hasCampaignTags(): bool
    {
        return (bool) ($this->utm_source || $this->utm_medium || $this->utm_campaign);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public static function makeReference(): string
    {
        do {
            $reference = 'SKL-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(3)));
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'confirmed' => 'bg-success',
            'cancelled' => 'bg-danger',
            'completed' => 'bg-primary',
            default => 'bg-warning text-dark',
        };
    }
}
