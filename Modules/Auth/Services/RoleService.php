<?php

namespace Modules\Auth\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Modules\Auth\DTOs\RoleDTO;
use Modules\Auth\Entities\Role;
use Modules\Auth\Entities\Permission;

class RoleService
{
    private const ROLE_CACHE_KEY = 'roles';
    private const PERMISSIONS_CACHE_KEY = 'permissions';

    public function all(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Role::query()->paginate(request()->per_page);
    }

    public function find(int $id): Role
    {
        return Cache::remember(self::ROLE_CACHE_KEY . "_id_{$id}", now()->addMinutes(10), function () use ($id) {
            return Role::query()->with('permissions')->findOrFail($id);
        });
    }

    public function store(RoleDTO $roleDto): Role
    {
        return DB::transaction(function () use ($roleDto): Role {
            $role = Role::create([
                'name' => $roleDto->name,
                'guard_name' => 'sanctum'
            ]);

            if (!empty($roleDto->permissions)) {
                $this->syncPermissionsToRole($role, $roleDto->permissions);
            }

            Cache::forget(self::ROLE_CACHE_KEY . "_id_{$role->id}");

            return $role->load('permissions');
        });
    }

    public function update(int $id, RoleDto $roleDto): Role
    {
        $role = $this->find($id);
        return DB::transaction(function () use ($id, $role, $roleDto): Role {
            $role->update([
                'name' => $roleDto->name,
            ]);

            if (!empty($roleDto->permissions)) {
                $this->syncPermissionsToRole($role, $roleDto->permissions);
            }

            Cache::forget(self::ROLE_CACHE_KEY . "_id_{$id}");

            return $role->load('permissions');
        });
    }

    public function destroy(int $id): bool
    {
        $role = $this->find($id);
        
        return DB::transaction(function () use ($id, $role): bool {

            $result = $role->delete();

            Cache::forget(self::ROLE_CACHE_KEY . "_id_{$id}");

            return $result;
        });
    }

    public function allPermissions(): Collection
    {
        return Cache::remember(self::PERMISSIONS_CACHE_KEY, now()->addMinutes(10), function () {
            return Permission::query()->get();
        });
    }

    private function syncPermissionsToRole(Role $role, array $permissionIds): void
    {
        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $role->syncPermissions($permissions);
    }
}