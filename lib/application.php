<?php

class Application {

  protected static $instance = null;
  private $config, $router;

  private function __construct() {
  }

  public static function instance() {
    if (!self::$instance) {
      self::$instance = new Application();
    }

    return self::$instance;
  }

  public function init() {
    $this->config = $this->loadConfig(ROOT_DIR . '/config/application.json');
    $databaseConfig = $this->loadConfig(ROOT_DIR . '/config/database.json');
    R::setup($databaseConfig->{APP_ENV}->uri, $databaseConfig->{APP_ENV}->username, $databaseConfig->{APP_ENV}->password);
    R::exec('set names utf8');
    if (APP_ENV == 'production') {
      R::freeze(true);
    }
    $this->router = new AltoRouter();
  }

  public function run() {
    $router = $this->router;
    $router->setBasePath($this->config->basePath);
    include(ROOT_DIR . '/config/routes.php');
    $match = $router->match();

    if ($match === false) {
      http_response_code(404);
      $this->render_404();
      return;
    }

    $params = new Params(array_merge($match['params'], array('action' => $this->getAction($match['target']))));

    $controllerClass = $this->getController($match['target']);
    $controller = new $controllerClass($this, $params);
    $controller->render_view($this->getAction($match['target']));
  }

  public function finalize() {
    R::close();
    self::$instance = null;
  }

  public function loadConfig($fileName = null) {
    $contents = utf8_encode(file_get_contents($fileName));
    return json_decode($contents);
  }

  public function config() {
    return $this->config;
  }

  public function getRouter() {
    return $this->router;
  }

  protected function getController($string) {
    $split = explode('#', $string);
    $klass = $split[0];
    return ucfirst($klass) . 'Controller';
  }

  protected function getAction($string) {
    $split = explode('#', $string);
    $action = $split[1];
    return $action;
  }

  protected function render_404() {
    $filename = ROOT_DIR . '/app/views/404.html';
    if (file_exists($filename)){
      echo file_get_contents($filename);
    } else {
      echo "<html><head><meta charset=\"utf-8\"></head><body><h1>404 Not Found</h1><hr>"
        ."Additional error while rendering 404 file: File `$filename' not found.</body></html>";
    }
  }
}
