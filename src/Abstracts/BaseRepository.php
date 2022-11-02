<?php

namespace LaravelRepository\Abstracts;

use Exception;
use LaravelRepository\Abstracts\Filters;
use LaravelRepository\Repositories\Notification\NotificationRepository;
use Illuminate\Database\Eloquent\Model;
use LaravelRepository\Contracts\EventContract;
use LaravelRepository\Contracts\BaseRepositoryContract;

abstract class BaseRepository implements BaseRepositoryContract
{
    protected $model;
    private $countRelations = [];
    
    private $relations = [];
    /*
    * can save event instance   
     */
    private EventContract $event;

    public function __construct()
    {
        $repositories = config('repository.repositories')??[];
        $current = get_class($this);
        $this->model = resolve($repositories[$current]['model']);
    }

    public final function setModel(Model $model)
    {

        $this->model = $model;
        return $this;
    }
    /* 
    * Set Count Relation for eloquent ORM
    */
    public final function withCount(array $relations = [])
    {
        $this->countRelations = $relations;
        return $this;
    }
    /* 
    * Set Relation (eager loading) for eloquent ORM
    */
    
    public final function with(array $relations = [])
    {
        $this->relations = $relations;
        return $this;
    }

    public final function findAll(Filters|null $filter = null)
    {
        try {
            $this->callEvent('beforeFetch');
            $model = $this->model->withCount($this->countRelations)->with($this->relations)->filter($filter)->get();
            return $this->callEvent('fetched',$model);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public final function findById(mixed $id, Filters|null $filter = null)
    {
        try {
            $this->callEvent('beforeFetch');
            $model = $this->model->withCount($this->countRelations)->with($this->relations)->filter($filter)->findOrFail($id);
            return $this->callEvent('fetched',$model);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public final function findOne(Filters|null $filter = null)
    {
        try {
            $this->callEvent('beforeFetch');
            $model = $this->model->withCount($this->countRelations)->with($this->relations)->filter($filter)->first();
            return $this->callEvent('fetched',$model);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public final function paginate(int $perPage = 10, Filters|null $filter = null)
    {
        try {
            $this->callEvent('beforeFetch');
            $model = $this->model->withCount($this->countRelations)->with($this->relations)->filter($filter)->paginate($perPage);
            return $this->callEvent('fetched',$model);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public final function create(array $params)
    {
        
        
        try {
            $params = $this->callEvent('beforeCreate',$params);
            $model = $this->model->create($params);
            return $this->callEvent('created',$model);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public final function update(int $id, array $params, Filters|null $filter = null)
    {
        try {
            $params = $this->callEvent('beforeUpdate',$params);
            $model = $this->findById($id, filter: $filter);
            $model->update($params);
            return $this->callEvent('updated',$model);
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public final function delete(mixed $id, Filters|null $filter = null)
    {
        try {
            $this->callEvent('beforeDelete',$id);
            $model = $this->findById($id, filter: $filter);
            $model->delete();
            return $this->callEvent('deleted',$id);
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public final function getTotal(Filters|null $filter = null)
    {
        try {
            $params = $this->callEvent('beforeFetch',$filter);
            return $this->model->filter($filter)->count();
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public final function notification(): NotificationRepository
    {
        return (new NotificationRepository());
    }
    /* 
    *  sets event instance of repositoryEvent 
    *
    */
    public final function event(string $eventNamespace): BaseRepository
    {        
        $this->event = resolve($eventNamespace);
        return $this;
    }

    private function callEvent(string $eventName,mixed $params = []){
        
        try {
            if($this->event){
                if(str_contains($eventName,'before')){
                    $data = $this->event->{$eventName}($this,$params);
                    if(is_array($params)){
                        return array_merge($params,$data);
                    }else{
                        return $params;
                    }
                }
                // if returned something from created then returned values will be event returned values;
                // otherwise we just return params; 
                $returnedValues = $this->event->{$eventName}($params);
                return $returnedValues?$returnedValues:$params;
            }
        } catch (\Throwable $th) {
            // if event is not initialized it will return the params; 
            return $params;
        }
    }
}
