<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Auth\DTOs\RoleDTO;
use Modules\Auth\Http\Requests\StoreRoleRequest;
use Modules\Auth\Http\Requests\UpdateRoleRequest;
use Modules\Auth\Http\Resources\PermissionResource;
use Modules\Auth\Http\Resources\RoleResource;
use Modules\Auth\Services\RoleService;

class RoleController extends Controller
{
    public function __construct (protected readonly RoleService $roleService)
    {
        // 
    }

    public function index()
    {
        $roles = $this->roleService->all();
        return $this->paginatedResponse(RoleResource::collection($roles));
    }
    
    public function show($id)
    {
        $role = $this->roleService->find($id);
        return $this->successResponse(RoleResource::make($role));
    }

    public function store(StoreRoleRequest $request)
    {
        $role = $this->roleService->store(RoleDTO::fromRequest($request->validated()));
        return $this->successResponse(RoleResource::make($role), __("Role Created Successfully"), 201);
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $role = $this->roleService->update($id, RoleDTO::fromRequest($request->validated()));
        return $this->successResponse(RoleResource::make($role), __("Role Updated Successfully"));
    }

    public function destroy($id)
    {
        $this->roleService->destroy($id);
        return $this->successResponse([], __("Role Deleted Successfully"));
    }

    public function getAllPermissions()
    {
        $permissions = $this->roleService->allPermissions();
        return $this->successResponse(PermissionResource::collection($permissions));
    }
}