<?php

spl_autoload_register(function ($name) {
  if (!preg_match('/^\w+Controller$/', $name)) {
    return;
  }
  include(ROOT_DIR . '/app/controllers/' . getControllerFileName($name) . '.php');
});

function getControllerFileName($name) {
  $matches = Array();
  preg_match('/^(\w+)Controller$/', $name, $matches);
  return strtolower($matches[1]);
}

class Controller {

  protected $params, $application;

  public function __construct($application, $params) {
    $this->application = $application;
    $this->params = $params;
  }

  public function render($action) {
    $variables = null;
    if (method_exists($this, $action)) {
      $variables = call_user_func(array($this, $action), $this->params);
    }
    if ($variables === false) {
      return;
    }
    $view = $this->getView($action, $variables);
    $view->render();
  }

  public function getParams() {
    return $this->params;
  }

  public function getApplication() {
    return $this->application;
  }

  public function getRouter() {
    return $this->application->getRouter();
  }

  protected function getView($action, $variables) {
    $name = get_class($this);
    $vname = getControllerFileName($name);
    $viewName = $vname . '/' . $action;
    $view = new HamlView($viewName, $variables, $this);
    return $view;
  }

  /// helpers
  protected function redirect($path) {
    $router = $this->application->getRouter();
    $url = $router->generate($path);
    header('Location: ' . $url);
    return false;
  }

}
