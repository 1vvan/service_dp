<?php

namespace App\Support;

final class PhoneNormalizer
{
    public static function normalize(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $value = trim($value);
        if ($value === '') {
            return '';
        }

        $digits = preg_replace('/\D/', '', $value);
        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '380')) {
            $digits = substr($digits, 3);
        } elseif (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        $digits = substr($digits, 0, 9);

        if ($digits === '') {
            return '';
        }

        return '+380'.$digits;
    }
}
