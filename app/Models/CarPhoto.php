<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CarPhoto extends Model
{
    protected $table = 'client_car_photos';

    protected $fillable = ['client_car_id', 'path', 'sort_order'];

    protected $appends = ['url'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    public function clientCar(): BelongsTo
    {
        return $this->belongsTo(ClientCar::class, 'client_car_id');
    }
}
