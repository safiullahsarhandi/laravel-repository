<?php 
namespace LaravelRepository\Providers;

use Illuminate\Support\ServiceProvider;
use LaravelRepository\Commands\CreateRepository;
use LaravelRepository\Commands\CreateRepositoryContract;
use LaravelRepository\Commands\GenerateFilters;

class RepositoryServiceProvider extends ServiceProvider {

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // registering commands
            $this->commands([
                CreateRepository::class,
                CreateRepositoryContract::class,
                GenerateFilters::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../config' => config_path(),
        ], 'repository-config');
        
    }

}

?>