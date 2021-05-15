<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateArticlesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function guests_users_cannot_update_articles()
    {
        $article = Article::factory()->create();

        $this->jsonApi()
            ->patch(route('api.v1.articles.update', $article))
            ->assertUnauthorized();
    }

    /**
     * @test
     */
    public function authenticated_users_can_update_their_articles()
    {
        $article = Article::factory()->create();
        
        $attributes = Article::factory()->for($article->user)->raw();

        Sanctum::actingAs($article->user);

        $this->jsonApi()
            ->withData([
                'type'       => 'articles',
                'id'         => $article->getRouteKey(),
                'attributes' => $attributes,
            ])
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(200);
        
        $this->assertDatabaseHas('articles', $attributes);
    }

    /**
     * @test
     */
    public function authenticated_users_cannot_update_others_articles()
    {
        $article = Article::factory()->create();
        
        $attributes = Article::factory()->for($article->user)->raw();

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type'       => 'articles',
                'id'         => $article->getRouteKey(),
                'attributes' => $attributes,
            ])
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(403);
        
        $this->assertDatabaseMissing('articles', $attributes);
    }

    /**
     * @test
     */
    public function can_update_title_only()
    {
        $article = Article::factory()->create();
        
        Sanctum::actingAs($article->user);

        $this->jsonApi()
            ->withData([
                'type'       => 'articles',
                'id'         => $article->getRouteKey(),
                'attributes' => ['title' => 'Title Changed'],
            ])
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(200);
        
        $this->assertDatabaseHas('articles', ['title' => 'Title Changed']);
    }

    /**
     * @test
     */
    public function can_update_slug_only()
    {
        $article = Article::factory()->create();
        
        Sanctum::actingAs($article->user);

        $this->jsonApi()
            ->withData([
                'type'       => 'articles',
                'id'         => $article->getRouteKey(),
                'attributes' => ['slug' => 'slug-changed'],
            ])
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(200);
        
        $this->assertDatabaseHas('articles', ['slug' => 'slug-changed']);
    }
    
}
