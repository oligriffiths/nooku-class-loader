<?php

require_once __DIR__.'/../../src/class/locator/interface.php';
require_once __DIR__.'/../../src/class/locator/abstract.php';
require_once __DIR__.'/../../src/class/locator/library.php';

class ClassLocatorLibraryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Nooku\Library\ClassLocatorLibrary
     */
    protected $_locator;

    public function setup()
    {
        $this->_locator = new Nooku\Library\ClassLocatorLibrary(array(
            'namespaces' => array(
                'Nooku\\Library' => dirname(__DIR__).'/fixtures/classes/library'
            )
        ));
    }

    /**
     * Test the locator returns the correct name
     */
    public function testGetName()
    {
        $this->assertEquals('library', $this->_locator->getName());
    }

    /**
     * Tests the loader to ensure it locates the correct path
     */
    public function testLocate()
    {
        $basedir = dirname(__DIR__).'/fixtures/classes/library';

        $this->assertEquals($basedir.'/class/loader.php', $this->_locator->locate('Nooku\Library\ClassLoader', $basedir));
        $this->assertEquals($basedir.'/object/object.php', $this->_locator->locate('Nooku\Library\Object', $basedir));
    }

    /**
     * Tests getting a specific namespace
     */
    public function testGetNamespace()
    {
        $this->assertEquals(dirname(__DIR__).'/fixtures/classes/library', $this->_locator->getNamespace('Nooku\\Library'));
    }

    /**
     * Tests getting all namespace
     */
    public function testGetNamespaces()
    {
        $namespaces = $this->_locator->getNamespaces();
        $this->assertContains(dirname(__DIR__).'/fixtures/classes/library', $namespaces);
        $this->assertTrue(isset($namespaces['Nooku\\Library']));
    }
    /**
     * Tests registering a namespace to a path
     */
    public function testRegisternamespace()
    {
        $this->_locator->registerNamespace('SomeNamespace', 'path/to/namespace');

        $this->assertEquals('path/to/namespace', $this->_locator->getNamespace('SomeNamespace'));
        $this->assertNotEquals('incorrect/path', $namespaces = $this->_locator->getNamespace('SomeNamespace'));
        $this->assertFalse($this->_locator->getNamespace('InvalidNamespace'));
    }
}
