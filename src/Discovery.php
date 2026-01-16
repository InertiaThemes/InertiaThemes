<?php

namespace InertiaThemes;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InertiaThemes\Contracts\Block;
use InertiaThemes\Contracts\Theme;
use ReflectionClass;

class Discovery
{
    /**
     * Discover theme classes in a directory
     */
    public static function themes(string $path, string $namespace): array
    {
        return static::discover($path, $namespace, Theme::class);
    }

    /**
     * Discover block classes in a directory
     */
    public static function blocks(string $path, string $namespace): array
    {
        return static::discover($path, $namespace, Block::class);
    }

    /**
     * Discover classes implementing an interface in a directory
     */
    protected static function discover(string $path, string $namespace, string $interface): array
    {
        $classes = [];

        if (!File::isDirectory($path)) {
            return $classes;
        }

        foreach (File::allFiles($path) as $file) {
            $relativePath = $file->getRelativePath();
            $className = $file->getFilenameWithoutExtension();

            // Build the fully qualified class name
            $subNamespace = $relativePath
                ? str_replace('/', '\\', $relativePath) . '\\'
                : '';

            $fqcn = $namespace . '\\' . $subNamespace . $className;

            // Check if class exists and implements the interface
            if (class_exists($fqcn) && static::implementsInterface($fqcn, $interface)) {
                $classes[] = $fqcn;
            }
        }

        return $classes;
    }

    /**
     * Check if a class implements an interface (non-abstract)
     */
    protected static function implementsInterface(string $class, string $interface): bool
    {
        try {
            $reflection = new ReflectionClass($class);

            return $reflection->implementsInterface($interface)
                && !$reflection->isAbstract();
        } catch (\ReflectionException $e) {
            return false;
        }
    }
}
