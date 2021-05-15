<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
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

        $article = Article::factory()->raw(['user_id' => null]);

        Sanctum::actingAs($user);

        $this->jsonApi()
            ->withData([
                'type'       => 'articles',
                'attributes' => $article,
            ])
            ->post(route('api.v1.articles.create'))
            ->assertCreated();
        
        $this->assertDatabaseHas('articles', array_merge($article, ['user_id' => $user->id]));
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
}
