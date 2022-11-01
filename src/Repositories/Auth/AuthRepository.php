<?php

namespace App\Repositories\Auth;

use App\Repositories\Auth\AuthContract as AuthContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

class AuthRepository  implements AuthContract
{

    protected $model;
    public function __construct(Model $model = null)
    {
        $this->model = $model;
    }

    public function setModel(Model $user)
    {
        $this->model = $user;

        return $this;
    }

    public function login(array $params, bool $rememberMe = false)
    {

        try {
            return $this->model->login($params, $rememberMe);
        } catch (\Throwable $e) {
            // dd($e);
            throw new \Exception('invalid email or password.', previous: $e);
        }
    }

    public function register(array $params)
    {
        DB::beginTransaction();
        try {
            $result = $this->model->register($params);
            DB::commit();
            return $result;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function logout(): bool
    {
        try {
            return $this->model->logout();
        } catch (\Throwable $th) {
            // dd($th);
            throw $th;
        }
    }
}
