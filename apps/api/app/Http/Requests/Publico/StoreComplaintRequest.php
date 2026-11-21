<?php

declare(strict_types=1);

namespace App\Http\Requests\Publico;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
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
            'consumer_name' => ['required', 'string', 'min:3', 'max:180'],
            'consumer_document_type' => ['required', 'string', 'in:dni,ce,passport,ruc'],
            'consumer_document_number' => ['required', 'string', 'min:6', 'max:20'],
            'consumer_phone' => ['required', 'string', 'min:6', 'max:30'],
            'consumer_email' => ['required', 'string', 'email', 'max:254'],
            'consumer_address' => ['required', 'string', 'min:5', 'max:255'],
            'is_minor' => ['boolean'],
            'representative_name' => ['required_if:is_minor,true', 'nullable', 'string', 'max:180'],
            'representative_address' => ['required_if:is_minor,true', 'nullable', 'string', 'max:255'],
            'representative_phone' => ['required_if:is_minor,true', 'nullable', 'string', 'max:30'],
            'representative_email' => ['required_if:is_minor,true', 'nullable', 'email', 'max:254'],
            'item_type' => ['required', 'string', 'in:product,service'],
            'item_description' => ['nullable', 'string', 'max:500'],
            'claimed_amount' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'complaint_type' => ['required', 'string', 'in:claim,complaint'],
            'detail' => ['required', 'string', 'min:10', 'max:3000'],
            'consumer_request' => ['required', 'string', 'min:5', 'max:1500'],
        ];
    }
}
