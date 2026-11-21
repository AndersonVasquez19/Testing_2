<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
        $userParam = $this->route('user');
        $userId = $userParam instanceof User ? $userParam->id : $userParam;

        return [
            'name' => ['sometimes', 'string', 'min:2', 'max:180'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'dni' => ['nullable', 'string', 'regex:/^\d{8}$/', Rule::unique('users', 'dni')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['sometimes', 'string', Rule::in(User::ROLES)],
            'password' => ['nullable', 'string', Password::min(8)],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
