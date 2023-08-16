<?php

namespace LaravelRepository\Traits;

use Illuminate\Database\Eloquent\Builder;
use LaravelRepository\Abstracts\FiltersAbstract;

trait UseFilter
{
    public function scopeFilter(Builder $builder, FiltersAbstract $filter, array $filters = []): Builder
    {
        return $filter->add($filters)->filter($builder);
    }
}
