<?php

// This is a simple test to ensure the proxying is working, not testing actual composer
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