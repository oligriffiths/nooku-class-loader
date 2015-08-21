<?php
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-platform for the canonical source repository
 */

namespace Nooku\Library;

/**
 * Class Loader Interface
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Nooku\Library\Class\Loader\Interface
 */
interface ClassLoaderInterface
{
    /**
     * Registers the loader with the PHP autoloader.
     *
     * @param Boolean $prepend Whether to prepend the autoloader or not
     * @see \spl_autoload_register();
     */
    public function register($prepend = false);

    /**
     * Unregisters the loader with the PHP autoloader.
     *
     * @see \spl_autoload_unregister();
     */
    public function unregister();

    /**
     * Load a class based on a class name
     *
     * @param string  $class    The class name
     * @throws \RuntimeException If debug is enabled and the class could not be found in the file.
     * @return boolean  Returns TRUE if the class could be loaded, otherwise returns FALSE.
     */
    public function load($class);

    /**
     * Get the path based on a class name
     *
     * @param string $class The class name
     * @param string $base  The base path. If NULL the global base path will be used.
     * @return string|boolean Returns canonicalized absolute pathname or FALSE of the class could not be found.
     */
    public function getPath($class, $base = null);

    /**
     * Get the path based on a class name
     *
     * @param string $class  The class name
     * @param string $path   The class path
     * @param string $base   The base path. If NULL the global base path will be used.
     * @return void
     */
    public function setPath($class, $path, $base = null);

    /**
     * Register a class locator
     *
     * @param  ClassLocatorInterface $locator
     * @param  bool $prepend If true, the locator will be prepended instead of appended.
     * @return void
     */
    public function registerLocator(ClassLocatorInterface $locator, $prepend = false);

    /**
     * Registers namesapces => paths mapping for a locator
     *
     * @param string|ClassLocatorInterface $locator The locator to register namespaces against
     * @param array $namespace An array where index is the namespace and value is the path or an array of paths
     */
    public function registerLocatorNamespaces($locator, array $namespaces);

    /**
     * Registers a single namespace to a path for a given locator
     *
     * @param string|ClassLocatorInterface $locator The locator to register namespaces against
     * @param array $namespace An array where index is the namespace and value is the path
     * @param string $path The file path the namespace is registered to
     */
    public function registerLocatorNamespace($locator, $namespace, $path);

    /**
     * Gets the paths for a given namespace
     *
     * @param null|string $namespace The namespace for the paths
     * @param null|string $locator The paths for a specific locator
     * @return array
     */
    public function getNamespacePaths($namespace = null, $locator = null);

    /**
     * Get a registered class locator based on his type
     *
     * @param string $type The locator type
     * @return ClassLocatorInterface|null  Returns the object locator or NULL if it cannot be found.
     */
    public function getLocator($type);

    /**
     * Register an alias for a class
     *
     * @param string  $class The original
     * @param string  $alias The alias name for the class.
     */
    public function registerAlias($class, $alias);

    /**
     * Get the registered alias for a class
     *
     * @param  string $class The class
     * @return array   An array of aliases
     */
    public function getAliases($class);

    /**
     * Get the base path
     *
     * @return string The base path
     */
    public function getBasePath();

    /**
     * Set the base path
     *
     * @param string $path The base path
     * @return ClassLoaderInterface
     */
    public function setBasePath($path);

    /**
     * Enable or disable class loading
     *
     * If debug is enabled the class loader should throw an exception if a file is found but does not declare the class.
     *
     * @param bool $debug True or false.
     * @return ClassLoaderInterface
     */
    public function setDebug($debug);

    /**
     * Check if the loader is running in debug mode
     *
     * @return bool
     */
    public function isDebug();

    /**
     * Tells if a class, interface or trait exists.
     *
     * @params string $class
     * @return boolean
     */
    public function isDeclared($class);
}