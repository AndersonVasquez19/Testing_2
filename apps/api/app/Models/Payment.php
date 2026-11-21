<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property int $enrollment_id
 * @property string $amount
 * @property string $payment_method
 * @property string|null $operation_code
 * @property string|null $voucher_path
 * @property string|null $voucher_hash
 * @property string $status
 * @property string|null $rejection_reason
 * @property int|null $validated_by
 * @property Carbon|null $validated_at
 * @property string|null $notes
 */
class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory, HasUuid;

    public const METHOD_YAPE = 'yape';

    public const METHOD_PLIN = 'plin';

    public const METHOD_TRANSFER = 'transfer';

    public const METHOD_GATEWAY = 'gateway';

    public const METHOD_CASH = 'cash';

    public const STATUS_PENDING = 'pending';

    public const STATUS_VALIDATED = 'validated';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_REVERSED = 'reversed';

    /** @var list<string> */
    protected $fillable = [
        'uuid',
        'enrollment_id',
        'amount',
        'payment_method',
        'operation_code',
        'voucher_path',
        'voucher_hash',
        'status',
        'rejection_reason',
        'validated_by',
        'validated_at',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'validated_at' => 'datetime',
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
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
