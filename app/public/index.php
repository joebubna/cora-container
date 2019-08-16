<?php
// Error Reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include Composer autoload
require('../vendor/autoload.php');

// OPTIONAL: grab config options to pass in to the autoloader
$config = include('../config/autoload.php');

// This register's Cora's autoload functions.
$autoload = new \Cora\Autoload($config);
$autoload->register();

// Create a new container
$container = new \Cora\Container();

// Grab a resource out (using di_config functions on classes)
$test = $container->{\Classes\TestMaster::class}("Jessy");

echo $test->sayHi();

$test1 = $container->get(\Classes\TestMaster::class, 'Jacob');
var_dump($test1->getName());