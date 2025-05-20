<?php

namespace Modules\Auth\DTOs;

use App\Contracts\DTOInterface;

readonly final class RoleDTO implements DTOInterface
{
    public function __construct(
        public ?string $name = null,
        public ?string $guard_name = null,
        public ?array $permissions = [],
    ) {
    }

    public static function fromRequest(array $array): self
    {
        return new self(
            name: $array['name'] ?? null,
            guard_name: $array['guard_name'] ?? null,
            permissions: $array['permissions'] ?? [],
        );
    }
}
