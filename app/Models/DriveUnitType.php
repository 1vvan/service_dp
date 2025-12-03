<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DriveUnitType extends Model
{
    protected $fillable = ['name'];

    public function clientCars(): HasMany
    {
        return $this->hasMany(ClientCar::class, 'drive_unit_type_id');
    }
}
