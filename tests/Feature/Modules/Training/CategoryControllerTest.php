<?php

namespace Tests\Feature\Modules\Training;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Auth\Entities\User;
use Modules\Training\Entities\Category;
use Modules\Training\Enums\CategoryTypeEnum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $authenticatedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authenticatedUser = User::query()->first();
    }

    #[Test]
    public function it_can_list_all_categories()
    {
        $categories = Category::factory()->count(3)->create();

        $response = $this->actingAs($this->authenticatedUser)
            ->getJson('/api/training/categories');
        
        info($response->json());

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'type']
                ],
                'pagination',
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    #[Test]
    public function it_can_show_a_specific_category()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->authenticatedUser)
            ->getJson("/api/training/categories/{$category->id}");

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'type' => $category->type
                ]
            ]);
    }

    #[Test]
    public function it_can_create_a_new_category()
    {
        $categoryData = [
            'name' => 'Test Category',
            'type' => CategoryTypeEnum::EXERCISES->value
        ];

        $response = $this->actingAs($this->authenticatedUser)
            ->postJson('/api/training/categories', $categoryData);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Test Category',
                    'type' => CategoryTypeEnum::EXERCISES->value
                ]
            ]);

        $this->assertDatabaseHas('training_categories', [
            'name' => 'Test Category',
            'type' => CategoryTypeEnum::EXERCISES->value
        ]);
    }

    #[Test]
    public function it_validates_category_creation()
    {
        $response = $this->actingAs($this->authenticatedUser)
            ->postJson('/api/training/categories', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'type']);
    }

    #[Test]
    public function it_can_update_an_existing_category()
    {
        $category = Category::factory()->create([
            'name' => 'Old Category',
            'type' => CategoryTypeEnum::EXERCISES->value
        ]);

        $response = $this->actingAs($this->authenticatedUser)
            ->putJson("/api/training/categories/{$category->id}", [
                'name' => 'Updated Category',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Updated Category',
                    'type' => CategoryTypeEnum::EXERCISES->value
                ]
            ]);

        $this->assertDatabaseHas('training_categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
            'type' => CategoryTypeEnum::EXERCISES->value
        ]);
    }

    #[Test]
    public function it_validates_category_updates()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->authenticatedUser)
            ->putJson("/api/training/categories/{$category->id}", [
                'name' => '',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    #[Test]
    public function it_can_delete_a_category()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->authenticatedUser)
            ->deleteJson("/api/training/categories/{$category->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertModelMissing($category);
    }
}