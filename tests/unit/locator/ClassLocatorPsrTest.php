<?php

require_once __DIR__.'/../../../src/class/locator/interface.php';
require_once __DIR__.'/../../../src/class/locator/abstract.php';
require_once __DIR__.'/../../../src/class/locator/psr.php';

class ClassLocatorPsrTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Nooku\Library\ClassLocatorPsr
     */
    protected $_locator;

    public function setup()
    {
        $this->_locator = new Nooku\Library\ClassLocatorPsr(array(
            'namespaces' => array(
                'Psr' => dirname(dirname(__DIR__)).'/fixtures/classes/psr'
            )
        ));
    }

    /**
     * Test the locator returns the correct name
     */
    public function testGetName()
    {
        $this->assertEquals('psr', $this->_locator->getName());
    }

    /**
     * Tests the loader to ensure it locates the correct path
     */
    public function testLocate()
    {
        $basedir = dirname(dirname(__DIR__)).'/fixtures/classes/psr';

        $this->assertEquals($basedir.'/Foo/Bar/Baz.php', $this->_locator->locate('Psr\Foo\Bar\Baz', 'Psr', $basedir));
        $this->assertEquals($basedir.'/Foo/BarBaz.php', $this->_locator->locate('Psr\Foo\BarBaz', 'Psr', $basedir));
    }
}
