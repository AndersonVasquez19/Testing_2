<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\ProgramFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $uuid
 * @property string $code
 * @property string $name
 * @property string $slug
 * @property string $type
 * @property string $description
 * @property array<int|string, mixed>|null $syllabus_json
 * @property string $price
 * @property int $duration_hours
 * @property string $min_attendance_percentage
 * @property string $min_passing_grade
 * @property string|null $certification_label
 * @property string|null $image_path
 * @property bool $is_published
 * @property string $status
 */
class Program extends Model
{
    /** @use HasFactory<ProgramFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    public const TYPE_COURSE = 'course';

    public const TYPE_TRAINING = 'training';

    public const TYPE_ADVISORY = 'advisory';

    /** @var list<string> */
    protected $fillable = [
        'uuid',
        'code',
        'name',
        'slug',
        'type',
        'description',
        'syllabus_json',
        'price',
        'duration_hours',
        'min_attendance_percentage',
        'min_passing_grade',
        'certification_label',
        'image_path',
        'is_published',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'syllabus_json' => 'array',
            'price' => 'decimal:2',
            'duration_hours' => 'integer',
            'min_attendance_percentage' => 'decimal:2',
            'min_passing_grade' => 'decimal:2',
            'is_published' => 'boolean',
        ];
    }

    /**
     * @return HasMany<Cohort, $this>
     */
    public function cohorts(): HasMany
    {
        return $this->hasMany(Cohort::class);
    }

    /**
     * @return HasMany<AdvisorySlot, $this>
     */
    public function advisorySlots(): HasMany
    {
        return $this->hasMany(AdvisorySlot::class);
    }

    /**
     * @return HasMany<ContactLead, $this>
     */
    public function contactLeads(): HasMany
    {
        return $this->hasMany(ContactLead::class);
    }
}
