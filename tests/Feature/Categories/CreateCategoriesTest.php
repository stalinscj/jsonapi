<?php

namespace Tests\Feature\Categories;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateCategoriesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function guests_users_cannot_create_categories()
    {
        $category = Category::factory()->raw();

        $this->jsonApi()
            ->withData([
                'type'       => 'categories',
                'attributes' => $category,
            ])
            ->post(route('api.v1.categories.create'))
            ->assertUnauthorized();
        
        $this->assertDatabaseMissing('categories', $category);
    }

    /**
     * @test
     */
    public function authenticated_users_can_create_categories()
    {
        $category = Category::factory()->raw();

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'categories',
                'attributes' => $category,
            ])
            ->post(route('api.v1.categories.create'))
            ->assertCreated();
        
        $this->assertDatabaseHas('categories', $category);
    }

    /**
     * @test
     */
    public function name_is_required()
    {
        $category = Category::factory()->raw(['name' => '']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'categories',
                'attributes' => $category,
            ])
            ->post(route('api.v1.categories.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/name');
        
        $this->assertDatabaseMissing('categories', $category);
    }

    /**
     * @test
     */
    public function slug_is_required()
    {
        $category = Category::factory()->raw(['slug' => '']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'categories',
                'attributes' => $category,
            ])
            ->post(route('api.v1.categories.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug');
        
        $this->assertDatabaseMissing('categories', $category);
    }

    /**
     * @test
     */
    public function slug_must_be_unique()
    {
        Category::factory()->create(['slug' => 'same-slug']);
        
        $category = Category::factory()->raw(['slug' => 'same-slug']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'categories',
                'attributes' => $category,
            ])
            ->post(route('api.v1.categories.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug');
        
        $this->assertDatabaseMissing('categories', $category);
    }

    /**
     * @test
     */
    public function slug_must_only_contain_letters_numbers_and_dashes()
    {
        $category = Category::factory()->raw(['slug' => '#$%&!']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'categories',
                'attributes' => $category,
            ])
            ->post(route('api.v1.categories.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug');
        
        $this->assertDatabaseMissing('categories', $category);
    }

    /**
     * @test
     */
    public function slug_must_not_contain_underscores()
    {
        $category = Category::factory()->raw(['slug' => 'with_underscore']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'categories',
                'attributes' => $category,
            ])
            ->post(route('api.v1.categories.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug')
            ->assertSee(trans('validation.no_underscores', ['attribute' => 'slug']));
        
        $this->assertDatabaseMissing('categories', $category);
    }

    /**
     * @test
     */
    public function slug_must_not_start_with_dashes()
    {
        $category = Category::factory()->raw(['slug' => '-starts-with-dash']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'categories',
                'attributes' => $category,
            ])
            ->post(route('api.v1.categories.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug')
            ->assertSee(trans('validation.no_starting_dashes', ['attribute' => 'slug']));
        
        $this->assertDatabaseMissing('categories', $category);
    }

    /**
     * @test
     */
    public function slug_must_not_end_with_dashes()
    {
        $category = Category::factory()->raw(['slug' => 'ends-with-dash-']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'categories',
                'attributes' => $category,
            ])
            ->post(route('api.v1.categories.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug')
            ->assertSee(trans('validation.no_ending_dashes', ['attribute' => 'slug']));
        
        $this->assertDatabaseMissing('categories', $category);
    }
}
