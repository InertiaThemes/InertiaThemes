<?php

namespace InertiaThemes\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeTheme extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:theme {name : The name of the theme class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new InertiaThemes theme class';

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
            $this->components->error("Theme [{$className}] already exists.");
            return self::FAILURE;
        }

        $this->makeDirectory($path);

        $stub = $this->getStub();
        $stub = $this->replaceNamespace($stub, $namespace);
        $stub = $this->replaceClass($stub, $className);
        $stub = $this->replaceId($stub, $className);
        $stub = $this->replaceName($stub, $className);

        $this->files->put($path, $stub);

        $this->components->info("Theme [{$className}] created successfully.");

        return self::SUCCESS;
    }

    /**
     * Get the class name from the input.
     */
    protected function getClassName(string $name): string
    {
        $name = Str::studly($name);

        if (!Str::endsWith($name, 'Theme')) {
            $name .= 'Theme';
        }

        return $name;
    }

    /**
     * Get the destination namespace.
     */
    protected function getNamespace(): string
    {
        return config('inertiathemes.namespaces.themes', 'App\\Themes');
    }

    /**
     * Get the destination file path.
     */
    protected function getPath(string $className): string
    {
        $path = config('inertiathemes.paths.themes', app_path('Themes'));

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
        $customPath = base_path('stubs/inertiathemes/theme.stub');

        if ($this->files->exists($customPath)) {
            return $this->files->get($customPath);
        }

        return $this->files->get(__DIR__ . '/../../../stubs/theme.stub');
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
     * Replace the theme ID placeholder.
     */
    protected function replaceId(string $stub, string $className): string
    {
        $id = Str::kebab(Str::replaceLast('Theme', '', $className));

        return str_replace('{{ id }}', $id, $stub);
    }

    /**
     * Replace the theme name placeholder.
     */
    protected function replaceName(string $stub, string $className): string
    {
        $name = Str::headline(Str::replaceLast('Theme', '', $className));

        return str_replace('{{ name }}', $name, $stub);
    }
}
