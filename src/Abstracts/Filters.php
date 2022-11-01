<?php

namespace LaravelRepository\Abstracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class Filters
{
    protected $filters = [], $request;

    /** @var Builder $builder */
    protected $builder;

    function __construct(Request $request)
    {
        
        $this->request = $request;
    }

    public function extendRequest(array $array = []){
        $this->request->merge($array);
    }

    public function apply($builder)
    {
        $this->builder = $builder;
        
        foreach($this->getFilters() as $filter => $value)
        {
            if(method_exists($this, $filter))
            {
                if(!is_null($value))
                    $this->$filter($value);
            }
        }

        return $this->builder;
    }

    protected function getFilters()
    {
        return $this->request->only($this->filters);
    }
}
