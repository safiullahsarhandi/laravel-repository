<?php

namespace LaravelRepository\Commands;


use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

class CreateEvent extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository-event {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create repository events';

    private $folder = "Events";

    function getStub(){
        return __DIR__.'/../stubs/repository.event.stub'; 
    }


    protected function replaceNamespace(&$stub, $name)
    {
        $name = str_replace('App\\', '', $name);
        $className = class_basename(str_replace('\\', '/', $name));
        $userInputPath = Str::before($name,'\\'.$className);
        $namespace = 'App\\'.$this->folder.'\\'.$userInputPath;
        
        $stub = str_replace('{{ namespace }}', $namespace, $stub);
        $stub = str_replace('{{ class }}', $className, $stub);

        return $this;
    }
    
    protected function getPath($name)
    {
        $name = str_replace('App\\', '', $name);
        $name = str_replace('\\', '/', $name);
        return "{$this->laravel['path']}"."/{$this->folder}/{$name}.php";
    }
}
