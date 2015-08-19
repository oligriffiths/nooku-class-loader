<?php

require_once __DIR__.'/../../../src/class/locator/interface.php';
require_once __DIR__.'/../../../src/class/locator/abstract.php';
require_once __DIR__.'/../../fixtures/locators/stub.php';

class ClassLocatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ClassLocatorStub
     */
    protected $_locator;

    public function setup()
    {
        $this->_locator = new ClassLocatorStub(array(
            'namespaces' => array(
                'The\\Namespace' => '/path/to/namespace'
            )
        ));
    }

    /**
     * Test the locator returns the correct name
     */
    public function testGetName()
    {
        $this->assertEquals('stub', $this->_locator->getName());
    }

    /**
     * Tests getting a specific namespace
     */
    public function testGetNamespace()
    {
        $this->assertEquals('/path/to/namespace', $this->_locator->getNamespace('The\\Namespace'));
    }

    /**
     * Tests getting all namespace
     */
    public function testGetNamespaces()
    {
        $namespaces = $this->_locator->getNamespaces();
        $this->assertContains('/path/to/namespace', $namespaces);
        $this->assertArrayHasKey('The\\Namespace', $namespaces);
    }
    /**
     * Tests registering a namespace to a path
     */
    public function testRegisternamespace()
    {
        $this->_locator->registerNamespace('SomeNamespace', '/path/to/namespace');

        $this->assertEquals('/path/to/namespace', $this->_locator->getNamespace('SomeNamespace'));
        $this->assertNotEquals('incorrect/path', $namespaces = $this->_locator->getNamespace('SomeNamespace'));
        $this->assertFalse($this->_locator->getNamespace('InvalidNamespace'));
    }
}
