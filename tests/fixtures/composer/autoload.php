<?php

if (!class_exists('ComposerAutoloaderFixture', false)) {
    class ComposerAutoloaderFixture
    {
        public function findFile($class)
        {
            return $class;
        }
    }
}

return new ComposerAutoloaderFixture();