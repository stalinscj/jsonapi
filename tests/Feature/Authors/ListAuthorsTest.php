<?php

namespace Tests\Feature\Authors;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListAuthorsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_fetch_single_author()
    {
        $author = User::factory()->create();

        $response = $this->jsonApi()->get(route('api.v1.authors.read', $author));

        $response->assertExactJson([
            'data' => [
                'type'       => 'authors',
                'id'         => (string) $author->getRouteKey(),
                'attributes' => [
                    'name'     => $author->name,
                ],
                'links' => [
                    'self' => route('api.v1.authors.read', $author),
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function can_fetch_all_authors()
    {
        $authors = User::factory(3)->create();

        $response = $this->jsonApi()->get(route('api.v1.authors.index'));

        $response->assertJsonFragment([
            'data' => [
                [
                    'type'       => 'authors',
                    'id'         => (string) $authors[0]->getRouteKey(),
                    'attributes' => [
                        'name'     => $authors[0]->name,
                    ],
                    'links' => [
                        'self' => route('api.v1.authors.read', $authors[0])
                    ]
                ],
                [
                    'type'       => 'authors',
                    'id'         => (string) $authors[1]->getRouteKey(),
                    'attributes' => [
                        'name'     => $authors[1]->name,
                    ],
                    'links' => [
                        'self' => route('api.v1.authors.read', $authors[1])
                    ]
                ],
                [
                    'type'       => 'authors',
                    'id'         => (string) $authors[2]->getRouteKey(),
                    'attributes' => [
                        'name'     => $authors[2]->name,
                    ],
                    'links' => [
                        'self' => route('api.v1.authors.read', $authors[2])
                    ]
                ],
            ]
        ]);
    }
}
