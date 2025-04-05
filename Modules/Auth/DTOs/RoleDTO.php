<?php

namespace Modules\Auth\DTOs;

use App\Contracts\DTOInterface;

readonly final class RoleDTO implements DTOInterface
{
    public function __construct(
        public ?string $name = null,
        public ?string $guard_name = 'web',
        public ?array $permissions = [],
    ) {
    }

    public static function fromRequest(array $array): self
    {
        return new self(
            name: $array['name'],
            guard_name: $array['guard_name'],
            permissions: $array['permissions'],
        );
    }
}
