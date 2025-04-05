<?php

namespace Database\Factories\Modules\Auth\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;
use Modules\Auth\Enums\GuardEnum;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Auth\Entities\Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

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

    public function withPermissions($permissions = [])
    {
        return $this->afterCreating(function (Role $role) use ($permissions) {
            // Get the guard name from the role
            $guardName = $role->guard_name;
            
            $permissions = empty($permissions) 
                ? Permission::factory()
                    ->count(3)
                    ->setGuard($guardName) // Set same guard as role
                    ->create()
                : $permissions;
            
            $role->givePermissionTo($permissions);
        });
    }
}
