<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateArticlesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_create_articles()
    {
        $article = Article::factory()->raw();

        $this->jsonApi()
            ->withData([
                'type'       => 'articles',
                'attributes' => $article,
            ])
            ->post(route('api.v1.articles.create'))
            ->assertCreated();
        
        $this->assertDatabaseHas('articles', $article);
    }

    /**
     * @test
     */
    public function title_is_required()
    {
        $article = Article::factory()->raw(['title' => '']);

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
