<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;

class UpdateRoleRequest extends FormRequest
{
    public function rules(): array
    {
        $roleId = $this->route('role');
        return [
            'name' => ['required', 'string', Rule::unique(Role::class, 'name')->ignore($roleId)],
            'guard_name' => ['required', 'string', Rule::in(['web', 'api'])],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['required', 'integer', Rule::exists(Permission::class, 'id')],
        ];
    }

    public function passes($attribute, $value)
    {
        $invalidCount = Permission::whereIn('id', $value)
            ->where('guard_name', '!=', $this->roleGuardName)
            ->count();

        return $invalidCount === 0;
    }

    public function authorize(): bool
    {
        return true;
    }
}
