<?php

namespace InertiaThemes;

use Illuminate\Support\Facades\File;
use InertiaThemes\Contracts\Block;
use InertiaThemes\Contracts\Theme;
use ReflectionClass;
use ReflectionException;

/**
 * Discovery Service
 *
 * Automatically discovers theme and block classes from specified directories.
 * Uses reflection to validate that discovered classes implement the required interfaces.
 *
 * @package InertiaThemes
 */
class Discovery
{
    /**
     * Discover theme classes in a directory.
     *
     * Scans the given directory for PHP classes that implement the Theme interface.
     *
     * @param string $path Directory path to scan
     * @param string $namespace Base namespace for the directory
     * @return array<int, class-string<Theme>> List of theme class names
     */
    public static function themes(string $path, string $namespace): array
    {
        return static::discover($path, $namespace, Theme::class);
    }

    /**
     * Discover block classes in a directory.
     *
     * Scans the given directory for PHP classes that implement the Block interface.
     *
     * @param string $path Directory path to scan
     * @param string $namespace Base namespace for the directory
     * @return array<int, class-string<Block>> List of block class names
     */
    public static function blocks(string $path, string $namespace): array
    {
        return static::discover($path, $namespace, Block::class);
    }

    /**
     * Discover classes implementing an interface in a directory.
     *
     * Recursively scans the directory for PHP files and checks if the
     * corresponding class implements the specified interface.
     *
     * @param string $path Directory path to scan
     * @param string $namespace Base namespace for the directory
     * @param class-string $interface Interface the classes must implement
     * @return array<int, class-string> List of fully qualified class names
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

            $subNamespace = $relativePath
                ? str_replace('/', '\\', $relativePath) . '\\'
                : '';

            $fqcn = $namespace . '\\' . $subNamespace . $className;

            if (class_exists($fqcn) && static::implementsInterface($fqcn, $interface)) {
                $classes[] = $fqcn;
            }
        }

        return $classes;
    }

    /**
     * Check if a class implements an interface and is not abstract.
     *
     * @param class-string $class The class to check
     * @param class-string $interface The interface to check for
     * @return bool True if the class implements the interface and is instantiable
     */
    protected static function implementsInterface(string $class, string $interface): bool
    {
        try {
            $reflection = new ReflectionClass($class);

            return $reflection->implementsInterface($interface)
                && !$reflection->isAbstract();
        } catch (ReflectionException) {
            return false;
        }
    }
}
