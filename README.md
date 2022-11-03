This Package is used to implement repository pattern in laravel with very minimal code. Idea behind this implementation is to allow modular approach and reusable every single entity in your project and enhance your coding experience with features like model binding, repository generator, repository events etc.  


## Installation
using below mentioned command you can add this package in your laravel project

`composer require safiullahsarhandi/laravel-repository`

## publish assets
1. `php artisan vendor:publish --tag=repository-config`
this command will create `repository.php` configuration file in `config` directory which will store information like this this. 


``` 
<?php 
//config/repository.php
return [
      /* 
      * repositories will contain all the repositories namespaces from app/Repositories directory  
      * and bind Contract and model associated with it  
      *
      */
      'repositories' => [],
  ];
?> 
```

## Usage

This package offers few commands which helps to perform different tasks or you can say that it's required to use this. 

1. Create Repository
      
      command: `php artisan make:repository <path/to/repository> <model>`
      
      creating repository would be easy by using this package. it register your repository in config file.
      and create set of files in app\Repositories directory, for instance `php artisan make:repository User/UserRepository User` will update your `app\Repositories` folder like this
      
      ![repository-dir](https://user-images.githubusercontent.com/36722999/199682649-8fa5718e-40c7-4371-ae6a-1d48931ec897.png)

      and update your config file like this
      
      ```
      <?php 
      //config/repository.php
      return [
            /* 
            * repositories will contain all the repositories namespaces from app/Repositories directory  
            * and bind Contract and model associated with it  
            *
            */
            'repositories' => [
                  App\Repositories\User\UserRepository::class => [
                     'model' => App\Models\User::class,
                     'contract' => App\Repositories\User\UserRepositoryContract::class,
                  ]
            ],
        ];
      ?> 
      ```
      
      
2. Create Query Filter

      idea behind filter is to extend database query. When calling any specific repository, every repository function which you will see below has some how similar structure and contain minimum amount of code. i will explain implementation for filters in next section of documentation so that you will get familier with it but for now let's check how to generate query filters with given command.
      
      command: `php artisan make:filter <path/to/filter>`
      
      eg: `php artisan make:filter Api/UserFilter` this will generate filter in `app/Filters/Api/UserFilters.php`
      
      ![filter-dir](https://user-images.githubusercontent.com/36722999/199750595-0a7a8b04-d830-4584-893c-7ee6c3f9d763.png)
      
      
      this will be final output for `make:filter` command. 
      ```
      <?php
      
      namespace App\Filters\Api;
      use LaravelRepository\Abstracts\Filters;

      class UserFilter extends Filters {

          protected $filters = ['search'];

          public function search($value)
          {
              $this->builder->where("status", $value);
          }
      }
      ```
      
      filter class should have methods with same name which you haved registered in `protected $filters` property. whenever you bind query filter with repository it reads laravel `Illuminate\Support\Request` instance and verify the availablity of parameters in http request. you can use it this way
      
      ```
      use App\Filters\Api\UserFilter;
      use App\Repositories\User\UserRepositoryContract;
      use Illuminate\Support\Facades\Route;
      
      
      //URL looks like "http://localhost:8000/users?search=active" will give you better understanding for filters   
      
      Route::get('/users', function (UserRepositoryContract $user,UserFilter $filter) {
          $data = $user->findAll($filter);
          // under the hood it work like this
         // User::where(status,'active')->get();
      });

      ```
      sometimes you want to extend filters under the hood, and don't want to expose every parameter when any specific `route` called. you can extend filter programitically unless filter is passed to repository. 
      
      ```
      Route::get('/account', function (UserRepositoryContract $user,UserFilter $filter) {
          // extends request after it has been initialized
          $filter->extendRequest([
              'user_id' => auth()->id(),
          ]);
          $data = $user->findOne($filter);
          // under the hood it work like this
          // User::where('user_id',1)->first();
      });
      
      ```
      and your `UserFilter::class` should be like this  
      ```
      <?php
      
      namespace App\Filters\Api;
      use LaravelRepository\Abstracts\Filters;

      class UserFilter extends Filters {

          protected $filters = ['search','user_id'];

          public function search($value)
          {
              $this->builder->where("status", $value);
          }
          public function user_id($value)
          {
              $this->builder->where("id", $value);
          }
      }
      
      ```
      
      
      
 3. Create Repository Events

      you can't override the predefined functions which repository offers, but sometimes or you can say most of the time you need to filter or performing any specific operation on particular time while execution. Using direct repository functions cannot acheive it because they are just interacting with data but do not implement your business logic. To acheive this you can bind `repository event` with it. you can assume that events are similar to laravel observers. whereas observers are just triggered for model but here repository event could be available for repository only   
      
      command: `php artisan make:repository <path/to/repository-event>`
      
      eg: `php artisan make:repository-event User/UserRepositoryEvent` this will create event class in app/Events/User/UserRepositoryEvent.php
      

