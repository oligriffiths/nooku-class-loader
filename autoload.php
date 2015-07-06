<?php
//Include the class loader
require_once dirname(__FILE__).'/lib/class/loader.php';

//Make class loader config available
global $class_loader_config;

Nooku\Library\ClassLoader::getInstance($class_loader_config);