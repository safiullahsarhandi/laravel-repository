<?php

namespace LaravelRepository\Repositories\Notification;

use App\Core\Abstracts\Filters;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface NotificationRepositoryContract
{
    public function send(mixed $user,...$params);
}
