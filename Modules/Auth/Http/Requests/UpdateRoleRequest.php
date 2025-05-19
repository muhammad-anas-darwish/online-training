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
            'permissions' => ['required', 'array'],
            'permissions.*' => ['required', 'integer', Rule::exists(Permission::class, 'id')],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
