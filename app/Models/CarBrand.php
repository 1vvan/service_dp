<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarBrand extends Model
{
    protected $fillable = ['name', 'logo'];

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        if (filter_var($this->logo, FILTER_VALIDATE_URL)) {
            return $this->logo;
        }

        if (str_starts_with($this->logo, 'images/')) {
            return asset($this->logo);
        }

        return asset('storage/' . $this->logo);
    }

    public function carModels(): HasMany
    {
        return $this->hasMany(CarModel::class, 'brand_id');
    }
}
