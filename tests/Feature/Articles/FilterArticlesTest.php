<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FilterArticlesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_filter_articles_by_title()
    {
        Article::factory()
            ->sequence(
                ['title' => 'Implements JsonApi on Laravel'],
                ['title' => 'Another Article']
            )
            ->create();

        $url = route('api.v1.articles.index', ['filter[title]' => '%JsonApi%']);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Implements JsonApi on Laravel')
            ->assertDontSee('Another Article');
    }

    /**
     * @test
     */
    public function can_filter_articles_by_content()
    {
        Article::factory()
            ->sequence(
                ['content' => '<div>Filter by Content Test<div>'],
                ['content' => '<div>Another Article<div>']
            )
            ->create();

        $url = route('api.v1.articles.index', ['filter[content]' => '%by%']);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Filter by Content Test')
            ->assertDontSee('Another Article');
    }

    /**
     * @test
     */
    public function can_filter_articles_by_year()
    {
        Article::factory(2)
            ->sequence(
                ['title' => 'Newer Article', 'created_at' => now()->year(2020)],
                ['title' => 'Another Article', 'created_at' => now()->year(2019)]
            )
            ->create();

        $url = route('api.v1.articles.index', ['filter[year]' => 2020]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Newer Article')
            ->assertDontSee('Another Article');
    }

    /**
     * @test
     */
    public function can_filter_articles_by_month()
    {
        Article::factory(2)
            ->sequence(
                ['title' => 'Article From May', 'created_at' => now()->month(5)],
                ['title' => 'Another Article From May', 'created_at' => now()->month(5)],
                ['title' => 'Article From June', 'created_at' => now()->month(6)]
            )
            ->create();

        $url = route('api.v1.articles.index', ['filter[month]' => 5]);

        $this->getJson($url)
            ->assertJsonCount(2, 'data')
            ->assertSee('Article From May')
            ->assertSee('Another Article From May')
            ->assertDontSee('Article From June');
    }

    /** 
     * @test 
     * */
    public function cannot_filter_articles_by_unknown_filters()
    {
        Article::factory()->create();

        $url = route('api.v1.articles.index', ['filter[unknown]' => 2]);

        $this->getJson($url)->assertStatus(400);
    }

    /**
     * @test
     */
    public function can_search_articles_by_title_and_content()
    {
        Article::factory(3)
            ->sequence(
                ['title' => 'Laravel Article', 'content' => 'Article Content'],
                ['title' => 'PHP Article',     'content' => 'Laravel Content'],
                ['title' => 'Another Article', 'content' => 'Another Content']
            )
            ->create();

        $url = route('api.v1.articles.index', ['filter[search]' => '%Laravel%']);

        $this->getJson($url)
            ->assertJsonCount(2, 'data')
            ->assertSee('Laravel Article')
            ->assertSee('PHP Article')
            ->assertDontSee('Another Article');
    }

    /**
     * @test
     */
    public function can_search_articles_by_title_and_content_with_multiple_terms()
    {
        Article::factory(3)
            ->sequence(
                ['title' => 'Laravel Article', 'content' => 'Article Content'],
                ['title' => 'PHP Article',     'content' => 'Laravel Content'],
                ['title' => 'Docker Article',  'content' => 'Containers HUB.'],
                ['title' => 'Another Article', 'content' => 'Another Content']

            )
            ->create();

        $url = route('api.v1.articles.index', ['filter[search]' => '%Laravel% %Docker%']);

        $this->getJson($url)
            ->assertJsonCount(3, 'data')
            ->assertSee('Laravel Article')
            ->assertSee('PHP Article')
            ->assertSee('Docker Article')
            ->assertDontSee('Another Article');
    }
}
