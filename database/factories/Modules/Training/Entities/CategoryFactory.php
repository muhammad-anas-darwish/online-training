<?php

namespace Database\Factories\Modules\Training\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Training\Enums\CategoryTypeEnum;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Training\Entities\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = \Modules\Training\Entities\Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'type' => fake()->randomElement(CategoryTypeEnum::values()),
        ];
    }
}
