<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GearboxType extends Model
{
    protected $fillable = ['name'];

    public function clientCars(): HasMany
    {
        return $this->hasMany(ClientCar::class, 'gearbox_type_id');
    }
}
