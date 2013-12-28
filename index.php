<?php

define('APP_ENV', 'development');
//define('APP_ENV', 'production');

define('ROOT_DIR', __DIR__);
require 'vendor/autoload.php';
require 'lib/support.php';
require 'lib/renderer.php';
require 'lib/forms.php';
require 'lib/view.php';
require 'lib/params.php';
require 'lib/controller.php';
require 'lib/application.php';

//use RedBean_Facade as R;
class_alias('RedBean_Facade', 'R');

if (APP_ENV != 'production') {
  error_reporting(E_ALL);
} else {
  error_reporting(0);
}

$application = Application::instance();
$application->init();
$application->run();
$application->finalize();
