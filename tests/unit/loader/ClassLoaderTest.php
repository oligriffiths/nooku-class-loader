<?php

class ClassLoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Nooku\Library\ClassLoader
     */
    protected $_loader;

    /**
     * @var string The base directory
     */
    protected $_basedir = TEST_BASEDIR.'/fixtures/classes/fixture';

    public function setup()
    {
        //Setup loader
        $this->_loader = Nooku\Library\ClassLoader::getInstance();

        //Add fixture locator
        $this->_loader->registerLocator(new ClassLocatorFixture(array(
            'namespaces' => array(
                'Fixture' => $this->_basedir
            )
        )));
    }

    /**
     * Tests registering the autoloader
     */
    public function testRegister()
    {
        //Autoloaders are callbacks, arrays are 2 items, first being the object, flatten array
        $loaders = array_map(function($loader){
            return is_array($loader) && count($loader) == 2 ? $loader[0] : $loader;
        }, spl_autoload_functions());

        $this->assertContains($this->_loader, $loaders);
    }

    /**
     * Test unregistering the autoloader
     */
    public function testUnregister()
    {
        $this->_loader->unregister();

        //Autoloaders are callbacks, arrays are 2 items, first being the object, flatten array
        $loaders = array_map(function($loader){
            return is_array($loader) && count($loader) == 2 ? $loader[0] : $loader;
        }, spl_autoload_functions());

        $this->assertNotContains($this->_loader, $loaders);
    }

    /**
     * Tests loading a specific class
     */
    public function testLoad()
    {
        $this->assertTrue($this->_loader->load('Fixture\\Loader'));
        $this->assertTrue(class_exists('Fixture\\Loader', false));
    }

    /**
     * Tests auto loading a specific class
     */
    public function testAutoLoad()
    {
        new Fixture\Loader;
    }

    /**
     * Tests that class paths are resolved correctly
     */
    public function testGetPath()
    {
        $this->assertEquals($this->_basedir.'/loader.php', $this->_loader->getPath('Fixture\\Loader'));
    }

    /**
     * Tests setting a class => path mapping
     */
    public function testSetPath()
    {
        $class = 'MyFooBar';
        $path = 'my/foo/bar.php';

        $this->_loader->setPath($class, $path);
        $this->assertEquals($path, $this->_loader->getPath($class));
    }

    /**
     * Tests registering a locator with the class loader
     */
    public function testRegisterLocator()
    {
        $this->assertNull($this->_loader->getLocator('stub'));

        $this->_loader->registerLocator(new ClassLocatorStub());

        $this->assertInstanceof('ClassLocatorStub', $this->_loader->getLocator('stub'));
    }

    /**
     * Tests registering a class alias
     */
    public function testRegisterAlias()
    {
        $class = 'Fixture\\Loader';
        $alias = 'MyLoader';

        $this->_loader->registerAlias($class, $alias);

        $this->assertContains($alias, $this->_loader->getAliases($class));
    }

    /**
     * Tests getting and setting the base path
     */
    public function testBasePath()
    {
        $this->assertNull($this->_loader->getBasePath());

        $path = dirname(__DIR__);
        $this->_loader->setBasePath($path);

        $this->assertEquals($path, $this->_loader->getBasePath());
    }

    /**
     * Tests setting debug flag
     */
    public function testDebug()
    {
        $this->assertFalse($this->_loader->isDebug());

        $this->_loader->setDebug(true);
        $this->assertTrue($this->_loader->isDebug());

        $this->_loader->setDebug(false);
    }

    /**
     * Tests that classes are/aren't declared
     */
    public function testIsDeclared()
    {
        $this->assertTrue($this->_loader->isDeclared('ClassLocatorFixture'));
        $this->assertFalse($this->_loader->isDeclared('FooBarBaz'));
    }

    /**
     * Ensure close in private, cloning is now allowed
     */
    public function testNoClone()
    {
        $reflection = new ReflectionClass('Nooku\\Library\\ClassLoader');
        $method = $reflection->getMethod('__clone');

        $this->assertTrue($method->isPrivate());
    }

    /**
     * Tests registering multiple namespaces and multiple paths
     */
    public function testRegisterLocatorNamespaces()
    {
        $this->_loader->registerLocatorNamespaces('library', array(
            'Fake\\Namespace\\One' => array(
                __DIR__,
                dirname(__DIR__),
            ),

            'Fake\\Namespace\\Two' => array(
                TEST_BASEDIR.'/fixtures',
                TEST_BASEDIR.'/unit',
            )
        ));

        $this->assertContains(__DIR__, $this->_loader->getNamespacePaths('Fake\\Namespace\\One', 'library'));
        $this->assertContains(dirname(__DIR__), $this->_loader->getNamespacePaths('Fake\\Namespace\\One', 'library'));

        $this->assertContains(TEST_BASEDIR.'/fixtures', $this->_loader->getNamespacePaths('Fake\\Namespace\\Two', 'library'));
        $this->assertContains(TEST_BASEDIR.'/unit', $this->_loader->getNamespacePaths('Fake\\Namespace\\Two', 'library'));
    }

    /**
     * Tests registering a single namespace to a single path
     */
    public function testRegisternamespace()
    {
        $this->_loader->registerLocatorNamespace('library', 'Another\\Fake\\Namespace', __DIR__);

        $this->assertContains(__DIR__, $this->_loader->getNamespacePaths('Another\\Fake\\Namespace', 'library'));
    }

    /**
     * Tests the registered namespaces resolve to the correct file paths
     */
    public function testGetNamespacePaths()
    {
        $fixture_path = $this->_basedir;
        $library_path = dirname(TEST_BASEDIR).'/src';

        // Get namespace path
        $namespaces = $this->_loader->getNamespacePaths('Fixture');
        $this->assertArrayHasKey('fixture', $namespaces);
        $this->assertContains($fixture_path, $namespaces['fixture']);

        $namespaces = $this->_loader->getNamespacePaths('Nooku\\Library');
        $this->assertArrayHasKey('library', $namespaces);
        $this->assertContains($library_path, $namespaces['library']);

        // Get locator namespaces
        $locator = $this->_loader->getNamespacePaths(null, 'fixture');
        $this->assertArrayHasKey('Fixture', $locator);
        $this->assertContains($fixture_path, $locator['Fixture']);

        $locator = $this->_loader->getNamespacePaths(null, 'library');
        $this->assertArrayHasKey('Nooku\\Library', $locator);
        $this->assertContains($library_path, $locator['Nooku\\Library']);

        // Get namespace & locator paths
        $paths = $this->_loader->getNamespacePaths('Fixture', 'fixture');
        $this->assertContains($fixture_path, $paths);

        $paths = $this->_loader->getNamespacePaths('Nooku\\Library', 'library');
        $this->assertContains($library_path, $paths);

        // Nonexistant namespace
        $paths = $this->_loader->getNamespacePaths('Something', 'library');
        $this->assertEmpty($paths);

        // Nonexistant locator
        $paths = $this->_loader->getNamespacePaths('Nooku\\Library', 'missing');
        $this->assertEmpty($paths);
    }
}
