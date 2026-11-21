<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\AdvisorySlotFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property int $program_id
 * @property int $advisor_id
 * @property Carbon $slot_date
 * @property string $start_time
 * @property string $end_time
 * @property string $modality
 * @property string|null $meeting_or_location
 * @property bool $is_booked
 * @property int|null $student_id
 * @property int|null $payment_id
 * @property string|null $topic
 */
class AdvisorySlot extends Model
{
    /** @use HasFactory<AdvisorySlotFactory> */
    use HasFactory, HasUuid;

    /** @var list<string> */
    protected $fillable = [
        'uuid',
        'program_id',
        'advisor_id',
        'slot_date',
        'start_time',
        'end_time',
        'modality',
        'meeting_or_location',
        'is_booked',
        'student_id',
        'payment_id',
        'topic',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'slot_date' => 'date',
            'is_booked' => 'boolean',
        ];
    }

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
    public function advisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * @return BelongsTo<Payment, $this>
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
