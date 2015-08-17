<?php

use Nooku\Library;

class ClassLocatorStub extends Library\ClassLocatorAbstract
{
    /**
     * The locator name
     *
     * @var string
     */
    protected static $_name = 'stub';

    public function locate($class, $basepath)
    {
        return null;
    }
}