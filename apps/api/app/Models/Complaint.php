<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\ComplaintFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property string $correlative_code
 * @property string $consumer_name
 * @property string $consumer_document_type
 * @property string $consumer_document_number
 * @property string $consumer_phone
 * @property string $consumer_email
 * @property string $consumer_address
 * @property bool $is_minor
 * @property string|null $representative_name
 * @property string|null $representative_address
 * @property string|null $representative_phone
 * @property string|null $representative_email
 * @property string $item_type
 * @property string|null $item_description
 * @property string|null $claimed_amount
 * @property string $complaint_type
 * @property string $detail
 * @property string $consumer_request
 * @property string $status
 * @property Carbon $legal_deadline
 * @property string|null $company_response
 * @property int|null $responded_by
 * @property Carbon|null $responded_at
 * @property string|null $receipt_pdf_path
 */
class Complaint extends Model
{
    /** @use HasFactory<ComplaintFactory> */
    use HasFactory, HasUuid;

    public const STATUS_REGISTERED = 'registered';

    public const STATUS_IN_REVIEW = 'in_review';

    public const STATUS_RESPONDED = 'responded';

    public const STATUS_CLOSED = 'closed';

    /** @var list<string> */
    protected $fillable = [
        'uuid',
        'correlative_code',
        'consumer_name',
        'consumer_document_type',
        'consumer_document_number',
        'consumer_phone',
        'consumer_email',
        'consumer_address',
        'is_minor',
        'representative_name',
        'representative_address',
        'representative_phone',
        'representative_email',
        'item_type',
        'item_description',
        'claimed_amount',
        'complaint_type',
        'detail',
        'consumer_request',
        'status',
        'legal_deadline',
        'company_response',
        'responded_by',
        'responded_at',
        'receipt_pdf_path',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_minor' => 'boolean',
            'claimed_amount' => 'decimal:2',
            'legal_deadline' => 'date',
            'responded_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }
}
