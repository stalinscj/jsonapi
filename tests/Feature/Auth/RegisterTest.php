<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_register()
    {
        $user = User::factory()->make();

        $response = $this->postJson(route('api.v1.register',
            [
                'name'                  => $user->name,
                'email'                 => $user->email,
                'password'              => 'password',
                'password_confirmation' => 'password',
                'device_name'           => "$user->name's Phone"
            ]))
            ->assertOk();

        $this->assertDatabaseHas('users', [
            'name'     => $user->name,
            'email'    => $user->email,
        ]);

        $token = $response->json('plain-text-token');

        $this->assertNotNull(
            PersonalAccessToken::findToken($token),
            'The plain text token is invalid'
        );
    }

    /**
     * @test
     */
    public function name_is_required()
    {
        $user = User::factory()->make();

        $this->postJson(route('api.v1.register',
            [
                'name'                  => '',
                'email'                 => $user->email,
                'password'              => 'password',
                'password_confirmation' => 'password',
                'device_name'           => "$user->name's Phone"
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name' => trans('validation.required', ['attribute' => 'name'])
            ]);
    }

    /**
     * @test
     */
    public function email_is_required()
    {
        $user = User::factory()->make();

        $this->postJson(route('api.v1.register',
            [
                'name'                  => $user->name,
                'email'                 => '',
                'password'              => 'password',
                'password_confirmation' => 'password',
                'device_name'           => "$user->name's Phone"
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
        $user = User::factory()->make();

        $this->postJson(route('api.v1.register',
            [
                'name'                  => $user->name,
                'email'                 => 'invalid-email',
                'password'              => 'password',
                'password_confirmation' => 'password',
                'device_name'           => "$user->name's Phone"
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email' => trans('validation.email', ['attribute' => 'email'])
            ]);
    }

    /**
     * @test
     */
    public function email_must_be_unique()
    {
        $user = User::factory()->create();

        $this->postJson(route('api.v1.register',
            [
                'name'                  => $user->name,
                'email'                 => $user->email,
                'password'              => 'password',
                'password_confirmation' => 'password',
                'device_name'           => "$user->name's Phone"
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email' => trans('validation.unique', ['attribute' => 'email'])
            ]);
    }

    /**
     * @test
     */
    public function password_is_required()
    {
        $user = User::factory()->make();

        $this->postJson(route('api.v1.register',
            [
                'name'                  => $user->name,
                'email'                 => $user->email,
                'password'              => '',
                'password_confirmation' => 'password',
                'device_name'           => "$user->name's Phone"
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'password' => trans('validation.required', ['attribute' => 'password'])
            ]);
    }

    /**
     * @test
     */
    public function password_must_be_confirmed()
    {
        $user = User::factory()->make();

        $this->postJson(route('api.v1.register',
            [
                'name'                  => $user->name,
                'email'                 => $user->email,
                'password'              => 'password',
                'password_confirmation' => 'distinct_password',
                'device_name'           => "$user->name's Phone"
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'password' => trans('validation.confirmed', ['attribute' => 'password'])
            ]);
    }

    /**
     * @test
     */
    public function device_name_is_required()
    {
        $user = User::factory()->make();

        $this->postJson(route('api.v1.register',
            [
                'name'                  => $user->name,
                'email'                 => $user->email,
                'password'              => 'password',
                'password_confirmation' => 'password',
                'device_name'           => ''
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'device_name' => trans('validation.required', ['attribute' => 'device name'])
            ]);
    }

}
