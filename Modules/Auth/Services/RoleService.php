<?php

namespace Modules\Auth\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Modules\Auth\DTOs\RoleDTO;
use Modules\Auth\Entities\Role;
use Modules\Auth\Entities\Permission;

class RoleService
{
    private const ROLES_CACHE_PREFIX = 'roles';
    private const PERMISSIONS_CACHE_KEY = 'permissions.all';
    private const CACHE_TTL = 86400; // 1 day

    public function all(): LengthAwarePaginator
    {
        $page = request()->get('page', 1);
        $perPage = request()->get('per_page', 15);
        $cacheKey = $this->getPaginatedRolesCacheKey($page, $perPage);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($perPage) {
            return Role::query()
                ->with('permissions')
                ->paginate($perPage);
        });
    }

    public function find(int $id): Role
    {
        $cacheKey = $this->getRoleCacheKey($id);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($id) {
            return Role::query()
                ->with('permissions')
                ->findOrFail($id);
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

            $this->clearRoleCache($role->id);
            $this->clearPaginatedRolesCache();

            return $role->load('permissions');
        });
    }

    public function update(int $id, RoleDTO $roleDto): Role
    {
        $role = $this->find($id);
        
        return DB::transaction(function () use ($role, $roleDto): Role {
            $role->update([
                'name' => $roleDto->name,
            ]);

            if (!empty($roleDto->permissions)) {
                $this->syncPermissionsToRole($role, $roleDto->permissions);
            }

            $this->clearRoleCache($role->id);
            $this->clearPaginatedRolesCache();

            return $role->load('permissions');
        });
    }

    public function destroy(int $id): bool
    {
        $role = $this->find($id);
        
        return DB::transaction(function () use ($role): bool {
            $result = $role->delete();

            $this->clearRoleCache($role->id);
            $this->clearPaginatedRolesCache();

            return $result;
        });
    }

    public function allPermissions(): Collection
    {
        return Cache::remember(self::PERMISSIONS_CACHE_KEY, self::CACHE_TTL, function () {
            return Permission::query()->get();
        });
    }

    private function syncPermissionsToRole(Role $role, array $permissionIds): void
    {
        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $role->syncPermissions($permissions);
        
        Cache::forget(self::PERMISSIONS_CACHE_KEY);
    }

    private function getRoleCacheKey(int $id): string
    {
        return self::ROLES_CACHE_PREFIX . ".{$id}";
    }

    private function getPaginatedRolesCacheKey(int $page, int $perPage): string
    {
        return self::ROLES_CACHE_PREFIX . ".page.{$page}.per_page.{$perPage}";
    }

    private function clearRoleCache(int $id): void
    {
        Cache::forget($this->getRoleCacheKey($id));
    }

    private function clearPaginatedRolesCache(): void
    {
        Cache::tags([self::ROLES_CACHE_PREFIX . '_pages'])->flush();
    }
}