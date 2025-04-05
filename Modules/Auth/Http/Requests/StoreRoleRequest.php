<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Modules\Auth\Enums\GuardEnum;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;

class StoreRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', Rule::unique(Role::class, 'name')],
            'guard_name' => ['required', 'string', new Enum(GuardEnum::class)],
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
