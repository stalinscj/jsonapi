<?php

namespace App\JsonApi\Categories;

use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'categories';

    /**
     * @param \App\Models\Category $category
     *      the domain record being serialized.
     * @return string
     */
    public function getId($category)
    {
        return (string) $category->getRouteKey();
    }

    /**
     * @param \App\Models\Category $category
     *      the domain record being serialized.
     * @return array
     */
    public function getAttributes($category)
    {
        return [
            'name'      => $category->name,
            'slug'      => $category->slug,
            'createdAt' => $category->created_at->toAtomString(),
            'updatedAt' => $category->updated_at->toAtomString(),
        ];
    }
}
