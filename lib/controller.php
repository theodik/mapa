<?php

spl_autoload_register(function ($name) {
  include(ROOT_DIR . '/app/controllers/' . getControllerFileName($name) . '.php');
});

function getControllerFileName($name) {
  $matches = Array();
  preg_match('/^(\w+)Controller$/', $name, $matches);
  return strtolower($matches[1]);
}

class Controller {

  protected $params;

  public function __construct($params) {
    $this->params = $params;
  }

  public function render($action) {
    $variables = null;
    if (method_exists($this, $action)) {
      $variables = call_user_func(array($this, $action), $this->params);
    }

    $view = $this->getView($action, $variables);
    $view->render();
  }

  public function getParams() {
    return $this->params;
  }

  protected function getView($action, $variables) {
    $name = get_class($this);
    $vname = getControllerFileName($name);
    $viewName = $vname . '/' . $action;
    $view = new HamlView($viewName, $variables, $this);
    return $view;
  }

}
