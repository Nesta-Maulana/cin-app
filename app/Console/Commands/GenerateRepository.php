<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class GenerateRepository extends GeneratorCommand
{
    protected $signature = 'app:generate-repository {name}';
    protected $description = 'Generate a repository and its interface';

    protected function getStub()
    {
        return resource_path('stubs/repository/repository.stub');
    }

    protected function getInterfaceStub()
    {
        return resource_path('stubs/repository/repositoryinterface.stub');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\Repositories';
    }

    protected function buildClass($name)
    {
        $name = str_replace('/', '\\', $name);
        $modelName = class_basename($name);
        $className = $modelName . 'Repository';
        $interfaceName = $modelName . 'RepositoryInterface';

        $replace = [
            '{{ namespace }}' => $this->getNamespace($name),
            '{{ model }}' => $modelName,
            '{{ name }}' => $className,
            '{{ name_interface }}' => $interfaceName,
        ];

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    protected function buildInterface($name)
    {
        $name = str_replace('/', '\\', $name);
        $modelName = class_basename($name);
        $interfaceName = $modelName . 'RepositoryInterface';

        $replace = [
            '{{ namespace }}' => $this->getNamespace($name),
            '{{ name_interface }}' => $interfaceName,
        ];

        return str_replace(
            array_keys($replace), array_values($replace), file_get_contents($this->getInterfaceStub())
        );
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the repository'],
        ];
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);
        $name = str_replace('\\', '/', $name);
        $modelName = class_basename($name);
        $path = $this->laravel['path'] . '/Repositories/' . $name . '/' . $modelName . 'Repository.php';

        $directory = dirname($path);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        return $path;
    }

    protected function getInterfacePath($name)
    {
        $name = str_replace('\\', '/', $name);
        $modelName = class_basename($name);
        $path = $this->laravel['path'] . '/Repositories/' . $name . '/' . $modelName . 'RepositoryInterface.php';

        $directory = dirname($path);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        return $path;
    }

    public function handle()
    {
        $name = $this->argument('name');
        $repositoryNamespace = $this->rootNamespace() . 'Repositories\\' . str_replace('/', '\\', $name);

        // Generate the repository class
        $path = $this->getPath($name);
        $this->files->put($path, $this->sortImports($this->buildClass($repositoryNamespace)));

        $this->info($this->type . ' created successfully.');

        // Generate the repository interface
        $interfacePath = $this->getInterfacePath($name);
        $this->files->put($interfacePath, $this->sortImports($this->buildInterface($repositoryNamespace)));

        $this->info('Interface created successfully.');
    }
}
