<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FuelType extends Model
{
    protected $fillable = ['name'];

    public function clientCars(): HasMany
    {
        return $this->hasMany(ClientCar::class, 'fuel_type_id');
    }
}
