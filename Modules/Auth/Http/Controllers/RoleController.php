<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
        $this->applyPermissions(
            'roles', 
            ['index', 'show', 'store', 'update', 'destroy'],
            ['getAllPermissions' => 'get-all-permissions'],
        );
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
        return $this->successResponse(RoleResource::make($role))->created('role');
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $role = $this->roleService->update($id, RoleDTO::fromRequest($request->validated()));
        return $this->successResponse(RoleResource::make($role))->updated('role');
    }

    public function destroy($id)
    {
        $this->roleService->destroy($id);
        return $this->successResponse()->deleted('role');
    }

    public function getAllPermissions()
    {
        $permissions = $this->roleService->allPermissions();
        return $this->successResponse(PermissionResource::collection($permissions));
    }

    public function getUserPermissions()
    {
        $permissions = $this->roleService->getUserPermissions(Auth::id());
        return $this->successResponse(PermissionResource::collection($permissions));
    }
}