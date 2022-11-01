<?php 
namespace App\Repositories\Auth;

use Illuminate\Database\Eloquent\Model;

interface AuthContract {

    public function login(Array $params,bool $rememberMe);

    public function setModel(Model $model);

    public function logout();
}
?>