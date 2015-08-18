# Nooku Class Loader

![TravisCI](https://travis-ci.org/oligriffiths/nooku-class-loader.svg)

The nooku class loader is PHP autoloader that allows multiple `locators` to be registered and called 
squentially when a class is request.

The class loader is registered via the `spl_autoload_register()` PHP function.


## Locators

Locators perform the task of translating a "classname" (class, interface, trait) into a file path and 
returning this to the loader. By default, the "library" locator is registered with the Nooku namespace 
`Nooku\Library` to the `src` folder of this package. 

Multiple different namespace can be registered per locator, each namespace maps to a base directory.

When creating a custom locator, you must define the `$_name` and the `locate` method, the loader then uses this 
to identify each locator.


```php
class LocatorCustom extends Nooku\Library\ClassLocatorAbstract
{
    protected $_name = 'custom';
    
    public function locate($class, $basedir)
    {
        // ... your custom logic
        
        return $path;
    }
} 
```

Namespaces can be optionally registered with the locator, and then iterated over when resolving a classname.

```php
class LocatorCustom extends Nooku\Library\ClassLocatorAbstract
{
    public function locate($class, $basepath)
	{
        foreach($this->getNamespaces() as $namespace => $basepath)
        {
            if(empty($namespace) && strpos($class, '\\')) {
                continue;
            }

            if(strpos('\\'.$class, '\\'.$namespace) !== 0) {
                continue;
            }
            
            // .. your custom logic
        }
        
        return false;
    }
}

$locator = new LocatorCustom([
    'namespaces' => array(
        'My\Custom\Namespace' => 'path/to/base/dir'
    )
]);
```


## Getting started

The class loader acts as a singleton, and is instantiated via:

```php
$loader = Nooku\Library\ClassLoader::getInstance(array $options = array());
```

This will auto-register the loader with PHP. 

Once a loader instance is setup, you can register a locator with the loader. Note that if the locator is
currently in a non-autoloadable namespace & directory, you will need to manually `require` the file.

```php
$loader->registerLocator(
    new LocatorCustom()
);
```

Now, whenever a new unkown class is instantiated, the custom locator will be called to try and locate
the file path. Once a file path has been located, other locators are not called.

The ClassLoader can take an `$options` array as the only argument. There are 3 options that can be set:

```php
$options = [
    'cache' => true | false,    // Enables an APC cache of class => file path mappings
    'cache_namespace' => 'a namespace for APC', // Sets a namespace for the cache
    'debug' => true | false,    // Enables/disabled debugging
];

## API

### Nooku\Library\ClassLoader

```php
namespace Nooku\Library;

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
    public function registerLocator(\Nooku\Library\ClassLocatorInterface $locator, $prepend = false );

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
```

### Nooku\Library\ClassLocatorInterface

```php
namespace Nooku\Library;

interface ClassLocatorInterface
{
    /**
     * Get locator name
     *
     * @return string
     */
    public static function getName();

    /**
     * Get a fully qualified path based on a class name
     *
     * @param  string $class    The class name
     * @param  string $basepath The basepath to use to find the class
     * @return string|false     Returns canonicalized absolute pathname or FALSE of the class could not be found.
     */
    public function locate($class, $basepath);

    /**
     * Register a namespace
     *
     * @param  string $namespace
     * @param  string $path The location of the namespace
     * @return ClassLocatorInterface
     */
    public function registerNamespace($namespace, $paths);

    /**
     * Get the namespace path
     *
     * @param string $namespace The namespace
     * @return string|false The namespace path or FALSE if the namespace does not exist.
     */
    public function getNamespace($namespace);

    /**
     * Get the registered namespaces
     *
     * @return array An array with namespaces as keys and path as values
     */
    public function getNamespaces();
}
```