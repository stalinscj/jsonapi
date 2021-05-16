<?php

namespace Tests\Feature\Categories;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateCategoriesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function guests_users_cannot_update_categories()
    {
        $category = Category::factory()->create();

        $this->jsonApi()
            ->patch(route('api.v1.categories.update', $category))
            ->assertUnauthorized();
    }

    /**
     * @test
     */
    public function authenticated_users_can_update_categories()
    {
        $category = Category::factory()->create();
        
        $attributes = Category::factory()->raw();

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'categories',
                'id'         => $category->getRouteKey(),
                'attributes' => $attributes,
            ])
            ->patch(route('api.v1.categories.update', $category))
            ->assertStatus(200);
        
        $this->assertDatabaseHas('categories', $attributes);
    }

    /**
     * @test
     */
    public function can_update_name_only()
    {
        $category = Category::factory()->create();
        
        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'categories',
                'id'         => $category->getRouteKey(),
                'attributes' => ['name' => 'Title Changed'],
            ])
            ->patch(route('api.v1.categories.update', $category))
            ->assertStatus(200);
        
        $this->assertDatabaseHas('categories', ['name' => 'Title Changed']);
    }

    /**
     * @test
     */
    public function can_update_slug_only()
    {
        $category = Category::factory()->create();
        
        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'categories',
                'id'         => $category->getRouteKey(),
                'attributes' => ['slug' => 'slug-changed'],
            ])
            ->patch(route('api.v1.categories.update', $category))
            ->assertStatus(200);
        
        $this->assertDatabaseHas('categories', ['slug' => 'slug-changed']);
    }
    
}
