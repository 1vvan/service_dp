<?php

namespace App\Http\Requests\Booking;

use App\Support\PhoneNormalizer;
use Illuminate\Foundation\Http\FormRequest;

class PublicBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('phone')) {
            $this->merge([
                'phone' => PhoneNormalizer::normalize((string) $this->input('phone')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^\+380\d{9}$/'],
            'email' => ['nullable', 'email', 'max:255'],

            'car' => ['required', 'array'],
            'car.brand_id' => ['required', 'integer', 'exists:car_brands,id'],
            'car.car_model_id' => ['required', 'integer', 'exists:car_models,id'],
            'car.year' => ['required', 'integer', 'min:1900', 'max:2100'],
            'car.licence_plate' => ['required', 'string', 'max:10'],
            'car.mileage' => ['required', 'integer', 'min:0'],
            'car.vin' => ['required', 'string', 'max:17'],

            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['required', 'integer', 'exists:services,id'],

            'date' => [
                'required',
                'date_format:Y-m-d H:i:s',
                function ($attribute, $value, $fail) {
                    $selectedDate = \Carbon\Carbon::parse($value);
                    $now = \Carbon\Carbon::now();
                    if ($selectedDate->lt($now)) {
                        $fail('Дата та час не можуть бути в минулому');
                    }
                },
            ],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Введіть ПІБ',
            'phone.required' => 'Введіть номер телефону',
            'phone.regex' => 'Введіть повний номер у форматі +380 (XX) XXX XX XX',
            'car.car_model_id.required' => 'Виберіть модель авто',
            'car.year.required' => 'Вкажіть рік випуску',
            'car.licence_plate.required' => 'Введіть номерний знак',
            'service_ids.required' => 'Виберіть хоча б одну послугу',
            'date.required' => 'Виберіть дату та час',
        ];
    }
}
