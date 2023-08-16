<?php

namespace LaravelRepository\Traits;


trait UseFilter
{
    public function scopeFilter($query, $filters)
    {
        if ($filters) {

            return $filters->apply($query);
        }
        return $query;
    }
}
