<?php

namespace LaravelRepository\Traits;


trait Filterable{

    public function scopeFilter($query, $filters)
    {   
        if($filters){

            return $filters->apply($query);
        }
        return $query;
    }

}
