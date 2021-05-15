<?php

namespace App\JsonApi\Authors;

use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{

    /**
     * @var string
     */
    protected $resourceType = 'authors';

    /**
     * @param \App\Models\User $author
     *      the domain record being serialized.
     * @return string
     */
    public function getId($author)
    {
        return (string) $author->getRouteKey();
    }

    /**
     * @param \App\Models\User $author
     *      the domain record being serialized.
     * @return array
     */
    public function getAttributes($author)
    {
        return [
            'name' => $author->name,
        ];
    }
}
