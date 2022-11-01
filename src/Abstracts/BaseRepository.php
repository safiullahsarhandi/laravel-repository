<?php

namespace LaravelRepository\Abstracts;

use LaravelRepository\Abstracts\Filters;
use App\Models\Notification;
use App\Repositories\Notification\NotificationRepository;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected $model;
    protected $countRelations = [];
    
    public final function setModel(Model $model)
    {

        $this->model = $model;
        return $this;
    }
    public final function withCount($relations = [])
    {
        $this->countRelations = $relations;
        return $this;
    }

    public final function findAll(Filters|null $filter = null, array $relations = [])
    {
        try {
            return $this->model->withCount($this->countRelations)->with($relations)->filter($filter)->get();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public final function findById(int $id, array $relations = [], Filters|null $filter = null)
    {
        try {
            return $this->model->withCount($this->countRelations)->with($relations)->filter($filter)->findOrFail($id);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public final function findOne(array $relations = [], Filters|null $filter = null)
    {
        try {
            return $this->model->withCount($this->countRelations)->with($relations)->filter($filter)->first();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public final function paginate(int $perPage = 10, array $relations = [], Filters|null $filter = null)
    {
        try {
            return $this->model->withCount($this->countRelations)->with($relations)->filter($filter)->paginate($perPage);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public final function create(array $params)
    {

        try {
            return $this->model->create($params);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public final function update(int $id, array $params, Filters|null $filter = null)
    {
        try {
            $model = $this->findById($id, filter: $filter);
            $model->update($params);
            return $model;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public final function delete(int $id, Filters|null $filter = null)
    {
        try {
            $model = $this->findById($id, filter: $filter);
            $model->delete();
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public final function getTotal(Filters|null $filter = null)
    {
        try {
            return $this->model->filter($filter)->count();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public final function notification()
    {
        return (new NotificationRepository())->setModel(new Notification());
    }  
}
