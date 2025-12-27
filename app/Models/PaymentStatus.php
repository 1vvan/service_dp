<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PaymentStatus extends Model
{
    protected $fillable = ['name'];

    public static function getPendingStatusId(): int
    {
        return Cache::rememberForever("payment_status_pending_id", function () {
            return self::where('name', 'Очікується')->first()->id;
        });
    }

    public static function getCompletedStatusId(): int
    {
        return Cache::rememberForever("payment_status_completed_id", function () {
            return self::where('name', 'Завершено')->first()->id;
        });
    }

    public static function getFailedStatusId(): int
    {
        return Cache::rememberForever("payment_status_failed_id", function () {
            return self::where('name', 'Помилка')->first()->id;
        });
    }
}

