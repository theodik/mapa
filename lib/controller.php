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

  public static function controller_name($className) {
    return strtolower(str_replace("Controller", '', $className));
  }

  public function name() {
    return self::controller_name(get_class($this));
  }

  public function render($action) {
    $result = null;
    if (method_exists($this, $action)) {
      $result = call_user_func(array($this, $action), $this->params);
    }
    if ($result === false) {
      return;
    }

    $builder = new ViewBuilder($this, $action);
    if (get_key($result, 'layout', true)) {
      $builder->layout();
    }
    $builder->view($action);
    $view = $builder->build();

    $context = get_object_vars($this);
    $context['app'] = $this->application;
    $context['controller'] = $this;
    $context['router'] = $this->application->getRouter();
    $context['params'] = $this->params;

    $view->_render($context);
  }

  /// helpers
  protected function redirect($path) {
    $router = $this->application->getRouter();
    $url = $router->generate($path);
    header('Location: ' . $url);
    return false;
  }

}
