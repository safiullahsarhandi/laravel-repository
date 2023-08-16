<?php

namespace LaravelRepository\Abstracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class FiltersAbstract
{
    protected array $filters = [];

    /**
     * The request.
     *
     * @var Request
     */
    protected Request $request;

    /**
     * Construct filter with request.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Add filters.
     *
     * @param array $filters
     * @return FiltersAbstract
     */
    public function add(array $filters): FiltersAbstract
    {
        $this->filters = array_merge($this->filters, $filters);

        return $this;
    }

    /**
     * Apply filters.
     *
     * @param Builder $builder
     * @return Builder
     */
    public function filter(Builder $builder): Builder
    {
        foreach ($this->getFilters() as $filter => $value) {
            $this->resolveFilter($filter)->filter($builder, $value);
        }

        return $builder;
    }

    /**
     * Get filters to be used.
     *
     * @return array
     */
    protected function getFilters(): array
    {
        return $this->filterFilters($this->filters);
    }

    /**
     * Filter filters that are only in the request.
     *
     * @param array $filters
     * @return array
     */
    protected function filterFilters($filters): array
    {
        return array_filter(
            $this->request->only(array_keys($filters)),
            static function ($var) {
                return !is_null($var) && $var !== [] && $var !== '';
            }
        );
    }

    /**
     * Instantiate a filter.
     *
     * @param string $filter
     * @return mixed
     */
    protected function resolveFilter(string $filter)
    {
        return new $this->filters[$filter];
    }
}
