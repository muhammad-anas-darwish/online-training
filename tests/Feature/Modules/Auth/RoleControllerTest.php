<?php

namespace Tests\Feature\Modules\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Auth\Entities\User;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $authenticatedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authenticatedUser = User::query()->first();
    }

    #[Test]
    public function it_can_list_all_roles()
    {
        $countRoles = Role::query()->count();
        $roles = Role::factory()->withPermissions()->count(3)->create();

        $response = $this->actingAs($this->authenticatedUser)
            ->getJson('/api/roles');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name']
                ],
                'pagination',
            ]);

        $this->assertCount(3 + $countRoles, $response->json('data'));
    }

    #[Test]
    public function it_can_show_a_specific_role()
    {
        $role = Role::factory()->withPermissions()->create();

        $response = $this->actingAs($this->authenticatedUser)
            ->getJson("/api/roles/{$role->id}");

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                ]
            ]);
    }

    #[Test]
    public function it_can_create_a_new_role()
    {
    $permission = Permission::factory()->create();
        $roleData = [
            'name' => 'admin',
            'permissions' => [$permission->id]
        ];

        $response = $this->actingAs($this->authenticatedUser)
            ->postJson('/api/roles', $roleData);

        $response->assertCreated()
            ->assertJson([
                'message' => 'Role Created Successfully',
                'data' => [
                    'name' => 'admin'
                ]
            ]);

        $this->assertDatabaseHas('roles', ['name' => 'admin']);
        $this->assertDatabaseHas('role_has_permissions', [
            'permission_id' => $permission->id
        ]);
    }

    #[Test]
    public function it_validates_role_creation()
    {
        $response = $this->actingAs($this->authenticatedUser)
            ->postJson('/api/roles', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'permissions']);
    }

    #[Test]
    public function it_can_update_an_existing_role()
    {
        $role = Role::factory()->withPermissions()->create(['name' => 'old-name']);
        $permission = Permission::factory()->create();
        $updateData = [
            'name' => 'updated-name',
            'permissions' => [$permission->id],
        ];

        $response = $this->actingAs($this->authenticatedUser)
            ->putJson("/api/roles/{$role->id}", $updateData);

        $response->assertOk()
            ->assertJson([
                'message' => 'Role Updated Successfully',
                'data' => [
                    'name' => 'updated-name',
                ]
            ]);

        $this->assertDatabaseHas('roles', ['name' => 'updated-name']);
        $this->assertDatabaseHas('role_has_permissions', [
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
    }

    #[Test]
    public function it_can_delete_a_role()
    {
        $role = Role::factory()->create();

        $response = $this->actingAs($this->authenticatedUser)
            ->deleteJson("/api/roles/{$role->id}");

        $response->assertOk()
            ->assertJson(['message' => 'Role Deleted Successfully']);
    }

    #[Test]
    public function it_can_list_all_permissions()
    {
        $response = $this->actingAs($this->authenticatedUser)
            ->getJson('/api/permissions');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name']
                ],
            ]);
    }
}