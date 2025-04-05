<?php

namespace Modules\Auth\Entities;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as ModelsPermission;

class Permission extends ModelsPermission
{
    /** @use HasFactory<\Database\Factories\Modules\Auth\Entities\PermissionFactory> */
    use HasFactory;
}
