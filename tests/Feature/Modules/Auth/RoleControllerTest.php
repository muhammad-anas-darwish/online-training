<?php

namespace Tests\Feature\Modules\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Auth\Entities\User;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $authenticatedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authenticatedUser = $this->createUser();
    }

    /** @test */
    public function it_can_list_all_roles()
    {
        $roles = Role::factory()->withPermissions()->count(3)->create();

        $response = $this->actingAs($this->authenticatedUser)
            ->getJson('/api/roles');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'guard_name']
                ],
                'pagination',
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
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
                    'guard_name' => $role->guard_name,
                ]
            ]);
    }

    /** @test */
    public function it_can_create_a_new_role()
    {
        $permission = Permission::factory()->create();
        $roleData = [
            'name' => 'admin',
            'guard_name' => $permission->guard_name,
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

    /** @test */
    public function it_validates_role_creation()
    {
        $response = $this->actingAs($this->authenticatedUser)
            ->postJson('/api/roles', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'permissions']);
    }

    /** @test */
    public function it_can_update_an_existing_role()
    {
        $role = Role::factory()->withPermissions()->create(['name' => 'old-name']);
        $permission = Permission::factory()->setGuard($role->guard_name)->create();
        $updateData = [
            'name' => 'updated-name',
            'guard_name' => $role->guard_name,
            'permissions' => [$permission->id],
        ];

        $response = $this->actingAs($this->authenticatedUser)
            ->putJson("/api/roles/{$role->id}", $updateData);

        $response->assertOk()
            ->assertJson([
                'message' => 'Role Updated Successfully',
                'data' => [
                    'name' => 'updated-name',
                    'guard_name' => $role->guard_name,
                ]
            ]);

        $this->assertDatabaseHas('roles', ['name' => 'updated-name']);
        $this->assertDatabaseHas('role_has_permissions', [
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
    }

    /** @test */
    public function it_can_delete_a_role()
    {
        $role = Role::factory()->create();

        $response = $this->actingAs($this->authenticatedUser)
            ->deleteJson("/api/roles/{$role->id}");

        $response->assertOk()
            ->assertJson(['message' => 'Role Deleted Successfully']);
    }

    /** @test */
    public function it_can_list_all_permissions()
    {
        $permissions = Permission::factory()->count(5)->create();

        $response = $this->actingAs($this->authenticatedUser)
            ->getJson('/api/permissions');

        $response->assertOk()
            ->assertJsonCount(5, 'data');
    }

    protected function createUser()
    {
        $user = User::factory()->create();
        return $user;
    }
}