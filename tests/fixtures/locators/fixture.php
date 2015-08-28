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

    public function locate($class, $basepath)
    {
        foreach($this->getNamespaces() as $namespace => $basepath) {

            if (empty($namespace) && strpos($class, '\\')) {
                continue;
            }

            if (strpos('\\' . $class, '\\' . $namespace) !== 0) {
                continue;
            }

            //Remove the namespace from the class name
            $class = ltrim(substr($class, strlen($namespace)), '\\');

            $path = rtrim($basepath,'/').'/'.strtolower($class).'.php';

            if(file_exists($path)){
                return $path;
            }
        }

        return null;
    }
}