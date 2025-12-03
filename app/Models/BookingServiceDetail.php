<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingServiceDetail extends Model
{
    protected $fillable = [
        'booking_service_id',
        'year_modifier',
        'final_price',
    ];

    protected function casts(): array
    {
        return [
            'year_modifier' => 'decimal:2',
            'final_price' => 'decimal:2',
        ];
    }

    public function bookingService(): BelongsTo
    {
        return $this->belongsTo(BookingService::class, 'booking_service_id');
    }
}
