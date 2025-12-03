<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = ['full_name', 'email', 'phone'];

    public function cars(): HasMany
    {
        return $this->hasMany(ClientCar::class, 'client_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'client_id');
    }
}
