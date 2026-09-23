<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One text sent from Admin > SMS Marketing, kept as a record.
 */
class SmsMessage extends Model
{
    protected $fillable = [
        'message',
        'numbers',
        'recipients',
        'parts',
        'cost',
        'status',
        'error',
        'mode',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** @return array<int, string> */
    public function numberList(): array
    {
        return array_values(array_filter(preg_split('/\s*,\s*/', (string) $this->numbers) ?: []));
    }

    public function wasSent(): bool
    {
        return $this->status === 'sent';
    }

    public function statusBadgeClass(): string
    {
        return $this->wasSent() ? 'bg-success' : 'bg-danger';
    }
}
