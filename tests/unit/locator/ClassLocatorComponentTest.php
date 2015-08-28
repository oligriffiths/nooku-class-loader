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
    protected $_basedir = TEST_BASEDIR.'/fixtures/classes/component';

    public function setup()
    {
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
    public function testLocateNamespace($path, $namespace, $class)
    {
        $path_parts = explode('/',$path);

        $this->assertEquals($this->_basedir . '/' . $path, $this->_locator->locate($class, $namespace, $this->_basedir.'/'.$path_parts[0]));
    }

    /**
     * @return array
     */
    public function locateProvider()
    {
        return array(
            array('namespaced/foo/controller/bar.php', 'Fixture\Component', 'Fixture\Component\Foo\ControllerBar'),
            array('namespaced/foo/controller/baz/baz.php', 'Fixture\Component', 'Fixture\Component\Foo\ControllerBaz'),
            array('namespaced/foo/controller/toolbar/mixin/mixin.php', 'Fixture\Component', 'Fixture\Component\Foo\ControllerToolbarMixin'),
            array('namespaced/foo/controller/exception/notfound.php', 'Fixture\Component', 'Fixture\Component\Foo\ControllerExceptionNotFound'),
            array('global/foo/controller/bar.php', null, 'FooControllerBar'),
            array('global/foo/controller/baz/baz.php', null, 'FooControllerBaz'),
            array('global/foo/controller/toolbar/mixin/mixin.php', null, 'FooControllerToolbarMixin'),
            array('global/foo/controller/exception/notfound.php', null, 'FooControllerExceptionNotFound'),
        );
    }
}
