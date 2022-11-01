# safiullahsarhandi/laravel-repository
This Package is used to implement repository pattern in laravel with very minimal code. Idea behind this implementation is to allow modular approach and reusable every single entity in your project 
i have made this package to enhance your coding experience with dynamic model binding in repository.  


# Installation
using below mentioned command you can add this package in your laravel project

`composer require safiullahsarhandi/laravel-repository`

# publish assets
1. `php artisan vendor:publish --tag=repository-config`

# Commands

This package offers few commands to finish headache for creating core features 

1. 
`php artisan make:repository <path/to/repository> <model>`

eg: `php artisan make:repository User/UserRepository User` this will create repository in app/Repositories/User/UserRepository
