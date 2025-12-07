<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'car_id' => ['required', 'integer', 'exists:client_cars,id'],
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
            'car_id.required' => 'Виберіть автомобіль',
            'car_id.exists' => 'Обраний автомобіль не існує',
            'service_ids.required' => 'Виберіть хоча б одну послугу',
            'service_ids.array' => 'Послуги повинні бути масивом',
            'service_ids.min' => 'Виберіть хоча б одну послугу',
            'service_ids.*.exists' => 'Одна з обраних послуг не існує',
            'date.required' => 'Виберіть дату та час',
            'date.date_format' => 'Невірний формат дати',
            'comment.max' => 'Коментар не може перевищувати 1000 символів',
        ];
    }
}

