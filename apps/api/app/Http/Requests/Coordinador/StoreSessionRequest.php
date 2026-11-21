<?php

declare(strict_types=1);

namespace App\Http\Requests\Coordinador;

use Illuminate\Foundation\Http\FormRequest;

class StoreSessionRequest extends FormRequest
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
            'session_number' => ['required', 'integer', 'min:1', 'max:200'],
            'title' => ['required', 'string', 'min:2', 'max:200'],
            'session_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i:s,H:i'],
            'end_time' => ['required', 'date_format:H:i:s,H:i', 'after:start_time'],
            'session_type' => ['required', 'string', 'in:virtual,presential'],
            'meeting_url' => ['required_if:session_type,virtual', 'nullable', 'url', 'max:500'],
            'classroom' => ['nullable', 'string', 'max:120'],
            'location_name' => ['nullable', 'string', 'max:180'],
            'location_map_url' => ['nullable', 'url', 'max:500'],
            'recording_url' => ['nullable', 'url', 'max:500'],
            'material_pdf_path' => ['nullable', 'string', 'max:500'],
        ];
    }
}
