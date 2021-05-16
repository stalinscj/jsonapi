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
            'createdAt' => $article->created_at->toAtomString(),
            'updatedAt' => $article->updated_at->toAtomString(),
        ];
    }

    /**
     * Get article links.
     *
     * @param object $article
     * @param bool   $isPrimary
     * @param array  $includeRelationships A list of relationships that will be included as full articles.
     *
     * @return array
     */
    public function getRelationships($article, $isPrimary, array $includeRelationships)
    {
        return [
            'authors' => [
                self::SHOW_RELATED => true,
                self::SHOW_SELF    => true,
                self::SHOW_DATA    => isset($includeRelationships['authors']),
                self::DATA         => function () use ($article) {
                    return $article->user;
                }
            ],
            'categories' => [
                self::SHOW_RELATED => true,
                self::SHOW_SELF    => true,
                self::SHOW_DATA    => isset($includeRelationships['categories']),
                self::DATA         => function () use ($article) {
                    return $article->category;
                }
            ]
        ];
    }
}
