<?php

namespace App\JsonApi\Articles;

use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{

    /**
     * @var string
     */
    protected $resourceType = 'articles';

    /**
     * @param \App\Models\Article $article
     *      the domain record being serialized.
     * @return string
     */
    public function getId($article)
    {
        return (string) $article->getRouteKey();
    }

    /**
     * @param \App\Models\Article $article
     *      the domain record being serialized.
     * @return array
     */
    public function getAttributes($article)
    {
        return [
            'title'     => $article->title,
            'slug'      => $article->slug,
            'content'   => $article->content,
            'createdAt' => $article->created_at,
            'updatedAt' => $article->updated_at,
        ];
    }
}
