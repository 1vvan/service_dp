<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BookingStatus extends Model
{
    protected $fillable = ['name'];

    public static function getNewStatusId(): int
    {
        return Cache::rememberForever("booking_status_new_id", function () {
            return self::where('name', 'Нова')->first()->id;
        });
    }

    public static function getConfirmedStatusId(): int
    {
        return Cache::rememberForever("booking_status_confirmed_id", function () {
            return self::where('name', 'Підтверджена')->first()->id;
        });
    }

    public static function getInProgressStatusId(): int
    {
        return Cache::rememberForever("booking_status_in_progress_id", function () {
            return self::where('name', 'В роботі')->first()->id;
        });
    }

    public static function getCompletedStatusId(): int
    {
        return Cache::rememberForever("booking_status_completed_id", function () {
            return self::where('name', 'Виконана')->first()->id;
        });
    }

    public static function getCancelledStatusId(): int
    {
        return Cache::rememberForever("booking_status_cancelled_id", function () {
            return self::where('name', 'Скасована')->first()->id;
        });
    }
}
