<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientCar extends Model
{
    protected $table = 'client_cars';

    protected $fillable = [
        'client_id',
        'car_model_id',
        'fuel_type_id',
        'engine_type_id',
        'gearbox_type_id',
        'drive_unit_type_id',
        'car_year',
        'mileage',
        'vin',
        'license_plate',
    ];

    protected $appends = ['full_name', 'brand_logo'];

    protected function casts(): array
    {
        return [
            'car_year' => 'integer',
            'mileage' => 'integer',
        ];
    }

    public function getBrandLogoAttribute(): ?string
    {
        return $this->carModel->brand->logo_url;
    }

    public function getFullNameAttribute(): ?string
    {
        if (!$this->relationLoaded('carModel')) {
            $this->load('carModel.brand');
        }

        if (!$this->carModel) {
            return null;
        }

        if (!$this->carModel->relationLoaded('brand')) {
            $this->carModel->load('brand');
        }

        $brandName = $this->carModel->brand ? $this->carModel->brand->name : '';
        $modelName = $this->carModel->name;

        return trim($brandName . ' ' . $modelName);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }

    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class, 'fuel_type_id');
    }

    public function engineType(): BelongsTo
    {
        return $this->belongsTo(EngineType::class, 'engine_type_id');
    }

    public function gearboxType(): BelongsTo
    {
        return $this->belongsTo(GearboxType::class, 'gearbox_type_id');
    }

    public function driveUnitType(): BelongsTo
    {
        return $this->belongsTo(DriveUnitType::class, 'drive_unit_type_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'car_id');
    }
}
