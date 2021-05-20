<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Permission;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_login_with_valid_credentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('api.v1.login',
            [
                'email'       => $user->email,
                'password'    => 'password',
                'device_name' => "$user->name's Phone"
            ]))
            ->assertOk();

        $token = $response->json('plain-text-token');

        $this->assertNotNull(
            PersonalAccessToken::findToken($token),
            'The plain text token is invalid'
        );
    }

    /**
     * @test
     */
    public function user_permissions_are_assigned_as_abilities_to_the_token_response()
    {
        $user = User::factory()->create();

        $permissions = Permission::factory(3)
            ->create()
            ->each(function ($permission) use ($user) {
                $user->givePermissionTo($permission);
            });

        $response = $this->postJson(
                route('api.v1.login', ['email' => $user->email, 'password' => 'password', 'device_name' => '.'])
            )
            ->assertOk();
            
        $token = PersonalAccessToken::findToken($response->json('plain-text-token'));
            
        $permissions->each(function ($permission) use ($token) {
            $this->assertTrue($token->can($permission->name));
        });
        
        $this->assertFalse($token->can('unassigned-permission'));

    }

    /**
     * @test
     */
    public function cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create();

        $this->postJson(route('api.v1.login',
            [
                'email'       => $user->email,
                'password'    => 'wrong-password',
                'device_name' => "$user->name's Phone"
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email' => trans('auth.failed')
            ]);
        
        $this->postJson(route('api.v1.login',
            [
                'email'       => 'wrongg@email.com',
                'password'    => 'password',
                'device_name' => "$user->name's Phone"
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email' => trans('auth.failed')
            ]);
    }

    /**
     * @test
     */
    public function email_is_required()
    {
        $user = User::factory()->create();

        $this->postJson(route('api.v1.login',
            [
                'email'       => '',
                'password'    => 'password',
                'device_name' => "$user->name's Phone"
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email' => trans('validation.required', ['attribute' => 'email'])
            ]);
    }

    /**
     * @test
     */
    public function email_must_be_valid()
    {
        $user = User::factory()->create();

        $this->postJson(route('api.v1.login',
            [
                'email'       => 'invalid-email',
                'password'    => 'password',
                'device_name' => "$user->name's Phone"
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email' => trans('validation.email', ['attribute' => 'email'])
            ]);
    }

    /**
     * @test
     */
    public function password_is_required()
    {
        $user = User::factory()->create();

        $this->postJson(route('api.v1.login',
            [
                'email'       => $user->email,
                'password'    => '',
                'device_name' => "$user->name's Phone"
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'password' => trans('validation.required', ['attribute' => 'password'])
            ]);
    }

    /**
     * @test
     */
    public function device_name_is_required()
    {
        $user = User::factory()->create();

        $this->postJson(route('api.v1.login',
            [
                'email'       => $user->email,
                'password'    => 'password',
                'device_name' => ''
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'device_name' => trans('validation.required', ['attribute' => 'device name'])
            ]);
    }

    /**
     * @test
     */
    public function cannot_login_twice()
    {
        $user = User::factory()->create();

        $token = $user->createToken($user->name)->plainTextToken;

        $this->withHeader('Authorization', "Bearer $token")
            ->postJson(route('api.v1.login'))
            ->assertNoContent();
    }
}
