<?php
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-platform for the canonical source repository
 */

namespace Nooku\Library;

require_once dirname(__FILE__).'/interface.php';
require_once dirname(__FILE__).'/locator/interface.php';
require_once dirname(__FILE__).'/locator/abstract.php';
require_once dirname(__FILE__).'/locator/library.php';
require_once dirname(__FILE__).'/registry/interface.php';
require_once dirname(__FILE__).'/registry/registry.php';
require_once dirname(__FILE__).'/registry/cache.php';

/**
 * Class Loader
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Nooku\Library\Class|Loader
 */
class ClassLoader implements ClassLoaderInterface
{
    /**
     * The class registry
     *
     * @var array
     */
    private $__registry = null;

    /**
     * The class locators
     *
     * @var array
     */
    protected $_locators = array();

    /**
     * Namespace => loader => paths map
     *
     * @var array
     */
    protected $_namespaces = array();

    /**
     * The loader basepath
     *
     * @var  string
     */
    protected $_base_path;

    /**
     * Debug
     *
     * @var boolean
     */
    protected $_debug = false;

    /**
     * Config array
     *
     * @var array
     */
    protected $_config = array();

    /**
     * Constructor
     *
     * @param array $config Array of configuration options.
     */
    final private function __construct($config = array())
    {
        //Set the config
        $this->setConfig($config);

        //Register the library locator
        $this->registerLocator(new ClassLocatorLibrary($config));

        //Register the Nooku\Library namesoace
        $this->registerLocatorNamespace('library', __NAMESPACE__, dirname(dirname(__FILE__)));

        //Register the loader with the PHP autoloader
        $this->register();
    }

    /**
     * Force creation of a singleton
     *
     * @param  array  $config An optional array with configuration options.
     * @return ClassLoader
     */
    final public static function getInstance($config = array())
    {
        static $instance;

        if($instance === NULL) {
            $instance = new self($config);
        }

        return $instance;
    }

