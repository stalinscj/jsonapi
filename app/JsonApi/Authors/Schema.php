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

    /**
     * Get author links.
     *
     * @param object $author
     * @param bool   $isPrimary
     * @param array  $includeRelationships A list of relationships that will be included as full authors.
     *
     * @return array
     */
    public function getRelationships($author, $isPrimary, array $includeRelationships)
    {
        return [
            'articles' => [
                self::SHOW_RELATED => true,
                self::SHOW_SELF    => true,
                self::SHOW_DATA    => isset($includeRelationships['articles']),
                self::DATA         => function () use ($author) {
                    return $author->articles;
                }
            ]
        ];
    }
}
