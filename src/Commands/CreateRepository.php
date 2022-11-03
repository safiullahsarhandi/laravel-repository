<?php

namespace LaravelRepository\Commands;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

class CreateRepository extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name} --{model}';
    
    private $repositoryFolder = "Repositories";
    private $contract = "Contract";
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new Repository';

    /**
     * Execute the console command.
     *
     * @return int
     */
    protected function getStub(){
        return __DIR__.'/../stubs/repository.stub';
    }

    protected function replaceNamespace(&$stub, $name)
    {
        $model = \Str::studly($this->argument('model'));
        $name = str_replace('App\\', '', $name);
        $className = class_basename(str_replace('\\', '/', $name));
        $userInputPath = Str::before($name,'\\'.$className);
        $namespace = 'App\\'.$this->repositoryFolder.'\\'.$userInputPath;
        $this->contract = Str::studly($className.'Contract');
        $contractNamespace = $namespace.'\\'.$this->contract;

        $stub = str_replace('{{ namespace }}', $namespace, $stub);
        $stub = str_replace('{{ class }}', $className, $stub);
        $stub = str_replace('{{ contract }}', $this->contract, $stub);
        $stub = str_replace('{{ contractNamespace }}', $contractNamespace, $stub);
        
        // handle Registration of namespaces
        $this->handleRegistration($namespace.'\\'.$className,$contractNamespace,'App\Models\\'.$model);
        
        if($userInputPath){
            $this->contract = $userInputPath.'\\'.$this->contract;
        }
        
        return $this;
    }
    
    protected function getPath($name)
    {
        $name = str_replace('App\\', '', $name);
        $name = str_replace('\\', '/', $name);
        return "{$this->laravel['path']}"."/{$this->repositoryFolder}/{$name}.php";
    }


    public function handle()
    {
        // First we need to ensure that the given name is not a reserved word within the PHP
        // language and that the class name will actually be valid. If it is not valid we
        // can error now and prevent from polluting the filesystem using invalid files.
        if ($this->isReservedName($this->getNameInput())) {
            $this->components->error('The name "'.$this->getNameInput().'" is reserved by PHP.');

            return false;
        }

        $name = $this->qualifyClass($this->getNameInput());

        $path = $this->getPath($name);

        // Next, We will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ((! $this->hasOption('force') ||
             ! $this->option('force')) &&
             $this->alreadyExists($this->getNameInput())) {
            $this->components->error($this->type.' already exists.');

            return false;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildClass($name)));

        $info = $this->type;

        if (in_array(CreatesMatchingTest::class, class_uses_recursive($this))) {
            if ($this->handleTestCreation($path)) {
                $info .= ' and test';
            }
        }

        $this->call("repository:contract",[
            'name' => $this->contract,
        ]);

        $this->components->info($info.' created successfully.');
    }

    private function handleRegistration($repositoryNamespace,$contract,$model){
        try {
            
            $repositories = config('repository.repositories');
            // first we will check that repository.php exists in config directory
            // if not exist we will force user to publish the config file 
            
            if(!is_array($repositories)){
                throw new \Exception('Please publish The Config first  using artisan command vendor:publish');
            }
            /* 
             * if user has completed its vendor:publish then we will proceed to put new repository in config file 
             */
            $configPath = config_path('repository.php');
            $configStub = $this->files->get(__DIR__.'/../stubs/config.stub');
            $configContent = '';
            // new repository;
            $repositories[$repositoryNamespace] = [
                'model' => \Str::studly($model),
                'contract' => $contract,
            ]; 
            // looping over all repositories to manage previously added and newly added repositories in list;
            $this->generateContent($repositories,$configContent);
            $configStub = str_replace('{{ configArray }}',$configContent, $configStub);
            $this->files->put($configPath,$configStub);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    private function generateContent($repositories,&$content){
        foreach($repositories as $key => $value){
            if(is_array($value)){
                
                $content .= "            $key::class => [".PHP_EOL;
                $this->generateContent($value,$content);
                $content .= '            ],'.PHP_EOL;
            }else{
                $content .= sprintf("                '%s' => %s::class,",$key,$value).PHP_EOL;
            }
        }
    }
}
