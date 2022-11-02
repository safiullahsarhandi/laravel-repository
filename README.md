# safiullahsarhandi/laravel-repository
This Package is used to implement repository pattern in laravel with very minimal code. Idea behind this implementation is to allow modular approach and reusable every single entity in your project 
i have made this package to enhance your coding experience with dynamic model binding in repository.  


# Installation
using below mentioned command you can add this package in your laravel project

`composer require safiullahsarhandi/laravel-repository`

# publish assets
1. `php artisan vendor:publish --tag=repository-config`
this command will create `repository.php` configuration file in `config` directory with following snippet in it. 


``` 
<?php 
//config/repository.php
return [
      /* 
      * repositories will contain all the repositories namespaces from app/Repositories directory  and bind Contract and model for it  
      * this allows you not to set model explicitly on every use.  
      */
      'repositories' => [],
  ];
?> 
```

# Commands

This package offers few commands which helps to perform different tasks or you can say that it's required to use this. 

1. 
`php artisan make:repository <path/to/repository> <model>`

eg: `php artisan make:repository User/UserRepository User` this will create repository in app/Repositories/User/UserRepository
