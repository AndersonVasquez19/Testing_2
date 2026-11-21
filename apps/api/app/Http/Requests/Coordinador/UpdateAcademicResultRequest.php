<?php

declare(strict_types=1);

namespace App\Http\Requests\Coordinador;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAcademicResultRequest extends FormRequest
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
            'final_grade' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'attendance_percentage' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'academic_approved' => ['required', 'boolean'],
        ];
    }
}
