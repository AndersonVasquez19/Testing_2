<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string $email
 * @property string|null $dni
 * @property string|null $phone
 * @property string $role
 * @property bool $is_active
 * @property bool $must_change_password
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, HasUuid, Notifiable, SoftDeletes;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_SECRETARY = 'secretary';

    public const ROLE_COORDINATOR = 'coordinator';

    public const ROLE_STUDENT = 'student';

    /** @var list<string> */
    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_SECRETARY,
        self::ROLE_COORDINATOR,
        self::ROLE_STUDENT,
    ];

    protected string $guard_name = 'web';

    /** @var list<string> */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'dni',
        'phone',
        'role',
        'is_active',
        'must_change_password',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    /**
     * @return HasMany<Enrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * @return HasMany<Cohort, $this>
     */
    public function coordinatedCohorts(): HasMany
    {
        return $this->hasMany(Cohort::class, 'coordinator_id');
    }

    /**
     * @return HasMany<AdvisorySlot, $this>
     */
    public function advisorySlotsAsAdvisor(): HasMany
    {
        return $this->hasMany(AdvisorySlot::class, 'advisor_id');
    }

    /**
     * @return HasMany<AdvisorySlot, $this>
     */
    public function advisorySlotsAsStudent(): HasMany
    {
        return $this->hasMany(AdvisorySlot::class, 'student_id');
    }

    /**
     * @return HasMany<ActivityLog, $this>
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function hasPortalRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true) || $this->hasAnyRole($roles);
    }

    public function normalizedWhatsappDigits(): ?string
    {
        if ($this->phone === null || $this->phone === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $this->phone) ?? '';
        if (strlen($digits) === 9 && str_starts_with($digits, '9')) {
            return '51'.$digits;
        }

        if (strlen($digits) >= 10 && strlen($digits) <= 15) {
            return $digits;
        }

        return null;
    }
}
