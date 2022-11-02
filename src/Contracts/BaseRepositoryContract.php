<?php 
namespace LaravelRepository\Contracts;

use Illuminate\Database\Eloquent\Model;
use LaravelRepository\Abstracts\BaseRepository;
use LaravelRepository\Abstracts\Filters;

interface BaseRepositoryContract{

    public function setModel(Model $model);
    
    public function withCount(array $relations = []);
    
    public function with(array $relations = []);

    public function findAll(Filters|null $filter = null);

    public function findById(int $id, Filters|null $filter = null);
    
    public function findOne(Filters|null $filter = null);

    public function paginate(int $perPage = 10, Filters|null $filter = null);

    public function create(array $params);

    public function update(int $id, array $params, Filters|null $filter = null);

    public function delete(int $id, Filters|null $filter = null);

    public function getTotal(Filters|null $filter = null);
    
    /* 
    *  return notification repository instance
    *
    */
    public function notification();

    public function event(string $eventNamespace): BaseRepository;
}

?>