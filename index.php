<?php

define('ENV', 'development');
//define('ENV', 'production');

define('ROOT_DIR', __DIR__);
require 'vendor/autoload.php';
require 'lib/params.php';
require 'lib/view.php';
require 'lib/controller.php';
require 'lib/application.php';

//use RedBean_Facade as R;
class_alias('RedBean_Facade', 'R');

if (ENV != 'production') {
  error_reporting(E_ALL);
} else {
  error_reporting(0);
}

$application = new Application();
$application->init();
$application->run();
$application->finalize();
