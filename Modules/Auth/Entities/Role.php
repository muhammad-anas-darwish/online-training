<?php

namespace Modules\Auth\Entities;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as ModelsRole;

class Role extends ModelsRole
{
    /** @use HasFactory<\Database\Factories\Modules\Auth\Entities\RoleFactory> */
    use HasFactory, Filterable;

    protected static array $filterableColumns = ['name'];
    protected static array $searchableColumns = ['name'];

    public function getMorphClass()
    {
        return \Modules\Auth\Entities\Role::class;
    }
}
