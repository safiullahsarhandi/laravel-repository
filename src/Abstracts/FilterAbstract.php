<?php

namespace LaravelRepository\Abstracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

abstract class FilterAbstract
{
    /**
     * Apply filter.
     *
     * @param Builder $builder
     * @param mixed $value
     *
     * @return Builder
     */
    abstract public function filter(Builder $builder, $value): Builder;

    /**
     * Resolve the value used for filtering.
     *
     * @param mixed $key
     * @return mixed
     */
    protected function resolveFilterValue($key)
    {
        return Arr::get($this->mappings(), $key);
    }

    /**
     * Database value mappings.
     *
     * @return array
     */
    protected function mappings(): array
    {
        return [];
    }

    /**
     * Resolve the order direction to be used.
     *
     * @param string $direction
     * @return string
     */
    protected function resolveOrderDirection(string $direction): string
    {
        return Arr::get(
            [
                'desc' => 'desc',
                'asc' => 'asc'
            ],
            $direction,
            'desc'
        );
    }
}
