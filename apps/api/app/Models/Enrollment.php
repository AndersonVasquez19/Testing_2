<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\EnrollmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property int $cohort_id
 * @property string $enrollment_status
 * @property string|null $reservation_reference
 * @property string|null $reservation_token
 * @property Carbon|null $reservation_expires_at
 * @property bool $added_to_whatsapp
 * @property int|null $whatsapp_added_by
 * @property Carbon|null $whatsapp_added_at
 * @property string|null $final_grade
 * @property string|null $attendance_percentage
 * @property bool $academic_approved
 * @property string|null $notes
 */
class Enrollment extends Model
{
    /** @use HasFactory<EnrollmentFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    public const STATUS_PENDING_PAYMENT = 'pending_payment';

    public const STATUS_ENROLLED = 'enrolled';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    /** @var list<string> */
    protected $fillable = [
        'uuid',
        'user_id',
        'cohort_id',
        'enrollment_status',
        'reservation_reference',
        'reservation_token',
        'reservation_expires_at',
        'added_to_whatsapp',
        'whatsapp_added_by',
        'whatsapp_added_at',
        'final_grade',
        'attendance_percentage',
        'academic_approved',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reservation_expires_at' => 'datetime',
            'added_to_whatsapp' => 'boolean',
            'whatsapp_added_at' => 'datetime',
            'final_grade' => 'decimal:2',
            'attendance_percentage' => 'decimal:2',
            'academic_approved' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<Cohort, $this>
     */
    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function whatsappAddedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'whatsapp_added_by');
    }

    /**
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @return HasMany<Attendance, $this>
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * @return HasOne<Certificate, $this>
     */
    public function activeCertificate(): HasOne
    {
        return $this->hasOne(Certificate::class)->whereNull('revoked_at');
    }

    /**
     * @return HasMany<Certificate, $this>
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
}
