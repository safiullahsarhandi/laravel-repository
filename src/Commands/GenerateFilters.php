<?php

namespace LaravelRepository\Commands;


use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

class GenerateFilters extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:filter {name} {--single} {--definition}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate model scope Filters';

    private $filterFolder = "Filters";
    private string $namespaceInitial = 'App\\';
    protected function getStub()
    {
        if ($this->option('single')) {
            return __DIR__ . '/../stubs/filter.single.stub';
        }
        if ($this->option('definition')) {
            return __DIR__ . '/../stubs/filter.definition.stub';
        }
        return __DIR__ . '/../stubs/filter.stub';
    }


    protected function replaceNamespace(&$stub, $name)
    {
        $name = str_replace($this->namespaceInitial, '', $name);
        $className = class_basename(str_replace('\\', '/', $name));
        $userInputPath = Str::before($name, '\\' . $className);
        $namespace = $this->namespaceInitial . $this->filterFolder . '\\' . $userInputPath;

        $stub = str_replace('{{ namespace }}', $namespace, $stub);
        $stub = str_replace('{{ class }}', $className, $stub);

        return $this;
    }
    protected function getPath($name)
    {
        $name = str_replace($this->namespaceInitial, '', $name);
        $name = str_replace('\\', '/', $name);
        return "{$this->laravel['path']}" . "/{$this->filterFolder}/{$name}.php";
    }
}
