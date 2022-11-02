<?php 
namespace LaravelRepository\Contracts;

use Illuminate\Database\Eloquent\Model;
use LaravelRepository\Contracts\BaseRepositoryContract;

interface EventContract{
   
    public function beforeCreate(BaseRepositoryContract $repository,array $params = []);

    public function created(Model $model);

    public function beforeUpdate(BaseRepositoryContract $repository,array $params = []);

    public function updated(Model $model);

    public function beforeDelete(BaseRepositoryContract $repository,mixed $param);

    public function deleted(mixed $param);

    public function beforeFetch(BaseRepositoryContract $repository,mixed $params = null);

    public function fetched(null|Model $model = null);
}
?>