This Package is used to implement repository pattern in laravel with very minimal code. Idea behind this implementation is to allow modular approach and reusable every single entity in your project and enhance your coding experience with features like model binding, repository generator, repository events etc.  


# Installation
using below mentioned command you can add this package in your laravel project

`composer require safiullahsarhandi/laravel-repository`

# publish assets
1. `php artisan vendor:publish --tag=repository-config`
this command will create `repository.php` configuration file in `config` directory which will store information. 


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

# Commands

This package offers few commands which helps to perform different tasks or you can say that it's required to use this. 

1. Create Repository

      command: `php artisan make:repository <path/to/repository> <model>`
      
      eg: `php artisan make:repository User/UserRepository User` this will create repository in app/Repositories/User/UserRepository.php
      
2. Create Query Filter

      idea behind filter is to extend database query. When calling any specific repository, every repository function which you will see below has some how similar structure and contain minimum amount of code. i will explain implementation for filters in next section of documentation so that you will get familier with it but for now you can generate your filter with given command.
      
      command: `php artisan make:filter <path/to/filter>`
      
      eg: `php artisan make:filter Api/User/UserFilter` this will generate filter in app/Filters/Api/User/UserFilters.php
      
 3. Create Repository Events

      you can't override the predefined functions which repository offers, but sometimes or you can say most of the time you need to filter or performing any specific operation on particular time while execution. Using direct repository functions cannot acheive it because they are just interacting with data but do not implement your business logic. To acheive this you can bind `repository event` with it. you can assume that events are similar to laravel observers. whereas observers are just triggered for model but here repository event could be available for repository only   
      
      command: `php artisan make:repository <path/to/repository-event>`
      
      eg: `php artisan make:repository-event User/UserRepositoryEvent` this will create event class in app/Events/User/UserRepositoryEvent.php
      

