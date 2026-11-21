<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'dni' => $this->dni,
            'phone' => $this->phone,
            'wa_digits' => $this->normalizedWhatsappDigits(),
            'role' => $this->role,
            'is_active' => $this->is_active,
            'must_change_password' => $this->must_change_password,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
