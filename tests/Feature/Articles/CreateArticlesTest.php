<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateArticlesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function guests_users_cannot_create_articles()
    {
        $article = Article::factory()->raw(['user_id' => null]);

        $this->jsonApi()
            ->withData([
                'type'       => 'articles',
                'attributes' => $article,
            ])
            ->post(route('api.v1.articles.create'))
            ->assertUnauthorized();
        
        $this->assertDatabaseMissing('articles', $article);
    }

    /**
     * @test
     */
    public function authenticated_users_can_create_articles()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $article = Article::factory()->raw(['user_id' => null, 'category_id' => null]);

        Sanctum::actingAs($user);

        $this->jsonApi()
            ->withData([
                'type'          => 'articles',
                'attributes'    => $article,
                'relationships' => [
                    'authors' => [
                        'data' => [
                            'id'   => $user->getRouteKey(),
                            'type' => 'authors',
                        ]
                    ],
                    'categories' => [
                        'data' => [
                            'id'   => $category->getRouteKey(),
                            'type' => 'categories',
                        ]
                    ]
                ]
            ])
            ->post(route('api.v1.articles.create'))
            ->assertCreated();
        
        $article['user_id'] = $user->id;
        $article['category_id'] = $category->id;

        $this->assertDatabaseHas('articles', $article);
    }

    /**
     * @test
     */
    public function authenticated_users_cannot_create_articles_on_behalf_of_another_users()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $article = Article::factory()->raw(['user_id' => null, 'category_id' => null]);

        Sanctum::actingAs($user);

        $this->jsonApi()
            ->withData([
                'type'          => 'articles',
                'attributes'    => $article,
                'relationships' => [
                    'authors' => [
                        'data' => [
                            'id'   => User::factory()->create()->getRouteKey(),
                            'type' => 'authors',
                        ]
                    ],
                    'categories' => [
                        'data' => [
                            'id'   => $category->getRouteKey(),
                            'type' => 'categories',
                        ]
                    ]
                ]
            ])
            ->post(route('api.v1.articles.create'))
            ->assertForbidden();
        
        $this->assertDatabaseCount('articles', 0);
    }

    /**
     * @test
     */
    public function title_is_required()
    {
        $article = Article::factory()->raw(['title' => '']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'articles',
                'attributes' => $article,
            ])
            ->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/title');
        
        $this->assertDatabaseMissing('articles', $article);
    }

    /**
     * @test
     */
    public function content_is_required()
    {
        $article = Article::factory()->raw(['content' => '']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'articles',
                'attributes' => $article,
            ])
            ->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/content');
        
        $this->assertDatabaseMissing('articles', $article);
    }

    /**
     * @test
     */
    public function slug_is_required()
    {
        $article = Article::factory()->raw(['slug' => '']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'articles',
                'attributes' => $article,
            ])
            ->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug');
        
        $this->assertDatabaseMissing('articles', $article);
    }

    /**
     * @test
     */
    public function slug_must_be_unique()
    {
        Article::factory()->create(['slug' => 'same-slug']);
        
        $article = Article::factory()->raw(['slug' => 'same-slug']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'articles',
                'attributes' => $article,
            ])
            ->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug');
        
        $this->assertDatabaseMissing('articles', $article);
    }

    /**
     * @test
     */
    public function slug_must_only_contain_letters_numbers_and_dashes()
    {
        $article = Article::factory()->raw(['slug' => '#$%&!']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'articles',
                'attributes' => $article,
            ])
            ->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug');
        
        $this->assertDatabaseMissing('articles', $article);
    }

    /**
     * @test
     */
    public function slug_must_not_contain_underscores()
    {
        $article = Article::factory()->raw(['slug' => 'with_underscore']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'articles',
                'attributes' => $article,
            ])
            ->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug')
            ->assertSee(trans('validation.no_underscores', ['attribute' => 'slug']));
        
        $this->assertDatabaseMissing('articles', $article);
    }

    /**
     * @test
     */
    public function slug_must_not_start_with_dashes()
    {
        $article = Article::factory()->raw(['slug' => '-starts-with-dash']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'articles',
                'attributes' => $article,
            ])
            ->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug')
            ->assertSee(trans('validation.no_starting_dashes', ['attribute' => 'slug']));
        
        $this->assertDatabaseMissing('articles', $article);
    }

    /**
     * @test
     */
    public function slug_must_not_end_with_dashes()
    {
        $article = Article::factory()->raw(['slug' => 'ends-with-dash-']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'articles',
                'attributes' => $article,
            ])
            ->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug')
            ->assertSee(trans('validation.no_ending_dashes', ['attribute' => 'slug']));
        
        $this->assertDatabaseMissing('articles', $article);
    }

    /**
     * @test
     */
    public function categories_is_required()
    {
        $article = Article::factory()->raw(['category_id' => null]);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'articles',
                'attributes' => $article,
            ])
            ->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertJsonFragment(['source' => ['pointer' => '/data']]);
        
        $this->assertDatabaseCount('articles', 0);
    }

    /**
     * @test
     */
    public function categories_must_be_a_relationship_object()
    {
        $article = Article::factory()->raw();

        $article['categories'] = 'invalid';

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'articles',
                'attributes' => $article,
            ])
            ->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('\/data\/attributes\/categories');
        
        $this->assertDatabaseCount('articles', 0);
    }

    /**
     * @test
     */
    public function authors_is_required()
    {
        $article = Article::factory()->raw(['user_id' => null]);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'articles',
                'attributes' => $article,
                'relationships' => [
                    'categories' => [
                        'data' => [
                            'id'   => Category::factory()->create()->getRouteKey(),
                            'type' => 'categories',
                        ]
                    ]
                ]
            ])
            ->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertJsonFragment(['source' => ['pointer' => '/data']]);
        
        $this->assertDatabaseCount('articles', 0);
    }

    /**
     * @test
     */
    public function authors_must_be_a_relationship_object()
    {
        $article = Article::factory()->raw();

        $article['authors'] = 'invalid';

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'articles',
                'attributes' => $article,
                'relationships' => [
                    'categories' => [
                        'data' => [
                            'id'   => Category::factory()->create()->getRouteKey(),
                            'type' => 'categories',
                        ]
                    ]
                ]
            ])
            ->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('\/data\/attributes\/authors');
        
        $this->assertDatabaseCount('articles', 0);
    }

}
