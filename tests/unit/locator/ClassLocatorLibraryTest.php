<?php

class ClassLocatorLibraryTest extends PHPUnit_Framework_TestCase
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
        $this->_basedir = dirname(dirname(__DIR__)).'/fixtures/classes/library';

        $this->_locator = new Nooku\Library\ClassLocatorLibrary(array(
            'namespaces' => array(
                'Nooku\\Library' => $this->_basedir
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
     * Tests that class maps to the correct file path
     *
     * @dataProvider locateProvider
     */
    public function testLocate($path, $class)
    {
        $this->assertEquals($this->_basedir . '/' .$path, $this->_locator->locate($class, null));
    }

    /**
     * @return array
     */
    public function locateProvider()
    {
        return array(
            array('object/object.php', 'Nooku\Library\Object'),
            array('class/loader.php', 'Nooku\Library\ClassLoader'),
            array('object/manager/manager.php', 'Nooku\Library\ObjectManager'),
            array('object/manager/interface.php', 'Nooku\Library\ObjectManagerInterface'),
            array('database/exception/notfound.php', 'Nooku\Library\DatabaseExceptionNotFound'),
            array('controller/toolbar/mixin/mixin.php', 'Nooku\Library\ControllerToolbarMixin'),
            array('controller/toolbar/mixin/interface.php', 'Nooku\Library\ControllerToolbarMixinInterface'),
        );
    }
}
