<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'manager_id',
        'client_id',
        'car_id',
        'status_id',
        'date',
        'description',
        'total_price',
        'price_calculation',
    ];

    protected $appends = ['status_name'];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'total_price' => 'decimal:2',
            'price_calculation' => 'array',
        ];
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        
        if (isset($array['date']) && $this->date) {
            $array['date'] = $this->date->format('d.m.Y H:i');
            $array['created_at'] = $this->created_at->format('d.m.Y H:i');
            $array['updated_at'] = $this->updated_at->format('d.m.Y H:i');
        }
        
        return $array;
    }

    public function getStatusNameAttribute(): string
    {
        return $this->status->name;
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(ClientCar::class, 'car_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(BookingStatus::class, 'status_id');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'booking_services', 'booking_id', 'service_id')
            ->withPivot('id')
            ->withTimestamps();
    }

    public function bookingServices(): HasMany
    {
        return $this->hasMany(BookingService::class, 'booking_id');
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'booking_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BookingComment::class, 'booking_id');
    }
}
