<?php

declare(strict_types=1);

namespace App\Http\Requests\Secretaria;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
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
            'enrollment_id' => ['required', 'integer', 'exists:enrollments,id'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
            'payment_method' => ['required', 'string', 'in:yape,plin,transfer,gateway,cash'],
            'operation_code' => ['nullable', 'string', 'max:80'],
            'voucher' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:3072'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
