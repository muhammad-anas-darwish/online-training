<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeCrudCommand extends Command
{
    protected $signature = 'make:crud
                            {name : Model name}
                            {--module= : Module name}
                            {--cache : Generate service with cache functionality}
                            {--no-cache : Generate service without cache functionality}';

    protected $description = 'Create complete CRUD (Controller, DTO, Requests, Resource, Service, Model)';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $modelName = $this->argument('name');
        $moduleName = $this->normalizeModuleName($this->option('module'));
        $withCache = $this->option('cache') || !$this->option('no-cache');
        
        if (empty($moduleName)) {
            $this->error('Module name is required!');
            $this->info('Usage: php artisan make:crud ModelName --module=ModuleName');
            return;
        }

        $this->createModel($modelName, $moduleName);
        $this->createDTO($modelName, $moduleName);
        $this->createController($modelName, $moduleName);
        $this->createRequests($modelName, $moduleName);
        $this->createResource($modelName, $moduleName);
        $this->createService($modelName, $moduleName, $withCache);
        
        $this->info("CRUD files for {$modelName} created successfully!");
    }

    protected function createModel($modelName, $moduleName = null)
    {
        $stub = $this->getStub('Model');
        $path = $this->getFilePath($modelName, 'Entities', $moduleName, "{$modelName}.php");
        
        $replacements = [
            '{{ namespace }}' => $moduleName ? "Modules\\{$moduleName}\\Entities" : "App\\Models",
            '{{ class }}' => $modelName,
            '{{ table }}' => Str::snake(Str::plural($modelName)),
        ];
        
        $this->createFile($path, $stub, $replacements);
    }

    protected function createDTO($modelName, $moduleName = null)
    {
        $stub = $this->getStub('DTO');
        $path = $this->getFilePath($modelName, 'DTOs', $moduleName, "{$modelName}DTO.php");
        
        $replacements = [
            '{{ namespace }}' => $moduleName ? "Modules\\{$moduleName}\\DTOs" : "App\\DTOs",
            '{{ class }}' => "{$modelName}DTO",
        ];
        
        $this->createFile($path, $stub, $replacements);
    }

    protected function createController($modelName, $moduleName = null)
    {
        $stub = $this->getStub('Controller');
        $path = $this->getFilePath($modelName, 'Http/Controllers', $moduleName, "{$modelName}Controller.php");
        
        $lowerModel = Str::camel($modelName);
        $pluralModel = Str::plural($lowerModel);
        
        $replacements = [
            '{{ namespace }}' => $moduleName ? "Modules\\{$moduleName}" : "App\\Http\\Controllers",
            '{{ class }}' => "{$modelName}Controller",
            '{{ model }}' => $modelName,
            '{{ modelVariable }}' => $lowerModel,
            '{{ modelPlural }}' => $pluralModel,
            '{{ modelNamespace }}' => $moduleName ? "Modules\\{$moduleName}\\Entities\\{$modelName}" : "App\\Models\\{$modelName}",
            '{{ dtoNamespace }}' => $moduleName ? "Modules\\{$moduleName}\\DTOs\\{$modelName}DTO" : "App\\DTOs\\{$modelName}DTO",
            '{{ resourceNamespace }}' => $moduleName ? "Modules\\{$moduleName}\\Http\\Resources\\{$modelName}Resource" : "App\\Http\\Resources\\{$modelName}Resource",
            '{{ serviceNamespace }}' => $moduleName ? "Modules\\{$moduleName}\\Services\\{$modelName}Service" : "App\\Services\\{$modelName}Service",
        ];
        
        $this->createFile($path, $stub, $replacements);
    }

    protected function createRequests($modelName, $moduleName = null)
    {
        $storeStub = $this->getStub('StoreRequest');
        $storePath = $this->getFilePath($modelName, 'Http/Requests', $moduleName, "Store{$modelName}Request.php");
        
        $replacements = [
            '{{ namespace }}' => $moduleName ? "Modules\\{$moduleName}\\Http\\Requests" : "App\\Http\\Requests",
            '{{ class }}' => "Store{$modelName}Request",
        ];
        
        $this->createFile($storePath, $storeStub, $replacements);
        
        $updateStub = $this->getStub('UpdateRequest');
        $updatePath = $this->getFilePath($modelName, 'Http/Requests', $moduleName, "Update{$modelName}Request.php");
        
        $replacements['{{ class }}'] = "Update{$modelName}Request";
        $this->createFile($updatePath, $updateStub, $replacements);
    }

    protected function createResource($modelName, $moduleName = null)
    {
        $stub = $this->getStub('Resource');
        $path = $this->getFilePath($modelName, 'Http/Resources', $moduleName, "{$modelName}Resource.php");
        
        $replacements = [
            '{{ namespace }}' => $moduleName ? "Modules\\{$moduleName}\\Http\\Resources" : "App\\Http\\Resources",
            '{{ class }}' => "{$modelName}Resource",
        ];
        
        $this->createFile($path, $stub, $replacements);
    }

    protected function createService($modelName, $moduleName = null, $withCache = true)
    {
        $stubType = $withCache ? 'ServiceWithCache' : 'Service';
        $stub = $this->getStub($stubType);
        $path = $this->getFilePath($modelName, 'Services', $moduleName, "{$modelName}Service.php");
        
        $replacements = [
            '{{ namespace }}' => $moduleName ? "Modules\\{$moduleName}\\Services" : "App\\Services",
            '{{ class }}' => "{$modelName}Service",
            '{{ model }}' => $modelName,
            '{{ modelNamespace }}' => $moduleName ? "Modules\\{$moduleName}\\Entities\\{$modelName}" : "App\\Models\\{$modelName}",
            '{{ dtoNamespace }}' => $moduleName ? "Modules\\{$moduleName}\\DTOs\\{$modelName}DTO" : "App\\DTOs\\{$modelName}DTO",
            '{{ modelVariable }}' => Str::camel($modelName),
            '{{ cachePrefix }}' => Str::snake(Str::plural($modelName)),
        ];
        
        $this->createFile($path, $stub, $replacements);
    }

    protected function getStub($type)
    {
        return $this->files->get(__DIR__."/stubs/crud/{$type}.stub");
    }

    protected function getFilePath($modelName, $subPath, $moduleName, $fileName)
    {
        if ($moduleName) {
            $modulePath = base_path("Modules/{$moduleName}");
            return "{$modulePath}/{$subPath}/{$fileName}";
        }
        
        return app_path("{$subPath}/{$fileName}");
    }

    protected function createFile($path, $stub, $replacements)
    {
        if ($this->fileExists($path)) {
            return false;
        }

        $content = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );
        
        $this->makeDirectory($path);
        $this->files->put($path, $content);
        $this->info("Created: {$path}");
        return true;
    }
    
    protected function fileExists(string $path): bool
    {
        return $this->files->exists($path);
    }

    protected function makeDirectory($path)
    {
        $dir = dirname($path);
        
        if (!$this->files->isDirectory($dir)) {
            $this->files->makeDirectory($dir, 0755, true, true);
        }
    }

    protected function normalizeModuleName(string $moduleName): string
    {
        // Replace all types of slashes with backslashes
        $normalized = str_replace(['/', '\\\\', '\\',], '\\', $moduleName);
        
        // Remove any trailing or leading slashes
        return trim($normalized, '\\');
    }
}