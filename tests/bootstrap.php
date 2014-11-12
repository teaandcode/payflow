<?php

error_reporting(E_ALL | E_STRICT);

// include the composer autoloader
$autoloader = require __DIR__.'/../vendor/autoload.php';

// autoload abstract TestCase classes in test directory
$autoloader->add('TeaAndCode', __DIR__);
