<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A guest's mobile number, saved for marketing texts.
 */
class SmsContact extends Model
{
    protected $fillable = ['name', 'phone', 'note', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Put a typed or imported number into one shape: +91XXXXXXXXXX.
     *
     * 98765 43210, 098765 43210, +91-9876543210 and 919876543210 all come
     * out the same, so the same guest is never stored twice.
     */
    public static function normalise(?string $raw): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $raw) ?? '';

        if (strlen($digits) < 10) {
            return null;
        }

        $last10 = substr($digits, -10);

        // Indian mobile numbers start 6-9. Anything else is a landline or junk.
        return preg_match('/^[6-9]\d{9}$/', $last10) ? '+91'.$last10 : null;
    }

    /** How the number reads on screen: +91 98765 43210 */
    public function prettyPhone(): string
    {
        $digits = substr($this->phone, -10);

        return '+91 '.substr($digits, 0, 5).' '.substr($digits, 5);
    }
}
