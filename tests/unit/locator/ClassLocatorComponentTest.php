<?php

class ClassLocatorComponentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Nooku\Library\ClassLocatorLibrary
     */
    protected $_locator;

    /**
     * @var string The base directory for the namespace
     */
    protected $_basedir;

    public function setup()
    {
        $this->_basedir = TEST_BASEDIR.'/fixtures/classes/component';

        $this->_locator = new Nooku\Library\ClassLocatorComponent(array(
            'namespaces' => array(
                'Fixture\\Component' => $this->_basedir.'/namespaced',
                '\\' => $this->_basedir.'/global',
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
     * Tests that class maps to the correct file path
     *
     * @dataProvider locateProvider
     */
    public function testLocateNamespace($path, $class)
    {
        $this->assertEquals($this->_basedir . '/' . $path, $this->_locator->locate($class, null));
    }

    /**
     * @return array
     */
    public function locateProvider()
    {
        return array(
            array('namespaced/foo/controller/bar.php', 'Fixture\Component\Foo\ControllerBar'),
            array('namespaced/foo/controller/baz/baz.php', 'Fixture\Component\Foo\ControllerBaz'),
            array('namespaced/foo/controller/toolbar/mixin/mixin.php', 'Fixture\Component\Foo\ControllerToolbarMixin'),
            array('namespaced/foo/controller/exception/notfound.php', 'Fixture\Component\Foo\ControllerExceptionNotFound'),
            array('global/foo/controller/bar.php', 'FooControllerBar'),
            array('global/foo/controller/baz/baz.php', 'FooControllerBaz'),
            array('global/foo/controller/toolbar/mixin/mixin.php', 'FooControllerToolbarMixin'),
            array('global/foo/controller/exception/notfound.php', 'FooControllerExceptionNotFound'),
        );
    }
}
