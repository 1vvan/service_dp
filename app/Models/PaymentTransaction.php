<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    const STATUS_PENDING = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_FAILED = 3;

    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'payment_status',
        'transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_status' => 'integer',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(PaymentStatus::class, 'payment_status');
    }
}
