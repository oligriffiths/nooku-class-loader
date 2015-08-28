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
}
