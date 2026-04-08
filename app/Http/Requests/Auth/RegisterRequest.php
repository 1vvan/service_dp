<?php

namespace App\Http\Requests\Auth;

use App\Support\PhoneNormalizer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'string', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone' => ['required', 'string', 'regex:/^\+380\d{9}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Будь ласка, введіть ваше ім\'я',
            'name.min' => 'Ім\'я повинно містити мінімум 2 символи',
            'name.max' => 'Ім\'я не може перевищувати 255 символів',
            'email.required' => 'Будь ласка, введіть електронну пошту',
            'email.email' => 'Будь ласка, введіть коректну електронну пошту',
            'email.unique' => 'Користувач з такою електронною поштою вже існує',
            'password.required' => 'Будь ласка, введіть пароль',
            'password.min' => 'Пароль повинен містити мінімум 6 символів',
            'password.confirmed' => 'Паролі не співпадають',
            'phone.regex' => 'Введіть повний номер у форматі +380 (XX) XXX XX XX',
        ];
    }
}

