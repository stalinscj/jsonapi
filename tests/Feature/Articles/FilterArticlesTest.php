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
}
