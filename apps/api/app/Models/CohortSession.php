<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\CohortSessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property int $cohort_id
 * @property int $session_number
 * @property string $title
 * @property Carbon $session_date
 * @property string $start_time
 * @property string $end_time
 * @property string $session_type
 * @property string|null $meeting_url
 * @property string|null $classroom
 * @property string|null $location_name
 * @property string|null $location_map_url
 * @property string|null $recording_url
 * @property string|null $material_pdf_path
 * @property string $status
 */
class CohortSession extends Model
{
    /** @use HasFactory<CohortSessionFactory> */
    use HasFactory, HasUuid;

    public const TYPE_VIRTUAL = 'virtual';

    public const TYPE_PRESENTIAL = 'presential';

    /** @var list<string> */
    protected $fillable = [
        'uuid',
        'cohort_id',
        'session_number',
        'title',
        'session_date',
        'start_time',
        'end_time',
        'session_type',
        'meeting_url',
        'classroom',
        'location_name',
        'location_map_url',
        'recording_url',
        'material_pdf_path',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'session_number' => 'integer',
            'session_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Cohort, $this>
     */
    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    /**
     * @return HasMany<Attendance, $this>
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'session_id');
    }
}
