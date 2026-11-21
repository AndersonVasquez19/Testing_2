<?php

declare(strict_types=1);

namespace App\Http\Requests\Secretaria;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
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
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'cohort_id' => ['required', 'integer', 'exists:cohorts,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
