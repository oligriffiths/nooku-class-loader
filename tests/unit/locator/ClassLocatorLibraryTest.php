<?php

require_once __DIR__.'/../../../src/class/locator/interface.php';
require_once __DIR__.'/../../../src/class/locator/abstract.php';
require_once __DIR__.'/../../../src/class/locator/library.php';

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
                'Nooku\\Library' => dirname(dirname(__DIR__)).'/fixtures/classes/library'
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
        $basedir = dirname(dirname(__DIR__)).'/fixtures/classes/library';

        $this->assertEquals($basedir.'/class/loader.php', $this->_locator->locate('Nooku\Library\ClassLoader', null));
        $this->assertEquals($basedir.'/object/object.php', $this->_locator->locate('Nooku\Library\Object', null));
    }

    /**
     * Tests the loader to ensure it locates the correct path
     */
    public function testLocateException()
    {
        $basedir = dirname(dirname(__DIR__)).'/fixtures/classes/library';

        $this->assertEquals($basedir.'/database/exception/connection.php', $this->_locator->locate('Nooku\Library\DatabaseExceptionConnection', null));
    }
}
