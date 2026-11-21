<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\CertificateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property int $enrollment_id
 * @property string $verification_uuid
 * @property string $certificate_code
 * @property string|null $pdf_path
 * @property string $qr_payload
 * @property array<string, mixed> $snapshot_data
 * @property int $issued_by
 * @property Carbon $issued_at
 * @property Carbon|null $revoked_at
 * @property int|null $revoked_by
 * @property string|null $revocation_reason
 */
class Certificate extends Model
{
    /** @use HasFactory<CertificateFactory> */
    use HasFactory, HasUuid;

    /** @var list<string> */
    protected $fillable = [
        'uuid',
        'enrollment_id',
        'verification_uuid',
        'certificate_code',
        'pdf_path',
        'qr_payload',
        'snapshot_data',
        'issued_by',
        'issued_at',
        'revoked_at',
        'revoked_by',
        'revocation_reason',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'snapshot_data' => 'array',
            'issued_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Enrollment, $this>
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function revoker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }
}
