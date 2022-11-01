<?php

namespace LaravelRepository\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

class CreateRepositoryContract extends GeneratorCommand
{
    protected $hidden = true;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repository:contract {name}';
    private $repositoryFolder = "Repositories";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Repository Contract';


    protected function replaceNamespace(&$stub, $name)
    {
        $name = str_replace('App\\', '', $name);
        $contractName = class_basename(str_replace('\\', '/', $name));
        $userInputPath = Str::before($name,'\\'.$contractName);
        $namespace = 'App\\'.$this->repositoryFolder.'\\'.$userInputPath;
        $stub = str_replace('{{ namespace }}', $namespace, $stub);
        $stub = str_replace('{{ contract }}', $contractName, $stub);        
        
        
        
        return $this;
    }
    
    protected function getStub(){
        return __DIR__.'/../stubs/repository.contract.stub';
    }
    
    protected function getPath($name)
    {
        $name = str_replace('App\\', '', $name);
        $name = str_replace('\\', '/', $name);
        // dd('$name',$name);
        return "{$this->laravel['path']}"."/{$this->repositoryFolder}/{$name}.php";
    }

}
