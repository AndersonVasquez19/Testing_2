<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /** @var list<string> */
    public const PERMISSIONS = [
        'users.manage',
        'programs.manage',
        'cohorts.view',
        'cohorts.manage',
        'sessions.manage',
        'attendance.record',
        'enrollments.view',
        'enrollments.create',
        'enrollments.approve_academic',
        'payments.view',
        'payments.validate',
        'whatsapp_queue.manage',
        'advisories.manage',
        'leads.manage',
        'complaints.view',
        'complaints.respond',
        'certificates.issue',
        'certificates.revoke',
        'activity_logs.view',
        'student_portal.access',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        $admin = Role::findOrCreate(User::ROLE_ADMIN, 'web');
        $admin->syncPermissions(self::PERMISSIONS);

        $secretary = Role::findOrCreate(User::ROLE_SECRETARY, 'web');
        $secretary->syncPermissions([
            'cohorts.view',
            'enrollments.view',
            'enrollments.create',
            'payments.view',
            'payments.validate',
            'whatsapp_queue.manage',
            'leads.manage',
            'complaints.view',
        ]);

        $coordinator = Role::findOrCreate(User::ROLE_COORDINATOR, 'web');
        $coordinator->syncPermissions([
            'cohorts.view',
            'cohorts.manage',
            'sessions.manage',
            'attendance.record',
            'enrollments.view',
            'enrollments.approve_academic',
            'advisories.manage',
            'certificates.issue',
        ]);

        $student = Role::findOrCreate(User::ROLE_STUDENT, 'web');
        $student->syncPermissions([
            'student_portal.access',
        ]);
    }
}
