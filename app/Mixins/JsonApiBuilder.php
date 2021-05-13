<?php

namespace App\Mixins;

use Illuminate\Support\Str;

class JsonApiBuilder
{
    /**
     * Apply sort fields
     *
     * @return \Closure
     */ 
    public function applySorts()
    {
        return function ()
        {
            if (!property_exists($this->model, 'allowedSorts')) {
                abort(500, 'Set the public property $allowedSorts inside '.get_class($this->model));
            }

            $sort = request('sort');

            if ($sort==null) {
                return $this;
            }

            $sortFields = Str::of($sort)->explode(',');

            foreach ($sortFields as $sortField) {
                $direction = 'asc';

                if (Str::of($sortField)->startsWith('-')) {
                    $sortField = Str::of($sortField)->substr(1);
                    $direction = 'desc';
                }

                if (!collect($this->model->allowedSorts)->contains($sortField)) {
                    abort(400, "Invalid Query Parameter, $sortField is not allowed.");
                }

                $this->orderBy($sortField, $direction);
            }

            return $this;
        };
    }

    /**
     * Paginate the given query apllying json api spec.
     *
     * @return \Closure
     */
    public function jsonPaginate()
    {
        return function ()
        {
            $perPage  = request('page.size');
            $columns  = ['*'];
            $pageName = 'page[number]';
            $page     = request('page.number');

            $perPage = $perPage == 0 ? PHP_INT_MAX : $perPage;

            return $this->paginate($perPage, $columns, $pageName, $page)
                ->appends(request()->except('page.number'));
        };
    }

}
