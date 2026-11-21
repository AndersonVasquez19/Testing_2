<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgramRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:30', 'unique:programs,code'],
            'name' => ['required', 'string', 'min:3', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', 'unique:programs,slug'],
            'type' => ['required', 'string', 'in:course,training,advisory'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'syllabus_json' => ['nullable', 'array'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'duration_hours' => ['required', 'integer', 'min:1', 'max:5000'],
            'min_attendance_percentage' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'min_passing_grade' => ['sometimes', 'numeric', 'min:0', 'max:20'],
            'certification_label' => ['nullable', 'string', 'max:255'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'is_published' => ['boolean'],
            'status' => ['sometimes', 'string', 'in:active,inactive,archived'],
        ];
    }
}
