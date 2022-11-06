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

1. How to Create Repository



      
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
      ### Make Model Filterable
      before going through that how to use `UserFilter::class` with any repository, we should know that what basic steps to be followed before binding filter with repository. we have to use `LaravelRepository\Traits\Filterable` in model to make any model filtered on demand.
      
      ```
      <?php

      namespace App\Models;

      use Illuminate\Database\Eloquent\Factories\HasFactory;
      use Illuminate\Database\Eloquent\Model;
      use LaravelRepository\Traits\Filterable;

      class User extends Model
      {
          use HasFactory, Filterable;

          protected $fillable = ['name','email','password'];
      }

      ```
      ### How to use Query Filter
      
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
      ### Extend Query Filter
      
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
      
      command: `php artisan make:repository-event <path/to/repository-event>`
      
      eg: `php artisan make:repository-event User/UserRepositoryEvent` will create event class in `app/Events/User/UserRepositoryEvent.php` with following code snippet in it by default.
      
      ```
      <?php
      namespace App\Events\User;

      use Illuminate\Database\Eloquent\Model;
      use LaravelRepository\Contracts\EventContract;

      class UserRepositoryEvent implements EventContract {

          public function beforeCreate($repository,array $params = []){}

          public function created(Model $model){}

          public function beforeUpdate($repository,array $params = []){}

          public function updated(Model $model){}

          public function beforeDelete($repository,mixed $param){}

          public function deleted(mixed $param){}

          public function beforeFetch($repository,mixed $params = null){}

          public function fetched(null|Model $value = null){}
      }
      
      ```
      
      ### how to use event
      
      repository events are called when you bind `event class` with any `repository`. you can see all repository methods listed below. lets say we want to create `Order and store products in database which user has selected. how it would be possible? you can create your own method and call it but we would recommend events; 
      
      ```
      use App\Repositories\Order\OrderRepositoryContract;
      use App\Events\Order\OrderRepositoryEvent;
      
      Route::post('/orders', function (OrderRepositoryContract $order) {
          // params will be passed using \Illuminate\Support\Request;
          // for now just take it as an example
          $params = [
              'customer_email' => 'customer@example.com',
              'customer_name' => 'Mark Endrew',
              'products' => [
                  1 => ['qty' => 1, 'price' => 200 ],
                  2 => ['qty' => 1, 'price' => 300 ],
              ] 
          ];  
          $data = $order->event(OrderRepositoryEvent::class)->create($params);

      });

      
      ```
      
      now we want to store `products` in our database which are associated to an `order`. as we have called `create` method so we can put some logics in `beforeCreate` or `created` in `App\Events\Order\OrderRepositoryEvent::class`.
      
      ```
      <?php
      namespace App\Events\Order;

      use Illuminate\Database\Eloquent\Model;
      use LaravelRepository\Contracts\EventContract;

      class OrderRepositoryEvent implements EventContract {
    
          private $params;
          
          public function beforeCreate($repository,array $params = [])
          {
               //you don't need to do this every time 
              // and we don't have another way to access params in created
              $this->params = $params;
          }


          public function created(Model $model)
          {
              // store products after order created 
              $products = $this->params['products'];
              // storing laravel belongsToMany order_products
              $model->products()->attach($products);      
              
              // process merchant payment  
              $model->pay();
                
          }
      } 
      
      ```
     
     ### Repository Methods: 
     
      | # SR No. | Method Name | access modifier | Events | Description | 
      | :-----:  | :--------:  | :-------------: | :----: | :---------: |
      | 01  | `setModel(Model $model)` | public | N/A |   sets or bind model with repository. by default model is injected but sometimes you need to set Model implicitly. so that it can work in such scenario. |  
      | 02 | `withCount(array $relations = [])` | public | N/A | you can pass laravel relations using this method to perform aggregation. |
      | 03 | `with(array $relations = [])` | public | N/A | you can pass laravel relations using this method to perform eager loading. |
      | 04 | `findAll(Filters\|null $filter = null)` | public | `beforeFetch`, `fetched` | fetches all records of injected model and can take Filter instance as parameter |
      | 05 | `findById(int $id, Filters\|null $filter = null)` | public | `beforeFetch`, `fetched` | fetches specific record of injected model |
      | 06 | `findOne(Filters\|null $filter = null)` | public | `beforeFetch`, `fetched` | used to fetch first user of injected model |
      | 07 | `paginate(int $perPage = 10, Filters\|null $filter = null)` | public | `beforeFetch`, `fetched` | fetches all records and returns as paginated |
      | 08 | `create(array $params)` | public | `beforeCreate`, `created` | create new record in model. take parameters set all columns which are fillable |
      | 09 | `update(int $id, array $params, Filters\|null $filter = null)` | public | `beforeUpdate`, `updated` | update record in model |
      | 10 | `delete(int $id, Filters\|null $filter = null)` | public | `beforeDelete`, `deleted` | delete record in model matched to id  |
      | 11 | `getTotal(Filters\|null $filter = null)` | public | `beforeFetch` | returns total no of records in model | 
      | 12 | `notification()` | public | N/A | returns `LaravelRepository\Repositories\NotificationRepository::class` instance |.
      | 13 | `event(string $eventNamespace)` | public | N/A | bind event class with any repository.   

