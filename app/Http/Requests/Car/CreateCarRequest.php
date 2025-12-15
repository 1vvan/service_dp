<?php

namespace App\Http\Requests\Car;

use Illuminate\Foundation\Http\FormRequest;

class CreateCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'model' => ['required', 'integer'],
            'licence_plate' => ['required', 'string', 'max:10'],
            'vin' => ['required', 'string', 'max:17'],
            'year' => ['required', 'integer'],
            'mileage' => ['required', 'integer'],
            'brand_id' => ['required', 'integer'],
            'engine_type' => ['nullable', 'integer'],
            'gearbox_type' => ['nullable', 'integer'],
            'drive_unit_type' => ['nullable', 'integer'],
            'fuel_type' => ['nullable', 'integer'],
        ];
    }
}

