<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;

class StoreRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', Rule::unique(Role::class, 'name')],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['required', 'integer', Rule::exists(Permission::class, 'id')],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
