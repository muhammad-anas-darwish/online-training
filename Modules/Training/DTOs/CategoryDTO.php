<?php

namespace Modules\Training\DTOs;

use App\Contracts\DTOInterface;

readonly final class CategoryDTO implements DTOInterface
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $type = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) { }

    public static function fromRequest(array $array): self
    {
        return new self(
            id: $array['id'] ?? null,
            name: $array['name'] ?? null,
            type: $array['type'] ?? null,
            created_at: $array['created_at'] ?? null,
            updated_at: $array['updated_at'] ?? null,
        );
    }
}