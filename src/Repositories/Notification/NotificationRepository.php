<?php

namespace App\Repositories\Notification;

use App\Core\Abstracts\Filters;
use App\Core\Abstracts\Repository\BaseRepository;
use App\Core\Notifications\PushNotification;
use App\Repositories\Notification\NotificationRepositoryContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Notification;

class NotificationRepository extends BaseRepository implements NotificationRepositoryContract
{

    public function markAsRead($id = null,array $params, Filters|null $filter = null)
    {
        try {
            if($id){
                $this->model->filter($filter)->where('id',$id)->update($params);
            }else{
                
                $model = $this->model->filter($filter)->update($params);
            }
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function send(mixed $user,...$params){
        Notification::send($user,new PushNotification(...$params));
    }
    
}
