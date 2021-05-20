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
            'json-api-v1.resources' => [
                'resource1' => 'class1',
                'resource2' => 'class2',
                'resource3' => 'class3',
                'resource4' => 'class4',
            ]
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

        $this->assertDatabaseCount('permissions', 16);
    }
}
