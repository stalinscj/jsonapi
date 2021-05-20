<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;


class UserPermissionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_assign_permissions_to_a_user()
    {
        $user = User::factory()->create();
        $permission = Permission::factory()->create();

        $user->givePermissionTo($permission);

        $this->assertCount(1, $user->permissions);
    }

    /**
     * @test
     */
    public function cannot_assign_same_permissions_to_a_user_twice()
    {
        $user = User::factory()->create();
        $permission = Permission::factory()->create();

        $user->givePermissionTo($permission);
        $user->givePermissionTo($permission);

        $this->assertCount(1, $user->permissions);
    }
}
