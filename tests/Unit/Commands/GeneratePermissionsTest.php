<?php

namespace Tests\Unit\Commands;

use Tests\TestCase;
use App\Models\Permission;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class GeneratePermissionsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function can_generate_permissions_for_registered_api_resources()
    {
        config([
            'json-api-v1.resources' => array_flip($this->faker->words(4))
        ]);
        
        $this->artisan('generate:permissions')
            ->expectsOutput('Permissions generated!');

        $resources = config('json-api-v1.resources');
        $abilities = Permission::$abilities;

        foreach ($resources as $resource => $class) {
            foreach ($abilities as $ability) {
                $name = "$resource:$ability";
                $this->assertDatabaseHas('permissions', compact('name'));
            }
        }

        $this->artisan('generate:permissions')
            ->expectsOutput('Permissions generated!');

        $this->assertDatabaseCount('permissions', 4*count($abilities));
    }
}
