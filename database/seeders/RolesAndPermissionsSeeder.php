<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $rolePermissions = [
            'familia' => [
                'scores.view.child',
                'payments.view.child',
                'payments.submit',
                'communications.view',
                'messages.create',
            ],
            'estudiante' => [
                'scores.view.self',
                'payments.view.self',
                'payments.submit',
                'communications.view',
                'courses.view',
                'messages.create',
            ],
            'docente' => [
                'scores.view.assigned',
                'scores.update.assigned',
                'courses.manage.assigned',
                'communications.view',
                'messages.create',
                'calendar.view',
            ],
            'administrador' => [
                'enrollment.manage',
                'academic.manage',
                'users.manage',
                'communications.manage',
                'calendar.manage',
                'reports.view',
            ],
            'caja' => [
                'enrollment.manage',
                'payments.manage',
                'vouchers.review',
                'receipts.issue',
            ],
            'superadmin' => [],
        ];

        $permissions = collect($rolePermissions)
            ->flatten()
            ->unique()
            ->mapWithKeys(fn (string $name) => [
                $name => Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']),
            ]);

        foreach ($rolePermissions as $name => $permissionNames) {
            $role = Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
            if ($name === 'superadmin') {
                $role->syncPermissions(Permission::all());
            } else {
                $role->syncPermissions($permissions->only($permissionNames)->values());
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
