<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RespondComplaintRequest extends FormRequest
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
            'company_response' => ['required', 'string', 'min:10', 'max:4000'],
            'status' => ['sometimes', 'string', 'in:responded,closed'],
        ];
    }
}
