<?php

namespace Database\Factories\Modules\Auth\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Enums\GuardEnum;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Auth\Entities\Permission>
 */
class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
            'guard_name' => $this->faker->randomElement(GuardEnum::values()),
        ];
    }

    public function setGuard(string $guardName = 'api')
    {
        return $this->state(fn () => ['guard_name' => $guardName]);
    }
}
