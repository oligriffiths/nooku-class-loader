<?php

require_once __DIR__.'/../../../src/class/registry/interface.php';
require_once __DIR__.'/../../../src/class/registry/registry.php';
require_once __DIR__.'/../../../src/class/registry/cache.php';

class ClassRegistryCacheTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Nooku\Library\ClassRegistryCache
     */
    protected $_registry;
    protected $_class = 'ClassName';
    protected $_path = 'path/to/class';

    public function setup()
    {
        if (!Nooku\Library\ClassRegistryCache::isSupported() || !ini_get('apc.enable_cli')) {
            $this->markTestSkipped('must be revisited.');
            return;
        }

        $this->_registry = new Nooku\Library\ClassRegistryCache();
        $this->_registry->setNamespace('phpunit');
    }

    /**
     * Tests setting a class/path in the registry cache, available to other tests as it's cached
     */
    public function testSet()
    {
        $this->_registry->set($this->_class, $this->_path);
    }

    /**
     * Tests setting/getting class => path
     */
    public function testGet()
    {
        $this->assertEquals($this->_path, $this->_registry->get($this->_class));
    }

    /**
     * Tests checking if class is set in registry
     */
    public function testHas()
    {
        $this->assertTrue($this->_registry->has($this->_class));
    }

    /**
     * Tests removing an item from the registry cache
     */
    public function testRemove()
    {
        $this->_registry->remove($this->_class);

        $this->assertFalse($this->_registry->has($this->_class));
    }

    /**
     * Tests removing a class
     */
    public function testClear()
    {
        $this->_registry->clear();

        $this->assertEmpty($this->_registry->getClasses());
    }
}
