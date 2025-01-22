<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class GenerateController extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-controller {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a custom controller with repository pattern';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return resource_path('stubs/controller/controller.stub');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\Http\\Controllers';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $modelName = class_basename($name);
        $viewName = Str::lower(str_replace("\\", ".", str_replace("App\\Http\\Controllers\\", '', $name)));
        $class = class_basename($name) . 'Controller';
        $replace = [
            '{{ namespace }}' => $this->getNamespace($name),
            '{{ rootNamespace }}' => $this->rootNamespace(),
            '{{ namespaceRepository }}' => 'App\\Repositories\\' . str_replace("App\\Http\\Controllers\\", '', $name),
            '{{ repositoryInterface }}' => $modelName . 'RepositoryInterface',
            '{{ classname }}' => $class,
            '{{ model }}' => $modelName,
            '{{ view }}' => $viewName,
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        );
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the controller'],
        ];
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);
        $name = str_replace('\\', '/', $name);

        // Remove any double Http/Controllers segments
        $path = str_replace('Http/Controllers/Http/Controllers/', 'Http/Controllers/', $this->laravel['path'] . '/Http/Controllers/' . $name . 'Controller.php');
        // Ensure the directory exists
        $directory = dirname($path);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        return $path;
    }
}
