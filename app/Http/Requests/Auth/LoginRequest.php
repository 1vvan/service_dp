<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'string'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Будь ласка, введіть електронну пошту',
            'email.email' => 'Будь ласка, введіть коректну електронну пошту',
            'password.required' => 'Будь ласка, введіть пароль',
            'password.min' => 'Пароль повинен містити мінімум 6 символів',
        ];
    }
}

