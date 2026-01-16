<?php

namespace InertiaThemes\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeBlock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:block {name : The name of the block class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new InertiaThemes block class';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $className = $this->getClassName($name);
        $namespace = $this->getNamespace();
        $path = $this->getPath($className);

        if ($this->files->exists($path)) {
            $this->components->error("Block [{$className}] already exists.");
            return self::FAILURE;
        }

        $this->makeDirectory($path);

        $stub = $this->getStub();
        $stub = $this->replaceNamespace($stub, $namespace);
        $stub = $this->replaceClass($stub, $className);
        $stub = $this->replaceType($stub, $className);
        $stub = $this->replaceName($stub, $className);
        $stub = $this->replaceComponent($stub, $className);

        $this->files->put($path, $stub);

        $this->components->info("Block [{$className}] created successfully.");

        return self::SUCCESS;
    }

    /**
     * Get the class name from the input.
     */
    protected function getClassName(string $name): string
    {
        $name = Str::studly($name);

        if (!Str::endsWith($name, 'Block')) {
            $name .= 'Block';
        }

        return $name;
    }

    /**
     * Get the destination namespace.
     */
    protected function getNamespace(): string
    {
        return config('inertiathemes.namespaces.blocks', 'App\\Blocks');
    }

    /**
     * Get the destination file path.
     */
    protected function getPath(string $className): string
    {
        $path = config('inertiathemes.paths.blocks', app_path('Blocks'));

        return $path . DIRECTORY_SEPARATOR . $className . '.php';
    }

    /**
     * Build the directory for the class if necessary.
     */
    protected function makeDirectory(string $path): void
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true);
        }
    }

    /**
     * Get the stub file contents.
     */
    protected function getStub(): string
    {
        $customPath = base_path('stubs/inertiathemes/block.stub');

        if ($this->files->exists($customPath)) {
            return $this->files->get($customPath);
        }

        return $this->files->get(__DIR__ . '/../../../stubs/block.stub');
    }

    /**
     * Replace the namespace placeholder.
     */
    protected function replaceNamespace(string $stub, string $namespace): string
    {
        return str_replace('{{ namespace }}', $namespace, $stub);
    }

    /**
     * Replace the class name placeholder.
     */
    protected function replaceClass(string $stub, string $className): string
    {
        return str_replace('{{ class }}', $className, $stub);
    }

    /**
     * Replace the block type placeholder.
     */
    protected function replaceType(string $stub, string $className): string
    {
        $type = Str::replaceLast('Block', '', $className);

        return str_replace('{{ type }}', $type, $stub);
    }

    /**
     * Replace the block name placeholder.
     */
    protected function replaceName(string $stub, string $className): string
    {
        $name = Str::headline(Str::replaceLast('Block', '', $className));

        return str_replace('{{ name }}', $name, $stub);
    }

    /**
     * Replace the component path placeholder.
     */
    protected function replaceComponent(string $stub, string $className): string
    {
        $component = Str::replaceLast('Block', '', $className);

        return str_replace('{{ component }}', $component, $stub);
    }
}
