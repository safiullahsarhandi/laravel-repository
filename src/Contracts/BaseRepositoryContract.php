<?php 
namespace LaravelRepository\Contracts;

use Illuminate\Database\Eloquent\Model;
use LaravelRepository\Abstracts\Filters;

interface BaseRepositoryContract{

    public function setModel(Model $model);
    
    public function withCount($relations = []);
    
    public function findAll(Filters|null $filter = null, array $relations = []);

    public function findById(int $id, array $relations = [], Filters|null $filter = null);
    
    public function findOne(array $relations = [], Filters|null $filter = null);

    public function paginate(int $perPage = 10, array $relations = [], Filters|null $filter = null);

    public function create(array $params);

    public function update(int $id, array $params, Filters|null $filter = null);

    public function delete(int $id, Filters|null $filter = null);

    public function getTotal(Filters|null $filter = null);
    
    /* 
    *  return notification repository instance
    *
    */
    public function notification();
}
?>