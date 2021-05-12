<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasSorts
{
    /**
     * Scope a query for apply sort fields
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */ 
    public function scopeApplySorts($query, $sort)
    {
        if (!property_exists($this, 'allowedSorts')) {
            abort(500, 'Set the public property $allowedSorts inside '.get_class($this));
        }

        if ($sort==null) {
            return;
        }

        $sortFields = Str::of($sort)->explode(',');

        foreach ($sortFields as $sortField) {
            $direction = 'asc';

            if (Str::of($sortField)->startsWith('-')) {
                $sortField = Str::of($sortField)->substr(1);
                $direction = 'desc';
            }

            if (!collect($this->allowedSorts)->contains($sortField)) {
                abort(400, "Invalid Query Parameter, $sortField is not allowed.");
            }

            $query->orderBy($sortField, $direction);
        }

        return $query;
    }
}