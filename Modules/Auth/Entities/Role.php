<?php

namespace Modules\Auth\Entities;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as ModelsRole;

class Role extends ModelsRole
{
    /** @use HasFactory<\Database\Factories\Modules\Auth\Entities\RoleFactory> */
    use HasFactory;

    public function getMorphClass()
    {
        return \Modules\Auth\Entities\Role::class;
    }
}
