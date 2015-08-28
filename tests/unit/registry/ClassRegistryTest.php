<?php

class ClassRegistryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Nooku\Library\ClassRegistry
     */
    protected $_registry;
    
    protected $_class = 'ClassName';
    protected $_path = 'path/to/class';

    public function setup()
    {
        $this->_registry = new Nooku\Library\ClassRegistry();

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
     * Tests removing a class
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

    /**
     * Tests finding a class
     */
    public function testFind()
    {
        $this->assertEquals($this->_path, $this->_registry->find($this->_class));
    }

    /**
     * Tests adding an alias
     */
    public function testAlias()
    {
        $alias = 'FooBar';

        $this->_registry->alias($this->_class, $alias);

        $this->assertEquals($this->_path, $this->_registry->find($alias));
        $this->assertContains($this->_class, $this->_registry->getAliases());
        $this->assertArrayHasKey($alias, $this->_registry->getAliases());
    }

    /**
     * Tests getting the classes
     */
    public function testGetClasses()
    {
        $classes = $this->_registry->getClasses();

        $this->assertContains($this->_class, $classes);
    }
}
