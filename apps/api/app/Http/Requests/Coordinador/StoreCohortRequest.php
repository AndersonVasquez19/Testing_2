<?php

declare(strict_types=1);

namespace App\Http\Requests\Coordinador;

use Illuminate\Foundation\Http\FormRequest;

class StoreCohortRequest extends FormRequest
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
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'coordinator_id' => ['nullable', 'integer', 'exists:users,id'],
            'code' => ['required', 'string', 'max:40', 'unique:cohorts,code'],
            'name' => ['required', 'string', 'min:3', 'max:180'],
            'modality' => ['required', 'string', 'in:virtual,presential,hybrid'],
            'whatsapp_group_link' => ['nullable', 'url', 'max:255'],
            'default_meeting_url' => ['nullable', 'url', 'max:500'],
            'location_name' => ['nullable', 'string', 'max:180'],
            'location_address' => ['nullable', 'string', 'max:255'],
            'location_map_url' => ['nullable', 'url', 'max:500'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'max_capacity' => ['required', 'integer', 'min:1', 'max:1000'],
            'status' => ['sometimes', 'string', 'in:draft,open,in_progress,closed,cancelled'],
        ];
    }
}
