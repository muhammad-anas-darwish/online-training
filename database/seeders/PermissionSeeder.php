<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;

class PermissionSeeder extends Seeder
{
    private $permissionGroups = [
        'roles' => ['list', 'show', 'create', 'edit', 'delete', 'get-all-permissions'],
        'training-categories' => ['list', 'show', 'create', 'edit', 'delete'],
    ];

    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        foreach ($this->permissionGroups as $group => $permissions) {  
            foreach ($permissions as $permission) {
                Permission::firstOrCreate([
                    'name' => "{$group}.{$permission}",
                    'guard_name' => 'sanctum'
                ]);
            }
        }

        // Create roles and assign permissions
        $this->createRolesWithPermissions();
    }

    protected function createRolesWithPermissions()
    {
        // Super Admin - gets all permissions for this guard
        $superAdmin = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'sanctum'
        ]);

        // Assign only permissions that belong to this guard
        $superAdmin->givePermissionTo(
            Permission::all()
        );
    }
}