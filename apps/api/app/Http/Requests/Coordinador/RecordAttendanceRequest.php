<?php

declare(strict_types=1);

namespace App\Http\Requests\Coordinador;

use Illuminate\Foundation\Http\FormRequest;

class RecordAttendanceRequest extends FormRequest
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
            'records' => ['required', 'array', 'min:1'],
            'records.*.enrollment_id' => ['required', 'integer', 'exists:enrollments,id'],
            'records.*.status' => ['required', 'string', 'in:present,late,absent,justified'],
            'records.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
