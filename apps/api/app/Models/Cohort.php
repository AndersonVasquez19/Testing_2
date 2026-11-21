<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\CohortFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property string $code
 * @property int $program_id
 * @property int|null $coordinator_id
 * @property string $name
 * @property string $modality
 * @property string|null $whatsapp_group_link
 * @property string|null $default_meeting_url
 * @property string|null $location_name
 * @property string|null $location_address
 * @property string|null $location_map_url
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property int $max_capacity
 * @property int $enrolled_count
 * @property string $status
 */
class Cohort extends Model
{
    /** @use HasFactory<CohortFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    public const MODALITY_VIRTUAL = 'virtual';

    public const MODALITY_PRESENTIAL = 'presential';

    public const MODALITY_HYBRID = 'hybrid';

    /** @var list<string> */
    protected $fillable = [
        'uuid',
        'code',
        'program_id',
        'coordinator_id',
        'name',
        'modality',
        'whatsapp_group_link',
        'default_meeting_url',
        'location_name',
        'location_address',
        'location_map_url',
        'start_date',
        'end_date',
        'max_capacity',
        'enrolled_count',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'max_capacity' => 'integer',
            'enrolled_count' => 'integer',
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
    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    /**
     * @return HasMany<CohortSession, $this>
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(CohortSession::class)->orderBy('session_number');
    }

    /**
     * @return HasMany<Enrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function availableSlots(): int
    {
        return max(0, $this->max_capacity - $this->enrolled_count);
    }
}
