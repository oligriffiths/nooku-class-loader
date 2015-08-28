<?php

class ClassLocatorComposerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Nooku\Library\ClassLocatorPsr
     */
    protected $_locator;

    public function setup()
    {
        $this->_locator = new Nooku\Library\ClassLocatorComposer(array(
            'vendor_path' => dirname(dirname(__DIR__)).'/fixtures/composer'
        ));
    }

    /**
     * Test the locator returns the correct name
     */
    public function testGetName()
    {
        $this->assertEquals('composer', $this->_locator->getName());
    }

    /**
     * Tests the loader to ensure it locates the correct path
     */
    public function testLocate()
    {
        // This is a simple test to ensure the proxying is working, not testing actual composer
        $this->assertEquals('Psr/Foo/Bar/Baz.php', $this->_locator->locate('Psr\Foo\Bar\Baz', null, null));
        $this->assertEquals('Psr/Foo/BarBaz.php', $this->_locator->locate('Psr\Foo\BarBaz', null, null));
        $this->assertEquals('Psr/Foo/Bar/Baz.php', $this->_locator->locate('Psr\Foo\Bar\Baz', 'Psr\Foo\Bar', null, null));
    }
}
