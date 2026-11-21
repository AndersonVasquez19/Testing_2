<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\ContactLeadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $uuid
 * @property string $full_name
 * @property string $email
 * @property string $phone
 * @property int|null $program_id
 * @property string|null $subject
 * @property string $message
 * @property string|null $source_path
 * @property string $status
 * @property int|null $assigned_to
 * @property string|null $notes
 */
class ContactLead extends Model
{
    /** @use HasFactory<ContactLeadFactory> */
    use HasFactory, HasUuid;

    public const STATUS_NEW = 'new';

    public const STATUS_CONTACTED = 'contacted';

    public const STATUS_QUALIFIED = 'qualified';

    public const STATUS_CONVERTED = 'converted';

    public const STATUS_DISCARDED = 'discarded';

    /** @var list<string> */
    protected $fillable = [
        'uuid',
        'full_name',
        'email',
        'phone',
        'program_id',
        'subject',
        'message',
        'source_path',
        'status',
        'assigned_to',
        'notes',
    ];

    /**
     * @return BelongsTo<Program, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
