<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['title', 'slug', 'content', 'category_id', 'user_id'];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the user that owns the article.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that owns the article.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope a query to filter by title
     *
     * @param \Illuminate\Database\Eloquent\Builder  $query
     * @param string
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTitle($query, $title)
    {
        return $query->where('title', 'LIKE', $title);
    }

    /**
     * Scope a query to filter by content
     *
     * @param \Illuminate\Database\Eloquent\Builder  $query
     * @param string
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeContent($query, $content)
    {
        return $query->where('content', 'LIKE', $content);
    }

    /**
     * Scope a query to filter by year
     *
     * @param \Illuminate\Database\Eloquent\Builder  $query
     * @param string
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeYear($query, $year)
    {
        return $query->whereYear('created_at', $year);
    }

    /**
     * Scope a query to filter by month
     *
     * @param \Illuminate\Database\Eloquent\Builder  $query
     * @param string
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMonth($query, $month)
    {
        return $query->whereMonth('created_at', $month);
    }

    /**
     * Scope a query to search
     *
     * @param \Illuminate\Database\Eloquent\Builder  $query
     * @param string
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $terms)
    {
        foreach (Str::of($terms)->explode(' ') as $term) {
            $query->orWhere('title', 'LIKE', $term)
                ->orWhere('content', 'LIKE', $term);
        }

        return $query;
    }

    /**
     * Scope a query to filter by categories
     *
     * @param \Illuminate\Database\Eloquent\Builder  $query
     * @param string
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCategories($query, $values)
    {
        return $query->whereHas('category', function ($query) use ($values) {
            return $query->whereIn('slug', explode(',', $values));
        });
    }

    /**
     * Scope a query to filter by authors
     *
     * @param \Illuminate\Database\Eloquent\Builder  $query
     * @param string
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAuthors($query, $values)
    {
        return $query->whereHas('user', function ($query) use ($values) {
            return $query->whereIn('name', explode(',', $values));
        });
    }

}
