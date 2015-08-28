<?php

use Nooku\Library;

class ClassLocatorFixture extends Library\ClassLocatorAbstract
{
    /**
     * The locator name
     *
     * @var string
     */
    protected static $_name = 'fixture';

    /**
     * Get a fully qualified path based on a class name
     *
     * @param  string $class    The class name
     * @param  string $namespace The namespace/prefix the class was matched against
     * @param  string $basepath The basepath to use to find the class
     * @return string|false     Returns canonicalized absolute pathname or FALSE of the class could not be found.
     */
    public function locate($class, $namespace, $basepath)
    {
        //Remove the namespace from the class name
        $class = ltrim(substr($class, strlen($namespace)), '\\');

        $path = rtrim($basepath,'/').'/'.strtolower($class).'.php';

        if(file_exists($path)){
            return $path;
        }

        return null;
    }
}