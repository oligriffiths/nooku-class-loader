<?php

if (!class_exists('ComposerAutoloaderFixture', false)) {
    class ComposerAutoloaderFixture
    {
        public function findFile($class)
        {
            return str_replace('\\', '/', $class).'.php';
        }
    }
}

return new ComposerAutoloaderFixture();