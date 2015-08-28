<?php

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
                'Psr' => TEST_BASEDIR.'/fixtures/classes/psr'
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

        $this->assertEquals($basedir.'/Foo/Bar/Baz.php', $this->_locator->locate('Psr\Foo\Bar\Baz', null));
        $this->assertEquals($basedir.'/Foo/BarBaz.php', $this->_locator->locate('Psr\Foo\BarBaz', null));
    }
}
