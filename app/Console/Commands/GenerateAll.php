<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class GenerateAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:all {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate migration, model, controller, and livewire for given path';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->argument('path');
        $pathParts = explode('/', $path);
        $modelName = end($pathParts);
        $controllerName = Str::studly(Str::singular($path)) . 'Controller';

        $this->generateModel($modelName);
        $this->generateRepository($path);
        $this->generateController($path);
        $this->generateLivewire($modelName, $path);
        $this->generateResources($modelName, $path);
        $this->generatePermissions($modelName);

        $this->info('Yay! All files generated successfully.');
        return true;
    }

    private function generateModel($name)
    {
        Artisan::call('make:model', [
            'name' => $name,
            '-m' => true
        ]);
    }

    private function generateController($path)
    {
        Artisan::call('app:generate-controller', [
            'name' => $path
        ]);

    }
    private function generateRepository($path)
    {
        Artisan::call('app:generate-repository', [
            'name' => $path
        ]);

    }

    private function generateLivewire($modelName, $path)
    {


        $className = 'Show' . ucfirst($modelName);
        $classStubPath = resource_path('stubs/livewire/class.stub');
        $classNamespace = (dirname($path) === '.') ? 'App\Http\Livewire' : 'App\Http\Livewire\\' . str_replace('/', '\\', ucwords(dirname($path), '/'));
        $classDir = (dirname($path) === '.') ? '' : dirname($path);

        $viewName = Str::kebab($className);
        $viewStubPath = resource_path('stubs/livewire/view.stub');
        $viewDirPath = Str::kebab($classDir);
        $viewDir = str_replace('/', '.', $viewDirPath) . '.' . $viewName;
        $routeName = Str::kebab($modelName);
        $permissionName = Str::kebab($modelName);

        // Class
        $classPath = app_path("Http/Livewire/{$classDir}/{$className}.php");
        if (!is_dir(dirname($classPath))) {
            mkdir(dirname($classPath), 0755, true);
        }

        copy($classStubPath, $classPath);

        $repositoryNamespace = 'App\\Repositories\\' . str_replace("\\", "/", str_replace("App\\Http\\Controllers\\", '', $path));
        $repositoryClass = class_basename($path) . 'RepositoryInterface';

        $search = ['{{ classNamespace }}', '{{ className }}', '{{ modelName }}', '{{ viewDir }}', '{{ namespaceRepository }}', '{{ repositoryInterface }}'];
        $replace = [$classNamespace, $className, $modelName, $viewDir, $repositoryNamespace, $repositoryClass];
        $contents = file_get_contents($classPath);
        $contents = str_replace($search, $replace, $contents);
        file_put_contents($classPath, $contents);

        // View
        $viewPath = resource_path("views/livewire/{$viewDirPath}/{$viewName}.blade.php");
        if (!is_dir(dirname($viewPath))) {
            mkdir(dirname($viewPath), 0755, true);
        }

        copy($viewStubPath, $viewPath);

        $search = ['{{ viewName }}', '{{ routeName }}', '{{ permissionName }}'];
        $replace = [$viewName, $routeName, $permissionName];
        $contents = file_get_contents($viewPath);
        $contents = str_replace($search, $replace, $contents);
        file_put_contents($viewPath, $contents);
    }

    private function generateResources($modelName, $path)
    {
        $model = $modelName;
        $dirname = dirname($path) === '.' ? '' : dirname($path);

        $partPath = explode('/', $path);
        $slugParts = array_map(function ($part) {
            return Str::kebab($part);
        }, $partPath);
        $resourceDir = implode('/', $slugParts);

        $viewDir = $dirname ? resource_path("views/{$resourceDir}") : resource_path("views");
        $livewireView = 'show-' . Str::kebab($model);
        $livewireViewPath = $dirname ? strtolower($dirname) . '.' . $livewireView : $livewireView;
        $route = Str::kebab($model);

        // Index
        $indexStub = resource_path('stubs/view/index.stub');
        $indexPath = $viewDir . '/index.blade.php';
        if (!is_dir($viewDir)) {
            mkdir($viewDir, 0755, true);
        }
        copy($indexStub, $indexPath);

        $search = ['{{ model }}', '{{ livewireViewPath }}'];
        $replace = [$model, $livewireViewPath];

        $contents = file_get_contents($indexPath);
        $contents = str_replace($search, $replace, $contents);
        file_put_contents($indexPath, $contents);

        // Create
        $createStub = resource_path('stubs/view/create.stub');
        $createPath = $viewDir . '/create.blade.php';
        copy($createStub, $createPath);

        $search = ['{{ model }}', '{{ route }}'];
        $replace = [$model, $route];

        $contents = file_get_contents($createPath);
        $contents = str_replace($search, $replace, $contents);
        file_put_contents($createPath, $contents);

        // Edit
        $editStub = resource_path('stubs/view/edit.stub');
        $editPath = $viewDir . '/edit.blade.php';
        copy($editStub, $editPath);

        $search = ['{{ model }}', '{{ route }}'];
        $replace = [$model, $route];

        $contents = file_get_contents($editPath);
        $contents = str_replace($search, $replace, $contents);
        file_put_contents($editPath, $contents);
    }

    private function generatePermissions($modelName)
    {
        $permissionNames = ['create', 'read', 'update', 'delete'];
        foreach ($permissionNames as $permissionName) {
            $permission = Permission::create([
                'name' => $permissionName . '-' . Str::kebab($modelName),
                'group' => $modelName,
                'guard_name' => 'web',
            ]);
        }
    }
}
