<?php

namespace LaravelRepository\Contracts;

use Illuminate\Database\Eloquent\Model;
use LaravelRepository\Abstracts\BaseRepository;
use LaravelRepository\Abstracts\Filters;
use LaravelRepository\Abstracts\FiltersAbstract;

interface BaseRepositoryContract
{

    public function setModel(Model $model);

    public function withCount(array $relations = []);

    public function with(array $relations = []);

    public function findAll(Filters|FiltersAbstract|null $filter = null);

    public function findById(int $id, Filters|FiltersAbstract|null $filter = null);

    public function findOne(Filters|FiltersAbstract|null $filter = null);

    public function paginate(int $perPage = 10, Filters|FiltersAbstract|null $filter = null);

    public function create(array $params);

    public function update(int $id, array $params, Filters|FiltersAbstract|null $filter = null);

    public function delete(int $id, Filters|FiltersAbstract|null $filter = null);

    public function select(mixed $selectValue);

    public function getTotal(Filters|FiltersAbstract|null $filter = null);

    /*
    *  return notification repository instance
    *
    */
    public function notification();

    public function event(string $eventNamespace): BaseRepository;
}
