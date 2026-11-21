<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:180'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'dni' => ['nullable', 'string', 'regex:/^\d{8}$/', 'unique:users,dni'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', Rule::in(User::ROLES)],
            'password' => ['required', 'string', Password::min(8)],
            'is_active' => ['boolean'],
        ];
    }
}
