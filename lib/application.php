<?php

class Application {

  private $config, $router;

  public function init() {
    $this->config = $this->loadConfig(ROOT_DIR . '/config/application.json');
    $databaseConfig = $this->loadConfig(ROOT_DIR . '/config/database.json');
    R::setup($databaseConfig->{ENV}->uri, $databaseConfig->{ENV}->username, $databaseConfig->{ENV}->password);
    R::exec('set names utf8');
    $this->router = new AltoRouter();
  }

  public function run() {
    $router = $this->router;
    $router->setBasePath($this->config->basePath);
    include(ROOT_DIR . '/config/routes.php');
    $match = $router->match();

    if ($match === false) {
      http_response_code(404);
    }

    $params = new Params($_GET, $_POST, $match['params']);

    $controllerClass = $this->getController($match['target']);
    $controller = new $controllerClass($this, $params);
    $controller->render($this->getAction($match['target']));
  }

  public function finalize() {
    R::close();
  }

  public function loadConfig($fileName = null) {
    $contents = utf8_encode(file_get_contents($fileName));
    return json_decode($contents);
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

}
