<?php

declare(strict_types=1);

namespace App\Http\Requests\Publico;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactLeadRequest extends FormRequest
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
            'full_name' => ['required', 'string', 'min:2', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:180'],
            'phone' => ['required', 'string', 'min:6', 'max:30'],
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
            'subject' => ['nullable', 'string', 'max:180'],
            'message' => ['required', 'string', 'min:5', 'max:3000'],
            'source_path' => ['nullable', 'string', 'max:255'],
        ];
    }
}
