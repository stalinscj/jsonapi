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



}