    /**
     * Registers the loader with the PHP autoloader.
     *
     * @param Boolean $prepend Whether to prepend the autoloader or not
     * @see \spl_autoload_register();
     */
    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'load'), true, $prepend);
    }

    /**
     * Unregisters the loader with the PHP autoloader.
     *
     * @see \spl_autoload_unregister();
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'load'));
    }

    /**
     * Get the class registry
     *
     * @return ClassRegistryInterface
     */
    protected function getRegistry()
    {
        if(!$this->__registry)
        {
            $config = $this->getConfig();

            if(isset($config['registry']))
            {
                $this->__registry = $config;
            }
            elseif(isset($config['cache']) && $config['cache'] && ClassRegistryCache::isSupported())
            {
                //Create the class registry
                $this->__registry = new ClassRegistryCache();

                if(isset($config['cache_namespace'])) {
                    $this->__registry->setNamespace($config['cache_namespace']);
                }
            }
            else $this->__registry = new ClassRegistry();
        }

        return $this->__registry;
    }

    /**
     * Get the config array
     *
     * @param null $key [optional] Lookup specific key in config
     * @return null
     */
    public function getConfig($key = null)
    {
        return $key ? (isset($this->_config[$key]) ? $this->_config[$key] : null) : $this->_config;
    }

    /**
     * Set the config array
     *
     * @param array $config
     * @throw \InvalidArgumentException ifsetting cache key and registry already initialized
     */
    public function setConfig(array $config)
    {
        if(isset($this->__registry) && isset($config['cache']) && $config['cache'] != $this->getConfig('cache'))
        {
            throw new \InvalidArgumentException('Class loader registry can not be changed once initialized');
        }

        //Set the debug mode
        $this->setDebug(isset($config['debug']) && $config['debug']);

        //Store the config
        $this->_config = $config;

        return $this;
    }

    /**
     * Load a class based on a class name
     *
     * @param string  $class    The class name
     * @throws \RuntimeException ifdebug is enabled and the class could not be found in the file.
     * @return boolean  Returns TRUE ifthe class could be loaded, otherwise returns FALSE.
     */
    public function load($class)
    {
        $result = false;

        //Get the path
        $path = $this->getPath( $class, $this->_base_path);

        if($path !== false)
        {
            if(!in_array($path, get_included_files()) && file_exists($path))
            {
                $result = true;

                require $path;

                if($this->_debug)
                {
                    if(!$this->isDeclared($class))
                    {
                        throw new \RuntimeException(sprintf(
                            'The autoloader expected class "%s" to be defined in file "%s".
                            The file was found but the class was not in it, the class name
                            or namespace probably has a typo.', $class, $path
                        ));
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get the path based on a class name
     *
     * @param string $class    The class name
     * @param string $base     The base_path. ifNULL the global base path will be used.
     * @return string|boolean  Returns canonicalized absolute pathname or FALSE of the class could not be found.
     */
    public function getPath($class, $base = null)
    {
        $result = false;

        $base = $base ? $base : $this->_base_path;
        $key  = $base ? $class.'-'.$base : $class;

        //Switch the namespace
        if(!$this->getRegistry()->has($key))
        {
            foreach($this->getLocatorsForClass($class) as $namespace => $locators)
            {
                // * is a special catch all, extract the namespace from the class
                if($namespace == '*')
                {
                    $namespace = explode('\\', $class);
                    array_pop($namespace);
                    $namespace = implode('\\', $namespace);
                }

                foreach($locators as $locator => $paths)
                {
                    $locator = $this->getLocator($locator);

                    foreach($paths as $path)
                    {
                        $result = $locator->locate($class, $namespace, $path ?: $base);

                        if($result !== false && file_exists($result))
                        {
                            break(3);
                        };
                    }
                }
            }

            //Also store ifthe class could not be found to prevent repeated lookups.
            $this->getRegistry()->set($key, $result);

        } else $result = $this->getRegistry()->get($key);

        return $result;
    }

    /**
     * Get the path based on a class name
     *
     * @param string $class     The class name
     * @param string $path      The class path
     * @param string $namespace The global namespace. ifNULL the active global namespace will be used.
     * @return void
     */
    public function setPath($class, $path, $base = null)
    {
        $base = $base ? $base : $this->_base_path;
        $key  = $base ? $class.'-'.$base : $class;

        $this->getRegistry()->set($key, $path);
    }

    /**
     * Find the locators that are associated with the class & namespace
     *
     * @param $class
     * @return array
     */
    protected function getLocatorsForClass($class)
    {
        $matched_locators = array();
        foreach($this->_namespaces as $namespace => $locators)
        {
            if($namespace != '*')
            {
                // ifclass contains a namespace, but namespace is empty, skip
                if(empty($namespace) && strpos($class, '\\'))
                {
                    continue;
                }

                // ifnamespace and class doesn't start with namespace
                if($namespace && strpos('\\'.$class, '\\'.$namespace) !== 0)
                {
                    continue;
                }
            }

            $matched_locators[$namespace] = $locators;
        }

        return $matched_locators;
    }

    /**
     * Register a class locator
     *
     * @param  ClassLocatorInterface $locator
     * @param  bool $prepend iftrue, the locator will be prepended instead of appended.
     * @return void
     */
    public function registerLocator(ClassLocatorInterface $locator, $prepend = false )
    {
        $array = array($locator->getName() => $locator);

        if($prepend) {
            $this->_locators = $array + $this->_locators;
        } else {
            $this->_locators = $this->_locators + $array;
        }

        $this->registerLocatorNamespaces($locator, $locator->getNamespaces());
    }

    /**
     * Get a registered class locator based on his type
     *
     * @param string $type The locator type
     * @return ClassLocatorInterface|null  Returns the object locator or NULL ifit cannot be found.
     */
    public function getLocator($type)
    {
        $result = null;

        if(isset($this->_locators[$type])) {
            $result = $this->_locators[$type];
        }

        return $result;
    }

    /**
     * Registers namesapces => paths mapping for a locator
     *
     * @param string|ClassLocatorInterface $locator The locator to register namespaces against
     * @param array $namespace An array where index is the namespace and value is the path or an array of paths
     */
    public function registerLocatorNamespaces($locator, array $namespaces)
    {
        foreach($namespaces as $namespace => $paths)
        {
            $paths = (array) $paths;

            foreach($paths as $path)
            {
                $this->registerLocatorNamespace($locator, $namespace, $path);
            }
        }
    }

    /**
     * Registers a single namespace to a path for a given locator
     *
     * @param string|ClassLocatorInterface $locator The locator to register namespaces against
     * @param array $namespace An array where index is the namespace and value is the path
     * @param string $path The file path the namespace is registered to
     */
    public function registerLocatorNamespace($locator, $namespace, $path)
    {
        if(!$locator instanceof ClassLocatorInterface)
        {
            $locator = $this->getLocator($locator);
        }

        $name = $locator->getName();

        //Ensure locator is registered already
        if(!$this->getLocator($name))
        {
            throw new \InvalidArgumentException('The locator '.$name.' passed to '.__CLASS__.'::'.__FUNCTION__.' is not registered. Please call registerLocator() instead');
        }

        // Ensure path exists and is readable
        if(!is_dir($path) || !is_readable($path))
        {
            throw new \InvalidArgumentException('Unable to register locator '.$name.' as path doesn\'t exist or is unreadable: '.$path);
        }

        $namespace = trim($namespace, '\\');

        if(!isset($this->_namespaces[$namespace]))
        {
            $this->_namespaces[$namespace] = array();
        }

        if(!isset($this->_namespaces[$namespace][$name]))
        {
            $this->_namespaces[$namespace][$name] = array();
        }

        if(!in_array($path, $this->_namespaces[$namespace][$name]))
        {
            $this->_namespaces[$namespace][$name][] = $path;
        }
    }

    /**
     * Gets the paths for a given namespace
     *
     * @param null|string $namespace The namespace for the paths
     * @param null|string $locator The paths for a specific locator
     * @return array
     */
    public function getNamespacePaths($namespace = null, $locator = null)
    {
        if($namespace && $locator)
        {
            return isset($this->_namespaces[$namespace][$locator]) ? $this->_namespaces[$namespace][$locator] : array();
        }

        if($namespace)
        {
            return isset($this->_namespaces[$namespace]) ? $this->_namespaces[$namespace] : array();
        }

        if($locator)
        {
            $namespaces = array();
            foreach($this->_namespaces as $namespace => $locators)
            {
                if(isset($locators[$locator]))
                {
                    $namespaces[$namespace] = $locators[$locator];
                }
            }

            return $namespaces;
        }

        return array();
    }

    /**
     * Register an alias for a class
     *
     * @param string  $class The original
     * @param string  $alias The alias name for the class.
     */
    public function registerAlias($class, $alias)
    {
        $alias = trim($alias);
        $class = trim($class);

        $this->getRegistry()->alias($class, $alias);
    }

    /**
     * Get the registered alias for a class
     *
     * @param  string $class The class
     * @return array   An array of aliases
     */
    public function getAliases($class)
    {
        return array_search($class, $this->getRegistry()->getAliases());
    }

    /**
     * Get the base path
     *
     * @return string The base path
     */
    public function getBasePath()
    {
        return $this->_base_path;
    }

    /**
     * Set the base path
     *
     * @param string $base_path The base path
     * @return ClassLoader
     */
    public function setBasePath($path)
    {
        $this->_base_path = $path;
        return $this;
    }

    /**
     * Enable or disable class loading
     *
     * ifdebug is enabled the class loader will throw an exception ifa file is found but does not declare the class.
     *
     * @param bool $debug True or false.
     * @return ClassLoader
     */
    public function setDebug($debug)
    {
        $this->_debug = (bool) $debug;
        return $this;
    }

    /**
     * Check ifthe loader is running in debug mode
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->_debug;
    }

    /**
     * Tells ifa class, interface or trait exists.
     *
     * @params string $class
     * @return boolean
     */
    public function isDeclared($class)
    {
        return class_exists($class, false)
        || interface_exists($class, false)
        || (function_exists('trait_exists') && trait_exists($class, false));
    }

    /**
     * Clone
     *
     * Prevent creating clones of this class
     */
    final private function __clone()
    {
        throw new \Exception("An instance of ".get_called_class()." cannot be cloned.");
    }
}