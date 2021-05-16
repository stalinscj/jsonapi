<?php

namespace Tests\Feature\Categories;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListCategoriesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_fetch_single_category()
    {
        $category = Category::factory()->create();

        $response = $this->jsonApi()->get(route('api.v1.categories.read', $category));

        $response->assertJson([
            'data' => [
                'type'       => 'categories',
                'id'         => (string) $category->getRouteKey(),
                'attributes' => [
                    'name'      => $category->name,
                    'slug'      => $category->slug,
                    'createdAt' => $category->created_at->toAtomString(),
                    'updatedAt' => $category->updated_at->toAtomString(),
                ],
                'links' => [
                    'self' => route('api.v1.categories.read', $category),
                ]
            ]
        ]);

    }

    /**
     * @test
     */
    public function can_fetch_all_categories()
    {
        $categories = Category::factory(3)->create();

        $response = $this->jsonApi()->get(route('api.v1.categories.index'));

        $response->assertJson([
            'data' => [
                [
                    'type'       => 'categories',
                    'id'         => (string) $categories[0]->getRouteKey(),
                    'attributes' => [
                        'name'      => $categories[0]->name,
                        'slug'      => $categories[0]->slug,
                        'createdAt' => $categories[0]->created_at->toAtomString(),
                        'updatedAt' => $categories[0]->updated_at->toAtomString(),
                    ],
                    'links' => [
                        'self' => route('api.v1.categories.read', $categories[0])
                    ]
                ],
                [
                    'type'       => 'categories',
                    'id'         => (string) $categories[1]->getRouteKey(),
                    'attributes' => [
                        'name'      => $categories[1]->name,
                        'slug'      => $categories[1]->slug,
                        'createdAt' => $categories[1]->created_at->toAtomString(),
                        'updatedAt' => $categories[1]->updated_at->toAtomString(),
                    ],
                    'links' => [
                        'self' => route('api.v1.categories.read', $categories[1])
                    ]
                ],
                [
                    'type'       => 'categories',
                    'id'         => (string) $categories[2]->getRouteKey(),
                    'attributes' => [
                        'name'      => $categories[2]->name,
                        'slug'      => $categories[2]->slug,
                        'createdAt' => $categories[2]->created_at->toAtomString(),
                        'updatedAt' => $categories[2]->updated_at->toAtomString(),
                    ],
                    'links' => [
                        'self' => route('api.v1.categories.read', $categories[2])
                    ]
                ],
            ]
        ]);
    }

}
