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
        ];
    }
}

