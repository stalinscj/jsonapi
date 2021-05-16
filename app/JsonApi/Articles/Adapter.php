<?php

namespace App\JsonApi\Articles;

use App\Models\Article;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use CloudCreativity\LaravelJsonApi\Eloquent\AbstractAdapter;
use CloudCreativity\LaravelJsonApi\Pagination\StandardStrategy;

class Adapter extends AbstractAdapter
{
    /**
     * Mapping of JSON API attribute field names to model keys.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Mapping of JSON API include paths to model relationship paths.
     *
     * @var array
     */
    protected $includePaths = [
        'authors' => 'user',
    ];

    /**
     * Mapping of JSON API filter names to model scopes.
     *
     * @var array
     */
    protected $filterScopes = [];

    /**
     * Adapter constructor.
     *
     * @param StandardStrategy $paging
     */
    public function __construct(StandardStrategy $paging)
    {
        parent::__construct(new Article, $paging);
    }

    /**
     * @param Builder $query
     * @param Collection $filters
     * @return void
     */
    protected function filter($query, Collection $filters)
    {
        $this->filterWithScopes($query, $filters);
    }

    /**
     * Fill the article attributes
     *
     * @param $article
     * @param Collection $attributes
     * @return void
     */
    protected function fillAttributes($article, Collection $attributes)
    {
        $article->fill($attributes->toArray());
        $article->user_id = auth()->id();
    }

    /**
     * Get the author that owns the article.
     * 
     * @return \CloudCreativity\LaravelJsonApi\Eloquent\BelongsTo
     */
    public function authors()
    {
        return $this->belongsTo('user');
    }

}
