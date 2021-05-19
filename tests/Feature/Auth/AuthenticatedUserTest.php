<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticatedUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_fetch_the_authenticated_user()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->getJson(route('api.v1.user'))
            ->assertOk()
            ->assertJson([
                'email' => $user->email,
            ]);
    }

    /**
     * @test
     */
    public function guests_cannot_fetch_any_user()
    {
        $this->getJson(route('api.v1.user'))
            ->assertUnauthorized();
    }
}
