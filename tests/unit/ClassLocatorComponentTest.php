<?php

require_once __DIR__.'/../../src/class/locator/interface.php';
require_once __DIR__.'/../../src/class/locator/abstract.php';
require_once __DIR__.'/../../src/class/locator/component.php';

class ClassLocatorComponentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Nooku\Library\ClassLocatorLibrary
     */
    protected $_locator;

    public function setup()
    {
        $this->_locator = new Nooku\Library\ClassLocatorComponent(array(
            'namespaces' => array(
                'Fixture\\Component\\Foo' => dirname(__DIR__).'/fixtures/classes/component/foo'
            )
        ));
    }

    /**
     * Test the locator returns the correct name
     */
    public function testGetName()
    {
        $this->assertEquals('component', $this->_locator->getName());
    }

    /**
     * Tests the loader to ensure it locates the correct path
     */
    public function testLocate()
    {
        $basedir = dirname(__DIR__).'/fixtures/classes/component/foo';

        $this->assertEquals($basedir.'/controller/bar.php', $this->_locator->locate('Fixture\Component\Foo\ControllerBar', null));
        $this->assertEquals($basedir.'/controller/baz/baz.php', $this->_locator->locate('Fixture\Component\Foo\ControllerBaz', null));
    }

    /**
     * Tests the loader to ensure it locates the correct path
     */
    public function testLocateException()
    {
        $basedir = dirname(__DIR__).'/fixtures/classes/component/foo';

        $this->assertEquals($basedir.'/controller/exception/unauthorized.php', $this->_locator->locate('Fixture\Component\Foo\ControllerExceptionUnauthorized', null));
    }
}
